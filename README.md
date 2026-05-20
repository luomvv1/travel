# Website Quản Lý Và Đặt Tour Du Lịch

Dự án xây dựng website quản lý và đặt tour du lịch bằng **PHP Laravel** và **MySQL/MariaDB**. Hệ thống hỗ trợ khách hàng tra cứu tour, xem chi tiết hành trình, đặt tour, áp dụng mã khuyến mãi, thanh toán và theo dõi lịch sử đặt tour. Đồng thời, hệ thống cung cấp khu vực quản trị để admin quản lý tour, lịch khởi hành, đơn đặt tour, khách hàng, khuyến mãi, thanh toán và thống kê doanh thu.

## Mục Tiêu Dự Án

- Xây dựng website đặt tour trực tuyến có giao diện dễ sử dụng cho khách hàng.
- Hỗ trợ doanh nghiệp du lịch quản lý thông tin tour, lịch khởi hành và đơn đặt tour tập trung.
- Tự động tính tổng tiền khi khách hàng chọn số lượng người lớn, trẻ em và áp dụng mã giảm giá.
- Tích hợp phương thức thanh toán tại văn phòng và thanh toán trực tuyến qua VNPAY/MoMo ở môi trường kiểm thử.
- Cung cấp dashboard giúp admin theo dõi tình hình hoạt động, đơn đặt tour và doanh thu.

## Đối Tượng Sử Dụng

### Khách Hàng

Khách hàng là người truy cập website để tìm kiếm tour du lịch, xem thông tin chi tiết, đặt tour, thanh toán và theo dõi các tour đã đặt.

### Quản Trị Viên

Admin là người quản lý toàn bộ dữ liệu của hệ thống, bao gồm tour, lịch khởi hành, đơn đặt tour, khách hàng, khuyến mãi, thanh toán và thống kê.

## Chức Năng Chính

### 1. Chức Năng Dành Cho Khách Hàng

**Trang chủ**

- Hiển thị banner giới thiệu du lịch.
- Hiển thị danh sách tour nổi bật.
- Cung cấp menu điều hướng đến các trang chính như danh sách tour, điểm đến, đăng nhập và tài khoản cá nhân.

**Tra cứu và xem danh sách tour**

- Xem danh sách các tour đang hoạt động.
- Hiển thị thông tin cơ bản của tour gồm hình ảnh, tên tour, địa điểm, thời gian và giá vé.
- Hỗ trợ lọc/tìm kiếm tour theo nhu cầu như khu vực, điểm đến, thời gian hoặc khoảng giá.

**Xem chi tiết tour**

- Xem hình ảnh tour.
- Xem mô tả chi tiết chuyến đi.
- Xem giá vé người lớn và giá vé trẻ em.
- Xem điểm khởi hành, địa điểm đến, số ngày du lịch và số người tối đa.
- Xem lịch trình theo từng ngày.
- Xem lịch khởi hành còn chỗ.
- Xem đánh giá tour nếu có.

**Đặt tour**

- Chọn lịch khởi hành.
- Chọn số lượng người lớn và trẻ em.
- Hệ thống tự động tính tổng tiền theo giá vé và số lượng khách.
- Kiểm tra số lượng khách không vượt quá số chỗ còn lại.
- Nhập và áp dụng mã khuyến mãi.
- Chọn phương thức thanh toán.
- Tạo đơn đặt tour và lưu thông tin vào cơ sở dữ liệu.

**Thanh toán**

- Hỗ trợ thanh toán tại văn phòng.
- Hỗ trợ thanh toán qua VNPAY ở môi trường sandbox.
- Có cấu trúc hỗ trợ MoMo trong quy trình đặt tour.
- Lưu trạng thái thanh toán để admin theo dõi.

**Quản lý tour đã đặt**

- Xem lịch sử đặt tour.
- Xem trạng thái đơn đặt tour: chờ xác nhận, đã xác nhận, hoàn thành hoặc đã hủy.
- Hủy tour khi còn trong điều kiện cho phép.
- Đánh giá tour sau khi hoàn thành.

**Tài khoản cá nhân**

- Xem thông tin cá nhân.
- Cập nhật họ tên, số điện thoại, địa chỉ.
- Đổi mật khẩu.
- Hỗ trợ đăng nhập thông thường và đăng nhập Google.

### 2. Chức Năng Dành Cho Admin

**Dashboard**

- Thống kê tổng số tour.
- Thống kê tổng số khách hàng.
- Thống kê tổng số đơn đặt tour.
- Thống kê doanh thu.
- Theo dõi đơn đặt mới và tình trạng thanh toán.
- Hiển thị dữ liệu hỗ trợ quản lý hoạt động kinh doanh.

