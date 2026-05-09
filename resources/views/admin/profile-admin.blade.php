@include('admin.blocks.header')
<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <!-- page content -->
        <div class="right_col" role="main">
            <div class="">
                <div class="page-title">
                    <div class="title_left">
                        <h3>Thông tin admin</h3>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="x_panel">
                            <div class="x_title">
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="col-md-3 col-sm-3 profile_left">
                                    <div class="profile_img">
                                        <div id="crop-avatar">
                                            <!-- Current avatar (Ảnh mặc định tĩnh) -->
                                            <img id="avatarAdminPreview" class="img-responsive avatar-view"
                                                src="{{ asset('admin/assets/images/user-profile/avt_admin.jpg') }}"
                                                alt="Avatar" style="width:100%" title="Avatar mặc định">
                                        </div>
                                    </div>
                                    <br>
                                    
                                    {{-- Đã ẩn nút Tải ảnh lên do Database chưa có cột lưu ảnh
                                    <label for="avatarAdmin" id="btn_avatar" class="btn btn-success"
                                        style=" align-items: center; text-align: center; width: 78%;margin: 10px 24px;" action={{ route('admin.update-avatar') }}>
                                        <i class="fa fa-edit m-right-xs"></i>Tải ảnh lên</label>
                                    --}}
                                    @csrf
                                    <!-- Đổi từ fullName thành hoten -->
                                    <h3 id="nameAdmin">{{ $admin->hoten }}</h3>

                                    <ul class="list-unstyled user_data">
                                        <li>
                                            <!-- Đổi từ address thành diachi -->
                                            <i class="fa fa-map-marker user-profile-icon"></i> <span
                                                id="addressAdmin">{{ $admin->diachi ?? 'Chưa cập nhật' }}</span>
                                        </li>
                                        <li>
                                            <i class="fa fa-envelope user-profile-icon"></i> <span
                                                id="emailAdmin">{{ $admin->email }}</span>
                                        </li>
                                    </ul>
                                    <br />

                                </div>
                                <div class="col-md-9 col-sm-9 ">
                                    <form action="{{ route('admin.update-admin') }}" id="formProfileAdmin"
                                        class="form-horizontal form-label-left">
                                        @csrf
                                        <div class="item form-group">
                                            <label class="col-form-label col-md-3 col-sm-3 label-align"
                                                for="fullName">Tên admin <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" id="fullName" name="fullName" required
                                                    class="form-control" placeholder="Nhập tên admin"
                                                    value="{{ $admin->hoten }}">
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="col-form-label col-md-3 col-sm-3 label-align"
                                                for="password">Mật khẩu <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <!-- Value đổi thành matkhau -->
                                                <input type="password" id="password" name="password" required
                                                    class="form-control" placeholder="Nhập mật khẩu"
                                                    value="{{ $admin->matkhau }}">
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label for="email"
                                                class="col-form-label col-md-3 col-sm-3 label-align">Email</label>
                                            <div class="col-md-6 col-sm-6">
                                                <input id="email" class="form-control" type="email" name="email"
                                                    required placeholder="Nhập email" value="{{ $admin->email }}">
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label for="address"
                                                class="col-form-label col-md-3 col-sm-3 label-align">Địa chỉ</label>
                                            <div class="col-md-6 col-sm-6">
                                                <input id="address" class="form-control" type="text"
                                                    name="address" required placeholder="Nhập địa chỉ"
                                                    value="{{ $admin->diachi }}">
                                            </div>
                                        </div>

                                        <div class="ln_solid"></div>

                                        <div class="item form-group">
                                            <div class="col-md-6 col-sm-6 offset-md-3">
                                                <button type="submit" class="btn btn-success">Cập nhật</button>
                                            </div>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page content -->

    </div>
</div>

@include('admin.blocks.footer')