<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('agency')->nullable();
            $table->string('certificate_number')->nullable();
            $table->text('verification_info')->nullable();
            $table->string('image_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->json('stone_details')->nullable();
            $table->timestamps();
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->string('faqable_type')->nullable()->after('id');
            $table->unsignedBigInteger('faqable_id')->nullable()->after('faqable_type');
            $table->index(['faqable_type', 'faqable_id']);
        });

        Schema::table('faqs', function (Blueprint $table) {
            if (Schema::hasColumn('faqs', 'category')) {
                $table->dropColumn('category');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->longText('seo_content')->nullable()->after('meta_description');
            $table->json('stone_content')->nullable()->after('seo_content');
            $table->string('og_image')->nullable()->after('stone_content');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('certification_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            $table->string('stone_type')->nullable()->after('sku');
            $table->string('metal_type')->nullable()->after('stone_type');
            $table->string('weight')->nullable()->after('metal_type');
            $table->string('shape')->nullable()->after('weight');
            $table->string('dimensions')->nullable()->after('shape');
            $table->json('specifications')->nullable()->after('description');
            $table->unsignedSmallInteger('delivery_days_min')->default(10)->after('specifications');
            $table->unsignedSmallInteger('delivery_days_max')->default(14)->after('delivery_days_min');
            $table->string('stock_status')->default('in_stock')->after('delivery_days_max');
            $table->boolean('igi_available')->default(true)->after('stock_status');
            $table->longText('seo_content')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('seo_content');
            $table->string('meta_keywords')->nullable()->after('og_image');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->json('options')->nullable()->after('unit_price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('options')->nullable()->after('total');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('igi_total', 12, 2)->default(0)->after('discount');
            $table->unsignedSmallInteger('delivery_days_min')->nullable()->after('notes');
            $table->unsignedSmallInteger('delivery_days_max')->nullable()->after('delivery_days_min');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['igi_total', 'delivery_days_min', 'delivery_days_max']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('options');
        });
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('options');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('certification_id');
            $table->dropColumn([
                'stone_type', 'metal_type', 'weight', 'shape', 'dimensions', 'specifications',
                'delivery_days_min', 'delivery_days_max', 'stock_status', 'igi_available',
                'seo_content', 'og_image', 'meta_keywords',
            ]);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['seo_content', 'stone_content', 'og_image']);
        });
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropMorphs('faqable');
            $table->string('category')->nullable();
        });
        Schema::dropIfExists('certifications');
    }
};
