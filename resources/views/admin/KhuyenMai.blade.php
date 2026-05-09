@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Quản lý Khuyến Mãi</h3>
                    </div>
                </div>

                <div class="clearfix"></div>

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

                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Thêm Mã Khuyến Mãi</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br />
                                <form action="{{ route('admin.khuyenmai.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Mã Code *</label>
                                        <input type="text" name="macode" class="form-control" placeholder="VD: SUMMER2026" required value="{{ old('macode') }}" style="text-transform: uppercase;">
                                    </div>
                                    <div class="form-group">
                                        <label>Tên Khuyến Mãi *</label>
                                        <input type="text" name="tenkhuyenmai" class="form-control" placeholder="VD: Giảm giá mùa hè" required value="{{ old('tenkhuyenmai') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Loại Giảm</label>
                                        <select name="loaigiam" class="form-control">
                                            <option value="so_tien" {{ old('loaigiam') == 'so_tien' ? 'selected' : '' }}>Giảm theo số tiền (VNĐ)</option>
                                            <option value="phan_tram" {{ old('loaigiam') == 'phan_tram' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Giá Trị Giảm *</label>
                                        <input type="number" name="giatri" class="form-control" placeholder="VD: 500000 hoặc 10" required value="{{ old('giatri') }}">
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Ngày Bắt Đầu *</label>
                                            <input type="date" name="ngaybatdau" class="form-control" required value="{{ old('ngaybatdau') }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Ngày Kết Thúc *</label>
                                            <input type="date" name="ngayketthuc" class="form-control" required value="{{ old('ngayketthuc') }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Số lần dùng tối đa (Để 0 là vô hạn)</label>
                                        <input type="number" name="solansudungtoida" class="form-control" value="{{ old('solansudungtoida', 0) }}" min="0">
                                    </div>
                                    
                                    <div class="ln_solid"></div>
                                    <div class="form-group text-right">
                                        <button type="reset" class="btn btn-secondary">Nhập lại</button>
                                        <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Tạo mới</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Danh sách Khuyến Mãi</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mã Code</th>
                                                <th>Tên / Loại</th>
                                                <th>Mức giảm</th>
                                                <th>Thời hạn</th>
                                                <th>Đã dùng</th>
                                                <th>Trạng thái</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($list_khuyenmai as $km)
                                            <tr>
                                                <td><strong class="text-primary">{{ $km->macode }}</strong></td>
                                                <td>
                                                    {{ $km->tenkhuyenmai }}<br>
                                                    <small class="text-muted">{{ $km->loaigiam == 'so_tien' ? 'Trừ tiền mặt' : 'Giảm phần trăm' }}</small>
                                                </td>
                                                <td>
                                                    @if($km->loaigiam == 'so_tien')
                                                        {{ number_format($km->giatri, 0, ',', '.') }} VNĐ
                                                    @else
                                                        {{ $km->giatri }} %
                                                    @endif
                                                </td>
                                                <td>
                                                    Từ: {{ date('d/m/Y', strtotime($km->ngaybatdau)) }}<br>
                                                    Đến: <strong class="{{ $km->ngayketthuc < date('Y-m-d') ? 'text-danger' : 'text-success' }}">{{ date('d/m/Y', strtotime($km->ngayketthuc)) }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    {{ $km->solandasudung }} / {{ $km->solansudungtoida == 0 ? '∞' : $km->solansudungtoida }}
                                                </td>
                                                <td class="text-center">
                                                    @if($km->danghoatdong == 'Y' && $km->ngayketthuc >= date('Y-m-d'))
                                                        <span class="badge badge-success">Đang bật</span>
                                                    @else
                                                        <span class="badge badge-danger">Đã tắt / Hết hạn</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div style="display:flex; gap: 5px;">
                                                        <form action="{{ route('admin.khuyenmai.toggle') }}" method="POST" style="margin:0">
                                                            @csrf
                                                            <input type="hidden" name="macode" value="{{ $km->macode }}">
                                                            @if($km->danghoatdong == 'Y')
                                                                <button class="btn btn-warning btn-sm" title="Tạm dừng"><i class="fa fa-pause"></i></button>
                                                            @else
                                                                <button class="btn btn-success btn-sm" title="Kích hoạt"><i class="fa fa-play"></i></button>
                                                            @endif
                                                        </form>
                                                        <form action="{{ route('admin.khuyenmai.delete') }}" method="POST" style="margin:0" onsubmit="return confirm('Xác nhận xóa mã khuyến mãi này?');">
                                                            @csrf
                                                            <input type="hidden" name="macode" value="{{ $km->macode }}">
                                                            <button class="btn btn-danger btn-sm" title="Xóa"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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