**Quản lý tour**

- Xem danh sách tour.
- Thêm tour mới.
- Cập nhật thông tin tour.
- Xóa hoặc thay đổi trạng thái tour.
- Quản lý hình ảnh tour.
- Quản lý lịch trình tour.
- Quản lý lịch khởi hành và số chỗ còn lại.

Thông tin tour bao gồm:

- Tên tour.
- Mô tả tour.
- Điểm khởi hành.
- Địa điểm đến.
- Khu vực.
- Giá người lớn.
- Giá trẻ em.
- Số ngày du lịch.
- Số người tối đa.
- Trạng thái hoạt động.

**Quản lý đơn đặt tour**

- Xem danh sách đơn đặt tour.
- Xem chi tiết đơn đặt tour.
- Cập nhật trạng thái đơn.
- Xác nhận đơn đặt tour.
- Hoàn thành đơn đặt tour.
- Ghi nhận đã nhận tiền.
- Hủy hoặc hoàn tiền cho đơn phù hợp.
- Gửi hóa đơn/phiếu xác nhận cho khách hàng nếu cấu hình email.

**Quản lý người dùng**

- Xem danh sách tài khoản khách hàng.
- Khóa hoặc mở khóa tài khoản.
- Theo dõi trạng thái hoạt động của người dùng.

**Quản lý khuyến mãi**

- Thêm mã khuyến mãi.
- Thiết lập loại giảm giá theo phần trăm hoặc theo số tiền.
- Thiết lập ngày bắt đầu và ngày kết thúc.
- Bật/tắt trạng thái hoạt động của mã khuyến mãi.
- Xóa mã khuyến mãi khi không còn sử dụng.

**Quản lý thanh toán**

- Theo dõi phương thức thanh toán.
- Theo dõi số tiền thanh toán.
- Theo dõi mã giao dịch.
- Theo dõi trạng thái thanh toán như chờ xử lý, thành công, thất bại hoặc hoàn tiền.

## Quy Trình Nghiệp Vụ Chính

### Quy trình đặt tour của khách hàng

1. Khách hàng đăng nhập vào hệ thống.
2. Khách hàng xem danh sách tour.
3. Khách hàng chọn một tour để xem chi tiết.
4. Khách hàng chọn lịch khởi hành.
5. Khách hàng chọn số lượng người lớn và trẻ em.
6. Hệ thống kiểm tra số chỗ còn lại.
7. Hệ thống tính tổng tiền.
8. Khách hàng nhập mã khuyến mãi nếu có.
9. Hệ thống kiểm tra mã khuyến mãi và tính số tiền giảm.
10. Khách hàng chọn phương thức thanh toán.
11. Hệ thống tạo đơn đặt tour.
12. Admin xác nhận đơn.
13. Khách hàng theo dõi trạng thái đơn trong mục tour đã đặt.

### Quy trình quản lý tour của admin

1. Admin đăng nhập vào trang quản trị.
2. Admin vào chức năng quản lý tour.
3. Admin thêm hoặc cập nhật thông tin tour.
4. Admin thêm hình ảnh, lịch trình và lịch khởi hành.
5. Hệ thống lưu dữ liệu vào các bảng liên quan.
6. Tour được hiển thị cho khách hàng nếu đang ở trạng thái hoạt động.

### Quy trình thanh toán VNPAY

1. Khách hàng đặt tour và chọn thanh toán qua VNPAY.
2. Hệ thống tạo thông tin giao dịch.
3. Hệ thống chuyển khách hàng sang cổng thanh toán VNPAY Sandbox.
4. Sau khi thanh toán, VNPAY chuyển hướng về URL callback của hệ thống.
5. Hệ thống kiểm tra chữ ký, mã phản hồi và cập nhật trạng thái thanh toán.

## Công Nghệ Sử Dụng

### Backend

- PHP 8.2+
- Laravel Framework 12
- Laravel Socialite dùng cho đăng nhập Google

### Database

- MySQL hoặc MariaDB
- Có sử dụng bảng dữ liệu chính, khóa ngoại, view và trigger ở file SQL mẫu.

### Frontend

- Blade Template Engine
- HTML5, CSS3, JavaScript
- jQuery
- Bootstrap
- AOS animation
- Vite
- Tailwind CSS có trong cấu hình build

### Công cụ hỗ trợ

- Composer
- NPM
- phpMyAdmin hoặc MySQL client
- XAMPP/Laragon/WAMP hoặc môi trường PHP tương đương
- VS Code

