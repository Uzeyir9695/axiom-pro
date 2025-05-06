<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;

trait SortsAds
{
    /**
     * Sort ads by their add-ons plan.
     *
     * @param Collection $ads
     * @return Collection
     */
    public function sortAds(Collection $ads): Collection
    {
        // Separate top search, boosted and normal ads
        $topSearchAds = $ads->filter(function ($ad) {
            return $ad->addOnsPlan &&
                $ad->addOnsPlan->is_top_searched &&
                now()->lt(Carbon::parse($ad->addOnsPlan->top_searched_ends_at));
        });

        $boostedAds = $ads->filter(function ($ad) {
            return $ad->addOnsPlan && $ad->addOnsPlan->is_boosted &&
                now()->lt(Carbon::parse($ad->addOnsPlan->boost_ends_at));
        });

        $normalAds = $ads->reject(function ($ad) {
            return $ad->addOnsPlan &&
                (($ad->addOnsPlan->is_top_searched && now()->lt(Carbon::parse($ad->addOnsPlan->top_searched_ends_at))) || ($ad->addOnsPlan->is_boosted && now()->lt(Carbon::parse($ad->addOnsPlan->boost_ends_at))));
        });

        // Sort top search ads by created_at in descending order
        $sortedTopSearchAds = $topSearchAds->sortByDesc(function ($ad) {
            return Carbon::parse($ad->created_at);
        })->values();

        // Sort boosted ads by created_at in descending order
        $sortedBoostedAds = $boostedAds->sortByDesc(function ($ad) {
            return Carbon::parse($ad->created_at);
        })->values();

        // Sort normal ads by created_at in descending order
        $sortedNormalAds = $normalAds->sortByDesc(function ($ad) {
            return Carbon::parse($ad->created_at);
        })->values();

        // Combine the results: top search ads first, then boosted ads and then normal ads
        return $sortedTopSearchAds
            ->concat($sortedBoostedAds)
            ->concat($sortedNormalAds);
    }
}
