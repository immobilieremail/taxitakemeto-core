<?php

namespace App\Rules;

use App\Models\Facet;
use Illuminate\Contracts\Validation\Rule;

class FacetRules implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Facet::find(getSwissNumberFromUrl($value)) != null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must exists.';
    }
}
