<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('path');
            $table->string('original_path')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('title')->nullable();
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('path');
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('template', 50)->default('default');
            $table->text('excerpt')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_alt')->nullable();
            $table->boolean('is_system')->default(false);
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->date('published_on')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('name')->nullable();
            $table->json('data')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['page_id', 'sort_order']);
        });

        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('badge')->nullable();
            $table->string('heading');
            $table->string('subheading')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('btn1_text')->nullable();
            $table->string('btn1_url')->nullable();
            $table->string('btn2_text')->nullable();
            $table->string('btn2_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon', 80)->nullable();
            $table->text('short_description')->nullable();
            $table->text('intro')->nullable();
            $table->longText('description')->nullable();
            $table->json('features')->nullable();
            $table->json('benefits')->nullable();
            $table->json('process')->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'sort_order']);
        });

        Schema::create('service_related', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_id')->constrained('services')->cascadeOnDelete();
            $table->primary(['service_id', 'related_id']);
        });

        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon', 80)->nullable();
            $table->text('short_description')->nullable();
            $table->text('intro')->nullable();
            $table->longText('description')->nullable();
            $table->json('workforce_requirements')->nullable();
            $table->json('challenges')->nullable();
            $table->json('solutions')->nullable();
            $table->json('benefits')->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'sort_order']);
        });

        Schema::create('industry_service', function (Blueprint $table) {
            $table->foreignId('industry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->primary(['industry_id', 'service_id']);
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('designation')->nullable();
            $table->string('company')->nullable();
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('content');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('faqable');
            $table->string('category', 50)->default('general')->index();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('company_name')->nullable();
            $table->string('phone', 30);
            $table->string('email');
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_name')->nullable();
            $table->string('workforce_size', 50)->nullable();
            $table->string('location')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->boolean('consent')->default(false);
            $table->string('form_type', 30)->default('enquiry');
            $table->string('source_url')->nullable();
            $table->enum('status', ['new', 'contacted', 'converted', 'closed'])->default('new')->index();
            $table->boolean('is_spam')->default(false)->index();
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location', 50)->unique();
            $table->timestamps();
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->cascadeOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('type', 30)->default('custom'); // custom | services | industries
            $table->boolean('new_tab')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('focus_keyword')->nullable();
            $table->string('secondary_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_url')->nullable();
            $table->string('og_type', 30)->default('website');
            $table->string('twitter_card', 30)->default('summary_large_image');
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->longText('schema_markup')->nullable();
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('to_url');
            $table->unsignedSmallInteger('type')->default(301);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        foreach (['activity_logs', 'redirects', 'seo_metadata', 'navigation_items', 'navigation_menus', 'enquiries', 'faqs',
            'testimonials', 'industry_service', 'industries', 'service_related', 'services', 'sliders', 'page_sections',
            'pages', 'media', 'website_settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
