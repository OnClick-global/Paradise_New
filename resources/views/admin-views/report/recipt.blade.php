@extends('layouts.admin.app')

@section('title',\App\CentralLogics\translate('earning'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="media mb-3">
                <!-- Avatar -->
                <div class="avatar avatar-xl avatar-4by3 mr-2">
                    <img class="avatar-img" src="{{asset('public/assets/admin')}}/svg/illustrations/earnings.png"
                         alt="Image Description">
                </div>
                <!-- End Avatar -->

                <div class="media-body">
                    <div class="row">
                        <div class="col-lg mb-3 mb-lg-0 text-capitalize">
                            <h1 class="page-header-title">التقرير اليومى</h1>

                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span>{{\App\CentralLogics\translate('admin')}}:</span>
                                    <a href="#">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                                </div>

                                <div class="col-auto">
                                    <div class="row align-items-center g-0">
                                        <div class="col-auto pr-2">{{\App\CentralLogics\translate('date')}}</div>

                                        <!-- Flatpickr -->
                                        <div>
                                            ( {{session('from_date')}} - {{session('to_date')}} )
                                        </div>
                                        <!-- End Flatpickr -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="d-flex">
                                <a class="btn btn-icon btn-primary rounded-circle" href="{{route('admin.dashboard')}}">
                                    <i class="tio-home-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Media -->

            <!-- Nav -->
            <!-- Nav -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <span class="hs-nav-scroller-arrow-prev" style="display: none;">
              <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                <i class="tio-chevron-left"></i>
              </a>
            </span>

                <span class="hs-nav-scroller-arrow-next" style="display: none;">
              <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                <i class="tio-chevron-right"></i>
              </a>
            </span>
            </div>
            <!-- End Nav -->
        </div>
        <!-- End Page Header -->

        <div class="row border-bottom border-right border-left border-top" style="border-radius: 10px">
            <div class="col-lg-12 pt-3">
                <form action="{{route('admin.report.printrecipt')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <input type="datetime-local" name="from" id="from_date"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <input type="datetime-local" name="to" id="to_date"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary btn-block">عرض</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @php
                $from = session('from_date');
               $to = session('to_date');
               $total_tax=\App\Model\Order::where(['order_status'=>'delivered'])
               ->whereBetween('created_at', [$from, $to])
               ->sum('total_tax_amount');
               if($total_tax==0){
                   $total_tax=0.01;
               }
            @endphp
            <div class="col-sm-6 col-lg-6 mb-3 mb-lg-6">
            @php
                $total_sold=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('order_amount');
            if($total_sold==0){
                $total_sold=.01;
            }
            @endphp
            <!-- Card -->
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <!-- Media -->
                                <div class="media">
                                    <i class="tio-dollar-outlined nav-icon"></i>

                                    <div class="media-body">
                                        <h4 class="mb-1">{{\App\CentralLogics\translate('total sold')}}</h4>
                                        <span class="font-size-sm text-success">
                                          <i class="tio-trending-up"></i> {{round(abs($total_sold-$total_tax))}} {{\App\CentralLogics\Helpers::currency_symbol()}}
                                        </span>
                                    </div>

                                </div>
                                <!-- End Media -->
                            </div>

                            <div class="col-auto">
                                <!-- Circle -->
                                <div class="js-circle"
                                     data-hs-circles-options='{
                                       "value": {{$total_sold=='.01'?0:round((($total_sold-$total_tax)/$total_sold)*100)}},
                                       "maxValue": 100,
                                       "duration": 2000,
                                       "isViewportInit": true,
                                       "colors": ["#e7eaf3", "green"],
                                       "radius": 25,
                                       "width": 3,
                                       "fgStrokeLinecap": "round",
                                       "textFontSize": 14,
                                       "additionalText": "%",
                                       "textClass": "circle-custom-text",
                                       "textColor": "green"
                                     }'></div>
                                <!-- End Circle -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                </div>
                <!-- End Card -->
            </div>
        </div>
        <!-- End Stats -->
        <hr>
        <!-- End Row -->
    </div>
@endsection

@push('script')

@endpush

@push('script_2')

    <script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script
        src="{{asset('public/assets/admin')}}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="{{asset('public/assets/admin')}}/js/hs.chartjs-matrix.js"></script>

    <script>
        $(document).on('ready', function () {

            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });


            // INITIALIZATION OF NAV SCROLLER
            // =======================================================
            $('.js-nav-scroller').each(function () {
                new HsNavScroller($(this)).init()
            });


            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);


            // INITIALIZATION OF CHARTJS
            // =======================================================
            $('.js-chart').each(function () {
                $.HSCore.components.HSChartJS.init($(this));
            });

            var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

            // Call when tab is clicked
            $('[data-toggle="chart"]').click(function (e) {
                let keyDataset = $(e.currentTarget).attr('data-datasets')

                // Update datasets for chart
                updatingChart.data.datasets.forEach(function (dataset, key) {
                    dataset.data = updatingChartDatasets[keyDataset][key];
                });
                updatingChart.update();
            })


            // INITIALIZATION OF MATRIX CHARTJS WITH CHARTJS MATRIX PLUGIN
            // =======================================================
            function generateHoursData() {
                var data = [];
                var dt = moment().subtract(365, 'days').startOf('day');
                var end = moment().startOf('day');
                while (dt <= end) {
                    data.push({
                        x: dt.format('YYYY-MM-DD'),
                        y: dt.format('e'),
                        d: dt.format('YYYY-MM-DD'),
                        v: Math.random() * 24
                    });
                    dt = dt.add(1, 'day');
                }
                return data;
            }

            $.HSCore.components.HSChartMatrixJS.init($('.js-chart-matrix'), {
                data: {
                    datasets: [{
                        label: 'Commits',
                        data: generateHoursData(),
                        width: function (ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.right - a.left) / 70;
                        },
                        height: function (ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.bottom - a.top) / 10;
                        }
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            title: function () {
                                return '';
                            },
                            label: function (item, data) {
                                var v = data.datasets[item.datasetIndex].data[item.index];

                                if (v.v.toFixed() > 0) {
                                    return '<span class="font-weight-bold">' + v.v.toFixed() + ' hours</span> on ' + v.d;
                                } else {
                                    return '<span class="font-weight-bold">No time</span> on ' + v.d;
                                }
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            position: 'bottom',
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'week',
                                round: 'week',
                                displayFormats: {
                                    week: 'MMM'
                                }
                            },
                            ticks: {
                                "labelOffset": 20,
                                "maxRotation": 0,
                                "minRotation": 0,
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 12,
                            },
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'day',
                                parser: 'e',
                                displayFormats: {
                                    day: 'ddd'
                                }
                            },
                            ticks: {
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 2,
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });


            // INITIALIZATION OF CLIPBOARD
            // =======================================================
            $('.js-clipboard').each(function () {
                var clipboard = $.HSCore.components.HSClipboard.init(this);
            });


            // INITIALIZATION OF CIRCLES
            // =======================================================
            $('.js-circle').each(function () {
                var circle = $.HSCore.components.HSCircles.init($(this));
            });
        });
    </script>

    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('Invalid date range!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush
