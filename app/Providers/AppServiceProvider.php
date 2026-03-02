<?php

namespace App\Providers;

use App\Repositories\Interface\GoodReceiveDetailInterface;
use App\Repositories\Interface\GoodReceiveInterface;
use App\Repositories\Interface\LogStatusInterface;
use Illuminate\Support\ServiceProvider;

use App\Repositories\Interface\ProductInterface;
use App\Repositories\Interface\PurchaseOrderDetailInterface;
use App\Repositories\Interface\PurchaseOrderInterface;
use App\Repositories\Interface\RequestProductDetailInterface;
use App\Repositories\Interface\RequestProductInterface;
use App\Repositories\Interface\StockOpnameDetailInterface;
use App\Repositories\Interface\StockOpnameInterface;
use App\Repositories\Repositories\GoodReceiveDetailRepository;
use App\Repositories\Repositories\GoodReceiveRepository;
use App\Repositories\Repositories\LogStatusRepository;
use App\Repositories\Repositories\ProductRepository;
use App\Repositories\Repositories\PurchaseOrderDetailRepository;
use App\Repositories\Repositories\PurchaseOrderRepository;
use App\Repositories\Repositories\RequestProductDetailRepository;
use App\Repositories\Repositories\RequestProductRepository;
use App\Repositories\Repositories\StockOpnameDetailRepository;
use App\Repositories\Repositories\StockOpnameRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(ProductInterface::class, ProductRepository::class);
        $this->app->bind(PurchaseOrderInterface::class, PurchaseOrderRepository::class);
        $this->app->bind(PurchaseOrderDetailInterface::class, PurchaseOrderDetailRepository::class);
        $this->app->bind(RequestProductInterface::class, RequestProductRepository::class);
        $this->app->bind(RequestProductDetailInterface::class, RequestProductDetailRepository::class);
        $this->app->bind(GoodReceiveInterface::class, GoodReceiveRepository::class);
        $this->app->bind(GoodReceiveDetailInterface::class, GoodReceiveDetailRepository::class);
        $this->app->bind(StockOpnameInterface::class, StockOpnameRepository::class);
        $this->app->bind(StockOpnameDetailInterface::class, StockOpnameDetailRepository::class);
        $this->app->bind(LogStatusInterface::class, LogStatusRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
