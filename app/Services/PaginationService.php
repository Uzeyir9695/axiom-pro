<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Enumerable;

class PaginationService
{
    /**
     * We manually paginate. Because we interact with collections sometimes on which paginate() can't be applied.
     */
    public static function paginate(Enumerable $items, $itemsPerPage = 15)
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $itemsPerPage;

        return new LengthAwarePaginator(
            $items->slice(($page - 1) * $perPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }
}
