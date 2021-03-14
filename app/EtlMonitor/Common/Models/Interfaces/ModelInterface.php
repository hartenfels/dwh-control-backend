<?php

namespace App\EtlMonitor\Common\Models\Interfaces;


interface ModelInterface
{

    public function delete();

    public function enrich(): self;

    public function transform(): array;

    public function getIcon(): string;

}
