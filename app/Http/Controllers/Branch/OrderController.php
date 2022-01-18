<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintBuffers\ImagePrintBuffer;
use Mike42\Escpos\ImagickEscposImage;
use \ArPHP\I18N\Arabic;
use PDF;


class OrderController extends Controller
{
    public function list($status, Request $request)
    {
        Order::where(['checked' => 0, 'branch_id' => auth('branch')->id()])->update(['checked' => 1]);
        if ($status == 'all') {
            $orders = Order::with(['customer'])->where(['branch_id' => auth('branch')->id()])
                ->whereDate('delivery_date','<=',\Carbon\Carbon::now()->format('Y-m-d'));
        } elseif ($status == 'schedule') {
            $orders = Order::whereDate('delivery_date','>', \Carbon\Carbon::now()->format('Y-m-d'))
                ->where(['branch_id' => auth('branch')->id()]);
        } else {
            $orders = Order::with(['customer'])
                ->where(['order_status' => $status, 'branch_id' => auth('branch')->id()])
                ->whereDate('delivery_date','<=',\Carbon\Carbon::now()->format('Y-m-d'));
        }

        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $orders = Order::where(['branch_id' => auth('branch')->id()])
                ->whereDate('delivery_date', '<=', \Carbon\Carbon::now()->format('Y-m-d'))
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        }

        $orders = $orders->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('branch-views.order.list', compact('orders', 'status', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $orders = Order::where(['branch_id' => auth('branch')->id()])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('branch-views.order.partials._table', compact('orders'))->render()
        ]);
    }

    public function details($id)
    {
        $order = Order::with('details')->where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        if (isset($order)) {
            return view('branch-views.order.order-view', compact('order'));
        } else {
            Toastr::info('No more orders!');
            return back();
        }
    }

    public function status(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'branch_id' => auth('branch')->id()])->first();

        if ($order['delivery_man_id'] == null && $request->order_status == 'out_for_delivery') {
            Toastr::warning('Please assign delivery man first!');
            return back();
        }

        $order->order_status = $request->order_status;
        $order->save();
        $fcm_token = $order->customer->cm_firebase_token;
        $value = Helpers::order_status_update_message($request->order_status);
        try {
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {

        }

        Toastr::success('Order status updated!');
        return back();
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::where(['id' => $order_id, 'branch_id' => auth('branch')->id()])->first();
        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $fcm_token = $order->delivery_man->fcm_token;
        $value = Helpers::order_status_update_message('del_assign');
        try {
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {

        }

        Toastr::success('Order deliveryman added!');
        return response()->json([], 200);
    }

    public function payment_status(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'branch_id' => auth('branch')->id()])->first();
        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning('Add your payment reference code first!');
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success('Payment status updated!');
        return back();
    }

    public function update_shipping(Request $request, $id)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success('Payment status updated!');
        return back();
    }

    public function generate_invoice($id)
    {
        $order = Order::where('id', $id)->first();
        return view('admin-views.order.invoice', ['order'=>$order]);
        $addonsCount = 0;
        foreach ($order->details as $key => $detail) {
            $addonsCount += count(json_decode($detail['add_on_ids'],true));
        }
        return $html;
        // return $pdf->download('invoice.pdf');
        $mpdf = new \Mpdf\Mpdf([           
            'format' => [80, 50+((count($order->details)+$addonsCount)+20)],
            'default_font' => 'taj',
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        return ;   
        $mpdf->Output(public_path('pdf/invoice.pdf', 'F'));   
        $pdf = public_path('pdf/invoice.pdf');
        $connector = new WindowsPrintConnector("XP-80C");
        $profile = CapabilityProfile::load("default");
        $printer = new Printer($connector,$profile);
        try {
            $pages = ImagickEscposImage::loadPdf($pdf);
            foreach ($pages as $page) {
                $printer->bitImage($page);
            }
            $printer -> cut();
        } catch (Exception $e) {
            echo $e -> getMessage() . "\n";
        } finally {
            $printer -> close();
        }
        return back();            
    }
    public function generate_kot($id)
    {
        $order = Order::where('id', $id)->first();
        return view('admin-views.order.invoice-kot', ['order'=>$order]);
        $addonsCount = 0;
        foreach ($order->details as $key => $detail) {
            $addonsCount += count(json_decode($detail['add_on_ids'],true));
        }
        return $html;
        $mpdf = new \Mpdf\Mpdf([           
            'format' => [80, 50+((count($order->details)+$addonsCount)+20)],
            'default_font' => 'taj',
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        return ;   
        $mpdf->Output(public_path('pdf/invoice.pdf', 'F'));   
        $pdf = public_path('pdf/invoice.pdf');
        $connector = new WindowsPrintConnector("XP-80C");
        $profile = CapabilityProfile::load("default");
        // return $pdf->download('invoice.pdf');
        $printer = new Printer($connector,$profile);
        try {
            $pages = ImagickEscposImage::loadPdf($pdf);
            foreach ($pages as $page) {
                $printer->bitImage($page);
            }
            $printer -> cut();
        } catch (Exception $e) {
            echo $e -> getMessage() . "\n";
        } finally {
            $printer -> close();
        }
        return back();            
    }
    public function generate_sticker($id)
    {
        $order   = Order::where('id', $id)->first();
        return view('admin-views.order.sticker', ['order'=>$order]);          
    }
    public function add_payment_ref_code(Request $request, $id)
    {
        Order::where(['id' => $id, 'branch_id' => auth('branch')->id()])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success('Payment reference code is added!');
        return back();
    }
}