## Cấu Trúc Thư Mục Quan Trọng

```text
app/
  Http/Controllers/
    admin/        # Controller xử lý chức năng quản trị
    clients/      # Controller xử lý giao diện khách hàng
  Models/
    admin/        # Model phục vụ khu vực admin

database/
  seeders/
    sample_data.sql       # Dữ liệu mẫu cơ bản
    tour_sample_full.sql  # Dữ liệu mẫu đầy đủ cho tour

public/
  admin/          # Tài nguyên giao diện admin
  clients/        # CSS, JS, hình ảnh giao diện khách hàng
  build/          # File build từ Vite

resources/
  views/
    admin/        # Giao diện admin
    clients/      # Giao diện khách hàng

routes/
  web.php         # Khai báo route của hệ thống

travel.sql        # File database MySQL/MariaDB
travel_sqlserver.sql # File chuyển đổi tham khảo cho SQL Server
```

## Cơ Sở Dữ Liệu

Các bảng chính của hệ thống:

- `nguoidung`: lưu thông tin tài khoản khách hàng, admin và hướng dẫn viên nếu có.
- `tour`: lưu thông tin tour du lịch.
- `hinhanhtour`: lưu hình ảnh của từng tour.
- `lichtrinh`: lưu lịch trình chi tiết theo từng tour.
- `lichkhoihanh`: lưu ngày khởi hành, ngày kết thúc và số chỗ còn lại.
- `dattour`: lưu đơn đặt tour của khách hàng.
- `thanhtoan`: lưu thông tin thanh toán.
- `khuyenmai`: lưu mã khuyến mãi.
- `danhgia`: lưu đánh giá tour.
- `yeuthich`: lưu danh sách tour yêu thích.
- `lienhe`: lưu thông tin liên hệ hoặc yêu cầu hỗ trợ.

Một số view hỗ trợ thống kê:

- `vwchitietdattour`: xem chi tiết đơn đặt tour.
- `vwdoanhthutour`: thống kê doanh thu theo tour.
- `vwlichconcho`: xem các lịch khởi hành còn chỗ.

## Hướng Dẫn Cài Đặt

### 1. Yêu cầu môi trường

- PHP >= 8.2
- Composer
- Node.js và NPM
- MySQL hoặc MariaDB
- Web server local như XAMPP, Laragon hoặc dùng `php artisan serve`

### 2. Cài đặt thư viện PHP

```bash
composer install
```

### 3. Cài đặt thư viện frontend

```bash
npm install
```

### 4. Tạo file môi trường

Sao chép file `.env.example` thành `.env`:

```bash
cp .env.example .env
```

Trên Windows PowerShell có thể dùng:

```powershell
Copy-Item .env.example .env
```

Sau đó tạo application key:

```bash
php artisan key:generate
```

### 5. Cấu hình database trong `.env`

Ví dụ:

```env
APP_NAME=Travel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel
DB_USERNAME=root
DB_PASSWORD=
```

Tạo database tên `travel` trong MySQL/MariaDB trước khi import dữ liệu.

### 6. Import dữ liệu

Có thể import file database bằng phpMyAdmin:

1. Tạo database `travel`.
2. Chọn database `travel`.
3. Vào tab Import.
4. Chọn file `travel.sql`.
5. Nhấn Import.

Nếu chỉ muốn thêm dữ liệu tour mẫu, có thể import thêm:

```text
database/seeders/tour_sample_full.sql
```

File này chỉ thêm/cập nhật dữ liệu liên quan đến:

- `tour`
- `hinhanhtour`
- `lichkhoihanh`
- `lichtrinh`

### 7. Chạy project

Chạy Laravel server:

```bash
php artisan serve
```

Chạy Vite trong lúc phát triển:

```bash
npm run dev
```

Hoặc build frontend:

```bash
npm run build
```

Truy cập website:

```text
http://127.0.0.1:8000
```

Truy cập trang admin:

```text
http://127.0.0.1:8000/admin/login
```

## Cấu Hình Thanh Toán

### VNPAY

Các biến môi trường nên cấu hình trong `.env`:

```env
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_TMN_CODE=your_terminal_code
VNPAY_HASH_SECRET=your_hash_secret
VNPAY_RETURN_URL=http://127.0.0.1:8000/vnpay-callback
```

Lưu ý:

- Không đưa secret key thật lên GitHub.
- Với môi trường local, VNPAY sandbox có thể yêu cầu URL hợp lệ hoặc cấu hình merchant đúng từ VNPAY.
- Số tiền gửi sang VNPAY thường được nhân `100` theo chuẩn của cổng thanh toán. Đây là yêu cầu kỹ thuật của VNPAY, không phải lỗi hiển thị giá trên website.

