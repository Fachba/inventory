<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('role_actions', function (Blueprint $table) {
            $table->id('role_action_id');
            $table->integer('role_id');
            $table->integer('action_menu_id');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('action_menus', function (Blueprint $table) {
            $table->id('action_menu_id');
            $table->string('menu');
            $table->string('action');
            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->integer('role_id')->nullable();

            $table->string('user_name');
            $table->string('user_email');
            $table->string('password');

            $table->boolean('is_active')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');

            $table->string('product_name');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('request_products', function (Blueprint $table) {
            $table->id('request_product_id');

            $table->dateTime('request_date');

            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('request_product_detail', function (Blueprint $table) {
            $table->id('request_product_detail_id');
            $table->integer('request_product_id');

            $table->string('product_id');
            $table->integer('qty_rp');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('purchase_order_id');

            $table->dateTime('po_date');
            $table->string('vendor');

            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->id('purchase_order_detail_id');
            $table->integer('purchase_order_id');

            $table->string('product_id');
            $table->integer('qty_po');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('good_receives', function (Blueprint $table) {
            $table->id('good_receive_id');

            $table->integer('purchase_order_id');
            $table->dateTime('gr_date');

            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('good_receive_detail', function (Blueprint $table) {
            $table->id('good_receive_detail_id');
            $table->integer('good_receive_id');

            $table->string('product_id');
            $table->integer('qty_gr');

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id('stock_opname_id');

            $table->integer('stock_opname_period');
            $table->integer('stock_opname_year');

            $table->integer('status_id')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('stock_opname_detail', function (Blueprint $table) {
            $table->id('stock_opname_detail_id');
            $table->integer('stock_opname_id');

            $table->string('product_id');
            $table->integer('system_stock')->nullable();
            $table->integer('physical_stock')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::create('log_status', function (Blueprint $table) {
            $table->id('log_status_id');

            $table->string('modul_name')->nullable();
            $table->integer('data_id')->nullable();
            $table->integer('old_status')->nullable();
            $table->integer('new_status')->nullable();

            $table->integer('user_add')->nullable();
            $table->integer('user_upd')->nullable();
            $table->integer('user_del')->nullable();

            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
        Schema::dropIfExists('user');
        Schema::dropIfExists('product');
        Schema::dropIfExists('purchase_request');
        Schema::dropIfExists('purchase_request_detail');
        Schema::dropIfExists('purchase_order');
        Schema::dropIfExists('purchase_order_detail');
        Schema::dropIfExists('good_receive');
        Schema::dropIfExists('good_receive_detail');
        Schema::dropIfExists('stock_opname');
        Schema::dropIfExists('stock_opname_detail');
        Schema::dropIfExists('log_status');
    }
};
