<?php

namespace App\EtlMonitor\Api\Http\Requests;

interface RequestInterface
{

    /**
     * @return array
     */
    public function rules(): array;

    /**
     * @return array
     */
    public function messages(): array;

}
