@include('clients.blocks.header')
@include('clients.blocks.banner')

@php
    $statusMap = [
        'cho_xac_nhan' => ['label' => 'Chờ xác nhận', 'class' => 'badge bg-warning text-dark'],
        'da_xac_nhan' => ['label' => 'Đã xác nhận', 'class' => 'badge bg-primary'],
        'da_thanh_toan' => ['label' => 'Đã thanh toán', 'class' => 'badge bg-success'],
        'da_huy' => ['label' => 'Đã hủy', 'class' => 'badge bg-danger'],
    ];
    $status = $statusMap[$booking->trangthai] ?? ['label' => $booking->trangthai, 'class' => 'badge bg-secondary'];
@endphp

<section class="container" style="margin-top:50px; margin-bottom: 100px">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div style="width:72px;height:72px;border-radius:50%;background:#e8f7ee;color:#1f8b4c;display:inline-flex;align-items:center;justify-content:center;font-size:34px;margin-bottom:14px;">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2 class="mb-2">Đặt tour thành công</h2>
                        <p class="text-muted mb-0">Mã đặt tour: #{{ $booking->dtid }}</p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6"><strong>Tour:</strong> {{ $booking->tentour }}</div>
                        <div class="col-md-6"><strong>Trạng thái:</strong> <span class="{{ $status['class'] }}">{{ $status['label'] }}</span></div>
                        <div class="col-md-6"><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($booking->ngaytao)->format('d/m/Y H:i') }}</div>
                        <div class="col-md-6"><strong>Lịch khởi hành:</strong> {{ $booking->ngaybatdau ? \Carbon\Carbon::parse($booking->ngaybatdau)->format('d/m/Y') : '-' }}</div>
                        <div class="col-md-6"><strong>Người lớn:</strong> {{ $booking->songuoilon }}</div>
                        <div class="col-md-6"><strong>Trẻ em:</strong> {{ $booking->sotreem }}</div>
                        <div class="col-md-6"><strong>Tổng tiền:</strong> {{ number_format($booking->tongtien, 0, ',', '.') }} VNĐ</div>
                        <div class="col-md-6"><strong>Giảm giá:</strong> {{ number_format($booking->giamgia, 0, ',', '.') }} VNĐ</div>
                        <div class="col-md-6"><strong>Giá cuối:</strong> {{ number_format($booking->giacuoi, 0, ',', '.') }} VNĐ</div>
                        <div class="col-md-6"><strong>Thanh toán:</strong> {{ $booking->phuongthuc }}</div>
                        @if(!empty($booking->makhuyenmai))
                            <div class="col-md-6"><strong>Mã giảm giá:</strong> {{ $booking->makhuyenmai }}</div>
                        @endif
                    </div>

                    <div class="alert alert-info mb-4">
                        Chúng tôi đã ghi nhận đơn đặt tour của bạn. Nếu thanh toán chuyển khoản hoặc MoMo, trạng thái thanh toán sẽ được cập nhật sau khi đối soát.
                    </div>

                    <!-- Phần thanh toán VNPAY/MoMo nếu chưa thanh toán -->
                    @if($booking->trangthai === 'cho_xac_nhan' && $booking->phuongthuc === 'momo')
                        <div class="alert alert-warning mb-4">
                            <strong>⚠️ Cần thanh toán MoMo</strong><br>
                            Vui lòng thanh toán để hoàn tất đặt tour. Số tiền cần thanh toán: <strong>{{ number_format($booking->giacuoi, 0, ',', '.') }} VNĐ</strong>
                        </div>
                        <div class="d-grid gap-2 mb-4">
                            <button type="button" class="btn btn-warning" onclick="paymentMomo({{ $booking->dtid }})">
                                <i class="fas fa-mobile-alt"></i> Thanh toán MoMo
                            </button>
                        </div>
                    @elseif($booking->trangthai === 'cho_xac_nhan' && $booking->phuongthuc === 'vnpay')
                        <div class="alert alert-warning mb-4">
                            <strong>⚠️ Cần thanh toán VNPAY</strong><br>
                            Vui lòng thanh toán để hoàn tất đặt tour. Số tiền cần thanh toán: <strong>{{ number_format($booking->giacuoi, 0, ',', '.') }} VNĐ</strong>
                        </div>
                        <div class="d-grid gap-2 mb-4">
                            <form action="{{ route('vnpay.payment') }}" method="POST">
                                @csrf
                                <input type="hidden" name="dtid" value="{{ $booking->dtid }}">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-credit-card"></i> Thanh toán VNPAY
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('my-tours') }}" class="theme-btn">Xem tour của tôi</a>
                        <a href="{{ route('tours') }}" class="theme-btn bgc-secondary">Đặt thêm tour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('clients.blocks.footer')
