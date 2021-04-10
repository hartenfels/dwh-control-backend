<?php

namespace App\DwhControl\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class Request extends FormRequest implements RequestInterface
{

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * @return Collection
     */
    public function valid(): Collection
    {

        return collect(parent::validated());
    }

}
