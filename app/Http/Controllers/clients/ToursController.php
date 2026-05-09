<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Tours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToursController extends Controller
{

    private $tours;


    public function __construct()
    {
        $this->tours = new Tours();
    }
      // Destination Địa điểm
    public function destination()
    {
        $title = 'Điểm đến';
        $tours = $this->tours->getAllTours(9);
        return view('clients.destination', compact('title','tours'));
    }
    public function index(Request $request)
    {
        $title = 'Tours';
        $tours = $this->tours->getAllTours(9);
        $domain = $this->tours->getDomain();
        // dd($tours);
        $domainsCount = [
            'mien_bac' => optional($domain->firstWhere('domain', 'b'))->count,
            'mien_trung' => optional($domain->firstWhere('domain', 't'))->count,
            'mien_nam' => optional($domain->firstWhere('domain', 'n'))->count,
        ];

        // Kiểm tra nếu yêu cầu là AJAX
        if ($request->ajax()) {
            return response()->json([
                'tours' => view('clients.partials.filter-tours', compact('tours'))->render(),
            ]);
        }
        $toursPopular = $this->tours->toursPopular(2);

        return view('clients.tours', compact('title', 'tours', 'domainsCount','toursPopular'));
    }

    //Xử lý filter tours
    public function filterTours(Request $req)
    {

        $conditions = [];
        $sorting = [];

        // Handle price filter
        if ($req->filled('minPrice') && $req->filled('maxPrice')) {
            $minPrice = $req->minPrice;
            $maxPrice = $req->maxPrice;
            $conditions[] = ['priceAdult', '>=', $minPrice];
            $conditions[] = ['priceAdult', '<=', $maxPrice];
        }

        // Handle domain filter
        if ($req->filled('domain')) {
            $domain = $req->domain;
            $conditions[] = ['domain', '=', $domain];
        }

        // Handle star rating filter
        if ($req->filled('star')) {
            $star = (int) $req->star;
            $conditions[] = ['averageRating', '=', $star];
        }

        // Handle duration filter
        if ($req->filled('time')) {
            $duration = $req->time;
            $daysMap = [
                '3n2d' => 3,
                '4n3d' => 4,
                '5n4d' => 5,
            ];
            if (isset($daysMap[$duration])) {
                $conditions[] = ['songay', '=', $daysMap[$duration]];
            }
        }

        // Handle orderby filter
        if ($req->filled('sorting')) {
            $sortingOption = trim($req->sorting); // Remove any whitespace

            // Handle sorting options
            if ($sortingOption == 'new') {
                $sorting = ['tourId', 'DESC']; // Sort by creation date, newest first
            } elseif ($sortingOption == 'old') {
                $sorting = ['tourId', 'ASC']; // Sort by creation date, oldest first
            } elseif ($sortingOption == "hight-to-low") {
                $sorting = ['priceAdult', 'DESC']; // Sort by price in descending order
            } elseif ($sortingOption == "low-to-high") {
                $sorting = ['priceAdult', 'ASC']; // Sort by price in ascending order
            }
        }

        // dd($conditions);
        $tours = $this->tours->filterTours($conditions, $sorting);

        // If not paginated, simulate pagination
        if (!$tours instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            // Create a fake paginator (pagination for non-paginated collection)
            $tours = new \Illuminate\Pagination\LengthAwarePaginator(
                $tours, // Collection
                count($tours), // Total items
                9, // Per page
                1, // Current page
                ['path' => url()->current()] // Path for pagination
            );
        }

        return view('clients.partials.filter-tours', compact('tours'));

    }

    // Show tour detail page
    public function show($id)
    {
        $tourDetail = $this->tours->getTourDetail($id);
        
        if (!$tourDetail) {
            abort(404);
        }
        
        // Get reviews
        $reviews = $this->tours->getReviews($id);
        
        // Get similar tours (same region)
        $similarTourIds = DB::table('tour')
            ->where('khuvuc', $tourDetail->khuvuc)
            ->where('tourid', '!=', $id)
            ->limit(3)
            ->pluck('tourid')
            ->toArray();
        $tourRecommendations = $this->tours->toursRecommendation($similarTourIds);
        
        // Check if user is logged in for review form display
        $checkDisplay = session()->has('tendangnhap') ? '' : 'd-none';
        
        // Average star rating (rounded)
        $avgStar = (int) round($tourDetail->rating);
        
        $title = $tourDetail->title;
        
        return view('clients.tour-detail', compact(
            'tourDetail',
            'reviews',
            'tourRecommendations',
            'checkDisplay',
            'avgStar',
            'title'
        ));
    }
}
