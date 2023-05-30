<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $action = app('request')->route() ? app('request')->route()->getAction() : '';
            if (isset($action['controller'])) {
                $controller = class_basename($action['controller']);
                if (strpos($controller, "@")) {
                    list($controller, $action) = explode('@', $controller);
                    $view->with(['getController' => str_replace('Controller', '', $controller), 'getAction' => $action]);
                }
            }
        });

        if (class_exists('Swift_Preferences')) {
            \Swift_Preferences::getInstance()->setTempDir(storage_path() . '/tmp');
        } else {
            \Log::warning('Class Swift_Preferences does not exists');
        }
    }
}
