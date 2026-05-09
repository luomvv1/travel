@forelse ($tours as $tour)
    <div class="col-xl-4 col-md-6" style="margin-bottom: 30px">
        <div class="destination-item tour-grid style-three bgc-lighter" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
            <div class="image">
                <div class="ratting"><i class="fas fa-star"></i> {{ number_format((float) ($tour->rating ?? 0), 1) }}</div>
                @php
                    $tourImage = collect($tour->images ?? [])->first();
                @endphp
                <img src="{{ $tourImage ? asset('admin/assets/images/gallery-tours/' . $tourImage) : asset('clients/assets/images/widgets/cta-widget.png') }}" alt="Tour">
            </div>
            <div class="content">
                <span class="location"><i class="fal fa-map-marker-alt"></i>{{ $tour->destination }}</span>
                <h5><a href="{{ route('tour-detail', ['id' => $tour->tourId]) }}">{{ $tour->title }}</a></h5>
                <span class="time">{{ $tour->time }}</span>
            </div>
            <div class="destination-footer">
                <span class="price"><span>{{ number_format($tour->priceAdult, 0, ',', '.') }}</span> VND / nguoi</span>
                <a href="{{ route('tour-detail', ['id' => $tour->tourId]) }}" class="read-more">Dat ngay <i class="fal fa-angle-right"></i></a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="alert alert-warning">Khong tim thay tour phu hop.</div>
    </div>
@endforelse

@if ($tours instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
    <div class="col-12">
        <div class="pagination-tours mt-30">
            {{ $tours->links() }}
        </div>
    </div>
@endif
