<?php

namespace  Nkaurelien\Momopay\Providers;

use Nkaurelien\Momopay\Commands\MomoPayVerificationCommand;
use Illuminate\Support\ServiceProvider;
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

        $this->registerArtisanCommand();
        $this->databases();

    }

//    /**
//     * Register config.
//     *
//     * @return void
//     */
//    protected function registerConfig()
//    {
//        $this->mergeConfigFrom(
//            __DIR__ . '/../Config/config.php', 'momopay'
//        );
//        $this->publishes([
//            __DIR__ . '/../Config/config.php' => config_path('momopay.php'),
//        ], 'momopay-config');
//    }

    /**
     * Register commands.
     *
     * @return void
     */
    public function registerArtisanCommand()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MomoPayVerificationCommand::class,
            ]);
        }
    }


    /**
     * Register Database.
     *
     * @return void
     */
    public function databases()
    {
        $this->loadMigrationsFrom(__DIR__.'/../stuff/migrations');
        $this->publishes([
            __DIR__ . '/../stuff/migrations/' => database_path('migrations'),
        ], 'momopay-database');
    }

}
