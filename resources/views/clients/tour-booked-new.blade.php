@include('clients.blocks.header')
@include('clients.blocks.banner')

@php
    // Nếu là VNPAY chưa thanh toán, hết hạn được tính từ ngaytao (15 phút)
    $hetHan = $hetHan ?? null;
    $conHan = $conHan ?? false;
@endphp

<section class="container" style="margin-top:50px; margin-bottom: 100px">
    <div class="booking-container">
        <!-- Contact Information -->
        <div class="booking-info">
            <h2 class="booking-header">Thông Tin Liên Lạc</h2>
            <div class="booking__infor">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" value="{{ $booking->fullName ?? '' }}" readonly class="form-control" style="background-color: #e9ecef;">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="{{ $booking->email ?? '' }}" readonly class="form-control" style="background-color: #e9ecef;">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" value="{{ $booking->tel ?? '' }}" readonly class="form-control" style="background-color: #e9ecef;">
                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" value="{{ $booking->address ?? '' }}" readonly class="form-control" style="background-color: #e9ecef;">
                </div>
            </div>

            <h2 class="booking-header mt-4">Phương Thức Thanh Toán Đã Chọn</h2>
            <div class="payment-methods-display">
                @if ($booking->phuongthuc == 'tai_van_phong')
                    <div class="alert alert-secondary"><i class="fas fa-money-bill-wave"></i> Thanh toán tiền mặt tại văn phòng</div>
                @elseif ($booking->phuongthuc == 'vnpay')
                    <div class="alert alert-secondary"><i class="fas fa-credit-card"></i> Chuyển khoản ngân hàng (VNPAY)</div>
                @elseif ($booking->phuongthuc == 'momo')
                    <div class="alert alert-secondary"><i class="fas fa-wallet"></i> Thanh toán bằng Ví MoMo</div>
                @endif
            </div>
        </div>

        <!-- Order Summary -->
        <div class="booking-summary">
            <div class="summary-section">
                <div>
                    <p>Mã tour: <strong>#{{ $booking->tourid }}</strong> | Mã đơn: <strong>#{{ $booking->dtid }}</strong></p>
                    <h5 class="widget-title">{{ $booking->tentour }}</h5>
                    <p>Ngày đặt: {{ date('d/m/Y H:i', strtotime($booking->ngaytao)) }}</p>
                    <p>Khởi hành: {{ date('d/m/Y', strtotime($booking->ngaybatdau ?? '')) }} - Kết thúc: {{ date('d/m/Y', strtotime($booking->ngayketthuc ?? '')) }}</p>
                </div>

                <div class="order-summary" style="border-bottom: 1px solid #d6d6d6; margin-bottom:20px; padding-bottom: 15px;">
                    <div class="summary-item">
                        <span>Người lớn:</span>
                        <div>
                            <span>{{ $booking->songuoilon }}</span> X 
                            <span>{{ number_format(($booking->tongtien + $booking->giamgia - $booking->giacuoi) / max($booking->songuoilon, 1), 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span>Trẻ em:</span>
                        <div>
                            <span>{{ $booking->sotreem }}</span> X <span>0 VNĐ</span>
                        </div>
                    </div>
                    @if ($booking->giamgia > 0)
                        <div class="summary-item" style="color: green;">
                            <span>Giảm giá:</span>
                            <span>- {{ number_format($booking->giamgia, 0, ',', '.') }} VNĐ</span>
                        </div>
                    @endif
                    <div class="summary-item total-price-booked mt-2" style="font-size: 18px; font-weight: bold; color: #d9534f;">
                        <span>Tổng phải trả:</span>
                        <span>{{ number_format($booking->giacuoi, 0, ',', '.') }} VNĐ</span>
                    </div>
                </div>

                <!-- Trạng thái đơn hàng & Thanh toán -->
                <div style="padding: 15px; background-color: #f8f9fa; border-radius: 5px; margin-bottom: 20px;">
                    @php
                        $statusMap = [
                            'cho_xac_nhan' => 'Chờ xác nhận đặt tour',
                            'da_xac_nhan' => 'Xác nhận đặt tour thành công',
                            'hoan_thanh' => 'Tour đã đi về thành công',
                            'da_huy' => 'Đã hủy đặt tour',
                        ];
                        $paymentStatusMap = [
                            'cho_xu_ly' => 'Chờ xử lý',
                            'thanh_cong' => 'Thanh toán thành công',
                            'that_bai' => 'Thanh toán thất bại',
                            'cho_hoan_tien' => 'Chờ hoàn tiền',
                            'da_hoan_tien' => 'Đã hoàn tiền',
                        ];
                        $isPaid = ($booking->trangthai_thanhtoan ?? null) === 'thanh_cong';
                        $cancelLabel = $isPaid ? 'Hủy Đơn Đặt Tour Này' : 'Hủy Thanh Toán';
                    @endphp

                    <p style="margin: 5px 0;"><strong>Trạng thái đặt tour:</strong> <span class="badge bg-info">{{ $statusMap[$booking->trangthai] ?? $booking->trangthai }}</span></p>
                    <p style="margin: 5px 0;"><strong>Trạng thái thanh toán:</strong> <span class="badge bg-secondary">{{ $paymentStatusMap[$booking->trangthai_thanhtoan] ?? 'Chưa thanh toán' }}</span></p>
                </div>

                <!-- KHU VỰC NÚT XỬ LÝ (THANH TOÁN / HỦY) -->
                @if ($booking->trangthai == 'da_huy')
                    <div class="alert alert-danger text-center">Đơn đặt tour này đã bị hủy.</div>
                @elseif ($booking->trangthai == 'hoan_thanh' || $booking->trangthai_thanhtoan == 'thanh_cong')
                    <a href="{{ route('tour-detail', ['id' => $booking->tourid]) }}" class="booking-btn mb-2" style="display: block; text-align: center; width: 100%; background-color: #ffc107; color: black;">
                        <i class="fas fa-star"></i> Xem & Viết Đánh Giá
                    </a>
                    <button type="button" onclick="showCancelModal()" class="booking-btn btn-cancel-booking" style="background-color: #dc3545; width: 100%; margin-top: 10px;">
                        <i class="fas fa-times"></i> Hủy Đơn Đặt Tour Này
                    </button>
                    <!-- Form ẩn để submit hủy -->
                    <form id="cancelTourForm" action="{{ route('cancelBooking') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="bookingId" value="{{ $booking->dtid }}">
                    </form>
                @else
                    <!-- Nếu chưa thanh toán và chưa hủy -->
                    @if($conHan)
                        <div class="alert alert-info text-center" style="font-size: 14px;">
                            <i class="fas fa-clock"></i> Đơn hàng sẽ bị hủy nếu không thanh toán trước <strong style="color: red;">{{ $hetHan->format('H:i:s - d/m/Y') }}</strong> (15 phút).
                        </div>

                        <!-- Các nút thanh toán online -->
                        @if ($booking->phuongthuc == 'vnpay')
                            <form action="{{ route('vnpay.payment') }}" method="POST" class="mb-2">
                                @csrf
                                <input type="hidden" name="dtid" value="{{ $booking->dtid }}">
                                <button type="submit" class="booking-btn" style="background-color: #1f8b4c; width: 100%;">
                                    <i class="fas fa-credit-card"></i> Thực Hiện Thanh Toán VNPAY
                                </button>
                            </form>
                        @elseif ($booking->phuongthuc == 'momo')
                            <form action="{{ route('createMomoPayment') ?? '#' }}" method="POST" class="mb-2">
                                @csrf
                                <input type="hidden" name="dtid" value="{{ $booking->dtid }}">
                                <button type="submit" class="booking-btn" style="background-color: #a50064; width: 100%; color: white;">
                                    <i class="fas fa-wallet"></i> Thực Hiện Thanh Toán MoMo
                                </button>
                            </form>
                        @endif
                    @else
                        <!-- Nếu quá hạn 15 phút -->
                        <div class="alert alert-danger text-center" style="font-size: 14px;">
                            <i class="fas fa-exclamation-triangle"></i> Đã quá hạn 15 phút thanh toán. Đơn hàng không còn hiệu lực giữ chỗ. Vui lòng hủy đơn và đặt lại.
                        </div>
                    @endif

                    <!-- Nút Hủy Tour gọi Popup Cảnh Báo -->
                    <button type="button" onclick="showCancelModal()" class="booking-btn btn-cancel-booking" style="background-color: #dc3545; width: 100%; margin-top: 10px;">
                        <i class="fas fa-times"></i> {{ $cancelLabel }}
                    </button>
                    
                    <!-- Form ẩn để submit hủy -->
                    <form id="cancelTourForm" action="{{ route('cancelBooking') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="bookingId" value="{{ $booking->dtid }}">
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- ================= POPUP CẢNH BÁO HỦY TOUR ================= -->
<div id="cancelWarningModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); overflow: auto;">
    <div style="background-color: #fff; margin: 10% auto; padding: 30px; border-radius: 10px; width: 90%; max-width: 500px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="color: #dc3545; text-align: center; margin-bottom: 20px;"><i class="fas fa-exclamation-triangle"></i> CẢNH BÁO HỦY TOUR</h3>
        
        <div style="font-size: 15px; color: #333; line-height: 1.6; margin-bottom: 25px;">
            <p>Bạn đang yêu cầu hủy đơn đặt tour <strong>#{{ $booking->dtid }}</strong>.</p>
            <p><strong>Xin lưu ý các điều khoản sau:</strong></p>
            <ul style="padding-left: 20px;">
                <li>Hành động hủy này <strong>không thể hoàn tác</strong>.</li>
                <li>Hệ thống sẽ ngay lập tức nhường chỗ của bạn cho khách hàng khác.</li>
                <li>Nếu bạn đã thanh toán 1 phần hoặc toàn bộ thông qua các kênh khác trước đó, vui lòng liên hệ nhân viên hỗ trợ để được hướng dẫn hoàn tiền theo quy định.</li>
            </ul>
        </div>

        <div style="display: flex; justify-content: space-between; gap: 15px;">
            <button onclick="hideCancelModal()" style="flex: 1; padding: 12px; background: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                Đóng / Không Hủy
            </button>
            <button onclick="confirmCancelTour()" style="flex: 1; padding: 12px; background: #dc3545; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                Đồng Ý Hủy Tour
            </button>
        </div>
    </div>
</div>

<script>
    function showCancelModal() {
        document.getElementById('cancelWarningModal').style.display = 'block';
    }

    function hideCancelModal() {
        document.getElementById('cancelWarningModal').style.display = 'none';
    }

    function confirmCancelTour() {
        // Submit form ẩn khi người dùng bấm đồng ý
        document.getElementById('cancelTourForm').submit();
    }
</script>
<!-- ================= END POPUP ================= -->

@include('clients.blocks.footer')