### MoMo

Project có route và giao diện hỗ trợ luồng MoMo ở mức kiểm thử. Khi triển khai thực tế cần bổ sung đầy đủ thông tin merchant, endpoint và xử lý callback theo tài liệu chính thức của MoMo.

## Route Chính

### Khách hàng

- `/`: trang chủ.
- `/login`: đăng nhập/đăng ký.
- `/logout`: đăng xuất.
- `/user-profile`: thông tin cá nhân.
- `/tours`: danh sách tour.
- `/filter-tours`: lọc tour.
- `/tour-detail/{id}`: chi tiết tour.
- `/booking/{id}`: đặt tour.
- `/my-tours`: danh sách tour đã đặt.
- `/tour-booked/{id}`: chi tiết tour đã đặt.
- `/check-promo`: kiểm tra mã khuyến mãi.
- `/vnpay-payment`: tạo thanh toán VNPAY.
- `/vnpay-callback`: nhận kết quả thanh toán VNPAY.

### Admin

- `/admin/login`: đăng nhập admin.
- `/admin/dashboard`: dashboard quản trị.
- `/admin/users`: quản lý người dùng.
- `/admin/booking`: quản lý đơn đặt tour.
- `/admin/tours`: quản lý tour.
- `/admin/tours/add`: thêm tour.
- `/admin/tours/{tourId}/edit-page`: chỉnh sửa tour.
- `/admin/tours/khuyenmai`: quản lý khuyến mãi.

## Giao Diện Chính

### Phía khách hàng

- Trang chủ.
- Trang danh sách tour.
- Trang chi tiết tour.
- Trang đặt tour.
- Trang tour đã đặt.
- Trang thông tin cá nhân.
- Trang đăng nhập/đăng ký.

### Phía admin

- Trang đăng nhập admin.
- Dashboard thống kê.
- Trang quản lý tour.
- Trang thêm tour.
- Trang chỉnh sửa tour.
- Trang quản lý đơn đặt tour.
- Trang chi tiết đơn đặt tour.
- Trang quản lý khách hàng.
- Trang quản lý khuyến mãi.

## Tài Khoản Mẫu

Tài khoản mẫu phụ thuộc vào file SQL được import. Nếu sử dụng file `travel.sql` hoặc dữ liệu mẫu đã có trong project, có thể kiểm tra bảng `nguoidung` để lấy tài khoản admin và khách hàng.

Một số dữ liệu mẫu thường dùng:

- Vai trò admin: `quan_tri`
- Vai trò khách hàng: `khach_hang`
- Trạng thái tài khoản hoạt động: `hoat_dong`

Không nên dùng tài khoản mẫu trong môi trường thật.

## Kiểm Tra Và Bảo Trì

Clear cache khi thay đổi route, view hoặc config:

```bash
php artisan optimize:clear
```

Clear view cache:

```bash
php artisan view:clear
```

Chạy test nếu cần:

```bash
php artisan test
```

Format code bằng Laravel Pint:

```bash
./vendor/bin/pint
```

Trên Windows có thể dùng:

```powershell
vendor\bin\pint
```

## Ghi Chú Phát Triển

- Không commit file `.env` chứa thông tin thật.
- Không commit secret key VNPAY/MoMo.
- Hình ảnh tour nên đặt trong thư mục public phù hợp để website có thể hiển thị.
- Khi thay đổi CSS/JS, nếu trình duyệt chưa cập nhật giao diện, hãy bấm `Ctrl + F5` để tải lại cache.
- Khi thay đổi dữ liệu tour bằng SQL mẫu, nên sao lưu database trước khi import.

## Hướng Phát Triển

- Hoàn thiện thanh toán online ở môi trường production.
- Bổ sung gửi email xác nhận tự động cho khách hàng.
- Thêm chức năng tìm kiếm nâng cao theo ngày, khu vực, mức giá và số người.
- Bổ sung phân quyền chi tiết cho nhiều loại nhân sự.
- Tối ưu giao diện responsive trên điện thoại.
- Bổ sung thống kê doanh thu theo tháng, theo khu vực và theo phương thức thanh toán.
- Thêm chức năng quản lý liên hệ và chăm sóc khách hàng.

## Tác Giả

Dự án được thực hiện phục vụ học tập và báo cáo môn học với đề tài:

**Xây dựng website quản lý và đặt tour du lịch sử dụng PHP & MySQL.**
