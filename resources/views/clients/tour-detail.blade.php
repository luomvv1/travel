@include('clients.blocks.header')

<style>
    .tour-hero-wrap {
        max-width: 1220px;
        margin: 0 auto;
    }

    .tour-hero-head {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 18px;
        align-items: end;
    }

    .tour-route-line {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        background: #f4f6fb;
        border: 1px solid #e7ebf3;
        border-radius: 999px;
        padding: 10px 16px;
        color: #2b3342;
        font-weight: 600;
    }

    .tour-route-sep {
        opacity: .45;
    }

    .tour-gallery-shell {
        margin-top: 12px;
        max-width: 1120px;
        margin-left: auto;
        margin-right: auto;
    }

    .tour-gallery-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .tour-gallery-toolbar .hint {
        color: #6b7280;
        font-size: 14px;
    }

    .tour-gallery-counter {
        font-weight: 700;
        color: #111827;
        background: #eef2f7;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 13px;
    }

    .tour-gallery-slider {
        position: relative;
    }

    .tour-gallery-viewport {
        width: 100%;
        overflow: hidden;
        border-radius: 30px;
        background: #0d1117;
        box-shadow: 0 26px 58px rgba(16, 24, 40, 0.22);
        height: clamp(320px, 56vw, 520px);
        min-height: 320px;
    }

    .tour-gallery-track {
        display: flex;
        height: 100%;
        transition: transform .5s cubic-bezier(.22, .61, .36, 1);
        will-change: transform;
    }

    .tour-gallery-card {
        position: relative;
        flex: 0 0 100%;
        height: 100%;
        overflow: hidden;
        background: #111;
    }

    .tour-gallery-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.14) 0%, rgba(0, 0, 0, 0) 42%);
        pointer-events: none;
    }

    .tour-gallery-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        image-rendering: auto;
        filter: contrast(1.04) saturate(1.03);
        transition: transform .45s ease;
    }

    .tour-gallery-card:hover img {
        transform: scale(1.015);
    }

    .tour-gallery-overlay {
        position: absolute;
        inset: auto 0 0 0;
        padding: 32px 28px 24px;
        color: #fff;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 12%, rgba(0, 0, 0, 0.7) 100%);
        z-index: 1;
    }

    .tour-gallery-overlay p {
        margin-bottom: 0;
        max-width: 75%;
        font-size: 17px;
        line-height: 1.55;
        font-weight: 600;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.45);
    }

    .tour-gallery-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        width: 52px;
        height: 52px;
        border: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        color: #1f2937;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.24);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .22s ease;
    }

    .tour-gallery-nav[data-gallery-prev] {
        left: 16px;
    }

    .tour-gallery-nav[data-gallery-next] {
        right: 16px;
    }

    .tour-gallery-nav:hover {
        background: #fff;
        transform: translateY(-50%) scale(1.04);
    }

    .tour-gallery-nav:disabled {
        opacity: .38;
        cursor: not-allowed;
        transform: translateY(-50%);
    }

    .tour-gallery-indicators {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 16px;
    }

    .tour-gallery-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        border: 0;
        background: rgba(17, 24, 39, 0.2);
        transition: all .25s ease;
    }

    .tour-gallery-dot.active {
        width: 30px;
        background: #111827;
    }

    @media (max-width: 991px) {
        .tour-hero-head {
            grid-template-columns: 1fr;
            align-items: start;
        }

        .tour-gallery-viewport {
            height: clamp(280px, 66vw, 440px);
            min-height: 280px;
        }

        .tour-gallery-overlay p {
            max-width: 100%;
            font-size: 16px;
        }
    }

    @media (max-width: 575px) {
        .tour-gallery-nav {
            width: 42px;
            height: 42px;
        }

        .tour-gallery-nav[data-gallery-prev] {
            left: 10px;
        }

        .tour-gallery-nav[data-gallery-next] {
            right: 10px;
        }

        .tour-gallery-overlay {
            padding: 22px 16px 16px;
        }
    }

</style>

