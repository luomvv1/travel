<h3 class="mb-30">Đánh giá từ khách hàng</h3>
@if ($reviews && $reviews->count() > 0)
    @foreach ($reviews as $review)
        <div class="comment-item mb-30">
            <div class="comment-header mb-15">
                <div class="comment-author-name">
                    <h6>{{ $review->hoten ?? 'Khách hàng' }}</h6>
                    <span class="comment-date">{{ \Carbon\Carbon::parse($review->ngaytao)->format('d/m/Y') }}</span>
                </div>
                <div class="ratting">
                    @for ($i = 0; $i < 5; $i++)
                        @if ($i < $review->sosao)
                            <i class="fas fa-star"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </div>
            </div>
            <p>{{ $review->binhluan ?? 'Không có nhận xét chi tiết' }}</p>
        </div>
    @endforeach
@else
    <p class="text-muted">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá tour này!</p>
@endif
