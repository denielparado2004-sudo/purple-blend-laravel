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
            $table->string('email', 150)->nullable();
            $table->string('password_hash');
            $table->string('role', 20)->default('manager');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password_hash');
            $table->string('phone', 20)->nullable();
            $table->string('area')->nullable();
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 100)->unique();
            $table->string('name', 150);
            $table->decimal('price', 10, 2);
            $table->string('category', 100);
            $table->string('variant_group', 32)->nullable();
            $table->string('size_label', 20)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->index(['category', 'is_available']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 30)->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_type', 20)->default('online');
            $table->string('dining_option', 20)->default('takeaway');
            $table->string('order_status', 20)->default('pending');
            $table->string('customer_name', 201);
            $table->string('customer_email', 150)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
            $table->index(['customer_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('product_code', 100);
            $table->string('product_name', 150);
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_status', 30)->default('pending');
            $table->decimal('amount', 10, 2);
            $table->timestamp('paid_at')->nullable();
            $table->string('provider')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->timestamps();
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
