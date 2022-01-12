<!DOCTYPE html>
<html lang="en" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Qr</title>
        <style>
            body {
                    font: 13pt Georgia, "Times New Roman", Times, serif;
                    line-height: 1.3;
                    background: #fff !important;
                    color: #000;
                    text-align: center;
                }
                .ticket {
                    border: 1px dotted #000;
                    width: 8cm;
                    display: inline-block;
                }
                .table {
                    width: 100%;
                    font: 9pt Georgia, "Times New Roman", Times, serif;
                }
                .right,.table {
                    text-align: center;
                }
                .timedate {
                    font: 10pt;
                }
                .qrcode {text-align: center;}
            @media print {
                body {
                    font: 9pt Georgia, "Times New Roman", Times, serif;
                    line-height: 1.3;
                    background: #fff !important;
                    color: #000;
                    text-align: inherit;
                }
                .ticket {border: none;
                    width: 100%;
                    display: auto;}
                .table {
                    width: 100%;
                    font: 6pt Georgia, "Times New Roman", Times, serif;
                }
                .timedate {
                    font: 6pt;
                }
            }

            @page {
                size: auto;   /* auto is the initial value */
                margin: .2cm;  /* this affects the margin in the printer settings */
                font-family: emoji !important;
            }
        </style>
    </head>
    <body>
@foreach($order->details as $detail)
	@if($detail->product)
        <div class="ticket">
            <div style="width:100%;text-align:center;">
                <b>  {{$detail->product['name']}} </b>
                <?php
                    echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG((string)$detail->product['id'], 'C128') . '" alt="barcode" width="75%"  />';
                ?>

                <table class="table" style="display: inline;">
                    <thead>
                    <tr>
                        <th class="text-left"><strong>رقم الطلب :{{$order->id}}</strong></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
	@endif
@endforeach    	
        <script type="text/javascript">
            window.print();
            window.onafterprint = function (e) {
                window.close();
            };
        </script>
    </body>
</html>
