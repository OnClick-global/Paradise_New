<?php

namespace App\Console\Commands;

use App\Model\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Processing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processing:change';

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
     * @return int
     */
    public function handle()
    {
        $cockingTime=\App\Model\BusinessSetting::where('key','cockingTime')->first()->value;
        $order= Order::where('order_status','confirmed')->where('updated_at','<=', Carbon::now()->subMinute($cockingTime))->update([
            'order_status'=>'processing'
        ]);
    }
}
