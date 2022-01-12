<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>Order</title>       
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


                <div class='center'>
                    <div class='display-block' style="border: 2px solid;border-radius: 5px;padding: 5px 10px;">الأسعار شاملة ضريبة القيمة المضافة</div>
                </div>    
        </div>      
        <script type="text/javascript">
            window.print();
            // window.onafterprint = function (e) {
            //     window.close();
            // };
        </script>
    </body>
</html>