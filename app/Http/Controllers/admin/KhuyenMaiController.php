<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\KhuyenMaiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KhuyenMaiController extends Controller
{
    private $khuyenmai;

    public function __construct()
    {
        $this->khuyenmai = new KhuyenMaiModel();
    }

    public function index()
    {
        $title = 'Quản lý Khuyến mãi';
        $list_khuyenmai = $this->khuyenmai->getAll();

        return view('admin.khuyenmai', compact('title', 'list_khuyenmai'));
    }

    public function store(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào (Validation)
        $validator = Validator::make($request->all(), [
            'macode' => 'required|unique:khuyenmai,macode',
            'tenkhuyenmai' => 'required',
            'giatri' => 'required|numeric',
            'ngaybatdau' => 'required|date',
            'ngayketthuc' => 'required|date|after_or_equal:ngaybatdau',
        ], [
            'macode.unique' => 'Mã code này đã tồn tại!',
            'ngayketthuc.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $data = [
            'macode' => strtoupper($request->macode),
            'tenkhuyenmai' => $request->tenkhuyenmai,
            'loaigiam' => $request->loaigiam,
            'giatri' => $request->giatri,
            'ngaybatdau' => $request->ngaybatdau,
            'ngayketthuc' => $request->ngayketthuc,
            'solansudungtoida' => $request->solansudungtoida ?? 0,
            'solandasudung' => 0,
            'danghoatdong' => 'Y',
            'ngaytao' => date('Y-m-d H:i:s'),
            'ngaycapnhat' => date('Y-m-d H:i:s'),
        ];

        if ($this->khuyenmai->createData($data)) {
            return redirect()->back()->with('success', 'Thêm mã khuyến mãi thành công!');
        }

        return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại!');
    }

    public function toggleStatus(Request $request)
    {
        $macode = $request->macode;
        $km = $this->khuyenmai->getById($macode);
        
        if ($km) {
            // Đảo ngược trạng thái Y thành N và ngược lại
            $newStatus = ($km->danghoatdong == 'Y') ? 'N' : 'Y';
            $this->khuyenmai->updateData($macode, ['danghoatdong' => $newStatus]);
            return redirect()->back()->with('success', 'Đã cập nhật trạng thái hoạt động.');
        }

        return redirect()->back()->with('error', 'Không tìm thấy mã khuyến mãi.');
    }

    public function destroy(Request $request)
    {
        $macode = $request->macode;
        if ($this->khuyenmai->deleteData($macode)) {
            return redirect()->back()->with('success', 'Đã xóa mã khuyến mãi thành công.');
        }
        return redirect()->back()->with('error', 'Xóa thất bại.');
    }
}