<!-- Tour Banner -->
<section class="page-banner-two rel z-1">
    <div class="container-fluid">
        <hr class="mt-0">
        <div class="container tour-hero-wrap">
            <div class="banner-inner pt-20 pb-18">
                <div class="tour-hero-head">
                    <div>
                        <h1 class="page-title mb-10 aos-init aos-animate" data-aos="fade-left" data-aos-duration="1500"
                            data-aos-offset="50">{{ $tourDetail->title }}</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-10 aos-init aos-animate" data-aos="fade-right"
                                data-aos-delay="200" data-aos-duration="1500" data-aos-offset="50">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item active">{{ $title }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="tour-route-line">
                        <i class="fal fa-map-marker-alt"></i>
                        <span>Xuất phát: {{ $tourDetail->departure ?? 'Đang cập nhật' }}</span>
                        <span class="tour-route-sep">|</span>
                        <span>Điểm đến: {{ $tourDetail->destination ?? 'Đang cập nhật' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Tour Gallery start -->
<div class="tour-gallery">

    <div class="container tour-hero-wrap">
        
        @if (count($tourDetail->galleryImages) > 0)
            <div class="tour-gallery-shell" data-gallery-slider>
                <div class="tour-gallery-toolbar">
                    <div class="hint">Bộ sưu tập hình ảnh</div>
                    <div class="tour-gallery-counter" data-gallery-counter>1 / {{ count($tourDetail->galleryImages) }}</div>
                </div>

                <div class="tour-gallery-slider">
                    <div class="tour-gallery-viewport">
                        <div class="tour-gallery-track" data-gallery-track>
                            @foreach ($tourDetail->galleryImages as $image)
                                <div class="tour-gallery-card">
                                    <img src="{{ asset('admin/assets/images/gallery-tours/' . $image->urlanh) }}" alt="{{ $tourDetail->title }}">
                                    <div class="tour-gallery-overlay">
                                        <p>{{ $image->motaanh ?? ($tourDetail->departure . ' → ' . $tourDetail->destination) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" class="tour-gallery-nav" data-gallery-prev aria-label="Ảnh trước">
                        <i class="fal fa-chevron-left"></i>
                    </button>
                    <button type="button" class="tour-gallery-nav" data-gallery-next aria-label="Ảnh tiếp theo">
                        <i class="fal fa-chevron-right"></i>
                    </button>
                </div>

                <div class="tour-gallery-indicators" data-gallery-dots></div>
            </div>
        @endif
    </div>
</div>
<!-- Tour Gallery End -->

<!-- Tour Header Area start -->
<section class="tour-header-area pt-70 rel z-1">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-xl-6 col-lg-7">
                <div class="tour-header-content mb-15" data-aos="fade-left" data-aos-duration="1500"
                    data-aos-offset="50">
                    <div class="section-title pb-5">
                        <h2>{{ $tourDetail->title }}</h2>
                    </div>
                    <div class="ratting">
                        @for ($i = 0; $i < 5; $i++)
                            @if ($avgStar && $i < $avgStar)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor

                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 text-lg-end" data-aos="fade-right" data-aos-duration="1500"
                data-aos-offset="50">
                <div class="tour-header-social mb-10">
                    <a href="#"><i class="far fa-share-alt"></i>Share tours</a>
                    <a href="#"><i class="fas fa-heart bgc-secondary"></i>Wish list</a>
                </div>
            </div>
        </div>
        <hr class="mt-50 mb-70">
    </div>
</section>
<!-- Tour Header Area end -->

<!-- Tour Details Area start -->
<section class="tour-details-page pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="tour-details-content">
                    <h3>Khám phá Tours</h3>
                    <p>{!! $tourDetail->description !!} </p>
                    <div class="row pb-55">
                        <div class="col-md-6">
                            <div class="tour-include-exclude mt-30">
                                <h5>Bao gồm và không bao gồm</h5>
                                <ul class="list-style-one check mt-25">
                                    <li><i class="far fa-check"></i> Dịch vụ đón và trả khách</li>
                                    <li><i class="far fa-check"></i> 1 bữa ăn mỗi ngày</li>
                                    <li><i class="far fa-check"></i> Bữa tối trên du thuyền & Sự kiện âm nhạc</li>
                                    <li><i class="far fa-check"></i> Tham quan 7 địa điểm tuyệt vời nhất trong thành phố
                                    </li>
                                    <li><i class="far fa-check"></i> Nước đóng chai trên xe buýt</li>
                                    <li><i class="far fa-check"></i> Phương tiện di chuyển Xe buýt du lịch hạng sang
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="tour-include-exclude mt-30">
                                <h5>Không bao gồm</h5>
                                <ul class="list-style-one mt-25">
                                    <li><i class="far fa-times"></i> Tiền boa</li>
                                    <li><i class="far fa-times"></i> Đón và trả khách tại khách sạn</li>
                                    <li><i class="far fa-times"></i> Bữa trưa, Đồ ăn & Đồ uống</li>
                                    <li><i class="far fa-times"></i> Nâng cấp tùy chọn lên một ly</li>
                                    <li><i class="far fa-times"></i> Dịch vụ bổ sung</li>
                                    <li><i class="far fa-times"></i> Bảo hiểm</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <h3>Lịch trình</h3>
                <div class="accordion-two mt-25 mb-60" id="faq-accordion-two">
                    @php
                        $day = 1;
                    @endphp
                    @foreach ($tourDetail->timeline as $timeline)
                        <div class="accordion-item">
                            <h5 class="accordion-header">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo{{ $timeline->ltid }}">
                                    Ngày {{ $day++ }} - {{ $timeline->tieude }}
                                </button>
                            </h5>
                            <div id="collapseTwo{{ $timeline->ltid }}" class="accordion-collapse collapse"
                                data-bs-parent="#faq-accordion-two">
                                <div class="accordion-body">
                                    <p>{!! $timeline->noidung !!}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="partials_reviews">
                    @include('clients.partials.reviews')
                </div>

                <h3 class="{{ $checkDisplay }}">Thêm Đánh giá</h3>
                <form id="comment-form" class="comment-form bgc-lighter z-1 rel mt-30 {{ $checkDisplay }}"
                    name="review-form" action="{{ route('reviews') }}" method="post" data-aos="fade-up"
                    data-aos-duration="1500" data-aos-offset="50">
                    @csrf
                    <div class="comment-review-wrap">
                        <div class="comment-ratting-item">
                            <span class="title">Đánh giá</span>
                            <div class="ratting" id="rating-stars">
                                <i class="far fa-star" data-value="1"></i>
                                <i class="far fa-star" data-value="2"></i>
                                <i class="far fa-star" data-value="3"></i>
                                <i class="far fa-star" data-value="4"></i>
                                <i class="far fa-star" data-value="5"></i>
                            </div>
                        </div>

                    </div>
                    <hr class="mt-30 mb-40">
                    <h5>Để lại phản hồi</h5>
                    <div class="row gap-20 mt-20">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message">Nội dung</label>
                                <textarea name="message" id="message" class="form-control" rows="5" required=""></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <button type="submit" class="theme-btn bgc-secondary style-two" id="submit-reviews"
                                    data-url-checkBooking="{{ route('checkBooking') }}"
                                    data-tourId-reviews="{{ $tourDetail->tourId }}">
                                    <span data-hover="Gửi đánh giá">Gửi đánh giá</span>
                                    <i class="fal fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-lg-4 col-md-8 col-sm-10 rmt-75">
                <div class="blog-sidebar tour-sidebar">

                    <div class="widget widget-booking" data-aos="fade-up" data-aos-duration="1500"
                        data-aos-offset="50">
                        <h5 class="widget-title">Tour Booking</h5>
                       <form action="{{ route('booking-form', ['id' => $tourDetail->tourId]) }}" method="GET">
                            @csrf
                            <div class="date mb-25">
                                <b>Chọn lịch khởi hành:</b>
                                @if(!empty($tourDetail->schedules) && count($tourDetail->schedules) > 0)
                                    <!-- Khung chọn dạng Dropdown (Select box) đã được fix ép hiển thị -->
                                    <div class="schedule-options mt-15 mb-20">
                                        <select name="lichid" id="schedule-select" required 
                                            style="display: block !important; opacity: 1 !important; visibility: visible !important; width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 6px; background-color: #fff; appearance: auto !important; -webkit-appearance: menulist !important; height: auto !important; cursor: pointer; color: #333;">
                                            <option value="" disabled selected>--- Kích vào đây để chọn ngày ---</option>
                                            @foreach($tourDetail->schedules as $schedule)
                                                <option value="{{ $schedule->lichid }}"
                                                    data-start="{{ date('d/m/Y', strtotime($schedule->ngaybatdau)) }}"
                                                    data-end="{{ date('d/m/Y', strtotime($schedule->ngayketthuc)) }}"
                                                    data-slots="{{ $schedule->sochocon }}">
                                                    {{ date('d/m/Y', strtotime($schedule->ngaybatdau)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Vùng hiển thị thông tin chi tiết (Mặc định ẩn) -->
                                    <div id="selected-schedule-info" class="p-3 rounded" style="display: none; border: 1px dashed #f7921e; background-color: #fff5eb;">
                                        <!-- JS sẽ đổ nội dung vào đây -->
                                    </div>
                                @else
                                    <p class="text-muted mt-10">Hiện không có lịch khởi hành nào cho tour này.</p>
                                @endif
                            </div>
    
                            <hr>
                            <div class="time py-5">
                                <b>Thời gian :</b>
                                <p>{{ $tourDetail->time }}</p>
                            </div>
                            <hr class="mb-25">
                            <h6>Vé:</h6>
                            <ul class="tickets clearfix">
                                <li>
                                    Người lớn <span
                                        class="price">{{ number_format($tourDetail->priceAdult, 0, ',', '.') }} VND
                                    </span>
                                </li>
                                <li>
                                    Trẻ em <span
                                        class="price">{{ number_format($tourDetail->priceChild, 0, ',', '.') }} VND
                                    </span>
                                </li>
                            </ul>
                            <button type="submit" class="theme-btn style-two w-100 mt-15 mb-5">
                                <span data-hover="Đặt ngay">Đặt ngay</span>
                                <i class="fal fa-arrow-right"></i>
                            </button>
                            <div class="text-center">
                                <a href="{{ route('contact') }}">Bạn cần trợ giúp không?</a>
                            </div>
                        </form>
                    </div>

                    <div class="widget widget-contact" data-aos="fade-up" data-aos-duration="1500"
                        data-aos-offset="50">
                        <h5 class="widget-title">Cần trợ giúp?</h5>
                        <ul class="list-style-one">
                            <li><i class="far fa-envelope"></i> <a
                                    href="mailto:luomvo0510@gmail.com">luomvo0510@gmail.com</a></li>
                            <li><i class="far fa-phone-volume"></i> <a href="callto:+84365531198">+84 365 531 198</a></li>
                        </ul>
                    </div>
                    
                    @if (!empty($tourRecommendations))
                        <div class="widget widget-tour" data-aos="fade-up" data-aos-duration="1500"
                            data-aos-offset="50">
                            <h6 class="widget-title">Tours tương tự</h6>
                            @foreach ($tourRecommendations as $tour)
                                <div class="destination-item tour-grid style-three bgc-lighter">
                                    <div class="image">
                                        <img src="{{ asset('admin/assets/images/gallery-tours/' . $tour->images[0]) }}"
                                            alt="Tour" style="max-height: 137px">
                                    </div>
                                    <div class="content">
                                        <div class="destination-header">
                                            <span class="location"><i class="fal fa-map-marker-alt"></i>
                                                {{ $tour->destination }}</span>
                                            <div class="ratting">
                                                <i class="fas fa-star"></i>
                                                <span>({{ $tour->rating }})</span>
                                            </div>
                                        </div>
                                        <h6><a href="{{ route('tour-detail', ['id' => $tour->tourId]) }}">{{ $tour->title }}</a></h6>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</section>

@include('clients.blocks.new_letter')
@include('clients.blocks.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slider = document.querySelector('[data-gallery-slider]');
        if (slider) {
            const track = slider.querySelector('[data-gallery-track]');
            const slides = Array.from(slider.querySelectorAll('.tour-gallery-card'));
            const prevButton = slider.querySelector('[data-gallery-prev]');
            const nextButton = slider.querySelector('[data-gallery-next]');
            const dotsWrap = slider.querySelector('[data-gallery-dots]');
            const counter = slider.querySelector('[data-gallery-counter]');
            let index = 0;

            const dots = slides.map((_, slideIndex) => {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'tour-gallery-dot';
                dot.setAttribute('aria-label', `Đi đến ảnh ${slideIndex + 1}`);
                dot.addEventListener('click', () => goTo(slideIndex));
                dotsWrap.appendChild(dot);
                return dot;
            });

            function updateSlider() {
                track.style.transform = `translateX(-${index * 100}%)`;
                prevButton.disabled = index === 0;
                nextButton.disabled = index === slides.length - 1;
                dots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === index));
                if (counter) {
                    counter.textContent = `${index + 1} / ${slides.length}`;
                }
            }

            function goTo(nextIndex) {
                index = Math.max(0, Math.min(nextIndex, slides.length - 1));
                updateSlider();
            }

            prevButton.addEventListener('click', () => goTo(index - 1));
            nextButton.addEventListener('click', () => goTo(index + 1));

            updateSlider();
        }
    });
</script>

<!-- Đoạn Script được đặt DƯỚI CÙNG (sau footer) để đảm bảo jQuery đã được load -->
<script>
    $(document).ready(function() {
        $('#schedule-select').on('change', function() {
            let selectedOption = $(this).find('option:selected');
            let displayArea = $('#selected-schedule-info');

            if (!selectedOption.val()) {
                displayArea.slideUp(200);
                return;
            }

            let start = selectedOption.attr('data-start');
            let end = selectedOption.attr('data-end');
            let slots = selectedOption.attr('data-slots');

            let html = `
                <h6 class="mb-2" style="color: #333; font-size: 14px;">Chi tiết lịch trình:</h6>
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <span class="mb-1"><i class="fal fa-calendar-check mr-1" style="color: #f7921e;"></i> <b>${start}</b> ➡️ ${end}</span>
                    <span style="background-color: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><i class="fal fa-user-friends mr-1"></i> Còn ${slots} chỗ</span>
                </div>
            `;

            displayArea.html(html).slideDown(300);
        });
    });
</script>
