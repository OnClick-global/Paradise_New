<?php

namespace App\Console\Commands;

use App\Model\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Delivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delivery:change';

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
        $order= Order::where('order_status','confirmed')->where('created_at', '>=', Carbon::now()->subMinute(40))->update([
            'order_status'=>'out_for_delivery'
        ]);
    }
}
