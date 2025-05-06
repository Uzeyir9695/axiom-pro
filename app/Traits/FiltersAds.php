<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait FiltersAds
{
    use ModelResolver;
 
    /**
     * Retrieve filtered ads based on the provided model, columns, request, and locations.
     *
     * @param $model
     * @param array $columns The columns to select from the database.
     * @param \Illuminate\Http\Request $request The request object containing filter parameters (title, location, priceFrom, priceTo).
     * @param array $locations The locations array containing sub-locations for filtering.
     * @return \Illuminate\Support\Collection The filtered collection of ads.
     */
    function getFilteredAds($request)
    {
        $locations = config('cities');
        $columns = ['id', 'user_id', 'title', 'price', 'negotiable', 'views', 'phone', 'location', 'wished', 'main_file', 'route', 'currency', 'created_at'];

        $models = self::getModelsClasses();

        $ads = collect($models)->flatMap(fn($model) =>
            $model::select($columns)
            ->with('addOnsPlan:id,ad_id,badge,countdown_timer,bg_color,has_timer,is_featured,highlighted_ends_at')
            ->when($request->has('need_featured_ads'), fn($query) =>
                $query->whereRelation('addOnsPlan', 'is_featured', true)
            )
            ->when($request->has('need_flash_sale_ads'), fn($query) =>
                $query->whereRelation('addOnsPlan', 'countdown_timer', '>', now('Asia/Tbilisi'))
            )
            ->filterByTitle($request->title)
            ->filterByLocation($request->location, $locations)
            ->filterByPrice($request->priceFrom, $request->priceTo)
            ->get()
        );

        return $ads
            ->when($request->sort_date, fn($collection) =>
            $request->sort_date === 'desc'
                ? $collection->sortByDesc('created_at')->values()
                : $collection->sortBy('created_at')->values()
            )
            ->when($request->sort_price, fn($collection) =>
            $request->sort_price === 'desc'
                ? $collection->sortByDesc(fn($ad) => $ad->getRawOriginal('price'))->values()
                : $collection->sortBy(fn($ad) => $ad->getRawOriginal('price'))->values()
            );
    }

    /**
     * Filter by subcategory slug.
     *
     * @param Builder $query
     * @param string|null $subcategorySlug
     * @return Builder
     */
    public function scopeFilterBySubcategorySlug(Builder $query, ?string $subcategorySlug): Builder
    {
        return $query->when($subcategorySlug, function ($query, $subcategorySlug) {
            return $query->where('subcategory_slug', $subcategorySlug);
        });
    }

    /**
     * Filter by sub-subcategory slug.
     *
     * @param Builder $query
     * @param string|null $subSubcategorySlug
     * @return Builder
     */
    public function scopeFilterBySubSubcategorySlug(Builder $query, ?string $subSubcategorySlug): Builder
    {
        return $query->when($subSubcategorySlug, function ($query, $subSubcategorySlug) {
            return $query->where('sub_subcategory_slug', Str::slug($subSubcategorySlug));
        });
    }

    /**
     * Filter by type.
     *
     * @param Builder $query
     * @param string|null $type
     * @return Builder
     */
    public function scopeFilterByType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, function ($query, $type) {
            return $query->where('type_name', $type);
        });
    }

    /**
     * Apply title filter to the query.
     *
     * @param Builder $query
     * @param string|null $title
     * @return Builder
     */
    public function scopeFilterByTitle(Builder $query, ?string $title): Builder
    {
        return $query->when($title, function ($query, $title) {
            $this->cacheUserSearchedKeywords($title);
            return $query->where('title', 'LIKE', $title . '%')
                ->orWhere('title', 'LIKE', '% ' . $title . '%');
        });
    }

    /**
     * Apply location filter to the query.
     *
     * @param Builder $query
     * @param string|null $location
     * @param array $locations
     * @return Builder
     */
    public function scopeFilterByLocation(Builder $query, ?string $location, array $locations): Builder
    {
        return $query->when($location, function ($query, $location) use ($locations) {
            if ($location === 'Georgia') {
                return $query->whereIn('location', $locations['georgia']['sub_locations']);
            } elseif ($location === 'Abroad') {
                return $query->whereIn('location', $locations['abroad']['sub_locations']);
            } else {
                return $query->where('location', $location);
            }
        });
    }

    /**
     * Apply price filter to the query.
     *
     * @param Builder $query
     * @param float|null $priceFrom
     * @param float|null $priceTo
     * @return Builder
     */
    public function scopeFilterByPrice(Builder $query, ?float $priceFrom, ?float $priceTo): Builder
    {
        return $query->when($priceFrom && !$priceTo, function ($query) use ($priceFrom) {
            return $query->where('price', '>=', $priceFrom);
        })->when(!$priceFrom && $priceTo, function ($query) use ($priceTo) {
            return $query->where('price', '<=', $priceTo);
        })->when($priceFrom && $priceTo, function ($query) use ($priceFrom, $priceTo) {
            return $query->whereBetween('price', [$priceFrom, $priceTo]);
        });
    }

    /**
     * Filter by negotiable price.
     *
     * @param Builder $query
     * @param bool|null $negotiable
     * @return Builder
     */
    public function scopeFilterByNegotiable(Builder $query, ?bool $negotiable): Builder
    {
        return $query->when($negotiable, function ($query, $negotiable) {
            return $query->where('negotiable', $negotiable);
        });
    }

    /**
     * Filter by exchangeable price.
     *
     * @param Builder $query
     * @param bool|null $exchangeable
     * @return Builder
     */
    public function scopeFilterByExchangeable(Builder $query, ?bool $exchangeable): Builder
    {
        return $query->when($exchangeable, function ($query, $exchangeable) {
            return $query->where('exchangeable', $exchangeable);
        });
    }

    /**
     * Filter by warranty.
     *
     * @param Builder $query
     * @param bool|null $warranty
     * @return Builder
     */
    public function scopeFilterByWarranty(Builder $query, ?bool $warranty): Builder
    {
        return $query->when($warranty, function ($query, $warranty) {
            return $query->where('warranty', $warranty);
        });
    }

    /**
     * Filter by customs clearance.
     *
     * @param Builder $query
     * @param bool|null $customsClearance
     * @return Builder
     */
    public function scopeFilterByCustomsClearance(Builder $query, ?bool $customsClearance): Builder
    {
        return $query->when($customsClearance, function ($query, $customsClearance) {
            return $query->where('customs_clearance', $customsClearance);
        });
    }

    /**
     * Filter by year range.
     *
     * @param Builder $query
     * @param int|null $yearFrom
     * @param int|null $yearTo
     * @return Builder
     */
    public function scopeFilterByYearRange(Builder $query, ?int $yearFrom, ?int $yearTo): Builder
    {
        return $query->when($yearFrom && !$yearTo, function ($query) use ($yearFrom) {
            return $query->where('year', '>=', $yearFrom);
        })->when(!$yearFrom && $yearTo, function ($query) use ($yearTo) {
            return $query->where('year', '<=', $yearTo);
        })->when($yearFrom && $yearTo, function ($query) use ($yearFrom, $yearTo) {
            return $query->whereBetween('year', [$yearFrom, $yearTo]);
        });
    }

    /**
     * Filter by kilometer range.
     *
     * @param Builder $query
     * @param int|null $kilometerFrom
     * @param int|null $kilometerTo
     * @return Builder
     */
    public function scopeFilterByKilometerRange(Builder $query, ?int $kilometerFrom, ?int $kilometerTo): Builder
    {
        return $query->when($kilometerFrom && !$kilometerTo, function ($query) use ($kilometerFrom) {
            return $query->where('kilometer', '>=', $kilometerFrom);
        })->when(!$kilometerFrom && $kilometerTo, function ($query) use ($kilometerTo) {
            return $query->where('kilometer', '<=', $kilometerTo);
        })->when($kilometerFrom && $kilometerTo, function ($query) use ($kilometerFrom, $kilometerTo) {
            return $query->whereBetween('kilometer', [$kilometerFrom, $kilometerTo]);
        });
    }

    /**
     * Apply JSON-based filters to the query.
     *
     * @param Builder $query
     * @param array|null $filters
     * @return Builder
     */
    public function scopeFilterByAttributes(Builder $query, ?array $filters): Builder
    {
        return $query->when($filters, function ($query, $filters) {
            foreach ($filters as $key => $value) {
                $query->whereJsonContains("filters->$key", $value);
            }
        });
    }

    /***********************************************
     *  Specific for Aircraft filters start
     * *********************************************/
    /**
     * Filter by total flight time range.
     *
     * @param Builder $query
     * @param int|null $tftFrom
     * @param int|null $tftTo
     * @return Builder
     */
    public function scopeFilterByTotalFlightTime(Builder $query, $tftFrom, $tftTo): Builder
    {
        return $query->when($tftFrom && !$tftTo, function ($query) use ($tftFrom) {
            return $query->where('total_flight_time', '>=', $tftFrom);
        })->when(!$tftFrom && $tftTo, function ($query) use ($tftTo) {
            return $query->where('total_flight_time', '<=', $tftTo);
        })->when($tftFrom && $tftTo, function ($query) use ($tftFrom, $tftTo) {
            return $query->whereBetween('total_flight_time', [$tftFrom, $tftTo]);
        });
    }

    /**
     * Filter by passengers range.
     *
     * @param Builder $query
     * @param int|null $passengersFrom
     * @param int|null $passengersTo
     * @return Builder
     */
    public function scopeFilterByPassengers(Builder $query, $passengersFrom, $passengersTo): Builder
    {
        return $query->when($passengersFrom && !$passengersTo, function ($query) use ($passengersFrom) {
            return $query->where('passengers', '>=', $passengersFrom);
        })->when(!$passengersFrom && $passengersTo, function ($query) use ($passengersTo) {
            return $query->where('passengers', '<=', $passengersTo);
        })->when($passengersFrom && $passengersTo, function ($query) use ($passengersFrom, $passengersTo) {
            return $query->whereBetween('passengers', [$passengersFrom, $passengersTo]);
        });
    }
    /***********************************************
     *  Specific for Aircraft filters end
     * *********************************************/

    /**
     * Sort the query by date.
     *
     * @param Builder $query
     * @param string|null $sortDate
     * @return Builder
     */
    public function scopeSortByDate(Builder $query, ?string $sortDate): Builder
    {
        return $query->when($sortDate, function ($query, $sortDate) {
            return $query->orderBy('created_at', $sortDate);
        });
    }

    /**
     * Sort the query by price.
     *
     * @param Builder $query
     * @param string|null $sortPrice
     * @return Builder
     */
    public function scopeSortByPrice(Builder $query, ?string $sortPrice): Builder
    {
        return $query->when($sortPrice, function ($query, $sortPrice) {
            return $query->orderBy('price', $sortPrice);
        });
    }
}
