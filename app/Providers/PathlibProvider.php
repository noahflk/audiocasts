<?php

namespace App\Providers;

use App\Utilities\Pathlib;
use Illuminate\Support\ServiceProvider;

class PathlibProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('pathlib', function(){
            return new Pathlib();
        });
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
