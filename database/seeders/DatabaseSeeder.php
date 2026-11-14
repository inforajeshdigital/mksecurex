<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Industry;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\Page;
use App\Models\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->users();
        $services = $this->services();
        $this->industries($services);
        $this->pages();
        $this->menus();
        $this->faqs();
        $this->testimonials();
        Setting::flush();
    }

    protected function settings(): void
    {
        $groups = [];
        foreach (config('settings.groups') as $g => $def) {
            foreach (array_keys($def['fields']) as $k) {
                $groups[$k] = $g;
            }
        }
        foreach (config('settings.defaults') as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['group' => $groups[$key] ?? 'seo', 'value' => $value]);
        }
    }

    protected function users(): void
    {
        $super = Role::updateOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'permissions' => ['*'], 'description' => 'Full access to everything']);
        Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Admin', 'description' => 'Manages content, leads and SEO', 'permissions' => array_values(array_diff(array_keys(config('admin.permissions')), ['users', 'maintenance']))]);
        Role::updateOrCreate(['slug' => 'editor'], ['name' => 'Editor', 'description' => 'Content editing only', 'permissions' => ['dashboard', 'homepage', 'pages', 'services', 'industries', 'testimonials', 'faqs', 'media']]);
        Role::updateOrCreate(['slug' => 'sales'], ['name' => 'Sales / Enquiry Manager', 'description' => 'Handles enquiries only', 'permissions' => ['dashboard', 'enquiries']]);

        $email = env('ADMIN_EMAIL', 'admin@mksecurex.com');
        User::firstOrCreate(['email' => $email], [
            'name' => 'Super Admin',
            'password' => env('ADMIN_PASSWORD', 'ChangeMe@123'),
            'role_id' => $super->id,
            'is_active' => true,
        ]);
    }

    protected function seo($model, string $title, string $desc, string $focus = '', string $secondary = ''): void
    {
        $model->saveSeo([
            'meta_title' => $title, 'meta_description' => $desc, 'focus_keyword' => $focus, 'secondary_keywords' => $secondary,
            'robots_index' => true, 'robots_follow' => true,
        ]);
    }

    protected function services(): array
    {
        $data = require __DIR__.'/data/services.php';
        $process = $data['__process'];
        unset($data['__process']);
        $map = [];
        foreach (array_values($data) as $i => $s) {
            $service = Service::updateOrCreate(['slug' => $s['slug']], [
                'title' => $s['title'], 'icon' => $s['icon'], 'short_description' => $s['short'], 'intro' => $s['intro'],
                'description' => $s['desc'], 'features' => $s['features'], 'benefits' => $s['benefits'], 'process' => $process,
                'image_alt' => $s['title'].' by MK Securex', 'sort_order' => $i + 1, 'status' => 'published',
            ]);
            $service->faqs()->delete();
            foreach ($s['faqs'] as $j => $f) {
                $service->faqs()->create(['question' => $f['q'], 'answer' => $f['a'], 'category' => 'item', 'sort_order' => $j + 1]);
            }
            $this->seo($service, $s['title'].' | MK Securex', $s['short'], $s['title']);
            $map[$s['slug']] = $service->id;
        }

        return $map;
    }

    protected function industries(array $services): void
    {
        $focus = [
            'manufacturing' => 'Manpower for Manufacturing Industry', 'warehousing-logistics' => 'Warehouse Manpower Services',
            'construction' => 'Construction Labour Supply', 'healthcare' => 'Healthcare Facility Management',
            'hospitality' => 'Hotel Housekeeping Services', 'corporate-offices' => 'Corporate Office Facility Management',
            'education' => 'School Security and Housekeeping', 'retail' => 'Retail Manpower Services',
        ];
        foreach (require __DIR__.'/data/industries.php' as $i => $d) {
            $industry = Industry::updateOrCreate(['slug' => $d['slug']], [
                'title' => $d['title'], 'icon' => $d['icon'], 'short_description' => $d['short'], 'intro' => $d['intro'], 'description' => $d['desc'],
                'workforce_requirements' => $d['workforce'], 'challenges' => $d['challenges'], 'solutions' => $d['solutions'], 'benefits' => $d['benefits'],
                'image_alt' => 'Security and manpower services for '.$d['title'], 'sort_order' => $i + 1, 'status' => 'published',
            ]);
            $industry->services()->sync(array_values(array_filter(array_map(fn ($s) => $services[$s] ?? null, $d['services']))));
            $industry->faqs()->delete();
            foreach ($d['faqs'] as $j => $f) {
                $industry->faqs()->create(['question' => $f['q'], 'answer' => $f['a'], 'category' => 'item', 'sort_order' => $j + 1]);
            }
            $this->seo($industry, $focus[$d['slug']].' | MK Securex', $d['short'], $focus[$d['slug']]);
        }
    }

    protected function page(string $slug, array $attrs, array $sections = []): Page
    {
        $page = Page::updateOrCreate(['slug' => $slug], $attrs + ['status' => 'published', 'is_system' => true, 'published_on' => now()]);
        if ($page->sections()->count() === 0) {
            foreach ($sections as $i => [$type, $data, $active]) {
                $page->sections()->create(['type' => $type, 'data' => $data, 'sort_order' => $i + 1, 'is_active' => $active ?? true]);
            }
        }

        return $page;
    }

    protected function pages(): void
    {
        $whyItems = [
            ['icon' => 'fa-user-shield', 'title' => 'Trained Professional Workforce', 'text' => 'Personnel are screened, briefed on site rules and deployed in proper uniform with clear post instructions.'],
            ['icon' => 'fa-handshake', 'title' => 'Reliable Service Delivery', 'text' => 'Rosters, relief staff and quick replacements keep every post and shift covered.'],
            ['icon' => 'fa-sliders', 'title' => 'Flexible Workforce Planning', 'text' => 'Scale headcount up or down for seasonal peaks, new sites or project phases.'],
            ['icon' => 'fa-clipboard-check', 'title' => 'Quality Monitoring', 'text' => 'Supervisor visits, attendance tracking and feedback reviews maintain agreed standards.'],
            ['icon' => 'fa-headset', 'title' => 'Dedicated Coordination', 'text' => 'A single point of contact for updates, escalations and new requirements.'],
            ['icon' => 'fa-file-contract', 'title' => 'Transparent Operations', 'text' => 'Roles, responsibilities, timings and commercials documented upfront.'],
        ];
        $processItems = [
            ['title' => 'Understand Requirements', 'text' => 'We discuss your roles, headcount, locations, shift timings and service expectations in detail.'],
            ['title' => 'Plan Workforce and Resources', 'text' => 'We prepare a staffing and service plan with role profiles, schedules and a clear scope of work.'],
            ['title' => 'Deployment and Coordination', 'text' => 'Suitable staff are onboarded and deployed, with supervisors coordinating handover at your site.'],
            ['title' => 'Monitoring and Ongoing Support', 'text' => 'We track attendance and performance, share updates and respond quickly to changes or replacements.'],
        ];

        $home = $this->page('home', ['title' => 'Home', 'sort_order' => 1, 'excerpt' => 'Security services, manpower supply and facility management solutions for businesses.'], [
            ['hero_slider', [], true],
            ['text_image', [
                'badge' => 'About MK Securex', 'heading' => 'A Security & Workforce Partner That Starts With Your Requirements',
                'content' => '<p>MK Securex Pvt. Ltd. is a B2B services company providing security services, manpower supply and facility management support to organisations across sectors. We work as an extension of your operations team — so you can focus on your business while we manage the people and services behind it.</p>',
                'layout' => 'right', 'background' => 'white', 'button_text' => 'Read More About Us', 'button_url' => '/about-us',
                'items' => [
                    ['icon' => 'fa-magnifying-glass', 'title' => 'Understand your needs', 'text' => 'We study your site, shifts, roles and risks before proposing a plan.'],
                    ['icon' => 'fa-people-arrows', 'title' => 'Coordinate deployment', 'text' => 'Sourcing, onboarding and deployment of suitable staff for each role.'],
                    ['icon' => 'fa-chart-line', 'title' => 'Support ongoing operations', 'text' => 'Regular check-ins, replacements and reporting after deployment.'],
                ],
            ], true],
            ['service_cards', ['badge' => 'Our Core Services', 'heading' => 'Security, Workforce & Facility Services Under One Partner', 'subheading' => 'Choose a single service or combine several into one managed plan — each scoped around your site, roles and schedule.', 'background' => 'light', 'limit' => 6, 'button_text' => 'View All Services', 'button_url' => '/services'], true],
            ['industry_cards', ['badge' => 'Industries We Serve', 'heading' => 'Support Planned for the Way Your Sector Works', 'subheading' => 'Each industry has its own roles, shift patterns and site conditions. We plan staffing and security around them.', 'background' => 'white', 'button_text' => 'View All Industries', 'button_url' => '/industries'], true],
            ['features', ['badge' => 'Why Choose MK Securex', 'heading' => 'A Structured, Transparent Way to Manage Security & Manpower', 'subheading' => 'We focus on the fundamentals that keep outsourced teams running smoothly.', 'columns' => '3', 'background' => 'dark', 'items' => $whyItems], true],
            ['process', ['badge' => 'How Our Process Works', 'heading' => 'From Requirement to Reliable Deployment in Four Steps', 'background' => 'white', 'items' => $processItems], true],
            ['stats', ['badge' => 'Our Numbers', 'heading' => 'Business Statistics', 'background' => 'primary', 'items' => [
                ['icon' => 'fa-calendar-check', 'value' => '0', 'suffix' => '+', 'title' => 'Years of Experience'],
                ['icon' => 'fa-handshake', 'value' => '0', 'suffix' => '+', 'title' => 'Clients Served'],
                ['icon' => 'fa-users', 'value' => '0', 'suffix' => '+', 'title' => 'Workforce Deployed'],
                ['icon' => 'fa-industry', 'value' => '8', 'suffix' => '', 'title' => 'Industries Supported'],
            ]], false],
            ['testimonials', ['badge' => 'Client Testimonials', 'heading' => 'What Our Clients Say', 'background' => 'light'], true],
            ['enquiry_form', ['badge' => 'Request a Quote', 'heading' => 'Tell Us About Your Requirement', 'subheading' => 'Share a few details and our team will contact you to discuss a suitable plan.', 'background' => 'white', 'items' => [
                ['icon' => 'fa-users-viewfinder', 'title' => 'Any Workforce Size', 'text' => 'From a few staff for one office to large multi-department teams.'],
                ['icon' => 'fa-map-location-dot', 'title' => 'Single or Multiple Locations', 'text' => 'Consistent coordination across sites, plants and warehouses.'],
                ['icon' => 'fa-clock', 'title' => 'Your Operating Schedule', 'text' => 'General, rotational or 24×7 shifts including weekends.'],
            ]], true],
            ['faqs', ['badge' => 'FAQs', 'heading' => 'Frequently Asked Questions', 'subheading' => 'Answers to common questions about our security and manpower services.', 'category' => 'general', 'background' => 'light'], true],
            ['cta', ['heading' => 'Ready to secure and staff your site the right way?', 'subheading' => 'Talk to our team today and get a plan built around your requirement.', 'button_text' => 'Request a Quote', 'button_url' => '/contact-us#enquiry', 'button2_text' => 'Contact Our Team', 'button2_url' => '/contact-us'], true],
        ]);
        $this->seo($home, 'Security Services & Manpower Supply Company in India | MK Securex', 'MK Securex Pvt. Ltd. provides security services, manpower supply, facility management and workforce solutions for businesses across industries.', 'Manpower Supply Company in India', 'Security Services, Manpower Supply Services, Facility Management Company, Workforce Solutions');

        $about = $this->page('about-us', ['title' => 'About Us', 'sort_order' => 2, 'excerpt' => 'Learn about MK Securex Pvt. Ltd. — a security, manpower and facility management company.'], [
            ['page_hero', ['badge' => 'About Us', 'heading' => 'About MK Securex Pvt. Ltd.', 'subheading' => 'Security, manpower and facility management support built around your requirements.'], true],
            ['text_image', ['badge' => 'Who We Are', 'heading' => 'Protecting Workplaces. Powering Operations.', 'content' => '<p>MK Securex Pvt. Ltd. helps organisations manage two things that keep a workplace running — <strong>security</strong> and <strong>people</strong>. We provide trained security personnel, skilled and semi-skilled manpower, housekeeping and integrated facility services.</p><p>Our approach is simple: understand your site first, plan the right team, deploy with clear instructions and stay engaged with regular supervision and reporting. <em>(Update this section with verified company history and credentials.)</em></p>', 'layout' => 'left', 'background' => 'white'], true],
            ['features', ['badge' => 'Our Purpose', 'heading' => 'Mission, Vision & Values', 'columns' => '3', 'background' => 'light', 'items' => [
                ['icon' => 'fa-bullseye', 'title' => 'Our Mission', 'text' => 'To provide dependable security and workforce solutions that let our clients operate safely and efficiently.'],
                ['icon' => 'fa-eye', 'title' => 'Our Vision', 'text' => 'To be a trusted partner for businesses that value disciplined, well-supervised and transparent service.'],
                ['icon' => 'fa-gem', 'title' => 'Our Values', 'text' => 'Integrity, discipline, accountability, respect for people and clear communication in everything we do.'],
            ]], true],
            ['text_image', ['badge' => 'Our Approach', 'heading' => 'Requirement-First, Supervision-Driven', 'content' => '<p>We do not offer fixed packages. Every engagement begins with a requirement study of your premises, risks, roles and shifts. Based on this, we define a scope of work, deployment plan and reporting structure that your team can review before we start.</p>', 'layout' => 'right', 'background' => 'white', 'items' => [
                ['icon' => 'fa-check', 'title' => 'Site-specific post instructions', 'text' => ''],
                ['icon' => 'fa-check', 'title' => 'Supervisor checks and reporting', 'text' => ''],
                ['icon' => 'fa-check', 'title' => 'Quick replacements and escalation', 'text' => ''],
            ]], true],
            ['features', ['badge' => 'Why Choose Us', 'heading' => 'What Makes Us Different', 'columns' => '3', 'background' => 'dark', 'items' => $whyItems], true],
            ['process', ['badge' => 'Working Process', 'heading' => 'How We Work With You', 'background' => 'white', 'items' => $processItems], true],
            ['industry_cards', ['badge' => 'Industries', 'heading' => 'Industries We Serve', 'background' => 'light', 'limit' => 8], true],
            ['enquiry_form', ['badge' => 'Get in Touch', 'heading' => 'Discuss Your Requirement', 'subheading' => 'Fill in the form and our team will get back to you.', 'background' => 'white'], true],
        ]);
        $this->seo($about, 'About MK Securex | Security, Manpower & Facility Management Company', 'About MK Securex Pvt. Ltd. — our mission, vision, values and requirement-first approach to security, manpower and facility management.', 'About MK Securex', 'Manpower and Facility Management Company');

        $services = $this->page('services', ['title' => 'Our Services', 'sort_order' => 3, 'excerpt' => 'Security, manpower supply, facility management, housekeeping, project management, payroll and labour supply services.']);
        $this->seo($services, 'Security, Manpower & Facility Services | MK Securex', 'Explore MK Securex services: security services, manpower supply, facility management, housekeeping, project management, payroll management and labour supply.', 'Manpower Supply Services', 'Security Services, Facility Management Services, Housekeeping Services');

        $industries = $this->page('industries', ['title' => 'Industries We Serve', 'sort_order' => 4, 'excerpt' => 'Security and manpower support for manufacturing, warehousing, construction, healthcare, hospitality, offices, education and retail.']);
        $this->seo($industries, 'Industries We Serve | MK Securex', 'Security, manpower and facility services tailored for manufacturing, warehousing, construction, healthcare, hospitality, corporate offices, education and retail.');

        $contact = $this->page('contact-us', ['title' => 'Contact Us', 'sort_order' => 5, 'excerpt' => 'Contact MK Securex for security, manpower and facility management requirements.'], [
            ['page_hero', ['badge' => 'Contact Us', 'heading' => 'Let’s Discuss Your Requirement', 'subheading' => 'Call, WhatsApp or send us your enquiry — our team will respond promptly.'], true],
            ['contact_info', ['badge' => 'Get in Touch', 'heading' => 'Contact MK Securex', 'subheading' => 'Share your requirement and we will prepare a plan around your site, shifts and scope.'], true],
        ]);
        $this->seo($contact, 'Contact MK Securex | Request a Quote', 'Contact MK Securex Pvt. Ltd. for security guards, manpower supply, housekeeping and facility management. Request a quote today.');

        $privacy = $this->page('privacy-policy', ['title' => 'Privacy Policy', 'sort_order' => 6, 'excerpt' => 'How MK Securex collects, uses and protects your information.'], array_merge(
            [['page_hero', ['badge' => 'Legal', 'heading' => 'Privacy Policy', 'subheading' => 'Draft content — please have this reviewed by your legal advisor before publishing.'], true]],
            array_map(fn ($s) => ['rich_text', ['heading' => $s[0], 'content' => $s[1], 'background' => 'white'], true], [
                ['Introduction', '<p>This Privacy Policy explains how MK Securex Pvt. Ltd. ("we", "us") collects and uses information when you visit our website or submit an enquiry.</p>'],
                ['Information We Collect', '<p>When you submit a form we collect details you provide such as your name, company name, phone number, email address, location and message. We may also collect technical information such as IP address and browser type for security purposes.</p>'],
                ['How We Use Information', '<p>We use your information to respond to your enquiry, prepare proposals, provide our services and improve our website. We do not sell your personal information.</p>'],
                ['Cookies Policy', '<p>Our website may use essential cookies required for the site to function (for example, session and security cookies). If analytics or marketing cookies are added, this section should be updated accordingly.</p>'],
                ['Data Protection', '<p>We take reasonable technical and organisational measures to protect the information stored in our systems against unauthorised access.</p>'],
                ['Third-Party Services', '<p>We may use third-party services such as email delivery, hosting or maps. These providers process data only as required to deliver their services.</p>'],
                ['Information Sharing', '<p>We share information only when required to provide our services, comply with law, or with your consent.</p>'],
                ['User Rights', '<p>You may request access, correction or deletion of the personal information you have shared with us by contacting us using the details below.</p>'],
                ['Contact Information', '<p>For privacy-related questions, please contact us through the details on our Contact Us page.</p>'],
            ])
        ));
        $this->seo($privacy, 'Privacy Policy | MK Securex', 'Read the MK Securex privacy policy to understand how we collect, use and protect your information.');

        if (Slider::count() === 0) {
            Slider::create(['badge' => 'Security · Manpower · Facility', 'heading' => 'Trusted Security & Manpower Solutions for Growing Businesses', 'subheading' => 'Trained guards, skilled workforce and facility support — planned around your site.', 'description' => 'MK Securex helps businesses protect their premises and manage workforce requirements through flexible, requirement-based service plans.', 'btn1_text' => 'Request a Quote', 'btn1_url' => '/contact-us#enquiry', 'btn2_text' => 'Explore Our Services', 'btn2_url' => '/services', 'sort_order' => 1, 'is_active' => true, 'image_alt' => 'MK Securex security personnel']);
            Slider::create(['badge' => 'Facility Management', 'heading' => 'Clean, Safe & Well-Run Workplaces — Under One Partner', 'subheading' => 'Housekeeping, front office and maintenance coordination with on-site supervision.', 'description' => 'Reduce vendor hassle with an integrated facility team and a single point of contact.', 'btn1_text' => 'Get a Free Consultation', 'btn1_url' => '/contact-us#enquiry', 'btn2_text' => 'Facility Services', 'btn2_url' => '/services/facility-management-services', 'sort_order' => 2, 'is_active' => true]);
            Slider::create(['badge' => 'Workforce Solutions', 'heading' => 'The Right People, at the Right Place, on Every Shift', 'subheading' => 'Manpower and labour supply for short-term peaks or long-term operations.', 'description' => 'Screened staff, relief planning and quick replacements to keep your operations moving.', 'btn1_text' => 'Talk to Our Experts', 'btn1_url' => '/contact-us', 'btn2_text' => 'Industries We Serve', 'btn2_url' => '/industries', 'sort_order' => 3, 'is_active' => true]);
        }
    }

    protected function menus(): void
    {
        $header = NavigationMenu::updateOrCreate(['location' => 'header'], ['name' => 'Header Menu']);
        if ($header->items()->count() === 0) {
            foreach ([['Home', '/', 'custom'], ['About Us', '/about-us', 'custom'], ['Services', '/services', 'services'], ['Industries', '/industries', 'industries'], ['Contact Us', '/contact-us', 'custom']] as $i => [$l, $u, $t]) {
                NavigationItem::create(['navigation_menu_id' => $header->id, 'label' => $l, 'url' => $u, 'type' => $t, 'sort_order' => $i + 1, 'is_active' => true]);
            }
        }
        $quick = NavigationMenu::updateOrCreate(['location' => 'footer_quick'], ['name' => 'Footer Quick Links']);
        if ($quick->items()->count() === 0) {
            foreach ([['Home', '/'], ['About Us', '/about-us'], ['Services', '/services'], ['Industries', '/industries'], ['Contact Us', '/contact-us'], ['Request a Quote', '/contact-us#enquiry']] as $i => [$l, $u]) {
                NavigationItem::create(['navigation_menu_id' => $quick->id, 'label' => $l, 'url' => $u, 'type' => 'custom', 'sort_order' => $i + 1, 'is_active' => true]);
            }
        }
        $legal = NavigationMenu::updateOrCreate(['location' => 'footer_legal'], ['name' => 'Footer Legal Links']);
        if ($legal->items()->count() === 0) {
            NavigationItem::create(['navigation_menu_id' => $legal->id, 'label' => 'Privacy Policy', 'url' => '/privacy-policy', 'type' => 'custom', 'sort_order' => 1, 'is_active' => true]);
            NavigationItem::create(['navigation_menu_id' => $legal->id, 'label' => 'Sitemap', 'url' => '/sitemap.xml', 'type' => 'custom', 'sort_order' => 2, 'is_active' => true]);
        }
    }

    protected function faqs(): void
    {
        if (Faq::whereNull('faqable_type')->exists()) {
            return;
        }
        $items = [
            ['What services does MK Securex provide?', 'We provide security services, manpower supply, facility management, housekeeping, project management support, payroll management and labour supply services.'],
            ['Can I combine more than one service?', 'Yes. Many clients combine security, housekeeping and manpower under a single plan with one coordinator.'],
            ['How quickly can you deploy staff?', 'Deployment time depends on the role, headcount and location. Share your requirement and we will confirm a realistic timeline.'],
            ['Do you provide staff for short-term requirements?', 'Yes. We support short-term, seasonal, project-based and long-term engagements.'],
            ['How do I request a quote?', 'Fill in the enquiry form, call us or message us on WhatsApp with your requirement, location and preferred start date.'],
        ];
        foreach ($items as $i => [$q, $a]) {
            Faq::create(['question' => $q, 'answer' => $a, 'category' => 'general', 'sort_order' => $i + 1, 'is_active' => true]);
        }
        Faq::create(['question' => 'Which service is right for my site?', 'answer' => 'If you are unsure, share your site details and our team will recommend a suitable combination of services.', 'category' => 'services', 'sort_order' => 1, 'is_active' => true]);
    }

    protected function testimonials(): void
    {
        if (Testimonial::exists()) {
            return;
        }
        // Placeholders – kept inactive until replaced with genuine, verified client feedback.
        foreach ([1, 2, 3] as $i) {
            Testimonial::create([
                'client_name' => 'Client Name '.$i, 'designation' => 'Designation', 'company' => 'Company Name', 'rating' => 5,
                'content' => 'Replace this placeholder with genuine feedback from a verified client before activating it.',
                'sort_order' => $i, 'is_active' => false,
            ]);
        }
    }
}
