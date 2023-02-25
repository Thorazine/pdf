<?php

namespace Thorazine\Pdf;

// use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class PdfServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Kernel $kernel, Router $router)
    {
        // publish 
        $this->publishes([

            // config
            __DIR__.'/config/pdf.php' => config_path('pdf.php'),

        ], 'pdf');

        dd(config('pdf'));
        
        if (app()->environment(['local', 'dev', 'development', 'test', 'testing', 'acc', 'acceptance'])) {
            $this->loadRoutesFrom(__DIR__.'/routes/pdf.php');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/pdf.php', 'pdf');
    }
}