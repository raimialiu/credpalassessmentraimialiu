<?php

namespace App\Providers;

use App\ExchangeRate\ConcreteExchangeRate;
use App\ExchangeRate\ExchangeRate;
use App\ExchangeRate\IExchangeRate;
use App\Loan\ILoanCalculator;
use App\Loan\LoanCalculator;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(ILoanCalculator::class,LoanCalculator::class);
        //$this->app->bind(IExchangeRate::class, ConcreteExchangeRate::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
