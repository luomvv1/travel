@include('clients.blocks.header')
@include('clients.blocks.banner')

<section class="container" style="margin-top:50px; margin-bottom: 100px">
    <form action="{{ route('booking', ['id' => $tour->tourId]) }}" method="post" class="booking-container" id="bookingForm">
        @csrf

        <input type="hidden" name="tourId" id="tourId" value="{{ $tour->tourId }}">

        <!-- Contact Information -->
        <div class="booking-info">
            <h2 class="booking-header">Thông Tin Liên Lạc</h2>
            <div class="booking__infor">
                <div class="form-group">
                    <label for="username">Họ và tên*</label>
                    <input type="text" id="username" placeholder="Nhập Họ và tên" name="fullName"
                        value="{{ $user->hoten ?? '' }}" readonly>
                    <span class="error-message" id="usernameError"></span>
                </div>

                <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="email" id="email" placeholder="sample@gmail.com" name="email"
                        value="{{ $user->email ?? '' }}" readonly>
                    <span class="error-message" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="tel">Số điện thoại*</label>
                    <input type="number" id="tel" placeholder="Nhập số điện thoại liên hệ" name="tel"
                        value="{{ $user->sodienthoai ?? '' }}" readonly>
                    <span class="error-message" id="telError"></span>
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ*</label>
                    <input type="text" id="address" placeholder="Nhập địa chỉ liên hệ" name="address"
                        value="{{ $user->diachi ?? '' }}" readonly>
                    <span class="error-message" id="addressError"></span>
                </div>
            </div>
            <p style="margin-top: 10px; font-size: 14px; color: #6c757d;">
                Thông tin lấy từ hồ sơ. Cập nhật tại <a href="{{ route('user-profile') }}">Thông tin cá nhân</a>.
            </p>

            <h2 class="booking-header">Lịch khởi hành</h2>
            <div class="form-group">
                <label for="lichid">Chọn lịch*</label>
                <select name="lichid" id="lichid" class="form-control" required>
                    <option value="">-- Chọn lịch khởi hành --</option>
                    @foreach(($tour->schedules ?? []) as $schedule)
                        <option value="{{ $schedule->lichid }}"
                            data-slots="{{ $schedule->sochocon }}"
                            @selected((string) ($selectedLichid ?? '') === (string) $schedule->lichid)>
                            {{ date('d/m/Y', strtotime($schedule->ngaybatdau)) }} - {{ date('d/m/Y', strtotime($schedule->ngayketthuc)) }}
                            (Còn {{ $schedule->sochocon }} chỗ)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Passenger Details -->
            <h2 class="booking-header">Hành Khách</h2>
            <div class="booking__quantity">
                <div class="form-group quantity-selector">
                    <label>Người lớn</label>
                    <div class="input__quanlity">
                        <button type="button" class="quantity-btn">-</button>
                        <input type="number" class="quantity-input" value="1" min="1" id="numAdults" name="numAdults" 
                               data-price-adults="{{ preg_replace('/[^0-9]/', '', $tour->gianguoilon ?? $tour->priceAdult ?? 0) }}" readonly>
                        <button type="button" class="quantity-btn">+</button>
                    </div>
                </div>

                <div class="form-group quantity-selector">
                    <label>Trẻ em</label>
                    <div class="input__quanlity">
                        <button type="button" class="quantity-btn">-</button>
                        <input type="number" class="quantity-input" value="0" min="0" id="numChildren" name="numChildren" 
                               data-price-children="{{ preg_replace('/[^0-9]/', '', $tour->giatreem ?? $tour->priceChild ?? 0) }}" readonly>
                        <button type="button" class="quantity-btn">+</button>
                    </div>
                </div>
            </div>

            <!-- Privacy Agreement Section (KHÔNG CHECKED SẴN) -->
            <div class="privacy-section">
                <p>Bằng cách nhấp chuột vào nút "ĐỒNG Ý" dưới đây, Khách hàng đồng ý rằng các điều kiện điều khoản
                    này sẽ được áp dụng. Vui lòng đọc kỹ điều kiện điều khoản trước khi lựa chọn sử dụng dịch vụ của Travela.</p>
                <div class="privacy-checkbox">
                    <!-- Đã bỏ 'checked' -->
                    <input type="checkbox" id="agree" name="agree" required>
                    <label for="agree">Tôi đã đọc và đồng ý với <a href="#" target="_blank">Điều khoản thanh toán</a></label>
                </div>
            </div>

            <!-- Payment Method (KHÔNG CÁI NÀO CHECKED SẴN) -->
            <h2 class="booking-header">Phương Thức Thanh Toán</h2>
            
            <label class="payment-option">
                <input type="radio" name="payment" value="tai_van_phong" required>
                <img src="{{ asset('clients/assets/images/contact/icon.png') }}" alt="Office Payment">
                Thanh toán tại văn phòng
            </label>

            <label class="payment-option">
                <input type="radio" name="payment" value="vnpay" required>
                <img src="{{ asset('clients/assets/images/booking/cong-thanh-toan-paypal.jpg') }}" alt="VNPAY">
                Chuyển khoản ngân hàng (VNPAY)
            </label>

            <!-- Đã thêm lại MoMo -->
            <label class="payment-option">
                <input type="radio" name="payment" value="momo" required>
                <img src="{{ asset('clients/assets/images/booking/thanh-toan-momo.jpg') }}" alt="MoMo">
                Thanh toán bằng MoMo
            </label>

            <input type="hidden" name="payment_hidden" id="payment_hidden">
        </div>

        <!-- Order Summary -->
        <div class="booking-summary">
            <div class="summary-section">
                <div>
                    <p>Mã tour : {{ $tour->tourId }}</p>
                    <h5 class="widget-title">{{ $tour->title }}</h5>
                    <p>Ngày khởi hành : {{ $tour->startDate ? date('d-m-Y', strtotime($tour->startDate)) : '-' }}</p>
                    <p>Ngày kết thúc : {{ $tour->endDate ? date('d-m-Y', strtotime($tour->endDate)) : '-' }}</p>
                    <p class="quantityAvailable">Số chỗ còn nhận : {{ $tour->quantity ?? $tour->sochocon ?? 50 }}</p>
                </div>

                <div class="order-summary">
                    <div class="summary-item">
                        <span>Người lớn:</span>
                        <div>
                            <span class="quantity__adults">1</span>
                            <span>X</span>
                            <span class="total-price">0 VNĐ</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span>Trẻ em:</span>
                        <div>
                            <span class="quantity__children">0</span>
                            <span>X</span>
                            <span class="total-price">0 VNĐ</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span>Giảm giá:</span>
                        <div>
                            <span class="total-price">0 VNĐ</span>
                        </div>
                    </div>
                    <div class="summary-item total-price">
                        <span>Tổng cộng:</span>
                        <span>0 VNĐ</span>
                        <input type="hidden" class="totalPrice" name="totalPrice" value="">
                    </div>
                </div>
                
                <div class="order-coupon">
                    <input type="text" name="promo" id="promo" placeholder="Mã giảm giá" style="width: 65%;">
                    <!-- Gắn thêm data-url-promo để gọi AJAX -->
                    <button type="button" style="width: 30%" class="booking-btn btn-coupon" id="applyPromo" data-url-promo="{{ route('check-promo') }}">Áp dụng</button>
                </div>

                <button type="submit" class="booking-btn btn-submit-booking">Xác Nhận</button>

            </div>
        </div>
    </form>
