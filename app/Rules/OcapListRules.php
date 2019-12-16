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
        $ocapCollection = collect($value)->map(function ($ocap) {
            return Facet::find(getSwissNumberFromUrl($ocap));
        });
        return $ocapCollection->search(null) !== false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be an array of valid facets.';
    }
}
