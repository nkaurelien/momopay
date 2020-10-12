<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use Nkaurelien\Momopay\Fluent\MomoVerification;
use Nkaurelien\Momopay\Repository\PaymentMomoRepository;


class MomoPayVerificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *  
     *  Example:
     *  Run 'php artisan momopay:verify-transactions' to verify all transactions
     *  Run 'php artisan momopay:verify-transactions [referenceId]' to verify a specific transactions
     *
     * @var string
     */
    protected $signature = 'momopay:verify-transactions {referenceId?} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle(PaymentMomoRepository $paymentMomoRepository)
    {

        // $options = $this->options();
        
        $referenceId = $this->argument('referenceId');

        $paymentMomoRepository->verifyTransactions($referenceId)->each(function (MomoVerification $verificaton) {
            if ($verificaton->hasError) {
                $this->error("{$verificaton->status} :: $verificaton->message");
            } else {
                $this->info("{$verificaton->status} :: $verificaton->message");
            }
        });
    }
}
