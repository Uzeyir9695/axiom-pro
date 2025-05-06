<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdService;
use App\Services\PlanService;
use App\Traits\FiltersAds;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class HomeController extends Controller
{
    use FiltersAds;

    public function index(AdService $adService, PlanService $planService)
    {
        // We get latest 10 searched keywords which we cache during ad search by keyword (title).
        // We cache it in Traits/FiltersAds.php
        $keywords = Cache::get('user-searched-keyword-' . auth()->id());
        $planID = auth()->user()?->load('plan')?->plan->id;
        
        return Inertia::render('home/Index', [
            'popular_ads' => $adService->getPopularAds(),
            'featured_ads' => $adService->getFeaturedAds(),
            'flash_sale_ads' => $adService->getFlashSaleAds(),
            'keywords' => $keywords,
            'plans' => $planService->getPlan(),
            'plan_id' => $planID,
        ]);
    }
}
