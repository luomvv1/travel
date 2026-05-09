@include('admin.blocks.header')

<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')


        <!-- page content -->
        <div class="right_col" role="main">
            <!-- top tiles -->
            <div class="row" style="display: inline-block;width: 100%">
                <div class="tile_count">
                    <div class="col-md-3 col-sm-4  tile_stats_count">
                        <span class="count_top"><i class="fa fa-user"></i> Tổng số tours đang hoạt động</span>
                        <div class="count green"><i class="fa fa-sort-asc"></i> {{ $summary['tourWorking'] }}</div>
                    </div>
                    <div class="col-md-3 col-sm-4  tile_stats_count">
                        <span class="count_top"><i class="fa fa-clock-o"></i> Tổng số lượt booking</span>
                        <div class="count green"><i class="fa fa-sort-asc"></i> {{ $summary['countBooking'] }}</div>
                    </div>
                    <div class="col-md-3 col-sm-4  tile_stats_count">
                        <span class="count_top"><i class="fa fa-user"></i> Số người dùng đăng ký</span>
                        <div class="count green"><i class="fa fa-sort-asc"></i> {{ number_format($summary['countUser'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-md-3 col-sm-4  tile_stats_count">
                        <span class="count_top"><i class="fa fa-user"></i> Tổng doanh thu</span>
                        <div class="count red">{{ number_format($summary['totalAmount'], 0, ',', '.') }} vnđ</div>
                        <span class="sparkline_two" style="height: 160px;"><canvas width="196" height="40"
                                style="display: inline-block; width: 196px; height: 40px; vertical-align: top;"></canvas></span>
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-md-6 col-sm-4 ">
                    <div class="x_panel tile fixed_height_320 overflow_hidden">
                        <div class="x_title">
                            <h2>Điểm đến </h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                        aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="" style="width:100%">
                                <tr>
                                    <th style="width:37%;">
                                        <p>Tổng hợp danh sách tours</p>
                                    </th>
                                    <th>
                                        <div class="col-lg-7 col-md-7 col-sm-7 ">
                                            <p class="">Tên</p>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-5 " style="text-align: center">
                                            <p class="">Số lượng</p>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <canvas class="canvasDoughnut" height="140" width="140"
                                            style="margin: 15px 10px 10px 0"
                                            data-chart-values="{{ json_encode($dataDomain['values']) }}"></canvas>
                                    </td>
                                    <td>
                                        <table class="tile_info">
                                            <tr>
                                                <td>
                                                    <p><i class="fa fa-square red"></i>Miền Bắc </p>
                                                </td>
                                                <td>{{ $dataDomain['values'][0] }}</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><i class="fa fa-square green"></i>Miền Trung </p>
                                                </td>
                                                <td>{{ $dataDomain['values'][1] }}</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><i class="fa fa-square purple"></i>Miền Nam </p>
                                                </td>
                                                <td>{{ $dataDomain['values'][2] }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-4  ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Đặt tour</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                        aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <div id="echart_donut" data-payment-method='{{ json_encode($paymentStatus) }}'
                                style="height: 350px; -webkit-tap-highlight-color: transparent; user-select: none; position: relative; background-color: transparent;"
                                _echarts_instance_="ec_1733563825119">
                                <div
                                    style="position: relative; overflow: hidden; width: 380px; height: 350px; cursor: default;">
                                    <canvas width="380" height="350" data-zr-dom-id="zr_0"
                                        style="position: absolute; left: 0px; top: 0px; width: 380px; height: 350px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></canvas>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6  ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Tours <small>được đặt nhiều nhất</small></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Số chỗ đã đặt</th>
                                        <th>Số chỗ còn trống</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($toursBooked as $item)
                                        <tr>
                                            <th scope="row">{{ $item->tourId }}</th>
                                            <td>{{ $item->title }}</td>
                                            <td>{{ $item->booked_quantity }}</td>
                                            <td>{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6  ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Đơn đặt mới</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ và tên</th>
                                        <th>Tên tours</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($newBooking as $item)
                                        <tr>
                                            <th scope="row">
                                                <a href="{{ route('admin.booking-detail',['id' => $item->bookingId]) }}">{{ $item->bookingId }}</a>
                                            </th>
                                            <td>{{ $item->fullName }}</td>
                                            <td>{{ $item->tour_name }}</td>
                                            <td>{{ number_format($item->totalPrice, 0, ',', '.') }}</td>
                                            <td>
                                                @if (in_array($item->bookingStatus, ['', 'cho_xac_nhan'], true))
                                                    <span class="badge badge-warning">Chờ xác nhận</span>
                                                @elseif ($item->bookingStatus === 'da_xac_nhan')
                                                    <span class="badge badge-primary">Đã xác nhận</span>
                                                @elseif ($item->bookingStatus === 'hoan_thanh')
                                                    <span class="badge badge-success">Hoàn thành</span>
                                                @else
                                                    <span class="badge badge-danger">Đã hủy</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 ">

                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Doanh thu theo tháng</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                        aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <canvas id="lineChart" data-revenue-per-month='{{ json_encode($revenue) }}'></canvas>
                        </div>

                    </div>

                    <div class="clearfix"></div>
                </div>

            </div>

            <!-- Chi tiết đặt tour -->
            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Chi tiết đặt tour gần đây</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đặt</th>
                                        <th>Khách hàng</th>
                                        <th>Tên tour</th>
                                        <th>Ngày khởi hành</th>
                                        <th>Số người</th>
                                        <th>Giá</th>
                                        <th>Trạng thái đặt</th>
                                        <th>Thanh toán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookingDetails as $detail)
                                        <tr>
                                            <td><strong>{{ $detail->booking_id }}</strong></td>
                                            <td>{{ $detail->customer_name }}</td>
                                            <td>{{ $detail->tour_name }}</td>
                                            <td>{{ date('d/m/Y', strtotime($detail->tour_date)) }}</td>
                                            <td>{{ $detail->total_people }}</td>
                                            <td>{{ number_format($detail->total_price, 0, ',', '.') }} VNĐ</td>
                                            <td>
                                                @if($detail->booking_status === 'cho_xac_nhan')
                                                    <span class="badge badge-warning">Chờ xác nhận</span>
                                                @elseif($detail->booking_status === 'da_xac_nhan')
                                                    <span class="badge badge-info">Đã xác nhận</span>
                                                @elseif($detail->booking_status === 'da_thanh_toan')
                                                    <span class="badge badge-success">Đã thanh toán</span>
                                                @else
                                                    <span class="badge badge-danger">Đã hủy</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($detail->payment_status === 'thanh_cong')
                                                    <span class="badge badge-success">✓ Thành công</span>
                                                @elseif($detail->payment_status === 'that_bai')
                                                    <span class="badge badge-danger">✗ Thất bại</span>
                                                @else
                                                    <span class="badge badge-secondary">Chờ xử lý</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doanh thu theo tour -->
            <div class="row">
                <div class="col-md-6 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Doanh thu theo tour</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên tour</th>
                                        <th>Số đơn</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($revenueByTour as $revenue)
                                        <tr>
                                            <td>{{ $revenue->tour_name }}</td>
                                            <td><span class="badge badge-primary">{{ $revenue->booking_count }}</span></td>
                                            <td><strong>{{ number_format($revenue->total_revenue, 0, ',', '.') }} VNĐ</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Lịch khởi hành còn chỗ -->
                <div class="col-md-6 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Lịch khởi hành còn chỗ</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tour</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Chỗ trống</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($scheduleAvailability as $schedule)
                                        <tr>
                                            <td>{{ substr($schedule->tour_name, 0, 25) }}...</td>
                                            <td>{{ date('d/m/Y', strtotime($schedule->start_date)) }}</td>
                                            <td>
                                                <span class="badge @if($schedule->available_slots > 10) badge-success @elseif($schedule->available_slots > 5) badge-warning @else badge-danger @endif">
                                                    {{ $schedule->available_slots }} chỗ
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Giao dịch VNPAY -->
            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Giao dịch VNPAY gần đây</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã giao dịch</th>
                                        <th>VNPAY ID</th>
                                        <th>Số tiền</th>
                                        <th>Ngân hàng</th>
                                        <th>Thời gian thanh toán</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vnpayTransactions as $transaction)
                                        <tr>
                                            <td><small>{{ $transaction->transaction_ref }}</small></td>
                                            <td><small>{{ $transaction->vnpay_id ?? 'N/A' }}</small></td>
                                            <td><strong>{{ number_format($transaction->amount, 0, ',', '.') }} VNĐ</strong></td>
                                            <td><small>{{ $transaction->bank_code ?? 'N/A' }}</small></td>
                                            <td>{{ $transaction->pay_date ? date('d/m/Y H:i', strtotime($transaction->pay_date)) : 'N/A' }}</td>
                                            <td>
                                                @if($transaction->status === 'thanh_cong')
                                                    <span class="badge badge-success">✓ Thành công</span>
                                                @elseif($transaction->status === 'that_bai')
                                                    <span class="badge badge-danger">✗ Thất bại</span>
                                                @else
                                                    <span class="badge badge-secondary">Chờ xử lý</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page content -->
    </div>
</div>

@include('admin.blocks.footer')
