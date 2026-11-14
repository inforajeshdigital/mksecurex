<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Show / hide controls for images so the same picture is not shown twice
 * (e.g. as banner background AND again on top of it).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->boolean('show_background')->default(true)->after('image_alt');   // full-width banner background
            $table->boolean('show_side_image')->default(false)->after('show_background'); // framed image on the right
        });

        foreach (['services', 'industries'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->boolean('show_banner')->default(true)->after('image_alt');  // hero banner background on detail page
                $table->boolean('show_image')->default(true)->after('show_banner'); // featured image inside detail page content
            });
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('show_banner')->default(true)->after('banner_alt');
        });
    }

    public function down(): void
    {
        Schema::table('sliders', fn (Blueprint $t) => $t->dropColumn(['show_background', 'show_side_image']));
        Schema::table('services', fn (Blueprint $t) => $t->dropColumn(['show_banner', 'show_image']));
        Schema::table('industries', fn (Blueprint $t) => $t->dropColumn(['show_banner', 'show_image']));
        Schema::table('pages', fn (Blueprint $t) => $t->dropColumn('show_banner'));
    }
};
