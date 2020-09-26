<?php

namespace  Nkaurelien\Momopay\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Nkaurelien\Momopay\Facades\MomoPay;
use Nkaurelien\Momopay\Repository\PaymentMomoRepository;

class MomopayServiceProvider extends ServiceProvider
{


    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {


        $this->app->bind(MomoPay::class, function()
        {
            return new PaymentMomoRepository;
        });

//        $this->registerConfig();

    }

//    /**
//     * Register config.
//     *
//     * @return void
//     */
//    protected function registerConfig()
//    {
//        $this->publishes([
//            __DIR__ . '/../Config/config.php.tmpl' => config_path('momopay.php'),
//        ], 'config');
//        $this->mergeConfigFrom(
//            __DIR__ . '/../Config/config.php.tmpl', 'momopay'
//        );
//    }

}
