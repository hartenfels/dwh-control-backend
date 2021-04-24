<?php

namespace App\DwhControl\Common\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PivotAttributeNames {

    public function __construct(public string $key, public string $foreign_key) {}

}
