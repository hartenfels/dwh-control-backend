<?php

namespace App\EtlMonitor\Common\Models\Interfaces;

use App\EtlMonitor\Common\Transfer\AutocompleteResult;
use Illuminate\Support\Collection;

interface SearchableInterface
{

    /**
     * @param string $search_text
     * @return Collection<AutocompleteResult>
     */
    public static function autocomplete(string $search_text): Collection;

}
