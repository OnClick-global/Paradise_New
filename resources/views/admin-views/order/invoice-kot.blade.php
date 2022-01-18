<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>Order #101</title>       
        <style type="text/css">
            @page  {
                margin: 0px;
                font-size: 15px;
                font-family: auto;
                font-weight: bolder;
            }            
            body{
                font-size: 15px;
                font-family: auto;
                font-weight: bolder;
            }
            strong {
                display: block;
                direction: ltr;
                text-align: right;
            }

            .w-100{
                width: 100% !important;
            }

            .w-50{
                width: 700% !important;
            }

            .small{
                font-size: 7px;
            }

            table{
                font-size: 11px;
                line-height: 15px;
            }

            td{
                padding-bottom: 5px;
            }

            .pt-1rem{
                /* padding-top: 1rem; */
            }

            .pb-1rem{
                padding-bottom: 5px;
            }

            .pr-20px{
                padding-right: 20px !important;
            }

            .pr-100px{
                padding-right:100px !important;
            }

            .mb-1rem{
                margin-bottom: 1rem !important;
            }

            .pb-0{
                padding-bottom: 0;
            }

            .display-block{
                display: inline-block;
            }

            .center{
                text-align: center;
            }

            .right{
                text-align: right;
            }

            .bold{
                font-weight: bold;
            }

            .border-bottom-dashed{
                border-bottom: 0.3px dashed #000;
            }

            .product-table {
                border-collapse: collapse;
            }

            .product-table th{
                background: #ddd;
            }
              
            .product-table, .product-table th, .product-table td {
                padding: 5px;
                border: 0.5px solid #ddd;
            }

            .footer{
                text-align: center;
            }

            .v-top{
                vertical-align: top !important;
            }

            .h-50px{
                height: 50px;
            }
            .page-break-avoid, .page-break-avoid tr, .page-break-avoid td {
                border: 1px solid black;
            }
            pre{
                font-family: 'Verdana';
            }             
        </style>         
    </head>
    <body>

<div id="printableArea">
        <div class='pt-1rem mb-1rem'>
            <table class='w-100'>
                <tr>
                    <td class='bold w-45'>التاريح : {{date('Y/m/d',strtotime($order['created_at']))}}</td>
                    <td class='right'>الوقت : {{date('h:i A',strtotime($order['created_at']))}}</td>
                </tr>
            </table>
        </div>
        <div class="pt-1rem mb-1rem">
            <div style="display:inline-block;width: 55%;text-align: center;">
                <label>{{$order->order_type}}</label><br>
                @if($order->order_status == 'returned')
                <label>مرتجعات  يومية</label>
                @else
                <label>مبيعات  يومية</label>
                @endif
            </div>
            <div style="display:inline-block;width: 40%;">
                <span style="border: 1px solid;border-radius: 5px;padding: 5px 8px;font-weight: bolder;">{{$order['id']}}</span>
            </div>
        </div>
        <table class='border-bottom-dashed mb-1rem w-100 page-break-avoid'>
            <tr>
                <td style="text-align: center;" class='bold w-50'>الصنف</td>
                <td style="text-align: center;" class='bold right'>الكمية</td>
            </tr>
            @php($addon_sub_amount=0)
            @php($sub_total=0)
            @php($total_tax=0)
            @php($total_dis_on_pro=0)
            @php($add_ons_cost=0)            
            @foreach($order->details as $detail)
                @if($detail->product)
                    <?php
                        if (count($detail->product['translations'])) {
                            foreach ($detail->product['translations'] as $translation) {
                                if ($translation->key == 'name') {
                                    $detail->product['name'] = $translation->value;
                                }
                                if ($translation->key == 'description') {
                                    $detail->product['description'] = $translation->value;
                                }
                            }
                        }                
                    ?>
                    @php($add_on_qtys=json_decode($detail['add_on_qtys'],true))
                    <tr>
                        <td class="">
                            {{$detail->product['name']}} - 
                            {{$detail->note}} <br>
                            @foreach(json_decode($detail['add_on_ids'],true) as $key2 =>$id)
                                @php($addon=\App\Model\AddOn::find($id))
                                    <strong>{{$addon['name']}} * @if($add_on_qtys==null)
                                                @php($add_on_qty=1)
                                            @else
                                                @php($add_on_qty=$add_on_qtys[$key2])
                                            @endif                            
                                            {{$add_on_qty}}</strong>
                                @php($add_ons_cost+=$addon['price']*$add_on_qty)
                            @endforeach                               
                        </td>
                        <td style="text-align: center;">
                            {{$detail['quantity']}}
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>  
</div>      
        <script type="text/javascript">
            window.print();
            window.onafterprint = function (e) {
                window.close(); 
            };
        </script>
    </body>
</html>