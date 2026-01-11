<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Policies\OrderPolicy;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
    ];
    public function register(): void
    {
        //
    }
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
