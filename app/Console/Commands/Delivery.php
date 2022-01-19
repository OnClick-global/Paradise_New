<?php

namespace App\Console\Commands;

use App\CentralLogics\Helpers;
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
    protected $signature = 'Delivery:change';

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
        $deliveryTime=\App\Model\BusinessSetting::where('key','deliveryTime')->first()->value;
        $order= Order::where('order_status','processing')->where('updated_at', '<=', Carbon::now()->subMinute($deliveryTime))->update([
            'order_status'=>'out_for_delivery'
        ]);
        $fcm_token = $order->customer->cm_firebase_token;
        $value = Helpers::order_status_update_message($order->order_status);
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
    }
}
