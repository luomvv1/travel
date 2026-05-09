<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Tours;
use App\Models\clients\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class MyTourController extends Controller
{
    private $tours;
    private $user;
    protected $table = 'nguoidung';

    public function __construct()
    {
        
        $this->tours = new Tours();
        $this->user = new User();
        
    }

    public function index()
    {
        $title = 'Tours đã đặt';
        $nguoidungid = session('ndid');
        if (! $nguoidungid) {
            return redirect()->route('login');
        }

        // Tự động kiểm tra các don vnpay quá 15 phút mà chưa thanh toán -> Hủy
        $expiryTime = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->subMinutes(15);
        $expiredVnpayBookings = DB::table('dattour as dt')
            ->join('thanhtoan as tt', 'dt.dtid', '=', 'tt.dtid')
            ->where('dt.ndid', $nguoidungid)
            ->where('tt.phuongthuc', 'vnpay')
            ->whereIn('dt.trangthai', ['cho_xac_nhan', 'da_xac_nhan'])
            ->where('tt.trangthai', 'cho_xu_ly')
            ->where('dt.ngaytao', '<', $expiryTime)
            ->select('dt.*')
            ->get();

        foreach ($expiredVnpayBookings as $bookingCheck) {
            DB::beginTransaction();
            try {
                $return_quantity = (int) $bookingCheck->songuoilon + (int) $bookingCheck->sotreem;
                DB::table('lichkhoihanh')->where('lichid', $bookingCheck->lichid)->increment('sochocon', $return_quantity);
                DB::table('dattour')->where('dtid', $bookingCheck->dtid)->update(['trangthai' => 'da_huy']);
                DB::table('thanhtoan')->where('dtid', $bookingCheck->dtid)->update(['trangthai' => 'that_bai']);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }

       // 3. Lấy thông tin tours
        $myTours = $this->user->getMyTours($nguoidungid);
        if ($nguoidungid) {
            // Gọi API Python để lấy danh sách tour được gợi ý cho từng người dùng 
            try {
                $apiUrl = 'http://127.0.0.1:5555/api/user-recommendations';
                $response = Http::get($apiUrl, [
                    'user_id' => $nguoidungid
                ]);

                if ($response->successful()) {
                    $tourIds = $response->json('recommended_tours');
                    $tourIds = array_slice($tourIds, 0, 2);
                } else {
                    $tourIds = [];
                }
            } catch (\Exception $e) {
                // Xử lý lỗi khi gọi API
                $tourIds = [];
                \Log::error('Lỗi khi gọi API liên quan: ' . $e->getMessage());
            }


            $toursPopular = $this->tours->toursRecommendation($tourIds);
            // dd($toursPopular);
        }else {
            $toursPopular = $this->tours->toursPopular(6);
        }

        return view('clients.my-tours', compact('title', 'myTours','toursPopular'));
    }
}
