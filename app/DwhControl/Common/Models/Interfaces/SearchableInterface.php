<?php

namespace App\DwhControl\Common\Models\Interfaces;

use App\DwhControl\Common\Transfer\AutocompleteResult;
use Illuminate\Support\Collection;

interface SearchableInterface
{

    /**
     * @param string $search_text
     * @return Collection<AutocompleteResult>
     */
    public static function autocomplete(string $search_text): Collection;

}
