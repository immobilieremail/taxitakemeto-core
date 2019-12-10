<?php

namespace App\Extensions;

use Illuminate\Support\Str;

class SwissNumber
{
    public function __invoke()
    {
        return str_replace(["+", "/"], ["@", "_"],
            base64_encode(gmp_export(gmp_random_bits(128))));
    }
}