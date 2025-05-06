<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuySellRequest;
use App\Models\BuySell;
use App\Services\FileService;
use App\Services\PaginationService;
use App\Services\PlanService;
use App\Traits\SortsAds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BuySellController extends Controller
{
    use SortsAds;

    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locations = config('cities');

        $ads = BuySell::where('category_slug', 'buy-sell')
            ->filterBySubcategorySlug($request->subcategory_slug)
            ->filterBySubSubcategorySlug($request->subSubcategory_slug)
            ->filterByType($request->type)
            ->filterByNegotiable($request->negotiable)
            ->filterByTitle($request->title)
            ->filterByLocation($request->location, $locations)
            ->filterByPrice($request->priceFrom, $request->priceTo)
            ->filterByAttributes($request->filters)
            ->sortByDate($request->sort_date)
            ->sortByPrice($request->sort_price)
            ->with('addOnsPlan')
            ->get();

        if(!$request->has('sort_date') && !$request->has('sort_price')) {
            $ads = $this->sortAds($ads); // By default, sorts ads by high priority (Top Searched, Boosted and Normal Ads);
        }

        $ads = PaginationService::paginate($ads);

        return Inertia::render('buy-sell/Index',[
            'ads' => Inertia::defer(function () use ($ads) {
                return  $ads;
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuySellRequest $request, PlanService $planService)
    {
        $validData = $request->validated();

        $user = auth()->user()->select('id')->with([
            'planUser' => function ($query) {
                $query->select('user_id', 'ad_active_days', 'images_upload_limit', 'ads_post_limit');
            }
        ])->first();

        $validData['user_id'] =  $user->id;

        $validData = $planService->getAdActiveDaysAndImagesUploadLimit($validData, $user->planUser);

        if ($request->hasFile('images')) {
            $fileData = $this->fileService->createFiles(
                $request,
                'buy-sell',
            );

            // Merge file data with the rest of the validated data
            $validData = array_merge($validData, $fileData);
        }

        DB::transaction(function () use ($planService, $validData, $user, &$buySell) {
            $buySell = BuySell::create($validData);

            if($buySell) {
                $planService->updateUserPlanFeaturesUsage($user->planUser);
            }
        });

        return response()->json(['message' => 'Ad created successfully!', 'ad_id' => $buySell->id, 'model_route' => $buySell->route], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(BuySell $buy_sell)
    {
        // I am omitting the code for brevity
    }

    /**
     * Display the specified resource.
     */
    public function edit(BuySell $buy_sell)
    {
        // I am omitting the code for brevity
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BuySellRequest $request, BuySell $buy_sell)
    {
        $validData = $request->validated();

        if ($request->hasFile('images') || $request->removed_files) {
            $fileData = $this->fileService->updateFiles(
                $request,
                'buy-sell',
            );

            // Merge file data with the rest of the validated data
            $validData = array_merge($validData, $fileData);
        }

        DB::transaction(function () use ($validData, $buy_sell) {
            $buy_sell->update($validData);
        });

        return response()->json(['message' => 'Your ad has been updated.', 'images' => $buy_sell->images]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
