<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\ToursModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ToursManagementController extends Controller
{
    private $tours;

    public function __construct()
    {
        $this->tours = new ToursModel();
    }

    public function index()
    {
        $title = 'Quản lý Tours';
        $tours = $this->tours->getAllTours();
        return view('admin.tours', compact('title', 'tours'));
    }

    public function pageAddTours()
    {
        $title = 'Thêm Tours';
        return view('admin.add-tours', compact('title'));
    }

    public function editTour(Request $request)
    {
        $tourId = $request->query('tourId', $request->query('id'));

        if ($tourId) {
            return redirect()->route('admin.tour-edit-page', ['tourId' => $tourId]);
        }

        return redirect()->route('admin.tours');
    }

    public function editPage($tourId)
    {
        $title = 'Chỉnh sửa Tour';
        $tourDetails = $this->tours->getTourWithDetails($tourId);

        if (!$tourDetails) {
            abort(404);
        }

        return view('admin.edit-tour', compact('title', 'tourDetails'));
    }

    // [BƯỚC 1]: Lưu thông tin chung của Tour
    public function addTours(Request $request)
    {
        // 1. Validate kiểm tra trùng tên Tour
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:tour,tentour'
        ], [
            'name.unique' => 'Tên tour này đã tồn tại trong hệ thống. Vui lòng nhập tên khác!'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('name')
            ]);
        }

        $name = $request->input('name');
        $departure = $request->input('departure');
        $destination = $request->input('destination');
        $khuvuc = $request->input('domain');
        $songuoitoida = $request->input('number');
        $price_adult = $request->input('price_adult');
        $price_child = $request->input('price_child');
        $songay = $request->input('songay');
        $description = $request->input('description');

        $dataTours = [
            'tentour'      => $name,
            'diemkhoihanh' => $departure,
            'diadiemden'   => $destination,
            'khuvuc'       => $khuvuc,
            'gianguoilon'  => $price_adult,
            'giatreem'     => $price_child,
            'songay'       => $songay,
            'songuoitoida' => $songuoitoida,
            'mota'         => $description,
            'trangthai'    => 'khong_hoat_dong', // Ẩn tour chờ hoàn tất
        ];

        DB::beginTransaction();
        try {
            $createTourId = $this->tours->createTours($dataTours);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo thông tin Tour thành công!',
                'tourId'  => $createTourId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // [BƯỚC 1 PHỤ]: Lưu Lộ trình (Timeline)
    public function addTimeline(Request $request)
    {
        $tourId = $request->tourId;
        $timelines = [];

        foreach ($request->all() as $key => $value) {
            if (preg_match('/^day-(\d+)$/', $key, $matches)) {
                $dayNumber = $matches[1];
                $itineraryKey = "itinerary-{$dayNumber}";
                if ($request->has($itineraryKey)) {
                    $timelines[] = [
                        'tourid'  => $tourId,
                        'tieude'  => $value,
                        'noidung' => $request->input($itineraryKey),
                    ];
                }
            }
        }

        foreach ($timelines as $timeline) {
            $this->tours->addTimeLine($timeline);
        }

        return response()->json(['success' => true, 'message' => 'Đã lưu lộ trình']);
    }

    private function saveGalleryImageWithFallback($image, $filename)
    {
        $destinationPath = public_path('admin/assets/images/gallery-tours/');
        if (!is_dir($destinationPath)) mkdir($destinationPath, 0755, true);

        try {
            Image::make($image->getRealPath())->resize(400, 350)->save($destinationPath . $filename);
        } catch (\Throwable $e) {
            $image->move($destinationPath, $filename);
        }
    }

    // [BƯỚC 2]: Upload ảnh
    public function addImagesTours(Request $request)
    {
        try {
            $image = $request->file('image');
            $tourId = $request->tourId;
            $description = $request->input('description', '');

            if (!$image || !$tourId || !$this->tours->getTour($tourId) || !$image->isValid()) {
                return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 400);
            }

            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName) . '_' . time() . '.' . $extension;

            $this->saveGalleryImageWithFallback($image, $filename);

            $maxOrder = DB::table('hinhanhtour')->where('tourid', $tourId)->max('thutuhienthi') ?? 0;

            $dataUpload = [
                'tourid'       => $tourId,
                'urlanh'       => $filename,
                'tenanh'       => $originalName,
                'motaanh'      => $description,
                'thutuhienthi' => $maxOrder + 1
            ];

            if ($this->tours->uploadImages($dataUpload)) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'data'    => ['filename' => $filename, 'description' => $description]
                    ], 200);
                }
                return redirect()->back()->with('success', 'Tải ảnh lên thành công');
            }

            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Lỗi lưu dữ liệu'], 500);
            return redirect()->back()->with('error', 'Lỗi lưu dữ liệu');

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // [BƯỚC 3]: Thêm lịch khởi hành
    public function addSchedule(Request $request)
    {
        try {
            $tourId = $request->tourId;
            $ngaybatdau = $request->ngaybatdau;
            $ngayketthuc = $request->ngayketthuc;
            $sochocon = $request->sochocon;
            $trangthai = $request->trangthai ?? 'con_cho';

            $tour = $this->tours->getTour($tourId);
            if (!$tour) return response()->json(['success' => false, 'message' => 'Không tìm thấy Tour'], 404);

            // 1. Kiểm tra số ngày có khớp không
            // Lấy khoảng cách giữa 2 ngày. Ví dụ: đi 01/05 về 03/05 -> Tính là 3 ngày.
            $requestedDays = Carbon::parse($ngaybatdau)->diffInDays(Carbon::parse($ngayketthuc)) + 1;
            
            if ((int) $tour->songay !== (int) $requestedDays) {
                $message = 'Lỗi: Ngày kết thúc không hợp lệ. Lịch khởi hành này phải kéo dài đúng ' . $tour->songay . ' ngày!';
                if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $message], 422);
                return redirect()->back()->withInput()->with('error', $message);
            }

            // 2. Kiểm tra trùng ngày khởi hành (Chặn lỗi Duplicate SQL 1062)
            $exists = DB::table('lichkhoihanh')
                ->where('tourid', $tourId)
                ->where('ngaybatdau', $ngaybatdau)
                ->exists();

            if ($exists) {
                $message = 'Lỗi: Tour này đã có lịch khởi hành vào ngày ' . date('d/m/Y', strtotime($ngaybatdau)) . '. Vui lòng chọn ngày khác!';
                if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $message], 422);
                return redirect()->back()->withInput()->with('error', $message);
            }

            // 3. Tiến hành lưu lịch
            $data = [
                'tourid'      => $tourId,
                'ngaybatdau'  => $ngaybatdau,
                'ngayketthuc' => $ngayketthuc,
                'sochocon'    => $sochocon,
                'trangthai'   => $trangthai
            ];

            if ($this->tours->addSchedule($data)) {
                // Sửa lỗi "Data truncated": Bảng tour dùng chữ 'hoat_dong', KHÔNG dùng 'con_cho'
                $this->tours->updateTour($tourId, ['trangthai' => 'hoat_dong']);
                
                if ($request->wantsJson()) return response()->json(['success' => true, 'message' => 'Thêm lịch thành công']);
                return redirect()->back()->with('success', 'Thêm lịch khởi hành thành công');
            }
            
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Lỗi lưu lịch'], 500);
            return redirect()->back()->with('error', 'Lỗi lưu lịch');

        } catch (\Exception $e) {
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // Quản lý Sửa Tour (Thông tin chung & lộ trình)
    public function updateTour(Request $request, $tourId = null)
    {
        if ($tourId && !$request->has('tourId')) {
            $request->merge(['tourId' => $tourId]);
        }
        $tourId = $request->tourId;
        $requestedDays = max(1, (int) $request->input('songay', 1));
        
        $dataTours = [
            'tentour'      => $request->input('name'),
            'mota'         => $request->input('description'),
            'songuoitoida' => $request->input('number'),
            'gianguoilon'  => $request->input('price_adult'),
            'giatreem'     => $request->input('price_child'),
            'diadiemden'   => $request->input('destination'),
            'khuvuc'       => $request->input('domain'),
            'songay'       => $requestedDays,
        ];

        if ($request->has('departure')) {
            $dataTours['diemkhoihanh'] = $request->input('departure');
        }

        DB::beginTransaction();
        try {
            $this->tours->updateTour($tourId, $dataTours);
            
            $timelines = $request->input('timeline', []);
            if (is_array($timelines) && count($timelines) > 0) {
                $this->tours->deleteData($tourId, 'lichtrinh');
                for ($day = 1; $day <= $requestedDays; $day++) {
                    $title = trim((string) data_get($timelines, $day . '.title', ''));
                    $itinerary = trim((string) data_get($timelines, $day . '.itinerary', ''));
                    if ($title === '' || $itinerary === '') continue;

                    $this->tours->addTimeLine([
                        'tourid'  => $tourId, 
                        'tieude'  => $title, 
                        'noidung' => $itinerary
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('admin.tour-edit-page', ['tourId' => $tourId])->with('success', 'Sửa thông tin thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // Xóa Tour
    public function deleteTour(Request $request)
    {
        $result = $this->tours->deleteTour($request->tourId);
        if ($result['success']) {
            return redirect()->route('admin.tours')->with('success', $result['message']);
        }
        return redirect()->route('admin.tours')->with('error', $result['message']);
    }

    // Các hàm phụ trợ
    public function updateScheduleStatus(Request $request, $tourId, $lichId) 
    {
        $this->tours->updateScheduleStatus($lichId, ['trangthai' => $request->trangthai]);
        return redirect()->back()->with('success', 'Đã cập nhật trạng thái lịch.');
    }

    public function deleteImage(Request $request) 
    {
        $this->tours->deleteImage($request->hinhId);
        return redirect()->back()->with('success', 'Hình ảnh đã được xóa');
    }

    public function deleteSchedule(Request $request) 
    {
        $this->tours->deleteSchedule($request->lichId);
        return redirect()->back()->with('success', 'Lịch khởi hành đã được xóa');
    }
}
