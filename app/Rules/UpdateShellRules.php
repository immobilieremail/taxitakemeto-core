<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UpdateShellRules implements Rule
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
        if (is_array($value)) {
            if (isset($value["audiolists"]) && is_array($value["audiolists"])) {
                foreach ($value["audiolists"] as $audiolist) {
                    if (!isset($audiolist["ocapType"], $audiolist["ocap"])
                        || !is_string($audiolist["ocapType"])
                        || !is_string($audiolist["ocap"])
                        || !preg_match('#[^/]+$#', str_replace(
                                "/edit", "", $audiolist["ocap"]))) {
                        return false;
                    }
                }
                return true;
            } else
                return false;
        } else
            return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
