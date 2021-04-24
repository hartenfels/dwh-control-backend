<?php

namespace App\DwhControl\Common\Transfer;

class AutocompleteResult
{

    /**
     * AutocompleteResult constructor.
     * @param int $id
     * @param string $type
     * @param string $name
     * @param object $info
     * @param string $model
     * @param string $entity
     * @param string $icon
     * @param array $tags
     */
    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public object $info,
        public string $model,
        public string $entity,
        public string $icon,
        public array $tags = []
    ) {}

}
