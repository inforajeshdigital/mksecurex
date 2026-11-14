<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Industry;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function admin(): User
    {
        return User::whereHas('role', fn ($q) => $q->where('slug', 'super-admin'))->first();
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/about-us', '/services', '/industries', '/contact-us', '/privacy-policy'] as $url) {
            $this->get($url)->assertOk()->assertSee('<link rel="canonical"', false);
        }
        foreach (Service::all() as $s) {
            $this->get('/services/'.$s->slug)->assertOk()->assertSee($s->title);
        }
        foreach (Industry::all() as $i) {
            $this->get('/industries/'.$i->slug)->assertOk()->assertSee($i->title);
        }
    }

    public function test_seo_endpoints_and_404(): void
    {
        $this->get('/robots.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')->assertSee('Disallow: /admin/');
        $this->get('/sitemap.xml')->assertOk()->assertSee('<urlset', false)->assertSee('/services/security-services')->assertDontSee('/admin');
        $this->get('/this-page-does-not-exist')->assertNotFound()->assertSee('Page Not Found');
        $this->get('/services/unknown-service')->assertNotFound();
    }

    public function test_each_page_has_single_title_and_canonical(): void
    {
        $html = $this->get('/services/security-services')->getContent();
        $this->assertSame(1, substr_count($html, '<title>'));
        $this->assertSame(1, substr_count($html, 'rel="canonical"'));
        $this->assertStringContainsString('Security Services | MK Securex', $html);
    }

    public function test_noindex_page_excluded_from_sitemap(): void
    {
        $s = Service::where('slug', 'payroll-management-services')->first();
        $s->saveSeo(['robots_index' => false, 'robots_follow' => true]);
        \Cache::forget('sitemap.xml');
        $this->get('/sitemap.xml')->assertDontSee('payroll-management-services');
        $this->get('/services/payroll-management-services')->assertSee('noindex, follow');
    }

    public function test_redirects_work(): void
    {
        Redirect::create(['from_path' => '/old-security', 'to_url' => '/services/security-services', 'type' => 301, 'is_active' => true]);
        $this->get('/old-security')->assertStatus(301)->assertRedirect('/services/security-services');
        $this->assertSame(1, Redirect::first()->hits);
    }

    public function test_enquiry_submission(): void
    {
        $service = Service::first();
        $this->postJson('/enquiry', [
            'full_name' => 'Test User', 'phone' => '+91 98765 43210', 'email' => 'test@example.com',
            'service_id' => $service->id, 'workforce_size' => '11 – 50', 'location' => 'Delhi', 'message' => 'Need 10 guards', 'consent' => '1',
        ])->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('enquiries', ['email' => 'test@example.com', 'service_name' => $service->title, 'status' => 'new']);

        $this->postJson('/enquiry', ['full_name' => 'X'])->assertStatus(422)->assertJsonValidationErrors(['phone', 'email', 'consent']);

        // honeypot
        $this->postJson('/enquiry', ['website' => 'spam', 'full_name' => 'Bot'])->assertOk();
        $this->assertSame(1, Enquiry::count());
    }

    public function test_admin_login_and_guard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
        $this->post('/admin/login', ['email' => $this->admin()->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => $this->admin()->email, 'password' => env('ADMIN_PASSWORD', 'ChangeMe@123')])->assertRedirect('/admin/dashboard');
    }

    public function test_all_admin_screens_render(): void
    {
        $this->actingAs($this->admin());
        $service = Service::first();
        $industry = Industry::first();
        $about = Page::where('slug', 'about-us')->first();
        $section = $about->sections()->first();
        $enquiry = Enquiry::create(['full_name' => 'A', 'phone' => '9999999999', 'email' => 'a@b.com', 'consent' => true]);

        $urls = ['/admin/dashboard', '/admin/homepage', '/admin/pages', '/admin/pages/create', "/admin/pages/{$about->id}/edit", "/admin/pages/{$about->id}/preview",
            '/admin/sliders/create', '/admin/sliders/1/edit', "/admin/sections/{$section->id}/edit", "/admin/pages/{$about->id}/sections/create?type=gallery",
            '/admin/services', '/admin/services/create', "/admin/services/{$service->id}/edit", '/admin/industries', '/admin/industries/create', "/admin/industries/{$industry->id}/edit",
            '/admin/testimonials', '/admin/testimonials/create', '/admin/faqs', '/admin/faqs/create', '/admin/enquiries', "/admin/enquiries/{$enquiry->id}", '/admin/enquiries/export',
            '/admin/media', '/admin/media/picker', '/admin/seo', "/admin/seo/service/{$service->id}", '/admin/robots', '/admin/sitemap', '/admin/redirects', '/admin/redirects/create',
            '/admin/menus', '/admin/menus?menu=footer_quick', '/admin/users', '/admin/users/create', '/admin/roles/create', '/admin/activity', '/admin/maintenance', '/admin/profile', '/admin/password'];
        foreach (array_keys(config('settings.groups')) as $g) {
            $urls[] = "/admin/settings/$g";
        }
        foreach (array_keys(config('sections.types')) as $t) {
            $urls[] = "/admin/pages/{$about->id}/sections/create?type=$t";
        }
        foreach ($urls as $u) {
            $this->get($u)->assertOk();
        }
    }

    public function test_admin_crud_flows(): void
    {
        $this->actingAs($this->admin());

        // Create a service with features, FAQ, SEO
        $this->post('/admin/services', [
            'title' => 'CCTV Monitoring', 'status' => 'published', 'short_description' => 'Remote video monitoring.',
            'features' => [['title' => 'Live monitoring', 'text' => '24x7']], 'faqs' => [['question' => 'Q?', 'answer' => 'A.']],
            'industry_ids' => [Industry::first()->id], 'seo' => ['meta_title' => 'CCTV Monitoring Services', 'robots_index' => '1', 'robots_follow' => '1'],
        ])->assertRedirect();
        $s = Service::where('slug', 'cctv-monitoring')->first();
        $this->assertNotNull($s);
        $this->assertSame('CCTV Monitoring Services', $s->seo->meta_title);
        $this->assertCount(1, $s->faqs);
        $this->get('/services/cctv-monitoring')->assertOk()->assertSee('Live monitoring')->assertSee('<title>CCTV Monitoring Services</title>', false);

        // Settings update reflects on frontend
        $this->put('/admin/settings/contact', ['contact_phone' => '+91 11111 22222', 'contact_email' => 'hello@mk.test', 'workforce_sizes' => "1-5\n6-10"])->assertRedirect();
        $this->get('/')->assertSee('+91 11111 22222');

        // Page builder: add a section and toggle
        $about = Page::where('slug', 'about-us')->first();
        $this->post("/admin/pages/{$about->id}/sections", ['type' => 'features', 'is_active' => '1', 'data' => ['heading' => 'Our Certifications Block'], 'items' => [['icon' => 'fa-star', 'title' => 'Card A', 'text' => 'x']]])->assertRedirect();
        $this->get('/about-us')->assertSee('Our Certifications Block')->assertSee('Card A');
        $sec = \App\Models\PageSection::latest('id')->first();
        $this->postJson("/admin/sections/{$sec->id}/toggle")->assertJson(['active' => false]);
        $this->get('/about-us')->assertDontSee('Our Certifications Block');

        // Reorder
        $ids = Service::orderBy('sort_order')->pluck('id')->reverse()->values()->all();
        $this->postJson('/admin/reorder', ['model' => 'services', 'ids' => $ids])->assertJson(['success' => true]);
        $this->assertSame($ids[0], Service::orderBy('sort_order')->first()->id);

        // Redirect loop prevention
        $this->post('/admin/redirects', ['from_path' => '/a', 'to_url' => '/a', 'type' => 301, 'is_active' => 1])->assertSessionHasErrors('to_url');

        // Media upload
        $this->post('/admin/media', ['files' => [UploadedFile::fake()->image('guard.jpg', 800, 600)]])->assertRedirect();
        $this->assertDatabaseCount('media', 1);
        app(\App\Services\ImageService::class)->delete(\App\Models\Media::first());

        // Robots edit
        $this->put('/admin/robots', ['content' => "User-agent: *\nDisallow: /private"])->assertRedirect();
        $this->get('/robots.txt')->assertSee('Disallow: /private');

        // Custom page creation via builder
        $this->post('/admin/pages', ['title' => 'Careers', 'status' => 'published'])->assertRedirect();
        $this->get('/careers')->assertOk()->assertSee('Careers');
        $this->post('/admin/pages', ['title' => 'Services', 'slug' => 'services', 'status' => 'published'])->assertSessionHasErrors('slug');
    }

    public function test_image_show_hide_options_and_no_duplicates(): void
    {
        $slide = \App\Models\Slider::orderBy('sort_order')->first();
        $slide->update(['image' => 'uploads/x/hero.jpg', 'show_background' => true, 'show_side_image' => false]);
        $html = $this->get('/')->getContent();
        $this->assertStringContainsString("hero-bg\" style=\"background-image:url('".asset('uploads/x/hero.jpg'), $html);
        $this->assertStringNotContainsString('hero-img-card', $html);

        $slide->update(['show_background' => false, 'show_side_image' => true]);
        $html = $this->get('/')->getContent();
        $this->assertStringNotContainsString("background-image:url('".asset('uploads/x/hero.jpg'), $html);
        $this->assertStringContainsString('hero-img-card', $html);

        // Same picture must not appear as banner AND inside the content
        $s = Service::where('slug', 'security-services')->first();
        $s->update(['image' => 'uploads/x/s.jpg', 'banner_image' => null, 'show_banner' => true, 'show_image' => true]);
        $this->get('/services/security-services')->assertDontSee('<img src="'.asset('uploads/x/s.jpg'), false);
        $s->update(['show_banner' => false]);
        $this->get('/services/security-services')->assertSee('<img src="'.asset('uploads/x/s.jpg'), false);
        $s->update(['show_banner' => true, 'banner_image' => 'uploads/x/b.jpg', 'show_image' => false]);
        $this->get('/services/security-services')->assertDontSee('<img src="'.asset('uploads/x/s.jpg'), false)->assertSee('uploads/x/b.jpg');

        // Admin forms save the switches and show size guidance
        $this->actingAs($this->admin());
        $this->get("/admin/services/{$s->id}/edit")->assertSee('Recommended size')->assertSee('1920 × 600 px');
        $this->get("/admin/sliders/{$slide->id}/edit")->assertSee('1920 × 1080 px')->assertSee('Framed image on the right');
        $this->put("/admin/sliders/{$slide->id}", ['heading' => 'H', 'is_active' => '1', 'show_background' => '1'])->assertRedirect();
        $this->assertTrue($slide->fresh()->show_background);
        $this->assertFalse($slide->fresh()->show_side_image);
    }

    public function test_role_permissions_are_enforced(): void
    {
        $sales = User::create(['name' => 'Sales', 'email' => 'sales@test.com', 'password' => 'Password123', 'role_id' => Role::where('slug', 'sales')->first()->id, 'is_active' => true]);
        $this->actingAs($sales);
        $this->get('/admin/enquiries')->assertOk();
        $this->get('/admin/services')->assertForbidden();
        $this->get('/admin/settings/general')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
    }
}
