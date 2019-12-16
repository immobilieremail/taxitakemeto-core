<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use App\Models\Facet;

class OcapListRules implements Rule
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
        if (!is_array($value))
            return false;
        $map_result = array_map(function ($ocap) {
            return Facet::find(getSwissNumberFromUrl($ocap));
        }, $value);
        return !in_array(null, $map_result);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field must be an array of valid facets.';
    }
}
