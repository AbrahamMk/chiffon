<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('clothing_type')->nullable();
            $table->string('fabric_preference')->nullable();
            $table->string('color_preference')->nullable();
            $table->text('design_description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->date('required_date')->nullable();
            $table->text('additional_instructions')->nullable();
            $table->string('status')->default('submitted');
            $table->decimal('quoted_amount', 12, 2)->nullable();
            $table->decimal('deposit_amount', 12, 2)->nullable();
            $table->decimal('deposit_paid', 12, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('custom_order_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('value')->nullable();
            $table->string('unit')->default('cm');
            $table->timestamps();
        });

        Schema::create('custom_order_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference')->unique();
            $table->decimal('amount', 12, 2);
            $table->decimal('deposit_required', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('production_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('production_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_number')->unique();
            $table->foreignId('custom_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->date('expected_completion_date')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('completed_quantity')->default(0);
            $table->boolean('is_delayed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('production_job_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('production_stage_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['production_job_id', 'production_stage_id'], 'job_stage_unique');
        });

        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('fee', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('city')->nullable();
            $table->string('subcity')->nullable();
            $table->text('address_line')->nullable();
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->date('scheduled_date')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('proof_path')->nullable();
            $table->string('failed_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('production_job_stages');
        Schema::dropIfExists('production_jobs');
        Schema::dropIfExists('production_stages');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('custom_order_images');
        Schema::dropIfExists('custom_order_measurements');
        Schema::dropIfExists('custom_orders');
    }
};
