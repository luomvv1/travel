@include('clients.blocks.header')
<div class="user-profile">
    <div class="container-xl px-4 mt-4">
        <div class="row">
            <div class="col-xl-4">
                <!-- Thẻ hiển thị Avatar tự động -->
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Ảnh đại diện</div>
                    <div class="card-body text-center">
                        @php
                            // Kiểm tra giới tính để gán ảnh mặc định
                            if ($user->gioitinh == 'Nam') {
                                $avatarSrc = asset('admin/assets/images/user-profile/male.png');
                            } elseif ($user->gioitinh == 'Nữ') {
                                $avatarSrc = asset('admin/assets/images/user-profile/female.png');
                            } else {
                                $avatarSrc = asset('admin/assets/images/user-profile/default.png');
                            }
                        @endphp

                        <img id="avatarPreview" class="img-account-profile rounded-circle mb-2"
                            src="{{ $avatarSrc }}"
                            style="width:160px; height: 160px; object-fit: cover; border: 3px solid #f7921e;" 
                            alt="Ảnh đại diện">

                        <div class="small font-italic text-muted mb-4">
                            Ảnh đại diện được hệ thống đặt tự động theo giới tính của bạn.
                        </div>
                        
                        <!-- Đã xóa các thẻ <input type="file"> -->
                    </div>
                </div>

                <div class="card mt-4 mb-4 mb-xl-0">
                    <button class="btn btn-primary w-100" id="update_password_profile">Đổi mật khẩu</button>
                </div>
            </div>
            
          <div class="col-xl-8">
                <!-- Thẻ thông tin tài khoản -->
                <div class="card mb-4">
                    <div class="card-header">Thông tin tài khoản</div>
                    <div class="card-body">
                        <!-- Xóa class updateUser (nếu đang dùng cho JS) -->
                        <form action="{{ route('update-user-profile') }}" method="POST">
                            @csrf
                            <div class="row gx-3 mb-3">
                                <div class="col-md-8">
                                    <label class="small mb-1" for="inputFullName">Họ và tên</label>
                                    <!-- Đã thêm name="fullName" -->
                                    <input class="form-control" id="inputFullName" name="fullName" type="text"
                                        placeholder="Họ và tên" value="{{ $user->hoten }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small mb-1" for="inputGender">Giới tính</label>
                                    <select class="form-control" name="gioitinh" id="inputGender">
                                        <option value="" disabled {{ empty($user->gioitinh) ? 'selected' : '' }}>Chọn giới tính</option>
                                        <option value="Nam" {{ $user->gioitinh == 'Nam' ? 'selected' : '' }}>Nam</option>
                                        <option value="Nữ" {{ $user->gioitinh == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                        <option value="Khác" {{ $user->gioitinh == 'Khác' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row gx-3 mb-3">
                                <div class="col-md-12">
                                    <label class="small mb-1" for="inputLocation">Địa chỉ</label>
                                    <!-- Đã thêm name="address" -->
                                    <input class="form-control" id="inputLocation" name="address" type="text" placeholder="Địa chỉ"
                                        value="{{ $user->diachi }}" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="small mb-1" for="inputEmailAddress">Email</label>
                                <!-- Đã thêm name="email" -->
                                <input class="form-control" id="inputEmailAddress" name="email" type="email" placeholder="Email"
                                    value="{{ $user->email }}" required>
                            </div>
                            
                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputPhone">Số điện thoại</label>
                                    <!-- Đã thêm name="phone" -->
                                    <input class="form-control" id="inputPhone" name="phone" type="number"
                                        placeholder="Số điện thoại" value="{{ $user->sodienthoai }}" required>
                                </div>
                            </div>

                            <button class="btn btn-primary" type="submit">Lưu thông tin</button>
                        </form>
                    </div>
                </div>
                
                <!-- Thẻ đổi mật khẩu -->
                <div class="card mb-4">
                    <div class="card-body" id="card_change_password" style="display: none;"> <!-- Thêm display none mặc định nếu bạn dùng JS để show/hide -->
                        <div class="invalid-feedback" style="margin-top:-15px" id="validate_password"></div>
                       <form action="{{ route('change-password') }}" method="post" class="change_password_profile">
                            @csrf
                            <div class="row gx-3">
                                <div class="col-md-4">
                                    <!-- ĐÃ THÊM name="oldPass" VÀO ĐÂY -->
                                    <input class="form-control" id="inputOldPass" name="oldPass" type="password"
                                        placeholder="Nhập mật khẩu cũ" required>
                                </div>
                                <div class="col-md-4">
                                    <!-- ĐÃ THÊM name="newPass" VÀO ĐÂY -->
                                    <input class="form-control" id="inputNewPass" name="newPass" type="password"
                                        placeholder="Nhập mật khẩu mới" required>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary w-100" type="submit">Xác nhận</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('clients.blocks.footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Nếu Controller gửi về thông báo thành công
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        // Nếu Controller gửi về thông báo lỗi
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        
        // Script ẩn/hiện form đổi mật khẩu
        const btnTogglePassword = document.getElementById('update_password_profile');
        const cardPassword = document.getElementById('card_change_password');

        if(btnTogglePassword) {
            btnTogglePassword.addEventListener('click', function() {
                if (cardPassword.style.display === 'none' || cardPassword.style.display === '') {
                    cardPassword.style.display = 'block'; 
                    cardPassword.scrollIntoView({ behavior: 'smooth', block: 'center' }); 
                } else {
                    cardPassword.style.display = 'none'; 
                }
            });
        }
    });
</script>