</section>

@include('clients.blocks.footer')
<script>
    // Áp dụng mã giảm giá (Kết nối Database)
    $(".btn-coupon").on("click", function (e) {
        e.preventDefault();
        const couponCode = $(".order-coupon input").val().trim();
        const urlCheckPromo = $(this).attr("data-url-promo"); // Lấy URL từ HTML

        if (couponCode === "") {
            toastr.warning("Vui lòng nhập mã giảm giá!");
            return;
        }

        // Tính tổng tiền vé trước khi áp dụng giảm giá
        const adultPrice = parseInt($("#numAdults").data("price-adults")) || 0;
        const childPrice = parseInt($("#numChildren").data("price-children")) || 0;
        const totalBeforeDiscount = (parseInt($("#numAdults").val()) * adultPrice) + 
                                    (parseInt($("#numChildren").val()) * childPrice);

        // Gọi AJAX về backend kiểm tra mã
        $.ajax({
            url: urlCheckPromo,
            method: "POST",
            data: {
                promo: couponCode,
                _token: $('input[name="_token"]').val()
            },
            success: function(response) {
                if (response.success) {
                    // Tính số tiền được giảm
                    if (response.loaigiam === 'phan_tram') {
                        discount = totalBeforeDiscount * (parseFloat(response.giatri) / 100);
                    } else {
                        discount = parseFloat(response.giatri);
                    }

                    // Đảm bảo tiền giảm không vượt quá tổng tiền vé
                    if (discount > totalBeforeDiscount) {
                        discount = totalBeforeDiscount;
                    }

                    toastr.success(response.message || "Áp dụng mã giảm giá thành công!");
                } else {
                    discount = 0;
                    toastr.error(response.message || "Mã giảm giá không hợp lệ!");
                }

                // Cập nhật lại giao diện
                $(".summary-item:nth-child(3) .total-price").text(discount.toLocaleString() + " VNĐ");
                updateSummary();
            },
            error: function() {
                toastr.error("Có lỗi xảy ra khi kiểm tra mã giảm giá.");
            }
        });
    });
</script>