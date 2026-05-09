@foreach ($list_booking as $booking)
    <tr>
        <td>{{ $booking->title }}</td>
        <td>{{ $booking->fullName }}</td>
        <td>{{ $booking->email }}</td>
        <td>{{ $booking->phoneNumber }}</td>
        <td>{{ $booking->address ?? 'Chưa cập nhật' }}</td>
        <td>{{ date('d-m-Y', strtotime($booking->bookingDate)) }}</td>
        <td>{{ $booking->numAdults }}</td>
        <td>{{ $booking->numChildren }}</td>
        <td>{{ number_format($booking->totalPrice, 0, ',', '.') }} đ</td>
        <td>
            @if ($booking->bookingStatus == 'da_huy')
                <span class="badge badge-danger">Đã hủy</span>
            @elseif ($booking->bookingStatus == 'cho_xac_nhan')
                <span class="badge badge-warning">Chưa xác nhận</span>
            @elseif ($booking->bookingStatus == 'da_xac_nhan')
                <span class="badge badge-primary">Đã xác nhận</span>
            @elseif ($booking->bookingStatus == 'hoan_thanh')
                <span class="badge badge-success">Đã hoàn thành</span>
            @endif
        </td>
        <td class="text-center">
            @if ($booking->paymentMethod == 'momo')
                <img src="{{ asset('admin/assets/images/icon/icon_momo.png') }}" class="icon_payment" alt="MoMo" style="height:30px">
            @elseif ($booking->paymentMethod == 'vnpay')
                <span class="badge badge-info" style="font-size: 14px;">VNPay</span>
            @elseif ($booking->paymentMethod == 'paypal')
                <img src="{{ asset('admin/assets/images/icon/icon_paypal.png') }}" class="icon_payment" alt="PayPal" style="height:30px">
            @else
                <img src="{{ asset('admin/assets/images/icon/icon_office.png') }}" class="icon_payment" alt="Văn phòng" style="height:30px" title="Tại văn phòng">
            @endif
        </td>

        <td>
            @if ($booking->paymentStatus == 'thanh_cong')
                <span class="badge badge-success">Đã thanh toán</span>
            @elseif ($booking->paymentStatus == 'that_bai')
                <span class="badge badge-danger">Thất bại</span>
             @elseif ($booking->paymentStatus == 'cho_hoan_tien')
                <span class="badge badge-warning">Chờ hoàn tiền</span>
             @elseif ($booking->paymentStatus == 'da_hoan_tien')
                 <span class="badge badge-success">Đã hoàn tiền</span>
            @else
                <span class="badge badge-warning">Chưa thanh toán</span>
            @endif
        </td>

        <td>
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Hành động 
                </button>
                <div class="dropdown-menu" x-placement="bottom-start">
                    
                    @if ($booking->bookingStatus == 'cho_xac_nhan')
                        <form action="{{ route('admin.confirm-booking') }}" method="POST" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="bookingId" value="{{ $booking->bookingId }}">
                            <button type="submit" class="dropdown-item" style="cursor: pointer; border: none; background: none; width: 100%; text-align: left; padding: .25rem 1.5rem;">Xác nhận</button>
                        </form>
                    @endif

                    @if ($booking->bookingStatus != 'hoan_thanh' && $booking->bookingStatus != 'da_huy' && $booking->hide == '')
                        <form action="{{ route('admin.finish-booking') }}" method="POST" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="bookingId" value="{{ $booking->bookingId }}">
                            <button type="submit" class="dropdown-item" style="cursor: pointer; border: none; background: none; width: 100%; text-align: left; padding: .25rem 1.5rem;">Đã hoàn thành tour</button>
                        </form>
                    @endif

                    <a class="dropdown-item" href="{{ route('admin.booking-detail',['id' => $booking->bookingId]) }}">Xem chi tiết</a>
                </div>
            </div>
        </td>
    </tr>
@endforeach