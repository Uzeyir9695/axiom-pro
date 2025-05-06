<?php

namespace App\Services;

use App\Traits\FiltersAds;
use App\Traits\ModelResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdService
{
    use ModelResolver, FiltersAds;

    protected $columns = ['id', 'user_id', 'title', 'price', 'negotiable', 'views', 'phone', 'location', 'wished', 'main_file', 'route', 'currency', 'created_at'];

    /**
     * Get Popular ads for each category.
     */
    public function getPopularAds()
    {
        $models = self::getModelsClasses();

        $cacheKey = 'popular_ads';

        // Get the data from cache, if not available, get and cache it for 15 minutes
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($models) {
            $popularAds = [];
            foreach ($models as $key => $model) {
                $popularAds[$key] = $model::select($this->columns) // We set model names as key to make access easy on front (Home page)
                    ->latest('views')
                    ->limit(10)
                    ->get();
            }

            return $popularAds;
        });
    }

    /**
     * Get Featured ads for each category.
     */
    public function getFeaturedAds($withCache = true)
    {
        $cacheKey = 'featured_ads';

        // Cache for only home page. For the index page we pass all request data as it can be filtered.
        if($withCache) {
            $request = new Request(['need_featured_ads' => true]);

            return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($request) {
                return $this->getFilteredAds($request);
            });
        } else {
            $mergedParams = array_merge(request()->all(), ['need_featured_ads' => true]);

            $request = new Request($mergedParams);

            return $this->getFilteredAds($request);
        }
    }

    /**
     * Get Flash Sale ads for each category.
     */
    public function getFlashSaleAds($withCache = true)
    {
        $cacheKey = 'flash_sale_ads';

        // Cache for only home page. For the index page we pass all request data as it can be filtered.
        if($withCache) {
            $request = new Request(['need_flash_sale_ads' => true]);

            return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($request) {
                return $this->getFilteredAds($request);
            });
        } else {
            $mergedParams = array_merge(request()->all(), ['need_flash_sale_ads' => true]);

            $request = new Request($mergedParams);

            return $this->getFilteredAds($request);
        }
    }
}
