<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password_hash', 255);
            $table->enum('role', ['manager', 'staff'])->default('manager');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password_hash', 255);
            $table->string('phone', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('area', 255)->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 100)->unique();
            $table->string('name', 150);
            $table->decimal('price', 10, 2);
            $table->string('category', 100);
            $table->char('variant_group', 32)->nullable();
            $table->string('size_label', 20)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('variant_group', 'idx_menu_items_variant_group');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 30)->unique();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            $table->enum('order_type', ['online', 'walk_in'])
                ->default('online');

            $table->enum('dining_option', ['takeaway', 'dine_in'])
                ->default('takeaway');

            $table->enum('order_status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->string('customer_name', 201);
            $table->string('customer_email', 150)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->time('pickup_time')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->date('pickup_date')->nullable();

            $table->index('customer_id', 'idx_orders_customer');
            $table->index('order_status', 'idx_orders_status');
            $table->index('created_at', 'idx_orders_created');
            $table->index('order_type', 'idx_orders_type');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('menu_item_id')
                ->nullable()
                ->constrained('menu_items')
                ->nullOnDelete();

            $table->string('product_code', 100);
            $table->string('product_name', 150);
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('order_id', 'idx_order_items_order');
            $table->index('menu_item_id', 'idx_order_items_menu');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->enum('payment_method', ['cash', 'online'])
                ->default('cash');

            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'refunded',
            ])->default('pending');

            $table->decimal('amount', 10, 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->string('provider', 100)->nullable();
            $table->string('transaction_reference', 255)->nullable();

            $table->index('order_id', 'idx_payments_order');
            $table->index('payment_status', 'idx_payments_status');
        });

        Schema::create('customer_password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 150)->primary();
            $table->string('token', 64);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('customer_password_reset_tokens');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('admins');
    }
};
