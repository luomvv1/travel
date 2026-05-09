@include('clients.blocks.header')
@include('clients.blocks.banner')

<!-- Tour List Area start -->
<section class="tour-list-page py-100 rel z-1">
    <div class="container">
        <div class="row">
            <!-- CỘT SIDEBAR: PHỔ BIẾN TOURS -->
            <div class="col-lg-3 col-md-6 col-sm-10 rmb-75">
                <div class="shop-sidebar mb-30">
                    @if (isset($toursPopular) && !$toursPopular->isEmpty())
                        <div class="widget widget-tour" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                            <h6 class="widget-title">Phổ biến Tours</h6>
                            
                            @foreach ($toursPopular as $tour)
                                <div class="destination-item tour-grid style-three bgc-lighter">
                                    <div class="image">
                                        @if(!empty($tour->images) && isset($tour->images[0]))
                                            <img src="{{ asset('admin/assets/images/gallery-tours/' . $tour->images[0]) }}" alt="Tour">
                                        @else
                                            <img src="{{ asset('admin/assets/images/gallery-tours/default-image.jpg') }}" alt="Tour mặc định">
                                        @endif
                                    </div>
                                    <div class="content">
                                        <div class="destination-header">
                                            <span class="location"><i class="fal fa-map-marker-alt"></i>
                                                {{ $tour->diadiemden ?? 'Đang cập nhật' }}</span>
                                            <div class="ratting">
                                                <i class="fas fa-star"></i>
                                                <span>{{ $tour->sosao ?? 0 }}</span>
                                            </div>
                                        </div>
                                        <h6>
                                            <a href="{{ route('tour-detail', ['id' => $tour->tourid]) }}">
                                                {{ $tour->tentour ?? 'Tên tour đang cập nhật' }}
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- CỘT MAIN: DANH SÁCH TOUR ĐÃ ĐẶT -->
            <div class="col-lg-9">
                @if(isset($myTours) && !$myTours->isEmpty())
                    @foreach ($myTours as $tour)
                        <div class="destination-item style-three bgc-lighter" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
                            <!-- Hình ảnh và Trạng thái -->
                            <div class="image">
                                <!-- Trạng thái booking từ bảng dattour -->
                                @php
                                    $bookingStatusMap = [
                                        'cho_xac_nhan' => ['label' => 'Chờ xác nhận đặt tour', 'class' => 'badge bg-warning text-dark'],
                                        'da_xac_nhan' => ['label' => 'Xác nhận đặt tour thành công', 'class' => 'badge bgc-pink'],
                                        'hoan_thanh' => ['label' => 'Tour đã đi về thành công', 'class' => 'badge bgc-primary'],
                                        'da_huy' => ['label' => 'Đã hủy', 'class' => 'badge bg-danger'],
                                    ];
                                    $paymentStatusMap = [
                                        'cho_xu_ly' => ['label' => 'Chờ xử lý', 'class' => 'badge bg-info text-dark'],
                                        'thanh_cong' => ['label' => 'Đã thanh toán', 'class' => 'badge bg-success'],
                                        'that_bai' => ['label' => 'Thanh toán thất bại', 'class' => 'badge bg-danger'],
                                        'cho_hoan_tien' => ['label' => 'Chờ hoàn tiền', 'class' => 'badge bg-warning text-dark'],
                                        'da_hoan_tien' => ['label' => 'Đã hoàn tiền', 'class' => 'badge bg-success'],
                                    ];
                                @endphp
                                @if (isset($tour->booking_status))
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 8px;">
                                        <span class="{{ $bookingStatusMap[$tour->booking_status]['class'] ?? 'badge bg-secondary' }}">
                                            {{ $bookingStatusMap[$tour->booking_status]['label'] ?? $tour->booking_status }}
                                        </span>
                                        @if (isset($tour->payment_status))
                                            <span class="{{ $paymentStatusMap[$tour->payment_status]['class'] ?? 'badge bg-secondary' }}">
                                                {{ $paymentStatusMap[$tour->payment_status]['label'] ?? $tour->payment_status }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                @if(!empty($tour->images) && isset($tour->images[0]))
                                    <img src="{{ asset('admin/assets/images/gallery-tours/' . $tour->images[0]) }}" alt="Hình ảnh tour">
                                @else
                                    <img src="{{ asset('admin/assets/images/gallery-tours/default-image.jpg') }}" alt="Chưa có hình ảnh">
                                @endif
                            </div>
                            
                            <!-- Nội dung Tour -->
                            <div class="content">
                                <div class="destination-header">
                                    <div class="ratting">
                                        @for ($i = 0; $i < 5; $i++)
                                            @if (isset($tour->sosao) && $i < $tour->sosao)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                
                                <h5>
                                    <!-- ĐÃ SỬA: Dẫn link vào trang chi tiết đơn đặt (tour-booked) và truyền mã đơn (dtid) -->
                                    <a href="{{ route('tour-booked', ['id' => $tour->dtid]) }}">
                                        {{ $tour->tentour ?? 'Tên tour' }}
                                    </a>
                                </h5>
                                
                                <div class="truncate-3-lines">
                                    {!! $tour->mota ?? 'Chưa có mô tả chi tiết cho tour này.' !!}
                                </div>

                                <ul class="blog-meta">
                                    <li><i class="far fa-calendar"></i> {{ $tour->ngaybatdau ? date('d-m-Y', strtotime($tour->ngaybatdau)) : 'Chưa xác định' }}</li>
                                    <li><i class="far fa-user"></i> {{ ($tour->songuoilon ?? 0) + ($tour->sotreem ?? 0) }} người</li>
                                </ul>
                                
                                <div class="destination-footer">
                                    <!-- Giá cuối từ dattour -->
                                    <span class="price"><span>{{ number_format($tour->giacuoi ?? 0, 0, ',', '.') }}</span> VNĐ</span>
                                    
                                    <!-- ĐÃ SỬA: Nút "Chi Tiết Đơn" luôn hiện để khách bấm vào xử lý thanh toán/hủy/đánh giá -->
                                    <a href="{{ route('tour-booked', ['id' => $tour->dtid]) }}" class="theme-btn style-two style-three">
                                        <span data-hover="Chi Tiết Đơn">Chi Tiết Đơn</span>
                                        <i class="fal fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- THANH PHÂN TRANG -->
                    <div class="pagination-wrapper mt-4 d-flex justify-content-center">
                        {{ $myTours->links('pagination::bootstrap-4') }}
                    </div>

                @else
                    <div class="alert alert-info text-center">
                        Bạn chưa đặt tour nào. Hãy khám phá các tour hấp dẫn của chúng tôi nhé!
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- Tour List Area end -->

@include('clients.blocks.footer')