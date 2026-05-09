@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Quản lý người dùng</h3>
                    </div>

                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 form-group pull-right top_search">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for...">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">Go!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="x_panel">
                    <div class="x_content row">
                        
                        @if(session('success'))
                            <div class="col-md-12">
                                <div class="alert alert-success">{{ session('success') }}</div>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="col-md-12">
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            </div>
                        @endif

                        @foreach ($users as $user)
                            <div class="col-md-4 col-sm-4 profile_details">
                                <div class="well profile_view">
                                    <div class="col-sm-12">
                                        <h4 class="brief">
                                            <span class="badge badge-info">{{ $user->role_text ?? 'Khách hàng' }}</span>
                                            <span class="badge {{ $user->trangthai === 'hoat_dong' ? 'badge-success' : ($user->trangthai === 'bi_khoa' ? 'badge-warning' : 'badge-danger') }}">
                                                {{ $user->isActive }}
                                            </span>
                                        </h4>
                                        
                                        <div class="left col-md-7 col-sm-7">
                                            <h2>{{ $user->hoten }}</h2>
                                            <p><strong>Tài khoản: </strong> {{ $user->tendangnhap }} </p>
                                            <ul class="list-unstyled">
                                                <li><i class="fa fa-envelope"></i> Email: {{ $user->email }}</li>
                                                <li><i class="fa fa-building"></i> Địa chỉ: {{ $user->diachi ?? 'Chưa cập nhật' }}</li>
                                                <li><i class="fa fa-phone"></i> SĐT: {{ $user->sodienthoai ?? 'Chưa cập nhật' }}</li>
                                            </ul>
                                        </div>
                                        <div class="right col-md-5 col-sm-5 text-center">
                                            <img src="{{ asset('admin/assets/images/user-profile/unnamed.png') }}"
                                                alt="" class="img-circle img-fluid">
                                        </div>
                                    </div>
                                    
                                    <div class="profile-bottom text-center">
                                        <div class="col-sm-12 emphasis" style="display: flex; justify-content: flex-end; gap: 5px;">
                                            
                                            @if ($user->trangthai === 'bi_khoa')
                                                <form action="{{ route('admin.status-user') }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <input type="hidden" name="userId" value="{{ $user->ndid }}">
                                                    <input type="hidden" name="status" value="hoat_dong">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fa fa-unlock"> </i> Mở khóa
                                                    </button>
                                                </form>
                                            @elseif ($user->trangthai === 'hoat_dong')
                                                <form action="{{ route('admin.status-user') }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <input type="hidden" name="userId" value="{{ $user->ndid }}">
                                                    <input type="hidden" name="status" value="bi_khoa">
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="fa fa-ban"> </i> Khóa
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($user->trangthai === 'xoa')
                                                <form action="{{ route('admin.status-user') }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <input type="hidden" name="userId" value="{{ $user->ndid }}">
                                                    <input type="hidden" name="status" value="hoat_dong">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-refresh"> </i> Khôi phục
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.status-user') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                                    @csrf
                                                    <input type="hidden" name="userId" value="{{ $user->ndid }}">
                                                    <input type="hidden" name="status" value="xoa">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-close"> </i> Xóa
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
        </div>
</div>
@include('admin.blocks.footer')