@foreach ($tours as $tour)
    <tr>
        <td>{{ $tour->tentour }}</td>
        <td>{{ $tour->songay }} ngày</td>
        <td>{!! Str::limit($tour->mota, 50) !!}</td>
        <td>{{ $tour->songuoitoida }}</td>
        <td>{{ number_format($tour->gianguoilon, 0, ',', '.') }} VNĐ</td>
        <td>{{ number_format($tour->giatreem, 0, ',', '.') }} VNĐ</td>
        <td>{{ $tour->diadiemden }}</td>
        <td>
            <span class="badge badge-{{ $tour->trangthai === 'hoat_dong' ? 'success' : 'danger' }}">
                {{ $tour->trangthai === 'hoat_dong' ? 'Hoạt động' : 'Không hoạt động' }}
            </span>
        </td>
        <td>
            {!! $tour->lichtrinh_list ? '<div class="small lh-sm">' . $tour->lichtrinh_list . '</div>' : '<span class="text-muted">Chưa có lịch</span>' !!}
            <div class="mt-1"><span class="badge badge-primary">{{ $tour->solich ?? 0 }} lịch</span></div>
        </td>
        <td>
            <a href="{{ route('admin.tour-edit-page', ['tourId' => $tour->tourid]) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết và chỉnh sửa">
                <span class="glyphicon glyphicon-edit" style="color: #26B99A; font-size:18px" aria-hidden="true"></span>
            </a>
        </td>
        <td>
            <form method="POST" action="{{ route('admin.delete-tour') }}" onsubmit="return confirm('Bạn có chắc muốn xóa tour này không?');">
                @csrf
                <input type="hidden" name="tourId" value="{{ $tour->tourid }}">
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <span class="glyphicon glyphicon-trash" style="font-size:18px" aria-hidden="true"></span>
                </button>
            </form>
        </td>
    </tr>
@endforeach
