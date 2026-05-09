$(document).ready(function () {
    /********************************************
     * USER MANAGEMENT                          *
     ********************************************/
    $("#btn-active").click(function () {
        var button = $(this);
        let dataAttr = button.data("attr");

        let userId = dataAttr.userId;
        let actionUrl = dataAttr.action;

        let formData = {
            userId: userId,
            _token: $('meta[name="csrf-token"]').attr("content"),
        };

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: formData,
            success: function (response) {
                if (response.success) {
                    button
                        .closest(".profile_view")
                        .find(".brief i")
                        .text("Đã kích hoạt");
                    button.hide();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    });

    $("#btn-ban, #btn-delete, #btn-unban, #btn-restore").click(function () {
        var button = $(this);
        let dataAttr = button.data("attr");

        let userId = dataAttr.userId;
        let status = dataAttr.status;
        let actionUrl = dataAttr.action;

        let formData = {
            userId: userId,
            status: status,
            _token: $('meta[name="csrf-token"]').attr("content"),
        };
        console.log(formData);

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: formData,
            success: function (response) {
                if (response.success) {
                    button
                        .closest(".profile_view")
                        .find(".brief i")
                        .text(response.status);
                    button.parent().find("button").hide(); // Ẩn tất cả các nút
                    if (status === "b") {
                        button.parent().find("#btn-unban").show();
                    } else if (status === "d") {
                        button.parent().find("#btn-restore").show();
                    } else {
                        button
                            .parent()
                            .find("#btn-ban, #btn-delete, #btn-active")
                            .show();
                    }
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    });
    /********************************************
     * TOURS MANAGEMENT                          *
     ********************************************/
    if ($("#description").length) {
        CKEDITOR.replace("description");
    }
    $("#start_date, #end_date").datetimepicker({
        format: "d/m/Y",
        timepicker: false,
    });

    let tourEditState = {
        tourId: null,
    };

    function ensureWizard() {
        if (typeof $.fn.smartWizard === "undefined") {
            return;
        }

        const wizard = $("#edit-tour-modal #wizard");
        if (!wizard.data("wizard-ready")) {
            wizard.smartWizard({ transitionEffect: "slide" });
            wizard.data("wizard-ready", true);
        }
    }

    function formatScheduleDate(dateValue) {
        if (!dateValue) {
            return "";
        }

        return moment(dateValue, ["YYYY-MM-DD", moment.ISO_8601]).format("DD/MM/YYYY");
    }

    function getTourStatusLabel(status) {
        if (status === "hoat_dong") {
            return '<span class="badge badge-success">Hoạt động</span>';
        }

        return '<span class="badge badge-danger">Không hoạt động</span>';
    }

    function getScheduleStatusLabel(status) {
        const map = {
            con_cho: '<span class="badge badge-success">Còn chỗ</span>',
            het_cho: '<span class="badge badge-warning">Hết chỗ</span>',
            huy: '<span class="badge badge-danger">Hủy</span>',
            khong_hoat_dong: '<span class="badge badge-secondary">Không hoạt động</span>',
        };

        return map[status] || '<span class="badge badge-light">N/A</span>';
    }

    function renderImages(images) {
        const tbody = $("#images-tbody");
        tbody.empty();

        if (!images || !images.length) {
            $("#no-images").show();
            return;
        }

        $("#no-images").hide();

        images.forEach(function (image, index) {
            const imageName = image.tenanh || image.urlanh || image.imageURL || "";
            const imageFile = image.urlanh || image.imageURL || "";
            const imageDescription = image.motaanh || "";

            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${imageName}</td>
                    <td>${imageDescription || '<span class="text-muted">Không có mô tả</span>'}</td>
                    <td>${image.thutuhienthi || index + 1}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger delete-image-btn" data-hinhid="${image.hinhid}">Xóa</button>
                    </td>
                </tr>
            `;

            tbody.append(row);
        });
    }

    function renderSchedules(schedules) {
        const tbody = $("#schedules-tbody");
        tbody.empty();

        if (!schedules || !schedules.length) {
            $("#no-schedules").show();
            return;
        }

        $("#no-schedules").hide();

        schedules.forEach(function (schedule, index) {
            const row = `
                <tr>
                    <td>${schedule.lichid}</td>
                    <td>${formatScheduleDate(schedule.ngaybatdau)}</td>
                    <td>${formatScheduleDate(schedule.ngayketthuc)}</td>
                    <td>${schedule.sochocon}</td>
                    <td>${getScheduleStatusLabel(schedule.trangthai)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger delete-schedule-btn" data-lichid="${schedule.lichid}">Xóa</button>
                    </td>
                </tr>
            `;

            tbody.append(row);
        });
    }

    function fillTourForm(tour, schedules) {
        $("input[name='name']").val(tour.tentour || "");
        $("input[name='destination']").val(tour.diadiemden || "");
        $("select[name='domain']").val(tour.khuvuc || "");
        $("input[name='number']").val(tour.songuoitoida || "");
        $("input[name='price_adult']").val(tour.gianguoilon || "");
        $("input[name='price_child']").val(tour.giatreem || "");

        const startDate = schedules && schedules.length ? schedules[0].ngaybatdau : null;
        const endDate = schedules && schedules.length ? schedules[schedules.length - 1].ngayketthuc : null;

        $("#start_date").val(formatScheduleDate(startDate));
        $("#end_date").val(formatScheduleDate(endDate));

        if (typeof CKEDITOR !== "undefined" && CKEDITOR.instances["description"]) {
            CKEDITOR.instances["description"].setData(tour.mota || "");
        }

        $("#edit-tour-modal .hiddenTourId").val(tour.tourid || tour.id || "");
    }

    function refreshTourDetails(tourId) {
        $.ajax({
            type: "GET",
            url: $(".edit-tour[data-tourid='" + tourId + "']").data("urledit"),
            data: { tourId: tourId, _token: $("meta[name='csrf-token']").attr("content") },
            success: function (response) {
                if (!response.success) {
                    toastr.error(response.message || "Không tải được dữ liệu tour.");
                    return;
                }

                const tour = response.tour || {};
                const images = response.images || [];
                const schedules = response.schedules || [];

                fillTourForm(tour, schedules);
                renderImages(images);
                renderSchedules(schedules);
            },
            error: function () {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    }

    $(document).on("click", ".edit-tour", function (e) {
        e.preventDefault();

        const tourId = $(this).data("tourid");
        tourEditState.tourId = tourId;

        ensureWizard();

        $("#edit-tour-modal .hiddenTourId").val(tourId);
        $("#images-tbody").empty();
        $("#schedules-tbody").empty();
        $("#no-images, #no-schedules").hide();

        refreshTourDetails(tourId);
    });

    $(document).on("click", "#upload-image-btn", function () {
        const tourId = $("#edit-tour-modal .hiddenTourId").val();
        const imageInput = $("#image-input")[0];
        const description = $("#image-description").val();

        if (!tourId) {
            toastr.error("Không tìm thấy tour cần cập nhật.");
            return;
        }

        if (!imageInput || !imageInput.files || !imageInput.files.length) {
            toastr.error("Vui lòng chọn ảnh.");
            return;
        }

        const formData = new FormData();
        formData.append("tourId", tourId);
        formData.append("image", imageInput.files[0]);
        formData.append("description", description || "");
        formData.append("_token", $("meta[name='csrf-token']").attr("content"));

        $.ajax({
            url: "/admin/tours/add-image",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message || "Đã tải ảnh lên.");
                    $("#image-input").val("");
                    $("#image-description").val("");
                    refreshTourDetails(tourId);
                } else {
                    toastr.error(response.message || "Không tải được ảnh.");
                }
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || "Không tải được ảnh.");
            },
        });
    });

    $(document).on("click", "#add-schedule-btn", function () {
        const tourId = $("#edit-tour-modal .hiddenTourId").val();
        const ngaybatdau = $("#schedule-start").val();
        const ngayketthuc = $("#schedule-end").val();
        const sochocon = $("#schedule-slots").val();
        const trangthai = $("#schedule-status").val();

        if (!tourId) {
            toastr.error("Không tìm thấy tour cần cập nhật.");
            return;
        }

        if (!ngaybatdau || !ngayketthuc || !sochocon || !trangthai) {
            toastr.error("Vui lòng nhập đầy đủ thông tin lịch khởi hành.");
            return;
        }

        if (ngayketthuc < ngaybatdau) {
            toastr.error("Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.");
            return;
        }

        $.ajax({
            url: "/admin/tours/add-schedule",
            method: "POST",
            data: {
                tourId: tourId,
                ngaybatdau: ngaybatdau,
                ngayketthuc: ngayketthuc,
                sochocon: sochocon,
                trangthai: trangthai,
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message || "Đã thêm lịch.");
                    $("#schedule-start").val("");
                    $("#schedule-end").val("");
                    $("#schedule-slots").val("");
                    $("#schedule-status").val("con_cho");
                    refreshTourDetails(tourId);
                } else {
                    toastr.error(response.message || "Không thêm được lịch.");
                }
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || "Không thêm được lịch.");
            },
        });
    });

    $(document).on("click", ".delete-image-btn", function () {
        const hinhId = $(this).data("hinhid");
        const tourId = $("#edit-tour-modal .hiddenTourId").val();

        $.ajax({
            url: "/admin/tours/delete-image",
            method: "POST",
            data: {
                hinhId: hinhId,
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message || "Đã xóa ảnh.");
                    refreshTourDetails(tourId);
                } else {
                    toastr.error(response.message || "Không xóa được ảnh.");
                }
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || "Không xóa được ảnh.");
            },
        });
    });

    $(document).on("click", ".delete-schedule-btn", function () {
        const lichId = $(this).data("lichid");
        const tourId = $("#edit-tour-modal .hiddenTourId").val();

        $.ajax({
            url: "/admin/tours/delete-schedule",
            method: "POST",
            data: {
                lichId: lichId,
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message || "Đã xóa lịch.");
                    refreshTourDetails(tourId);
                } else {
                    toastr.error(response.message || "Không xóa được lịch.");
                }
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || "Không xóa được lịch.");
            },
        });
    });

    $("#edit-tour-modal").on("hidden.bs.modal", function () {
        $("#images-tbody").empty();
        $("#schedules-tbody").empty();
        $("#no-images, #no-schedules").show();
        $("#image-input").val("");
        $("#image-description").val("");
        $("#schedule-start, #schedule-end, #schedule-slots").val("");
        $("#schedule-status").val("con_cho");
        $("#start_date").val("");
        $("#end_date").val("");
        $("#edit-tour-modal .hiddenTourId").val("");
        tourEditState.tourId = null;
    });

    $(document).on("click", ".delete-tour", function (e) {
        e.preventDefault();

        const tourId = $(this).data("tourid");
        const urlDelete = $(this).data("urldelete");

        $.ajax({
            type: "POST",
            url: urlDelete,
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                tourId: tourId,
            },
            success: function (response) {
                if (response.success) {
                    $("#tbody-listTours").html(response.data);
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    });

    $(document).on("click", ".delete-tour", function (e) {
        e.preventDefault();
        var tourId = $(this).data("tourid");
        var urlDelete = $(this).attr("href");

        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            type: "POST",
            url: urlDelete,
            data: {
                _token: csrfToken,
                tourId: tourId,
            },
            success: function (response) {
                if (response.success) {
                    $("#tbody-listTours").html(response.data);
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    });

    /********************************************
     * ADD TOURS                              *
     ********************************************/

    if ($(".add-tours").length) {

    let timelineCounter = 1;
    let maxTimelineDays;

    $(document).on("dataUpdated", function (event, daysDifference) {
        maxTimelineDays = daysDifference;
    });
    // Hàm thêm một timeline entry mới
    function addTimelineEntry() {
        // Kiểm tra nếu số lượng timeline entries đã đạt giới hạn
        console.log(maxTimelineDays);

        if (timelineCounter > maxTimelineDays) {
            toastr.error(`Không thể thêm quá ${maxTimelineDays} ngày.`);
            return;
        }
        const timelineEntry = `
                <div class="timeline-entry" id="timeline-entry-${timelineCounter}">
                    <label for="day-${timelineCounter}">Ngày ${timelineCounter}</label>
                    <input type="text" class="form-control" id="day-${timelineCounter}" name="day-${timelineCounter}" placeholder="Ngày thứ..." required>
                    
                    <label for="itinerary-${timelineCounter}" style="margin-top: 10px; display: block;">Lộ trình:</label>
                    <textarea id="itinerary-${timelineCounter}" name="itinerary-${timelineCounter}" required></textarea>
                    
                    <button type="button" class="btn btn-round btn-danger remove-btn" data-id="${timelineCounter}">Xóa Timeline này</button>
                </div>
            `;

        // Thêm vào div#step-3
        $("#step-3").append(timelineEntry);

        // Khởi tạo CKEditor cho textarea vừa thêm
        if ($(`#itinerary-${timelineCounter}`).length) {
            CKEDITOR.replace(`itinerary-${timelineCounter}`);
        }

        timelineCounter++;
    }

    // Xử lý khi nhấn nút thêm timeline
    $("#step-3").on("click", "#add-timeline", function () {
        addTimelineEntry();
    });

    // Xử lý khi nhấn nút xóa timeline
    $("#step-3").on("click", ".remove-btn", function () {
        const id = $(this).data("id");

        $(`#timeline-entry-${id}`).remove(); // Xóa div chứa timeline entry
    });

    // Thêm nút thêm timeline vào div#step-3
    const addButton = `<button type="button" id="add-timeline" class="btn btn-round btn-info" style="margin-top: 20px;">Thêm Timeline</button>`;
    $("#step-3").append(addButton);

    // Thêm timeline đầu tiên khi trang tải xong
    addTimelineEntry();

    $(".add-tours #wizard .buttonFinish").on("click", function () {
        // Lấy form cần kiểm tra
        const form = $("#timeline-form")[0]; // Chuyển đổi về DOM element để sử dụng checkValidity()

        // Kiểm tra tính hợp lệ của form
        if (form.checkValidity()) {
            // Form hợp lệ -> Gửi form
            $("#timeline-form").submit();
        } else {
            // Form không hợp lệ -> Hiện toastr
            toastr.error("Vui lòng điền đầy đủ thông tin trong form!");

            // Kích hoạt kiểm tra lỗi trên form để hiển thị lỗi HTML5
            form.reportValidity();
        }
    });
    }

    /********************************************
     * BOOKING MANAGEMENT                          *
     ********************************************/
    $(document).on("click", ".confirm-booking", function (e) {
        e.preventDefault();

        const bookingId = $(this).data("bookingid");
        const urlConfirm = $(this).data("urlconfirm");
        console.log("Booking ID:", bookingId);
        console.log("urlConfirm:", urlConfirm);

        // Thực hiện các hành động khác, ví dụ gọi AJAX
        $.ajax({
            url: urlConfirm,
            method: "POST",
            data: {
                bookingId: bookingId,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    $("#tbody-booking").html(response.data);
                    $(".confirm-booking").remove();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (error) {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    });

    $(document).on("click", ".finish-booking", function (e) {
        e.preventDefault();

        const bookingId = $(this).data("bookingid");
        const urlFinish = $(this).data("urlfinish");
        console.log("Booking ID:", bookingId);
        console.log("urlFinish:", urlFinish);

        // Thực hiện các hành động khác, ví dụ gọi AJAX
        $.ajax({
            url: urlFinish,
            method: "POST",
            data: {
                bookingId: bookingId,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    $("#tbody-booking").html(response.data);
                    $(".finish-booking").remove();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (error) {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    });

    
    /********************************************
     * BOOKING INVOICE                          *
     ********************************************/
    $("#send-pdf-btn").click(function () {
        // Lấy bookingId và email từ button
        const bookingId = $(this).data("bookingid");
        const email = $(this).data("email");
        const urlSendPdf = $(this).data("urlsendmail");

        // Gửi AJAX request
        $.ajax({
            url: urlSendPdf,
            type: "POST",
            data: {
                bookingId: bookingId,
                email: email,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                toastr.warning("Đang gửi mail!!!");
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Đã xảy ra lỗi khi gửi email. Vui lòng thử lại!");
                console.error(xhr.responseText); // Log lỗi chi tiết trong console
            },
        });
    });
    $(document).on("click", "#received-money", function (e) {
        e.preventDefault();

        const bookingId = $(this).data("bookingid");
        const urlPaid = $(this).data("urlpaid");
        console.log("Booking ID:", bookingId);
        console.log("url:", urlPaid);

        // Thực hiện các hành động khác, ví dụ gọi AJAX
        $.ajax({
            url: urlPaid,
            method: "POST",
            data: {
                bookingId: bookingId,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    $("#received-money").remove();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (error) {
                toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
            },
        });
    });

    /********************************************
     * CONTACT MANAGEMENT                       *
     ********************************************/
    $(".contact-item").click(function (e) {
        e.preventDefault();
        $(".mail_view").show();

        // Lấy dữ liệu từ các thuộc tính data-*
        var fullName = $(this).data("name");
        var email = $(this).data("email");
        var message = $(this).data("message");
        var contactId = $(this).data("contactid");

        $(".mail_view .inbox-body .sender-info strong").text(fullName);
        $(".mail_view .inbox-body .sender-info span").text("(" + email + ")");
        $(".mail_view .view-mail p").text(message);

        // Thêm thuộc tính data-email vào button
        $(".send-reply-contact").attr("data-email", email);
        $(".send-reply-contact").attr("data-contactid", contactId);
    });

    if ($("#editor-contact").length) {
        CKEDITOR.replace("editor-contact");
    }

    $(document).on("click", ".send-reply-contact", function (e) {
        e.preventDefault();

        // Lấy thông tin từ nút gửi
        var email = $(this).attr("data-email");
        var contactId = $(this).attr("data-contactid");
        var editorContent = CKEDITOR.instances["editor-contact"].getData();

        var urlReply = $(this).data("url");

        if (!email) {
            toastr.error("Không có địa chỉ email để gửi.");
            return;
        }

        // Gửi AJAX request
        $.ajax({
            url: urlReply,
            type: "POST",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // CSRF Token
            },
            data: {
                contactId: contactId,
                email: email,
                message: editorContent,
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Xóa element contact-item sau khi phản hồi thành công
                    $(
                        ".contact-item[data-contactid='" + contactId + "']"
                    ).remove();
                    $(".mail_view").hide();
                    CKEDITOR.instances["editor-contact"].setData(""); // Xóa nội dung CKEditor
                    $("#editor-contact").empty(); // Dọn sạch nội dung div nếu cần
                    $(".compose").slideToggle();

                    $(this)
                        .removeAttr("data-email")
                        .removeAttr("data-contactid");
                }
            },
            error: function (xhr) {
                alert("Đã xảy ra lỗi khi gửi email. Vui lòng thử lại.");
            },
        });
    });

    /********************************************
     * LOGIN ADMIN                             *
     ********************************************/
    $("#formLoginAdmin").on("submit", function (e) {
        const username = $("#username").val();
        const password = $("#password").val();

        // Biểu thức chính quy an toàn
        const sqlInjectionPattern = /['";=\\-]/;

        // Validate username
        if (sqlInjectionPattern.test(username)) {
            toastr.error("Tên tài khoản chứa ký tự không hợp lệ!");
            e.preventDefault(); // Ngăn form submit
            return false;
        }

        // Validate password
        if (sqlInjectionPattern.test(password)) {
            toastr.error("Mật khẩu chứa ký tự không hợp lệ!");
            e.preventDefault(); // Ngăn form submit
            return false;
        }

        // Đảm bảo mật khẩu có ít nhất 6 ký tự
        if (password.length < 6) {
            toastr.error("Mật khẩu phải có ít nhất 6 ký tự!");
            e.preventDefault(); // Ngăn form submit
            return false;
        }
    });

    /********************************************
     * ADMIN MANAGEMENT                        *
     ********************************************/

    $("#formProfileAdmin").on("submit", function (e) {
        e.preventDefault(); 

        var name = $("#fullName").val().trim();
        var password = $("#password").val().trim();
        var email = $("#email").val().trim();
        var address = $("#address").val().trim();

        var isValid = true;

        if (password === "" || password.length < 6) {
            isValid = false;
            toastr.error("Mật khẩu phải có ít nhất 6 ký tự.");
        }

        var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (!emailPattern.test(email)) {
            isValid = false;
            toastr.error("Email không hợp lệ.");
        }

        if (address === "") {
            isValid = false;
            toastr.error("Vui lòng nhập địa chỉ.");
        }

        if (isValid) {
            $.ajax({
                url: $(this).attr('action'), 
                method: "POST",
                data: {
                    fullName: name,
                    password: password,
                    email: email,
                    address: address,
                    '_token': $('meta[name="csrf-token"]').attr('content') 
                },
                success: function (response) {
                    if(response.success){
                        toastr.success("Cập nhật thành công!");
                        $('#nameAdmin').text(response.data.fullName);
                        $('#emailAdmin').text(response.data.email);
                        $('#addressAdmin').text(response.data.address);
                    }else{
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    // Xử lý lỗi nếu có
                    toastr.error("Đã có lỗi xảy ra. Vui lòng thử lại!");
                },
            });
        }
    });
    //Update avatar
    $("#avatarAdmin").on("change", function () {
        const file = event.target.files[0];

        if (file) {
            // Hiển thị ảnh vừa chọn trước khi gửi lên server
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#avatarAdminPreview").attr("src", e.target.result);
                $('#navbarDropdown img').attr("src", e.target.result);
                $('.profile_img').attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
            var url = $('#btn_avatar').attr('action');
            // Tạo FormData để gửi file qua AJAX
            const formData = new FormData();
            formData.append("avatarAdmin", file);

            console.log(formData);

            // // Gửi AJAX đến server
            $.ajax({
                url: url,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error("Có lỗi xảy ra. Vui lòng thử lại sau.");
                },
            });
        }
    });
    /********************************************
     * DASHBOARD                                  *
     ********************************************/
});
