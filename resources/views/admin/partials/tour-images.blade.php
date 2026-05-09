<div id="existing-images" class="row mt-3">
    <div class="col-md-12">
        <h5>🖼️ Danh sách hình ảnh tour</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered" id="images-table">
                <thead class="table-info">
                    <tr>
                        <th>STT</th>
                        <th>Tên ảnh</th>
                        <th>Mô tả</th>
                        <th>Thứ tự</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody id="images-tbody">
                    <!-- Images will be populated here -->
                </tbody>
            </table>
            <div id="no-images" class="alert alert-info">Chưa có hình ảnh nào. Vui lòng thêm hình ảnh mới.</div>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="row mt-3">
    <div class="col-md-12">
        <h5>➕ Thêm hình ảnh mới</h5>
        <form id="add-image-form" enctype="multipart/form-data" class="border p-3 rounded bg-light">
            @csrf
            <input type="hidden" name="tourId" class="hiddenTourId">
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="image-input"><strong>Chọn ảnh:</strong></label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image-input" name="image" accept="image/*" required>
                            <label class="custom-file-label" for="image-input">Chọn file ảnh...</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Hỗ trợ: JPG, PNG, GIF, WebP (tối đa 5MB)</small>
                </div>
                <div class="form-group col-md-4">
                    <label for="image-description"><strong>Mô tả ảnh:</strong></label>
                    <input type="text" class="form-control" id="image-description" name="description" placeholder="VD: View chính của tour">
                    <small class="form-text text-muted">Mô tả ngắn về ảnh</small>
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary btn-sm" id="upload-image-btn">
                    <i class="glyphicon glyphicon-upload"></i> Tải ảnh lên
                </button>
            </div>
        </form>
    </div>
</div>
