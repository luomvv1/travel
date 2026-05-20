@include('admin.blocks.header')

@php
    $tour = $tourDetails->tour;
    $tourDays = max(1, (int) old('songay', request('days', $tour->songay)));
    $existingItinerary = collect($tourDetails->itinerary ?? []);
    $images = collect($tourDetails->images ?? []);
    $schedules = collect($tourDetails->schedules ?? []);
    $money = fn ($value) => number_format((float) ($value ?? 0), 0, ',', '.') . ' VNĐ';
    $date = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : 'Chưa có';
    $regionLabels = ['b' => 'Miền Bắc', 't' => 'Miền Trung', 'n' => 'Miền Nam'];
    $tourStatusLabels = ['hoat_dong' => 'Hoạt động', 'khong_hoat_dong' => 'Không hoạt động'];
    $scheduleStatusLabels = [
        'con_cho' => ['Còn chỗ', 'status-success'],
        'het_cho' => ['Hết chỗ', 'status-danger'],
        'sap_dien_ra' => ['Sắp diễn ra', 'status-warning'],
        'hoan_thanh' => ['Hoàn thành', 'status-info'],
        'huy' => ['Đã hủy', 'status-muted'],
    ];
    $coverImage = $images->first();
@endphp

<style>
    .edit-tour-page {
        color: #253242;
    }

    .edit-tour-hero,
    .edit-tour-panel {
        background: #ffffff;
        border: 1px solid #e6edf3;
        border-radius: 8px;
        margin-bottom: 18px;
    }

    .edit-tour-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        padding: 20px 24px;
    }

    .edit-tour-hero h1 {
        color: #1f2d3d;
        font-size: 25px;
        font-weight: 700;
        letter-spacing: 0;
        margin: 0 0 6px;
    }

    .edit-tour-hero p {
        color: #6b7d90;
        font-size: 14px;
        margin: 0;
    }

    .edit-tour-actions {
        display: flex;
        gap: 8px;
        white-space: nowrap;
    }

    .edit-tour-panel {
        padding: 18px;
    }

    .panel-heading-clean {
        align-items: center;
        border-bottom: 1px solid #edf2f6;
        display: flex;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
    }

    .panel-heading-clean h2 {
        color: #1f2d3d;
        font-size: 17px;
        font-weight: 700;
        letter-spacing: 0;
        margin: 0;
    }

    .panel-heading-clean small {
        color: #8a98a8;
        font-size: 12px;
    }

    .tour-cover {
        background: #f3f6f9;
        border-radius: 8px;
        height: 220px;
        margin-bottom: 14px;
        overflow: hidden;
        position: relative;
    }

    .tour-cover img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .tour-cover-empty {
        align-items: center;
        color: #8a98a8;
        display: flex;
        height: 100%;
        justify-content: center;
    }

    .summary-list {
        margin: 0;
        padding: 0;
    }

    .summary-list li {
        align-items: center;
        border-bottom: 1px solid #edf2f6;
        display: flex;
        justify-content: space-between;
        list-style: none;
        padding: 10px 0;
    }

    .summary-list li:last-child {
        border-bottom: 0;
    }

    .summary-list span {
        color: #7c8b9d;
    }

    .summary-list strong {
        color: #1f2d3d;
        text-align: right;
    }

    .form-section-title {
        color: #1f2d3d;
        font-size: 15px;
        font-weight: 700;
        margin: 18px 0 12px;
    }

    .form-section-title:first-child {
        margin-top: 0;
    }

    .edit-tour-page label {
        color: #425166;
        font-weight: 600;
    }

    .edit-tour-page .form-control {
        border-color: #dbe5ed;
        box-shadow: none;
    }

    .edit-tour-page .form-control:focus {
        border-color: #1f7a8c;
        box-shadow: 0 0 0 2px rgba(31, 122, 140, 0.12);
    }

    .day-tools {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .day-tools .form-inline {
        align-items: center;
        display: flex;
        gap: 8px;
        margin: 0;
    }

    .day-item {
        border: 1px solid #e6edf3;
        border-radius: 8px;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .day-title {
        background: #f7fafc;
        border-bottom: 1px solid #e6edf3;
        color: #1f2d3d;
        font-weight: 700;
        padding: 12px 14px;
    }

    .day-body {
        padding: 14px;
    }

    .image-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }

    .image-item {
        border: 1px solid #e6edf3;
        border-radius: 8px;
        overflow: hidden;
    }

    .image-thumb {
        background: #f3f6f9;
        height: 135px;
    }

    .image-thumb img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .image-info {
        padding: 12px;
    }

    .image-info strong {
        color: #1f2d3d;
        display: block;
        margin-bottom: 4px;
        overflow-wrap: anywhere;
    }

    .image-info p {
        color: #7c8b9d;
        font-size: 12px;
        min-height: 34px;
        margin: 0 0 10px;
    }

    .status-pill {
        border-radius: 999px;
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 10px;
        white-space: nowrap;
    }

    .status-success {
        background: #e8f6ee;
        color: #20764a;
    }

    .status-danger {
        background: #ffecec;
        color: #b42318;
    }

    .status-warning {
        background: #fff6dd;
        color: #9a6500;
    }

    .status-info {
        background: #e8f2ff;
        color: #1b64b0;
    }

    .status-muted {
        background: #eef2f6;
        color: #5b6778;
    }

    .responsive-table {
        overflow-x: auto;
    }

    .clean-table {
        margin-bottom: 0;
    }

    .clean-table > thead > tr > th {
        border-bottom: 1px solid #e8eef3;
        color: #6b7d90;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .clean-table > tbody > tr > td {
        border-top: 1px solid #eef2f6;
        vertical-align: middle;
    }

    .empty-box {
        background: #f7fafc;
        border: 1px dashed #cbd8e3;
        border-radius: 8px;
        color: #7c8b9d;
        padding: 18px;
        text-align: center;
    }

    @media (max-width: 767px) {
        .edit-tour-hero {
            align-items: flex-start;
            display: block;
        }

        .edit-tour-actions {
            margin-top: 14px;
        }

        .day-tools .form-inline {
            align-items: stretch;
            display: block;
            width: 100%;
        }

        .day-tools .form-inline .form-control,
        .day-tools .form-inline .btn {
            margin-top: 8px;
            width: 100%;
        }
    }
</style>

<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col edit-tour-page" role="main">
            <div class="edit-tour-hero">
                <div>
                    <h1>Chỉnh sửa tour</h1>
                    <p>{{ $tour->tentour }}</p>
                </div>
                <div class="edit-tour-actions">
                    <a href="{{ route('admin.tours') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Danh sách
                    </a>
                    <button class="btn btn-primary" type="submit" form="tourEditForm">
                        <i class="fa fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="edit-tour-panel">
                        <div class="tour-cover">
                            @if($coverImage)
                                <img src="{{ asset('admin/assets/images/gallery-tours/' . $coverImage->urlanh) }}" alt="{{ $tour->tentour }}">
                            @else
                                <div class="tour-cover-empty">
                                    <i class="fa fa-image"></i>&nbsp; Chưa có ảnh
                                </div>
                            @endif
                        </div>
                        <ul class="summary-list">
                            <li>
                                <span>Mã tour</span>
                                <strong>#{{ $tour->tourid }}</strong>
                            </li>
                            <li>
                                <span>Khu vực</span>
                                <strong>{{ $regionLabels[$tour->khuvuc] ?? $tour->khuvuc ?? 'Chưa chọn' }}</strong>
                            </li>
                            <li>
                                <span>Thời lượng</span>
                                <strong>{{ $tourDays }} ngày</strong>
                            </li>
                            <li>
                                <span>Sức chứa</span>
                                <strong>{{ number_format($tour->songuoitoida ?? 0, 0, ',', '.') }} khách</strong>
                            </li>
                            <li>
                                <span>Giá người lớn</span>
                                <strong>{{ $money($tour->gianguoilon) }}</strong>
                            </li>
                            <li>
                                <span>Trạng thái</span>
                                <strong>{{ $tourStatusLabels[$tour->trangthai] ?? $tour->trangthai }}</strong>
                            </li>
                        </ul>
                    </div>

                    <div class="edit-tour-panel">
                        <div class="panel-heading-clean">
                            <h2>Điều chỉnh số ngày</h2>
                        </div>
                        <div class="day-tools">
                            <form method="GET" action="{{ route('admin.tour-edit-page', ['tourId' => $tour->tourid]) }}" class="form-inline">
                                <input type="number" name="days" min="1" value="{{ $tourDays }}" class="form-control" style="width: 110px;">
                                <button class="btn btn-info" type="submit">
                                    <i class="fa fa-refresh"></i> Cập nhật
                                </button>
                            </form>
                            <a class="btn btn-success" href="{{ route('admin.tour-edit-page', ['tourId' => $tour->tourid, 'days' => $tourDays + 1]) }}">
                                <i class="fa fa-plus"></i> Thêm 1 ngày
                            </a>
                        </div>
                        <p class="text-muted" style="margin: 12px 0 0;">
                            Cập nhật số ngày để hiển thị đúng số ô lịch trình cần nhập.
                        </p>
                    </div>
                </div>

                <div class="col-md-8 col-sm-12">
                    <form id="tourEditForm" method="POST" action="{{ route('admin.tour-update', ['tourId' => $tour->tourid]) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tourId" value="{{ $tour->tourid }}">

                        <div class="edit-tour-panel">
                            <div class="panel-heading-clean">
                                <h2>Thông tin tour</h2>
                                <small>Cập nhật thông tin hiển thị cho khách hàng</small>
                            </div>

                            <div class="form-section-title">Thông tin cơ bản</div>
                            <div class="form-group">
                                <label>Tên tour</label>
                                <input class="form-control" name="name" value="{{ old('name', $tour->tentour) }}" required>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Điểm khởi hành</label>
                                    <input class="form-control" name="departure" value="{{ old('departure', $tour->diemkhoihanh) }}" placeholder="Ví dụ: TP. Hồ Chí Minh">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Điểm đến</label>
                                    <input class="form-control" name="destination" value="{{ old('destination', $tour->diadiemden) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Khu vực</label>
                                    <select class="form-control" name="domain" required>
                                        <option value="">Chọn khu vực</option>
                                        <option value="b" {{ old('domain', $tour->khuvuc) == 'b' ? 'selected' : '' }}>Miền Bắc</option>
                                        <option value="t" {{ old('domain', $tour->khuvuc) == 't' ? 'selected' : '' }}>Miền Trung</option>
                                        <option value="n" {{ old('domain', $tour->khuvuc) == 'n' ? 'selected' : '' }}>Miền Nam</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Số ngày</label>
                                    <input class="form-control" type="number" name="songay" min="1" value="{{ old('songay', $tourDays) }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Số khách tối đa</label>
                                    <input class="form-control" type="number" name="number" min="1" value="{{ old('number', $tour->songuoitoida) }}" required>
                                </div>
                            </div>

                            <div class="form-section-title">Giá tour</div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Giá người lớn</label>
                                    <input class="form-control" type="number" name="price_adult" min="0" value="{{ old('price_adult', $tour->gianguoilon) }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Giá trẻ em</label>
                                    <input class="form-control" type="number" name="price_child" min="0" value="{{ old('price_child', $tour->giatreem) }}" required>
                                </div>
                            </div>

                            <div class="form-section-title">Mô tả</div>
                            <div class="form-group">
                                <textarea class="form-control" name="description" rows="7" placeholder="Mô tả điểm nổi bật, trải nghiệm và đối tượng phù hợp của tour">{{ old('description', $tour->mota) }}</textarea>
                            </div>
                        </div>

                        <div class="edit-tour-panel">
                            <div class="panel-heading-clean">
                                <h2>Lịch trình theo ngày</h2>
                                <small>{{ $tourDays }} ngày</small>
                            </div>

                            @for($day = 1; $day <= $tourDays; $day++)
                                @php
                                    $daySchedule = $existingItinerary[$day - 1] ?? null;
                                @endphp
                                <div class="day-item">
                                    <div class="day-title">Ngày {{ $day }}</div>
                                    <div class="day-body">
                                        <div class="form-group">
                                            <label>Tiêu đề</label>
                                            <input
                                                type="text"
                                                name="timeline[{{ $day }}][title]"
                                                class="form-control"
                                                value="{{ old('timeline.' . $day . '.title', $daySchedule->tieude ?? 'Ngày ' . $day) }}"
                                                placeholder="Ví dụ: Ngày {{ $day }} - Khởi hành"
                                            >
                                        </div>
                                        <div class="form-group">
                                            <label>Nội dung lịch trình</label>
                                            <textarea
                                                name="timeline[{{ $day }}][itinerary]"
                                                class="form-control"
                                                rows="4"
                                                placeholder="Mô tả hoạt động, điểm tham quan, bữa ăn và thời gian nghỉ ngơi"
                                            >{{ old('timeline.' . $day . '.itinerary', $daySchedule->noidung ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-sm-12">
                    <div class="edit-tour-panel">
                        <div class="panel-heading-clean">
                            <h2>Thư viện ảnh</h2>
                            <small>{{ $images->count() }} ảnh</small>
                        </div>

                        @if($images->count() > 0)
                            <div class="image-grid">
                                @foreach($images as $img)
                                    <div class="image-item">
                                        <div class="image-thumb">
                                            <img src="{{ asset('admin/assets/images/gallery-tours/' . $img->urlanh) }}" alt="{{ $img->tenanh ?: $tour->tentour }}">
                                        </div>
                                        <div class="image-info">
                                            <strong>{{ $img->tenanh ?: 'Ảnh tour' }}</strong>
                                            <p>{{ $img->motaanh ?: 'Chưa có mô tả ảnh.' }}</p>
                                            <form method="POST" action="{{ route('admin.tour-delete-image', ['tourId' => $tour->tourid, 'hinhId' => $img->hinhid]) }}">
                                                @csrf
                                                <button class="btn btn-danger btn-sm btn-block" type="submit">
                                                    <i class="fa fa-trash"></i> Xóa ảnh
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-box">Chưa có ảnh nào cho tour này.</div>
                        @endif
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="edit-tour-panel">
                        <div class="panel-heading-clean">
                            <h2>Thêm ảnh mới</h2>
                        </div>
                        <form method="POST" action="{{ route('admin.tour-add-image', ['tourId' => $tour->tourid]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Chọn ảnh</label>
                                <input type="file" name="image" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Mô tả ảnh</label>
                                <input type="text" name="description" class="form-control" placeholder="Ví dụ: Cầu Vàng Đà Nẵng">
                            </div>
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fa fa-upload"></i> Tải ảnh lên
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-sm-12">
                    <div class="edit-tour-panel">
                        <div class="panel-heading-clean">
                            <h2>Lịch khởi hành</h2>
                            <small>{{ $schedules->count() }} lịch</small>
                        </div>

                        @if($schedules->count() > 0)
                            <div class="responsive-table">
                                <table class="table clean-table">
                                    <thead>
                                        <tr>
                                            <th>Ngày bắt đầu</th>
                                            <th>Ngày kết thúc</th>
                                            <th>Số chỗ còn</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($schedules as $schedule)
                                            @php
                                                [$statusText, $statusClass] = $scheduleStatusLabels[$schedule->trangthai] ?? [$schedule->trangthai, 'status-muted'];
                                            @endphp
                                            <tr>
                                                <td>{{ $date($schedule->ngaybatdau) }}</td>
                                                <td>{{ $date($schedule->ngayketthuc) }}</td>
                                                <td>{{ number_format($schedule->sochocon ?? 0, 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="status-pill {{ $statusClass }}">{{ $statusText }}</span>
                                                    <form method="POST" action="{{ route('admin.tour-update-schedule-status', ['tourId' => $tour->tourid, 'lichId' => $schedule->lichid]) }}" style="margin-top: 8px;">
                                                        @csrf
                                                        <div class="input-group">
                                                            <select name="trangthai" class="form-control input-sm">
                                                                @foreach($scheduleStatusLabels as $value => $meta)
                                                                    <option value="{{ $value }}" {{ $schedule->trangthai === $value ? 'selected' : '' }}>{{ $meta[0] }}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-primary btn-sm" type="submit">Lưu</button>
                                                            </span>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td>
                                                    <form method="POST" action="{{ route('admin.tour-delete-schedule', ['tourId' => $tour->tourid, 'lichId' => $schedule->lichid]) }}">
                                                        @csrf
                                                        <button class="btn btn-danger btn-sm" type="submit">
                                                            <i class="fa fa-trash"></i> Xóa
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-box">Chưa có lịch khởi hành.</div>
                        @endif
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="edit-tour-panel">
                        <div class="panel-heading-clean">
                            <h2>Thêm lịch</h2>
                        </div>
                        <form method="POST" action="{{ route('admin.tour-add-schedule', ['tourId' => $tour->tourid]) }}">
                            @csrf
                            <div class="form-group">
                                <label>Ngày bắt đầu</label>
                                <input type="date" name="ngaybatdau" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Ngày kết thúc</label>
                                <input type="date" name="ngayketthuc" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Số chỗ còn</label>
                                <input type="number" name="sochocon" min="0" class="form-control" value="{{ $tour->songuoitoida }}" required>
                            </div>
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="trangthai" class="form-control">
                                    @foreach($scheduleStatusLabels as $value => $meta)
                                        <option value="{{ $value }}">{{ $meta[0] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-success btn-block" type="submit">
                                <i class="fa fa-calendar-plus-o"></i> Thêm lịch khởi hành
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.blocks.footer')
