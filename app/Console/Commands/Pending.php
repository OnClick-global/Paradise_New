<?php

namespace App\Console\Commands;

use App\Model\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Pending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Pending:change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return void
     */
    public function handle()
    {
        $order= Order::where('order_status','pending')->where('created_at', '<=', Carbon::now()->subMinute(1))->update([
            'order_status'=>'confirmed'
        ]);
    }
}
