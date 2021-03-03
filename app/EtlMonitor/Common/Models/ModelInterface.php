<?php

namespace App\EtlMonitor\Common\Models;

use Illuminate\Support\Collection;

interface ModelInterface
{

    public function delete();

    public function enrich(): self;

    public function transform(): array;

}
