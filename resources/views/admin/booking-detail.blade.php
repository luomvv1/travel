@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Hóa đơn <small>đặt tour du lịch</small></h3>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="x_panel">
                            
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="invoice_booking">
                                <div class="x_title">
                                    <h2>Hóa đơn chi tiết</h2>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <section class="content invoice">
                                        <div class="row">
                                            <div class="invoice-header">
                                                <h3>
                                                    <i class="fa fa-globe"></i>
                                                    {{ $invoice_booking->tentour }}
                                                    <small class="pull-right">Ngày đặt:
                                                        {{ date('d-m-Y H:i', strtotime($invoice_booking->ngaydat)) }}
                                                    </small>
                                                </h3>
                                            </div>
                                        </div>
                                        
                                        <div class="row invoice-info">
                                            <div class="col-sm-4 invoice-col">
                                                Từ (Khách hàng)
                                                <address>
                                                    <strong>{{ $invoice_booking->fullName }}</strong>
                                                    <br>Địa chỉ: {{ $invoice_booking->diachi ?? 'Chưa cập nhật' }}
                                                    <br>SĐT: {{ $invoice_booking->sodienthoai ?? 'Chưa cập nhật' }}
                                                    <br>Email: {{ $invoice_booking->email }}
                                                </address>
                                            </div>
                                            <div class="col-sm-4 invoice-col">
                                                Đến
                                                <address>
                                                    <strong>Công ty Travela</strong>
                                                    <br>470 Trần Đại Nghĩa
                                                    <br>Ngũ Hành Sơn, Đà Nẵng
                                                    <br>Phone: 0987 654 321
                                                    <br>Email: support@travela.vn
                                                </address>
                                            </div>
                                            <div class="col-sm-4 invoice-col">
                                                <b>Mã hóa đơn #{{ $invoice_booking->bookingId }}</b><br><br>
                                                <b>Mã giao dịch:</b> {{ $invoice_booking->transactionId }}<br>
                                                
                                                <b>Ngày thanh toán / hoàn tiền:</b> 
                                                @if($invoice_booking->ngaythanhtoan)
                                                    <span class="text-success fw-bold">{{ date('d-m-Y H:i:s', strtotime($invoice_booking->ngaythanhtoan)) }}</span>
                                                @else
                                                    <span class="text-danger">Chưa cập nhật</span>
                                                @endif
                                                <br>
                                                <b>Trạng thái Tour:</b>
                                                @if ($invoice_booking->trangthai == 'da_huy')
                                                    <span class="badge badge-danger">Đã hủy</span>
                                                @elseif ($invoice_booking->trangthai == 'cho_xac_nhan')
                                                    <span class="badge badge-warning">Chờ xác nhận</span>
                                                @elseif ($invoice_booking->trangthai == 'da_xac_nhan')
                                                    <span class="badge badge-primary">Đã xác nhận</span>
                                                @elseif ($invoice_booking->trangthai == 'hoan_thanh')
                                                    <span class="badge badge-success">Đã hoàn thành</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="table">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Đối tượng</th>
                                                            <th>Số lượng</th>
                                                            <th>Đơn giá</th>
                                                            <th>Điểm đến</th>
                                                            <th>Thành tiền</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Người lớn</td>
                                                            <td>{{ $invoice_booking->songuoilon }}</td>
                                                            <td>{{ number_format($invoice_booking->gianguoilon, 0, ',', '.') }} đ</td>
                                                            <td>{{ $invoice_booking->diadiemden }}</td>
                                                            <td>{{ number_format($invoice_booking->gianguoilon * $invoice_booking->songuoilon, 0, ',', '.') }} đ</td>
                                                        </tr>
                                                        @if($invoice_booking->sotreem > 0)
                                                        <tr>
                                                            <td>Trẻ em</td>
                                                            <td>{{ $invoice_booking->sotreem }}</td>
                                                            <td>{{ number_format($invoice_booking->giatreem, 0, ',', '.') }} đ</td>
                                                            <td>{{ $invoice_booking->diadiemden }}</td>
                                                            <td>{{ number_format($invoice_booking->giatreem * $invoice_booking->sotreem, 0, ',', '.') }} đ</td>
                                                        </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="lead">Phương thức thanh toán:</p>
                                                @if ($invoice_booking->phuongthuc == 'momo')
                                                    <img src="{{ asset('admin/assets/images/icon/icon_momo.png') }}" class="invoice_payment-method" style="height:40px;" alt="MoMo">
                                                @elseif ($invoice_booking->phuongthuc == 'vnpay')
                                                    <span class="badge badge-info" style="font-size: 16px;">VNPay</span>
                                                @elseif ($invoice_booking->phuongthuc == 'paypal')
                                                    <img src="{{ asset('admin/assets/images/icon/icon_paypal.png') }}" class="invoice_payment-method" style="height:40px;" alt="PayPal">
                                                @elseif ($invoice_booking->phuongthuc == 'tai_van_phong')
                                                    <img src="{{ asset('admin/assets/images/icon/icon_office.png') }}" style="height:35px;" alt="Tại văn phòng">
                                                    <span class="badge badge-secondary" style="font-size: 14px; margin-left:10px;">Thanh toán tại văn phòng</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <p class="lead">Chi tiết hóa đơn</p>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <th style="width:50%">Tổng phụ:</th>
                                                                <td>{{ number_format($invoice_booking->tongtien, 0, ',', '.') }} đ</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Khuyến mãi áp dụng:</th>
                                                                <td>{{ $invoice_booking->makhuyenmai ?? 'Không có' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Được giảm giá:</th>
                                                                <td>- {{ number_format($invoice_booking->giamgia, 0, ',', '.') }} đ</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Tổng phải thu:</th>
                                                                <td><strong style="color: red; font-size: 20px;">{{ number_format($invoice_booking->giacuoi, 0, ',', '.') }} đ</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Trạng thái thanh toán:</th>
                                                                <td>
                                                                    @if($invoice_booking->paymentStatus == 'thanh_cong')
                                                                        <span class="badge badge-success" style="font-size: 14px;">Đã thanh toán</span>
                                                                    @elseif($invoice_booking->paymentStatus == 'cho_xu_ly')
                                                                        <span class="badge badge-warning" style="font-size: 14px;">Chờ xử lý</span>
                                                                    @elseif($invoice_booking->paymentStatus == 'cho_hoan_tien')
                                                                        <span class="badge" style="font-size: 14px; background-color: #f39c12; color: #fff;">Chờ hoàn tiền</span>
                                                                    @elseif($invoice_booking->paymentStatus == 'da_hoan_tien')
                                                                        <span class="badge" style="font-size: 14px; background-color: #17a2b8; color: #fff;">Đã hoàn tiền</span>
                                                                    @elseif($invoice_booking->paymentStatus == 'that_bai')
                                                                        <span class="badge badge-danger" style="font-size: 14px;">Thất bại</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                            
                            <div class="row no-print" style="padding: 15px;">
                                <div class="col-md-12">
                                    <button class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> In hóa đơn</button>
                                    
                                    <form action="{{ route('admin.booking-send-pdf') }}" method="POST" style="display: inline-block; float: right; margin-left: 5px;">
                                        @csrf
                                        <input type="hidden" name="bookingId" value="{{ $invoice_booking->bookingId }}">
                                        <input type="hidden" name="email" value="{{ $invoice_booking->email }}">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Gửi email hóa đơn</button>
                                    </form>

                                    @if ($invoice_booking->trangthai == 'cho_xac_nhan' && $invoice_booking->trangthai != 'da_huy')
                                        <form action="{{ route('admin.confirm-booking') }}" method="POST" style="display: inline-block; float: right; margin-left: 5px;">
                                            @csrf
                                            <input type="hidden" name="bookingId" value="{{ $invoice_booking->bookingId }}">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Duyệt Tour</button>
                                        </form>
                                    @endif

                                    @if($invoice_booking->paymentStatus == 'cho_xu_ly' && $invoice_booking->trangthai != 'da_huy')
                                        <div style="display: inline-block; float: right; margin-left: 5px;">
                                            <form action="{{ route('admin.booking-received-money') }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="bookingId" value="{{ $invoice_booking->bookingId }}">
                                                <button type="submit" class="btn btn-info" onclick="return confirm('Xác nhận đã thu tiền mặt từ khách hàng này?');">
                                                    <i class="fa fa-money"></i> Xác nhận Đã thu tiền
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    @if($invoice_booking->paymentStatus == 'cho_hoan_tien')
                                        <div style="display: inline-block; float: right; margin-left: 5px;">
                                            <form action="{{ route('admin.booking-refund') }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="bookingId" value="{{ $invoice_booking->bookingId }}">
                                                <button type="submit" class="btn btn-warning" onclick="return confirm('Bạn có chắc chắn ĐÃ CHUYỂN KHOẢN hoàn tiền cho khách hàng này?');">
                                                    <i class="fa fa-undo"></i> Xác nhận Đã hoàn tiền
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.blocks.footer')