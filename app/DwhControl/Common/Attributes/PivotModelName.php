<?php

namespace App\DwhControl\Common\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PivotModelName {

    public function __construct(public string $pivot_name) {}

}
