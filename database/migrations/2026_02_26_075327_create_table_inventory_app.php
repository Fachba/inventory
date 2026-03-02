<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | ACTION MENUS
        |--------------------------------------------------------------------------
        */
        Schema::create('action_menus', function (Blueprint $table) {
            $table->id('action_menu_id');
            $table->string('menu');
            $table->string('action');
            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | ROLE ACTIONS (Pivot)
        |--------------------------------------------------------------------------
        */
        Schema::create('role_actions', function (Blueprint $table) {
            $table->id('role_action_id');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->foreignId('role_id')
                ->constrained('roles', 'role_id')
                ->cascadeOnDelete();

            $table->foreignId('action_menu_id')
                ->constrained('action_menus', 'action_menu_id')
                ->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->foreignId('role_id')
                ->nullable()
                ->constrained('roles', 'role_id')
                ->nullOnDelete();

            $table->string('user_name');
            $table->string('user_email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | REQUEST PRODUCTS (HEADER)
        |--------------------------------------------------------------------------
        */
        Schema::create('request_products', function (Blueprint $table) {
            $table->id('request_product_id');

            $table->dateTime('request_date');
            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | REQUEST PRODUCT DETAIL
        |--------------------------------------------------------------------------
        */
        Schema::create('request_product_detail', function (Blueprint $table) {
            $table->id('request_product_detail_id');

            $table->foreignId('request_product_id')
                ->constrained('request_products', 'request_product_id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products', 'product_id');

            $table->integer('qty_rp');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDERS (HEADER)
        |--------------------------------------------------------------------------
        */
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('purchase_order_id');

            $table->dateTime('po_date');
            $table->string('vendor');
            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDER DETAIL
        |--------------------------------------------------------------------------
        */
        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->id('purchase_order_detail_id');

            $table->foreignId('purchase_order_id')
                ->constrained('purchase_orders', 'purchase_order_id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products', 'product_id');

            $table->integer('qty_po');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | GOOD RECEIVES (HEADER)
        |--------------------------------------------------------------------------
        */
        Schema::create('good_receives', function (Blueprint $table) {
            $table->id('good_receive_id');

            $table->foreignId('purchase_order_id')
                ->constrained('purchase_orders', 'purchase_order_id')
                ->cascadeOnDelete();

            $table->dateTime('gr_date');
            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | GOOD RECEIVE DETAIL
        |--------------------------------------------------------------------------
        */
        Schema::create('good_receive_detail', function (Blueprint $table) {
            $table->id('good_receive_detail_id');

            $table->foreignId('good_receive_id')
                ->constrained('good_receives', 'good_receive_id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products', 'product_id');

            $table->integer('qty_gr');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | STOCK OPNAMES (HEADER)
        |--------------------------------------------------------------------------
        */
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id('stock_opname_id');

            $table->integer('stock_opname_period');
            $table->integer('stock_opname_year');
            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['stock_opname_period', 'stock_opname_year']);
        });

        /*
        |--------------------------------------------------------------------------
        | STOCK OPNAME DETAIL
        |--------------------------------------------------------------------------
        */
        Schema::create('stock_opname_detail', function (Blueprint $table) {
            $table->id('stock_opname_detail_id');

            $table->foreignId('stock_opname_id')
                ->constrained('stock_opnames', 'stock_opname_id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products', 'product_id');

            $table->integer('system_stock')->nullable();
            $table->integer('physical_stock')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['stock_opname_id', 'product_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | LOG STATUS
        |--------------------------------------------------------------------------
        */
        Schema::create('log_status', function (Blueprint $table) {
            $table->id('log_status_id');

            $table->string('modul_name')->nullable();
            $table->unsignedBigInteger('data_id')->nullable();
            $table->integer('old_status')->nullable();
            $table->integer('new_status')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_status');
        Schema::dropIfExists('stock_opname_detail');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('good_receive_detail');
        Schema::dropIfExists('good_receives');
        Schema::dropIfExists('purchase_order_detail');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('request_product_detail');
        Schema::dropIfExists('request_products');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');
        Schema::dropIfExists('role_actions');
        Schema::dropIfExists('action_menus');
        Schema::dropIfExists('roles');
    }
};
