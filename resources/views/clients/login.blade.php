@include('clients.blocks.header')

<div class="login-template">
    <div class="main">
        <section class="sign-in show">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="{{ asset('clients/assets/images/login/signin-image.jpg') }}" alt="sing up image"></figure>
                        <a href="javascript:void(0)" class="signup-image-link" id="sign-up">Tạo tài khoản</a>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">Đăng nhập</h2>
                        
                        @if(session('error_login'))
                            <div class="alert alert-danger" style="color: red; margin-bottom: 15px;">{{ session('error_login') }}</div>
                        @endif
                        @if ($errors->has('username_login') || $errors->has('password_login'))
                            <div class="alert alert-danger" style="color: red; margin-bottom: 15px;">
                                {{ $errors->first('username_login') ?: $errors->first('password_login') }}
                            </div>
                        @endif

                        <form action="{{ route('user-login') }}" method="POST" class="login-form" id="login-form" style="margin-top: 15px">
                            @csrf
                            <div class="form-group">
                                <label for="username_login"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="username_login" id="username_login" placeholder="Tên đăng nhập" value="{{ old('username_login') }}" required/>
                            </div>
                            
                            <div class="form-group">
                                <label for="password_login"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password_login" id="password_login" placeholder="Mật khẩu" required/>
                            </div>
                            
                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="Đăng nhập" />
                            </div>
                        </form>
                        <div class="social-login">
                            <span class="social-label">Hoặc đăng nhập bằng</span>
                            <ul class="socials">
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-facebook"></i></a></li>
                                <li><a href="{{ route('login-google') }}"><i class="display-flex-center zmdi zmdi-google"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="signup" style="display: none;">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title">Đăng ký</h2>
                        
                        @if(session('success_register'))
                            <div class="alert alert-success" style="color: green; margin-bottom: 10px;">{{ session('success_register') }}</div>
                        @endif
                        @if(session('error_register'))
                            <div class="alert alert-danger" style="color: red; margin-bottom: 10px;">{{ session('error_register') }}</div>
                        @endif

                        <form action="{{ route('register') }}" method="POST" class="register-form" id="register-form" style="margin-top: 15px">
                            @csrf
                            <div class="form-group">
                                <label for="username_register"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="username_register" id="username_register" placeholder="Tên tài khoản" value="{{ old('username_register') }}" required/>
                            </div>
                            @error('username_register')
                                <div class="invalid-feedback" style="display:block; color:red; margin-top:-15px; margin-bottom:15px;">{{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="email_register"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email_register" id="email_register" placeholder="Email" value="{{ old('email_register') }}" required/>
                            </div>
                            @error('email_register')
                                <div class="invalid-feedback" style="display:block; color:red; margin-top:-15px; margin-bottom:15px;">{{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="password_register"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password_register" id="password_register" placeholder="Mật khẩu" required/>
                            </div>
                            @error('password_register')
                                <div class="invalid-feedback" style="display:block; color:red; margin-top:-15px; margin-bottom:15px;">{{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="re_pass" id="re_pass" placeholder="Nhập lại mật khẩu" required/>
                            </div>
                            @error('re_pass')
                                <div class="invalid-feedback" style="display:block; color:red; margin-top:-15px; margin-bottom:15px;">{{ $message }}</div>
                            @enderror

                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="signup" class="form-submit" value="Đăng ký" />
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <figure><img src="{{ asset('clients/assets/images/login/signup-image.jpg') }}" alt="sing up image"></figure>
                        <a href="javascript:void(0)" class="signup-image-link" id="sign-in">Tôi đã có tài khoản rồi</a>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    @if ($errors->has('username_register') || $errors->has('email_register') || $errors->has('password_register') || $errors->has('re_pass') || session('error_register') || session('success_register'))
        $(document).ready(function() {
            $(".sign-in").hide();
            $(".signup").show();
        });
    @endif
</script>

@include('clients.blocks.footer')