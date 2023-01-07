<?php

namespace App\Providers;

use App\Repositories\Eloquent\DBInvoiceRepository;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\CustomerRepository;
use App\Repositories\Eloquent\DBCustomerRepository;
use App\Repositories\Eloquent\DBOrderDetailRepository;
use App\Repositories\Eloquent\DBOrderRepository;
use App\Repositories\Eloquent\DBTransactionRepository;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CustomerRepository::class, DBCustomerRepository::class);
        $this->app->bind(OrderDetailRepository::class, DBOrderDetailRepository::class);
        $this->app->bind(OrderRepository::class, DBOrderRepository::class);
        $this->app->bind(TransactionRepository::class, DBTransactionRepository::class);
        $this->app->bind(InvoiceRepository::class, DBInvoiceRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
