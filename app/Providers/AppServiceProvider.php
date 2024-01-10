<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();


        View::composer("*", function($view){

            $unread_notifications_count = 0;
            if(Auth::guard('web')->check()) {
                $unread_notifications_count = Auth::user()->unreadNotifications()->count();
            }

            $view->with("unread_notifications_count",$unread_notifications_count);
        });
    }
}
