<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>Order</title>       
        <style type="text/css">
            @page  {
                margin: 0px;
                font-size: 12px;
                font-family: auto;
                font-weight: bolder;
            }            
            body{
                font-size: 12px;
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
                width: 50% !important;
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
            .page-break-avoid td {
                border: 1px solid black;
            }
            pre{
                font-family: 'Verdana';
            }             
        </style>         
    </head>
    <body>
        <div id="printableArea">
                <div class='center'>
                    <div class='bold display-block'>{{\App\Model\BusinessSetting::where(['key'=>'restaurant_name'])->first()->value}}</div>
                </div>
                <div class='mb-1rem'>
                    <table class='w-100'>
                        <tr>
                            <td class='bold w-100'>التاريح  من : {{$from}}</td>
                        </tr>
                        <tr>
                            <td class='bold w-100'>التاريخ حتى : {{$to}}</td><br>
                        </tr>
                    </table>
                </div>
                <table class='mb-1rem w-100 page-break-avoid'>
                    <tr>
                        <td style="text-align: center;" class='bold w-45'>الصنف</td>
                        <td style="text-align: center;" class='bold right'>السعر</td>
                        <td style="text-align: center;" class='bold right'>الحجم</td>
                        <td style="text-align: center;" class='bold right'>العدد</td>
                        <td style="text-align: center;" class='bold right'>الإجمالى</td>
                    </tr>
                    @foreach($newArr as $key => $orders)
                        @php
                            $category = \App\Model\Category::find($key);
                        @endphp
                        <?php
                            if (count($category['translations'])) {
                                foreach ($category['translations'] as $translation) {
                                    if ($translation->key == 'name') {
                                        $category['name'] = $translation->value;
                                    }
                                    if ($translation->key == 'description') {
                                        $category['description'] = $translation->value;
                                    }
                                }
                            }                
                        ?>                    
                        <tr>
                            <td colspan="5" style="text-align: center;">{{$category->name}}</td>
                        </tr>                    
                        @foreach($orders as $detail)
                            @php($addon_sub_amount=0)
                            @php($sub_total=0)
                            @php($total_tax=0)
                            @php($total_dis_on_pro=0)
                            @php($add_ons_cost=0)            
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
                                        {{$detail->product['name']}} <br>
                                        @foreach(json_decode($detail['add_on_ids'],true) as $key2 =>$id)
                                            @php($addon=\App\Model\AddOn::find($id))
                                                <strong>{{$addon['name']}} * @if($add_on_qtys==null)
                                                            @php($add_on_qty=1)
                                                        @else
                                                            @php($add_on_qty=$add_on_qtys[$key2])
                                                        @endif                            
                                                        {{$add_on_qty}} = @php($addon_amount=($addon['price']))
                                                {{$addon_amount." ".\App\CentralLogics\Helpers::currency_symbol()}}</strong>
                                            @php($add_ons_cost+=$addon['price']*$add_on_qty)
                                        @endforeach                               
                                    </td>
                                    <td style="text-align: center;">
                                        @php($amount=($detail['price']-$detail['discount_on_product']))
                                        {{$amount." ".\App\CentralLogics\Helpers::currency_symbol()}}
                                    </td>
                                    <td style="text-align: center;">
                                        @if(count(json_decode($detail['variation'],true))>0)
                                            @foreach(json_decode($detail['variation'],true)[0] as $key1 => $variation)
                                                {{$variation}}
                                            @endforeach
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        {{$detail['quantity']}}
                                    </td>
                                    <td style="text-align: center;">
                                        @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity']+$add_ons_cost)
                                        {{$amount." ".\App\CentralLogics\Helpers::currency_symbol()}}
                                    </td>
                                </tr>    

                                @php($sub_total+=$amount)
                                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
                            @endif
                        @endforeach
                    @endforeach
                </table>
                <table class='mb-1rem w-100 page-break-avoid'>
                    <tr>
                        <td class='w-50'>إجمالى  توصيل :</td>
                        <td class='right'>{{round(array_sum($orderTypeCount['delivery']))." ".\App\CentralLogics\Helpers::currency_symbol()}}</td>
                    </tr>
                    <tr>
                        <td class='w-50'>إجمالى  تيك اواى :</td>
                        <td class='right'>{{round(array_sum($orderTypeCount['take_away']))." ".\App\CentralLogics\Helpers::currency_symbol()}}</td>
                    </tr>
                    <tr>
                        <td class='w-50'>إجمالى الخصم </td>
                        <td class='right'>{{round(array_sum($orderTypeCount['coupon_discount_amount']))." ".\App\CentralLogics\Helpers::currency_symbol()}}</td>
                    </tr>
                    <tr>
                        <td class='w-50'>الإجمالى</td>
                        <td class='right'>{{round(array_sum($orderTypeCount['delivery'])+array_sum($orderTypeCount['take_away']))." ".\App\CentralLogics\Helpers::currency_symbol()}}</td>
                    </tr>
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