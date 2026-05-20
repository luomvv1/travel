@include('admin.blocks.header')

@php
    $money = fn ($value) => number_format((float) ($value ?? 0), 0, ',', '.') . ' VNĐ';
    $shortMoney = function ($value) {
        $value = (float) ($value ?? 0);

        if ($value >= 1000000000) {
            return number_format($value / 1000000000, 1, ',', '.') . ' tỷ';
        }

        if ($value >= 1000000) {
            return number_format($value / 1000000, 1, ',', '.') . ' triệu';
        }

        return number_format($value, 0, ',', '.') . ' đ';
    };

    $date = fn ($value, $format = 'd/m/Y') => $value ? \Carbon\Carbon::parse($value)->format($format) : 'Chưa có';
    $domainValues = array_values($dataDomain['values'] ?? [0, 0, 0]);
    $domainLabels = ['Miền Bắc', 'Miền Trung', 'Miền Nam'];
    $domainColors = ['#1F7A8C', '#E0A458', '#6A994E'];
    $domainTotal = max(array_sum($domainValues), 1);
    $monthlyRevenue = array_values($revenue ?? array_fill(0, 12, 0));
    $paymentRows = collect($paymentStatus ?? []);
    $paymentMethodLabels = [
        'vnpay' => 'VNPAY',
        'momo' => 'MoMo',
        'tai_van_phong' => 'Tại văn phòng',
        '' => 'Chưa chọn',
    ];
    $paymentLabels = $paymentRows->map(fn ($item) => $paymentMethodLabels[$item->paymentMethod ?? ''] ?? 'Khác')->values();
    $paymentCounts = $paymentRows->map(fn ($item) => (int) ($item->count ?? 0))->values();
    $bookingLabels = [
        '' => ['Chờ xác nhận', 'status-waiting'],
        'cho_xac_nhan' => ['Chờ xác nhận', 'status-waiting'],
        'da_xac_nhan' => ['Đã xác nhận', 'status-confirmed'],
        'hoan_thanh' => ['Hoàn thành', 'status-success'],
        'da_huy' => ['Đã hủy', 'status-danger'],
    ];
    $paymentLabelsStatus = [
        '' => ['Chờ xử lý', 'status-muted'],
        'cho_xu_ly' => ['Chờ xử lý', 'status-muted'],
        'thanh_cong' => ['Thành công', 'status-success'],
        'that_bai' => ['Thất bại', 'status-danger'],
        'cho_hoan_tien' => ['Chờ hoàn tiền', 'status-waiting'],
        'da_hoan_tien' => ['Đã hoàn tiền', 'status-confirmed'],
    ];
@endphp

