@include('admin.blocks.header')
<style>
    .wizard-steps { display: flex; justify-content: space-between; list-style: none; padding: 0; margin-bottom: 30px; border-bottom: 2px solid #e0e0e0; }
    .wizard-steps li { flex: 1; text-align: center; padding: 15px 10px; position: relative; color: #999; font-weight: bold; text-transform: uppercase; transition: 0.3s; }
    .wizard-steps li.active { border-bottom: 3px solid #26B99A; color: #26B99A; }
    .wizard-steps li.completed { color: #26B99A; }
    .wizard-steps li span.step-num { display: inline-block; width: 30px; height: 30px; line-height: 30px; border-radius: 50%; background: #e0e0e0; color: #fff; margin-right: 8px; }
    .wizard-steps li.active span.step-num, .wizard-steps li.completed span.step-num { background: #26B99A; }
    .step-content { display: none; animation: fadeIn 0.5s; }
    .step-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container body">
    <div class="main_container">
        @include('admin.blocks.sidebar')

        <div class="right_col" role="main">
            <div class="page-title">
                <div class="title_left">
                    <h3>Thêm Tour Mới</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_content">
                            
                            <ul class="wizard-steps">
                                <li id="tab-step-1" class="active"><span class="step-num">1</span> Thông tin & Lộ trình</li>
                                <li id="tab-step-2"><span class="step-num">2</span> Hình ảnh</li>
                                <li id="tab-step-3"><span class="step-num">3</span> Lịch khởi hành</li>
                            </ul>

                            <div class="step-content active" id="step-1">
                                <div class="alert alert-info">Vui lòng điền thông tin cơ bản và lộ trình của Tour.</div>
                                <form id="form-step-1">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label>Tên Tour <span class="text-danger">*</span></label>
                                            <input class="form-control" name="name" placeholder="VD: Tour Vịnh Hạ Long..." required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Điểm khởi hành <span class="text-danger">*</span></label>
                                            <input class="form-control" name="departure" placeholder="VD: Hà Nội, TP.HCM..." required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Điểm đến <span class="text-danger">*</span></label>
                                            <input class="form-control" name="destination" placeholder="VD: Hạ Long" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Khu vực <span class="text-danger">*</span></label>
                                            <select class="form-control" name="domain" required>
                                                <option value="">Chọn khu vực</option>
                                                <option value="b">Miền Bắc</option>
                                                <option value="t">Miền Trung</option>
                                                <option value="n">Miền Nam</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label>Số ngày Tour <span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="songay" id="input-songay" min="1" value="1" required>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label>Số lượng khách tối đa <span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="number" min="1" required>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label>Giá người lớn (VNĐ) <span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="price_adult" min="0" required>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Giá trẻ em (VNĐ) <span class="text-danger">*</span></label>
                                            <input class="form-control" type="number" name="price_child" min="0" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Mô tả Tour</label>
                                        <textarea class="form-control" name="description" rows="3" required></textarea>
                                    </div>

                                    <hr>
                                    <h4><i class="fa fa-map-marker"></i> Lịch trình chi tiết</h4>
                                    <p class="text-muted">Lộ trình được tự động sinh ô nhập dựa vào <b>Số ngày Tour</b> bạn điền ở trên.</p>
                                    
                                    <div class="row" id="itinerary-container">
                                        </div>

                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-success btn-lg">Lưu & Tiếp tục <i class="fa fa-arrow-right"></i></button>
                                    </div>
                                </form>
                            </div>

                            <div class="step-content" id="step-2">
                                <div class="alert alert-success">✅ Đã tạo thông tin Tour thành công! Hãy thêm hình ảnh cho Tour.</div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card p-3 mb-4 bg-light">
                                            <h5>➕ Thêm ảnh mới</h5>
                                            <form id="form-step-2" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="tourId" class="hiddenTourId">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <input type="file" name="image" class="form-control-file border p-1 bg-white" accept="image/*" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="text" name="description" class="form-control" placeholder="Mô tả ảnh (Tùy chọn)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button class="btn btn-primary w-100" type="submit" id="btn-upload-img"><i class="fa fa-upload"></i> Tải lên</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <h5>🖼️ Danh sách ảnh vừa tải lên</h5>
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-light">
                                                <tr><th>Ảnh</th><th>Tên file / Mô tả</th></tr>
                                            </thead>
                                            <tbody id="preview-images">
                                                <tr><td colspan="2" class="text-center text-muted">Chưa có ảnh nào.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="button" class="btn btn-success btn-lg" onclick="goToStep(3)">Tiếp tục <i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>

                            <div class="step-content" id="step-3">
                                <div class="alert alert-success">✅ Bước cuối cùng: Hãy tạo một vài lịch khởi hành để khách có thể đặt Tour.</div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card p-3 mb-4 bg-light">
                                            <h5>➕ Thêm lịch khởi hành</h5>
                                            <form id="form-step-3">
                                                @csrf
                                                <input type="hidden" name="tourId" class="hiddenTourId">
                                                <div class="row">
                                                    <div class="col-md-3 form-group">
                                                        <label>Ngày bắt đầu</label>
                                                        <input type="date" name="ngaybatdau" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label>Ngày kết thúc</label>
                                                        <input type="date" name="ngayketthuc" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label>Số chỗ còn</label>
                                                        <input type="number" name="sochocon" id="schedule-slots" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label>Trạng thái</label>
                                                        <select name="trangthai" class="form-control">
                                                            <option value="con_cho">Còn chỗ</option>
                                                            <option value="het_cho">Hết chỗ</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <button class="btn btn-primary" type="submit" id="btn-upload-schedule"><i class="fa fa-plus"></i> Thêm lịch này</button>
                                            </form>
                                        </div>

                                        <h5>📅 Các lịch khởi hành vừa tạo</h5>
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-light">
                                                <tr><th>Bắt đầu</th><th>Kết thúc</th><th>Chỗ còn</th><th>Trạng thái</th></tr>
                                            </thead>
                                            <tbody id="preview-schedules">
                                                <tr><td colspan="4" class="text-center text-muted">Chưa có lịch nào.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="text-center mt-5">
                                    <a href="{{ route('admin.tours') }}" class="btn btn-success btn-lg px-5"><i class="fa fa-check-circle"></i> HOÀN TẤT & QUAY LẠI DANH SÁCH</a>
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

<script>
    let globalTourId = null;

    // 1. CHUYỂN BƯỚC WIZARD
    function goToStep(step) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');
        
        document.querySelectorAll('.wizard-steps li').forEach(el => el.classList.remove('active'));
        for(let i = 1; i <= step; i++) {
            let tab = document.getElementById('tab-step-' + i);
            if(i < step) tab.classList.add('completed');
            if(i === step) tab.classList.add('active');
        }
    }

    // 2. AUTO-GENERATE LỘ TRÌNH DỰA VÀO SỐ NGÀY
    function renderItinerary() {
        let days = parseInt(document.getElementById('input-songay').value) || 1;
        let container = document.getElementById('itinerary-container');
        container.innerHTML = ''; 

        for(let day = 1; day <= days; day++) {
            let html = `
                <div class="col-md-12 mb-3">
                    <div class="card border">
                        <div class="card-header bg-light"><strong>Ngày ${day}</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tiêu đề lộ trình</label>
                                <input type="text" name="day-${day}" class="form-control" placeholder="Ví dụ: Ngày ${day} - Khám phá..." required>
                            </div>
                            <div class="form-group">
                                <label>Nội dung chi tiết</label>
                                <textarea name="itinerary-${day}" class="form-control" rows="3" placeholder="Mô tả các hoạt động trong ngày ${day}" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
    }
    
    // Gắn sự kiện khi sửa số ngày
    document.getElementById('input-songay').addEventListener('input', renderItinerary);
    renderItinerary();

    // 3. SUBMIT BƯỚC 1 (THÔNG TIN CHUNG & LỘ TRÌNH)
    document.getElementById('form-step-1').addEventListener('submit', function(e) {
        e.preventDefault();
        let btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang lưu...';

        let formData = new FormData(this);

        // API 1: Lưu thông tin Tour
        fetch("{{ route('admin.add-tours') }}", {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                globalTourId = data.tourId;
                
                // API 2: Lưu Lộ trình (Timeline)
                let timelineData = new FormData();
                timelineData.append('_token', formData.get('_token'));
                timelineData.append('tourId', globalTourId);
                
                let days = parseInt(document.getElementById('input-songay').value) || 1;
                for(let day = 1; day <= days; day++) {
                    timelineData.append(`day-${day}`, formData.get(`day-${day}`));
                    timelineData.append(`itinerary-${day}`, formData.get(`itinerary-${day}`));
                }

                return fetch("{{ route('admin.add-timeline') }}", {
                    method: "POST",
                    body: timelineData,
                    headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
                });
            } else {
                throw new Error(data.message || "Không thể tạo Tour.");
            }
        })
        .then(res => res.json())
        .then(data => {
            // Cập nhật tourId vào form ẩn
            document.querySelectorAll('.hiddenTourId').forEach(i => i.value = globalTourId);
            // Gán sẵn số chỗ tối đa sang bước 3
            document.getElementById('schedule-slots').value = formData.get('number');
            goToStep(2);
        })
        .catch(err => {
            alert("Lỗi: " + err.message);
            btn.disabled = false;
            btn.innerHTML = 'Lưu & Tiếp tục <i class="fa fa-arrow-right"></i>';
        });
    });

    // 4. SUBMIT BƯỚC 2 (TẢI ẢNH)
    document.getElementById('form-step-2').addEventListener('submit', function(e) {
        e.preventDefault();
        let btn = document.getElementById('btn-upload-img');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang tải...';

        fetch("{{ route('admin.upload-image-tour') }}", {
            method: "POST",
            body: new FormData(this),
            headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-upload"></i> Tải lên';
            if(data.success) {
                let tbody = document.getElementById('preview-images');
                if(tbody.querySelector('td.text-muted')) tbody.innerHTML = '';
                
                let imgUrl = `{{ asset('admin/assets/images/gallery-tours') }}/${data.data.filename}`;
                tbody.insertAdjacentHTML('beforeend', `<tr>
                    <td width="150"><img src="${imgUrl}" class="img-fluid border" style="max-height: 80px;"></td>
                    <td>${data.data.description || data.data.filename} <br><span class="badge badge-success">Mới thêm</span></td>
                </tr>`);
                
                this.reset();
            } else {
                alert("Lỗi upload: " + data.message);
            }
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="fa fa-upload"></i> Tải lên'; });
    });

    // 5. SUBMIT BƯỚC 3 (TẢI LỊCH KHỞI HÀNH)
    document.getElementById('form-step-3').addEventListener('submit', function(e) {
        e.preventDefault();
        let btn = document.getElementById('btn-upload-schedule');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang thêm...';
        
        let formData = new FormData(this);

        fetch("{{ route('admin.add-schedule') }}", {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plus"></i> Thêm lịch này';
            if(data.success) {
                let tbody = document.getElementById('preview-schedules');
                if(tbody.querySelector('td.text-muted')) tbody.innerHTML = '';
                
                tbody.insertAdjacentHTML('beforeend', `<tr>
                    <td>${formData.get('ngaybatdau')}</td>
                    <td>${formData.get('ngayketthuc')}</td>
                    <td>${formData.get('sochocon')}</td>
                    <td><span class="badge badge-info">Mới thêm</span></td>
                </tr>`);
            } else {
                alert("Lỗi: " + data.message);
            }
        })
        .catch(err => { btn.disabled = false; btn.innerHTML = '<i class="fa fa-plus"></i> Thêm lịch này'; });
    });
</script>