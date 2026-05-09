@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="page-title">
                <div class="title_left">
                    <h3>Chỉnh sửa Tour</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Sửa Tour: {{ $tourDetails->tour->tentour }}</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @php
                                $tourDays = max(1, (int) old('songay', request('days', $tourDetails->tour->songay)));
                                $existingItinerary = collect($tourDetails->itinerary ?? []);
                            @endphp

                            <div class="mb-3 d-flex flex-wrap gap-2 align-items-end">
                                <form method="GET" action="{{ route('admin.tour-edit-page', ['tourId' => $tourDetails->tour->tourid]) }}" class="form-inline mr-2">
                                    <div class="form-group mr-2">
                                        <label class="mr-2">Số ngày</label>
                                        <input type="number" name="days" min="1" value="{{ $tourDays }}" class="form-control">
                                    </div>
                                    <button class="btn btn-info" type="submit">Cập nhật số ngày</button>
                                </form>

                                <a class="btn btn-outline-success" href="{{ route('admin.tour-edit-page', ['tourId' => $tourDetails->tour->tourid, 'days' => $tourDays + 1]) }}">
                                    Thêm 1 ngày
                                </a>
                            </div>

                            <form method="POST" action="{{ route('admin.tour-update', ['tourId' => $tourDetails->tour->tourid]) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="tourId" value="{{ $tourDetails->tour->tourid }}">

                                <div class="form-group">
                                    <label>Tên</label>
                                    <input class="form-control" name="name" value="{{ $tourDetails->tour->tentour }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Điểm đến</label>
                                    <input class="form-control" name="destination" value="{{ $tourDetails->tour->diadiemden }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Khu vực</label>
                                    <select class="form-control" name="domain">
                                        <option value="">Chọn khu vực</option>
                                        <option value="b" {{ $tourDetails->tour->khuvuc == 'b' ? 'selected' : '' }}>Miền Bắc</option>
                                        <option value="t" {{ $tourDetails->tour->khuvuc == 't' ? 'selected' : '' }}>Miền Trung</option>
                                        <option value="n" {{ $tourDetails->tour->khuvuc == 'n' ? 'selected' : '' }}>Miền Nam</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Số lượng</label>
                                        <input class="form-control" type="number" name="number" value="{{ $tourDetails->tour->songuoitoida }}" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Giá người lớn</label>
                                        <input class="form-control" type="number" name="price_adult" value="{{ $tourDetails->tour->gianguoilon }}" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Giá trẻ em</label>
                                        <input class="form-control" type="number" name="price_child" value="{{ $tourDetails->tour->giatreem }}" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Mô tả</label>
                                    <textarea class="form-control" name="description" rows="6">{{ $tourDetails->tour->mota }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Số ngày</label>
                                    <input class="form-control" type="number" name="songay" min="1" value="{{ $tourDays }}" required>
                                    <small class="text-muted">Đổi số ngày rồi bấm "Cập nhật số ngày" ở trên để nạp thêm ô lịch trình.</small>
                                </div>

                                <hr>
                                <h4>Lịch trình theo số ngày</h4>
                                <p class="text-muted">Tour này có {{ $tourDays }} ngày, bên dưới là nội dung lịch trình tương ứng cho từng ngày.</p>

                                <div class="row">
                                    @for($day = 1; $day <= $tourDays; $day++)
                                        @php
                                            $daySchedule = $existingItinerary[$day - 1] ?? null;
                                        @endphp
                                        <div class="col-md-12 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <strong>Ngày {{ $day }}</strong>
                                                </div>
                                                <div class="card-body">
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
                                                            placeholder="Mô tả lịch trình cho ngày {{ $day }}"
                                                        >{{ old('timeline.' . $day . '.itinerary', $daySchedule->noidung ?? '') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                                    <a href="{{ route('admin.tours') }}" class="btn btn-secondary">Hủy</a>
                                </div>

                            </form>

                            <hr>
                            <h4>Hình ảnh</h4>

                            <div class="mb-3">
                                @if($tourDetails->images && count($tourDetails->images) > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr><th>Ảnh</th><th>Tên</th><th>Mô tả</th><th>Hành động</th></tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tourDetails->images as $img)
                                            <tr>
                                                <td><img src="{{ asset('admin/assets/images/gallery-tours/' . $img->urlanh) }}" style="height:60px"></td>
                                                <td>{{ $img->tenanh }}</td>
                                                <td>{{ $img->motaanh }}</td>
                                                <td>
                                                    <form method="POST" action="{{ route('admin.tour-delete-image', ['tourId' => $tourDetails->tour->tourid, 'hinhId' => $img->hinhid]) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p>Chưa có ảnh nào.</p>
                                @endif

                                <div class="form-group">
                                    <label>Thêm ảnh mới</label>
                                    <form method="POST" action="{{ route('admin.tour-add-image', ['tourId' => $tourDetails->tour->tourid]) }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="image" class="form-control-file" required>
                                        <input type="text" name="description" class="form-control mt-2" placeholder="Mô tả ảnh (tùy chọn)">
                                        <button class="btn btn-primary mt-2" type="submit">Tải lên</button>
                                    </form>
                                </div>
                            </div>

                            <hr>
                            <h4>Lịch khởi hành</h4>

                            <div class="mb-3">
                                @if($tourDetails->schedules && count($tourDetails->schedules) > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr><th>Ngày bắt đầu</th><th>Ngày kết thúc</th><th>Số chỗ còn</th><th>Trạng thái</th><th>Hành động</th></tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tourDetails->schedules as $s)
                                            <tr>
                                                <td>{{ $s->ngaybatdau }}</td>
                                                <td>{{ $s->ngayketthuc }}</td>
                                                <td>{{ $s->sochocon }}</td>
                                                <td>
                                                    @php
                                                        $statusLabels = [
                                                            'con_cho' => 'Còn chỗ',
                                                            'het_cho' => 'Hết chỗ',
                                                            'sap_dien_ra' => 'Sắp diễn ra',
                                                            'hoan_thanh' => 'Hoàn thành',
                                                            'huy' => 'Đã hủy',
                                                        ];
                                                    @endphp
                                                    <div class="mb-2"><strong>{{ $statusLabels[$s->trangthai] ?? $s->trangthai }}</strong></div>
                                                    <form method="POST" action="{{ route('admin.tour-update-schedule-status', ['tourId' => $tourDetails->tour->tourid, 'lichId' => $s->lichid]) }}" class="form-inline">
                                                        @csrf
                                                        <select name="trangthai" class="form-control form-control-sm mr-2">
                                                            <option value="con_cho" {{ $s->trangthai === 'con_cho' ? 'selected' : '' }}>Còn chỗ</option>
                                                            <option value="het_cho" {{ $s->trangthai === 'het_cho' ? 'selected' : '' }}>Hết chỗ</option>
                                                            <option value="sap_dien_ra" {{ $s->trangthai === 'sap_dien_ra' ? 'selected' : '' }}>Sắp diễn ra</option>
                                                            <option value="hoan_thanh" {{ $s->trangthai === 'hoan_thanh' ? 'selected' : '' }}>Hoàn thành</option>
                                                            <option value="huy" {{ $s->trangthai === 'huy' ? 'selected' : '' }}>Đã hủy</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-primary" type="submit">Cập nhật</button>
                                                    </form>
                                                </td>
                                                <td class="pt-3">
                                                    <form method="POST" action="{{ route('admin.tour-delete-schedule', ['tourId' => $tourDetails->tour->tourid, 'lichId' => $s->lichid]) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p>Chưa có lịch khởi hành.</p>
                                @endif

                                <div class="card p-3">
                                    <form method="POST" action="{{ route('admin.tour-add-schedule', ['tourId' => $tourDetails->tour->tourid]) }}">
                                        @csrf
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>Ngày bắt đầu</label>
                                                <input type="date" name="ngaybatdau" class="form-control" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Ngày kết thúc</label>
                                                <input type="date" name="ngayketthuc" class="form-control" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Số chỗ còn</label>
                                                <input type="number" name="sochocon" class="form-control" value="{{ $tourDetails->tour->songuoitoida }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Trạng thái</label>
                                            <select name="trangthai" class="form-control">
                                                <option value="con_cho">Còn chỗ</option>
                                                <option value="het_cho">Hết chỗ</option>
                                                <option value="sap_dien_ra">Sắp diễn ra</option>
                                                <option value="hoan_thanh">Hoàn thành</option>
                                                <option value="huy">Đã hủy</option>
                                            </select>
                                        </div>
                                        <div class="form-group mt-2">
                                            <button class="btn btn-success" type="submit">Thêm lịch</button>
                                        </div>
                                    </form>
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