<style>
    .dashboard-page {
        color: #253242;
    }

    .dashboard-hero {
        background: #ffffff;
        border: 1px solid #e6edf3;
        border-radius: 8px;
        margin-bottom: 18px;
        padding: 20px 24px;
    }

    .dashboard-hero h1 {
        color: #1f2d3d;
        font-size: 26px;
        font-weight: 700;
        letter-spacing: 0;
        margin: 0 0 6px;
    }

    .dashboard-hero p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    .dashboard-kpi {
        background: #ffffff;
        border: 1px solid #e7edf2;
        border-left: 4px solid #1f7a8c;
        border-radius: 8px;
        min-height: 128px;
        padding: 18px;
    }

    .dashboard-kpi .kpi-icon {
        align-items: center;
        background: #eef7f8;
        border-radius: 8px;
        color: #1f7a8c;
        display: inline-flex;
        height: 38px;
        justify-content: center;
        margin-bottom: 12px;
        width: 38px;
    }

    .dashboard-kpi h3 {
        color: #65758a;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0;
        margin: 0 0 8px;
        text-transform: uppercase;
    }

    .dashboard-kpi strong {
        color: #172033;
        display: block;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.1;
        word-break: break-word;
    }

    .dashboard-kpi span {
        color: #8091a7;
        display: block;
        font-size: 12px;
        margin-top: 8px;
    }

    .dashboard-panel {
        background: #ffffff;
        border: 1px solid #e7edf2;
        border-radius: 8px;
        margin-bottom: 18px;
        padding: 18px;
    }

    .dashboard-panel-title {
        align-items: center;
        border-bottom: 1px solid #edf2f6;
        display: flex;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
    }

    .dashboard-panel-title h2 {
        color: #1f2d3d;
        font-size: 17px;
        font-weight: 700;
        letter-spacing: 0;
        margin: 0;
    }

    .dashboard-panel-title small {
        color: #8a98a8;
        font-size: 12px;
    }

    .dashboard-chart {
        height: 280px;
        position: relative;
    }

    .domain-row {
        margin-bottom: 15px;
    }

    .domain-meta {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .domain-meta span {
        color: #435366;
        font-weight: 600;
    }

    .domain-meta strong {
        color: #1f2d3d;
    }

    .progress.dashboard-progress {
        background: #eff4f7;
        border-radius: 999px;
        box-shadow: none;
        height: 8px;
        margin: 0;
    }

    .progress.dashboard-progress .progress-bar {
        box-shadow: none;
    }

    .dashboard-table {
        margin-bottom: 0;
    }

    .dashboard-table > thead > tr > th {
        border-bottom: 1px solid #e8eef3;
        color: #6b7d90;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .dashboard-table > tbody > tr > td {
        border-top: 1px solid #eef2f6;
        color: #344255;
        vertical-align: middle;
    }

    .tour-title {
        color: #1f2d3d;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .tour-muted,
    .empty-state {
        color: #8a98a8;
        font-size: 12px;
    }

    .dashboard-status {
        border-radius: 999px;
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 10px;
        white-space: nowrap;
    }

    .status-waiting {
        background: #fff6dd;
        color: #9a6500;
    }

    .status-confirmed {
        background: #e8f2ff;
        color: #1b64b0;
    }

    .status-success {
        background: #e8f6ee;
        color: #20764a;
    }

    .status-danger {
        background: #ffecec;
        color: #b42318;
    }

    .status-muted {
        background: #eef2f6;
        color: #5b6778;
    }

    .booking-feed {
        margin: 0;
        padding: 0;
    }

    .booking-feed li {
        border-bottom: 1px solid #edf2f6;
        list-style: none;
        padding: 12px 0;
    }

    .booking-feed li:first-child {
        padding-top: 0;
    }

    .booking-feed li:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .booking-feed a {
        color: #1f7a8c;
        font-weight: 700;
    }

    .responsive-table {
        overflow-x: auto;
    }

    @media (max-width: 767px) {
        .dashboard-kpi {
            margin-bottom: 12px;
        }

        .dashboard-hero {
            padding: 16px;
        }

        .dashboard-hero h1 {
            font-size: 22px;
        }
    }
</style>

<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col dashboard-page" role="main">
            <div class="dashboard-hero">
                <h1>Dashboard quản trị</h1>
                <p>Theo dõi nhanh tình hình tour, đơn đặt, thanh toán và doanh thu của hệ thống.</p>
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-kpi">
                        <div class="kpi-icon"><i class="fa fa-map-signs"></i></div>
                        <h3>Tour hoạt động</h3>
                        <strong>{{ number_format($summary['tourWorking'] ?? 0, 0, ',', '.') }}</strong>
                        <span>Tour đang được mở bán</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-kpi" style="border-left-color:#6A994E">
                        <div class="kpi-icon" style="background:#eef8ef;color:#6A994E"><i class="fa fa-calendar-check-o"></i></div>
                        <h3>Lượt đặt tour</h3>
                        <strong>{{ number_format($summary['countBooking'] ?? 0, 0, ',', '.') }}</strong>
                        <span>Tổng số đơn trong hệ thống</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-kpi" style="border-left-color:#E0A458">
                        <div class="kpi-icon" style="background:#fff7e8;color:#B56B00"><i class="fa fa-users"></i></div>
                        <h3>Khách hàng</h3>
                        <strong>{{ number_format($summary['countUser'] ?? 0, 0, ',', '.') }}</strong>
                        <span>Tài khoản đang hoạt động</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-kpi" style="border-left-color:#9B5DE5">
                        <div class="kpi-icon" style="background:#f4edff;color:#7b3fc6"><i class="fa fa-money"></i></div>
                        <h3>Doanh thu</h3>
                        <strong>{{ $shortMoney($summary['totalAmount'] ?? 0) }}</strong>
                        <span>{{ $money($summary['totalAmount'] ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 18px;">
                <div class="col-md-8 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Doanh thu theo tháng</h2>
                            <small>Chỉ tính giao dịch thành công</small>
                        </div>
                        <div class="dashboard-chart">
                            <canvas id="dashboardRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Phương thức thanh toán</h2>
                            <small>{{ number_format($paymentCounts->sum(), 0, ',', '.') }} giao dịch</small>
                        </div>
                        <div class="dashboard-chart">
                            <canvas id="dashboardPaymentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Phân bổ tour</h2>
                            <small>Theo khu vực</small>
                        </div>
                        @foreach ($domainLabels as $index => $label)
                            @php
                                $value = (int) ($domainValues[$index] ?? 0);
                                $percent = round(($value / $domainTotal) * 100);
                            @endphp
                            <div class="domain-row">
                                <div class="domain-meta">
                                    <span>{{ $label }}</span>
                                    <strong>{{ $value }} tour</strong>
                                </div>
                                <div class="progress dashboard-progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%; background: {{ $domainColors[$index] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Tour được đặt nhiều</h2>
                            <small>Top {{ $toursBooked->count() }}</small>
                        </div>
                        @forelse ($toursBooked as $item)
                            @php
                                $capacity = max((int) ($item->quantity ?? 0), 1);
                                $booked = (int) ($item->booked_quantity ?? 0);
                                $percent = min(round(($booked / $capacity) * 100), 100);
                            @endphp
                            <div class="domain-row">
                                <div class="tour-title">{{ \Illuminate\Support\Str::limit($item->title, 44) }}</div>
                                <div class="domain-meta">
                                    <span>{{ $booked }} khách đã đặt</span>
                                    <strong>{{ $capacity }} chỗ</strong>
                                </div>
                                <div class="progress dashboard-progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%; background: #1F7A8C"></div>
                                </div>
                            </div>
                        @empty
                            <p class="empty-state">Chưa có dữ liệu đặt tour.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Đơn đặt mới</h2>
                            <small>Cập nhật gần đây</small>
                        </div>
                        <ul class="booking-feed">
                            @forelse ($newBooking as $item)
                                @php
                                    [$label, $class] = $bookingLabels[$item->bookingStatus ?? ''] ?? ['Khác', 'status-muted'];
                                @endphp
                                <li>
                                    <a href="{{ route('admin.booking-detail', ['id' => $item->bookingId]) }}">#{{ $item->bookingId }}</a>
                                    <span class="pull-right dashboard-status {{ $class }}">{{ $label }}</span>
                                    <div class="tour-title">{{ $item->fullName ?? 'Khách hàng' }}</div>
                                    <div class="tour-muted">{{ \Illuminate\Support\Str::limit($item->tour_name, 45) }}</div>
                                    <div class="tour-muted">{{ $money($item->totalPrice) }} · {{ $date($item->bookingDate, 'd/m/Y H:i') }}</div>
                                </li>
                            @empty
                                <li class="empty-state">Chưa có đơn đặt mới.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Đơn đặt tour gần đây</h2>
                            <small>10 đơn mới nhất</small>
                        </div>
                        <div class="responsive-table">
                            <table class="table dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Tour</th>
                                        <th>Khởi hành</th>
                                        <th>Số người</th>
                                        <th>Giá trị</th>
                                        <th>Đặt tour</th>
                                        <th>Thanh toán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bookingDetails as $detail)
                                        @php
                                            [$bookingText, $bookingClass] = $bookingLabels[$detail->booking_status ?? ''] ?? ['Khác', 'status-muted'];
                                            [$paymentText, $paymentClass] = $paymentLabelsStatus[$detail->payment_status ?? ''] ?? ['Chưa có', 'status-muted'];
                                        @endphp
                                        <tr>
                                            <td><a href="{{ route('admin.booking-detail', ['id' => $detail->booking_id]) }}">#{{ $detail->booking_id }}</a></td>
                                            <td>{{ $detail->customer_name ?? 'Khách hàng' }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($detail->tour_name, 42) }}</td>
                                            <td>{{ $date($detail->tour_date) }}</td>
                                            <td>{{ number_format($detail->total_people ?? 0, 0, ',', '.') }}</td>
                                            <td><strong>{{ $money($detail->total_price) }}</strong></td>
                                            <td><span class="dashboard-status {{ $bookingClass }}">{{ $bookingText }}</span></td>
                                            <td><span class="dashboard-status {{ $paymentClass }}">{{ $paymentText }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="empty-state">Chưa có dữ liệu đơn đặt tour.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Doanh thu theo tour</h2>
                            <small>Top 5</small>
                        </div>
                        <div class="responsive-table">
                            <table class="table dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Tour</th>
                                        <th>Đơn</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($revenueByTour as $item)
                                        <tr>
                                            <td>{{ \Illuminate\Support\Str::limit($item->tour_name, 48) }}</td>
                                            <td>{{ number_format($item->booking_count ?? 0, 0, ',', '.') }}</td>
                                            <td><strong>{{ $money($item->total_revenue) }}</strong></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="empty-state">Chưa có doanh thu thành công.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Lịch khởi hành còn chỗ</h2>
                            <small>Ưu tiên ngày gần nhất</small>
                        </div>
                        <div class="responsive-table">
                            <table class="table dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Tour</th>
                                        <th>Ngày đi</th>
                                        <th>Ngày về</th>
                                        <th>Còn chỗ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($scheduleAvailability as $schedule)
                                        @php
                                            $slotClass = $schedule->available_slots > 10 ? 'status-success' : ($schedule->available_slots > 5 ? 'status-waiting' : 'status-danger');
                                        @endphp
                                        <tr>
                                            <td>{{ \Illuminate\Support\Str::limit($schedule->tour_name, 38) }}</td>
                                            <td>{{ $date($schedule->start_date) }}</td>
                                            <td>{{ $date($schedule->end_date) }}</td>
                                            <td><span class="dashboard-status {{ $slotClass }}">{{ $schedule->available_slots }} chỗ</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="empty-state">Chưa có lịch khởi hành còn chỗ.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="dashboard-panel">
                        <div class="dashboard-panel-title">
                            <h2>Giao dịch VNPAY gần đây</h2>
                            <small>Theo thời gian thanh toán</small>
                        </div>
                        <div class="responsive-table">
                            <table class="table dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Mã giao dịch</th>
                                        <th>Mã đơn</th>
                                        <th>Số tiền</th>
                                        <th>Ngân hàng</th>
                                        <th>Thời gian</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($vnpayTransactions as $transaction)
                                        @php
                                            [$statusText, $statusClass] = $paymentLabelsStatus[$transaction->status ?? ''] ?? ['Khác', 'status-muted'];
                                        @endphp
                                        <tr>
                                            <td>{{ $transaction->transaction_ref ?? 'N/A' }}</td>
                                            <td><a href="{{ route('admin.booking-detail', ['id' => $transaction->booking_id]) }}">#{{ $transaction->booking_id }}</a></td>
                                            <td><strong>{{ $money($transaction->amount) }}</strong></td>
                                            <td>{{ $transaction->bank_code ?? 'N/A' }}</td>
                                            <td>{{ $date($transaction->pay_date, 'd/m/Y H:i') }}</td>
                                            <td><span class="dashboard-status {{ $statusClass }}">{{ $statusText }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="empty-state">Chưa có giao dịch VNPAY.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            if (!window.Chart) {
                return;
            }

            var revenueCanvas = document.getElementById('dashboardRevenueChart');
            var paymentCanvas = document.getElementById('dashboardPaymentChart');
            var revenueData = @json($monthlyRevenue);
            var paymentLabels = @json($paymentLabels);
            var paymentCounts = @json($paymentCounts);

            Chart.defaults.global.defaultFontFamily = "'Helvetica Neue', Arial, sans-serif";
            Chart.defaults.global.defaultFontColor = '#526172';

            if (revenueCanvas) {
                new Chart(revenueCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                        datasets: [{
                            label: 'Doanh thu',
                            data: revenueData,
                            borderColor: '#1F7A8C',
                            backgroundColor: 'rgba(31, 122, 140, 0.12)',
                            borderWidth: 2,
                            pointBackgroundColor: '#1F7A8C',
                            pointBorderColor: '#ffffff',
                            pointRadius: 4,
                            lineTension: 0.25,
                            fill: true
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        legend: { display: false },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    return Number(tooltipItem.yLabel || 0).toLocaleString('vi-VN') + ' VNĐ';
                                }
                            }
                        },
                        scales: {
                            xAxes: [{
                                gridLines: { display: false }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    callback: function (value) {
                                        return Number(value || 0).toLocaleString('vi-VN');
                                    }
                                }
                            }]
                        }
                    }
                });
            }

            if (paymentCanvas) {
                new Chart(paymentCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: paymentLabels,
                        datasets: [{
                            data: paymentCounts,
                            backgroundColor: ['#1F7A8C', '#E0A458', '#6A994E', '#9B5DE5', '#D1495B'],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        cutoutPercentage: 64,
                        maintainAspectRatio: false,
                        responsive: true,
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 14
                            }
                        }
                    }
                });
            }
        })();
    </script>
@endpush

@include('admin.blocks.footer')
