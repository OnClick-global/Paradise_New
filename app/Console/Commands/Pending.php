<?php

namespace App\Console\Commands;

use App\CentralLogics\Helpers;
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
        $acceptedTime = \App\Model\BusinessSetting::where('key', 'acceptedTime')->first()->value;
        $order = Order::where('order_status', 'pending')->where('created_at', '<=', Carbon::now()->subMinute($acceptedTime))->get();
        foreach ($order as $row) {
            $one = Order::find($row->id);
            $one->order_status = 'out_for_delivery';
            if ($one->save()) {
                $fcm_token = $row->customer->cm_firebase_token;
                $value = Helpers::order_status_update_message($row->order_status);
                if ($value) {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $row->id,
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            }
        }
    }
}
