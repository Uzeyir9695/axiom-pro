<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlanAddOnController extends Controller
{
     /****************************************************************
     *  Cache ad add-ons request temporary. After successful payment store them in the database.
     * ***************************************************************/
    public function cacheAddOnsRequest(AddOnsPlanRequest $request)
    {
        $cacheKey = 'add_ons_request_'.auth()->id();
        // Store the request data in the cache for 2 hours
        Cache::put($cacheKey, $request->fluent(), now()->addHours(2));
    }

    /****************************************************************
     *  Upgrade ad by add-ons
     * ***************************************************************/
    public function upgradeAd(Request $request, PlanService $planService)
    {
        $userID = auth()->id();
        $cacheKey = 'add_ons_request_'.$userID;

        $cachedRequest = Cache::get($cacheKey);

        if ($cachedRequest && $cachedRequest->missing('ad_id')) {
            $cachedRequest->ad_id = $request->ad_id;
        }
        $columns = ['id','user_id','active_until','images_upload_limit'];

        $model = self::getModel($request->model_route);
        $ad = $model::select($columns)->where('user_id', $cachedRequest->user_id)->where('id', $cachedRequest->ad_id)->firstOrFail();

        DB::transaction(function () use ($ad, $cachedRequest, $planService) {
            $planService->upgradeAdAttributes($ad, $cachedRequest);
            $planService->upgradeAddOnsPlan($cachedRequest);
        });

        Cache::forget($cacheKey); // Clear current add-ons from request.
        $userPlanKey= 'user_with_plan_'.$userID;
        Cache::forget($userPlanKey); // Clear auth user plan.

        return back()->with(['message' => 'Upgraded Successfully!']);
    }

}
