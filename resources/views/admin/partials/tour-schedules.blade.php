<div id="existing-schedules" class="row mt-3">
    <div class="col-md-12">
        <h5>📅 Danh sách lịch khởi hành</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered" id="schedules-table">
                <thead class="table-info">
                    <tr>
                        <th>Mã lịch</th>
                        <th>Ngày khởi hành</th>
                        <th>Ngày kết thúc</th>
                        <th>Số chỗ còn</th>
                        <th>Trạng thái lịch</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody id="schedules-tbody">
                    <!-- Schedules will be populated here -->
                </tbody>
            </table>
            <div id="no-schedules" class="alert alert-info">Chưa có lịch khởi hành nào. Vui lòng thêm lịch mới.</div>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="row mt-3">
    <div class="col-md-12">
        <h5>➕ Thêm lịch khởi hành mới</h5>
        <form id="add-schedule-form" class="border p-3 rounded bg-light">
            @csrf
            <input type="hidden" name="tourId" class="hiddenTourId">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="schedule-start"><strong>Ngày khởi hành:</strong></label>
                    <input type="date" class="form-control" id="schedule-start" name="ngaybatdau" required>
                    <small class="form-text text-muted">Chọn ngày tour bắt đầu</small>
                </div>
                <div class="form-group col-md-6">
                    <label for="schedule-end"><strong>Ngày kết thúc:</strong></label>
                    <input type="date" class="form-control" id="schedule-end" name="ngayketthuc" required>
                    <small class="form-text text-muted">Chọn ngày tour kết thúc</small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="schedule-slots"><strong>Số chỗ còn:</strong></label>
                    <input type="number" class="form-control" id="schedule-slots" name="sochocon" min="1" placeholder="20" required>
                    <small class="form-text text-muted">Số chỗ trống có sẵn</small>
                </div>
                <div class="form-group col-md-6">
                    <label for="schedule-status"><strong>Trạng thái lịch:</strong></label>
                    <select class="form-control" id="schedule-status" name="trangthai" required>
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="con_cho" selected>Còn chỗ</option>
                        <option value="het_cho">Hết chỗ</option>
                        <option value="sap_dien_ra">Sắp diễn ra</option>
                        <option value="hoan_thanh">Hoàn thành</option>
                        <option value="huy">Hủy</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-success btn-sm" id="add-schedule-btn">
                    <i class="glyphicon glyphicon-plus"></i> Thêm lịch khởi hành
                </button>
            </div>
        </form>
    </div>
</div>
