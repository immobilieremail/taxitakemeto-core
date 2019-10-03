<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DropboxMessageRules implements Rule
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
            foreach ($value as $data) {
                if (!isset($data["ocapType"], $data["dropbox"], $data["ocap"])
                    || !is_string($data["ocapType"])
                    || !is_string($data["dropbox"])
                    || !is_string($data["ocap"])
                    || !preg_match('#[^/]+$#', $data["dropbox"])
                    || !preg_match('#[^/]+$#', str_replace(
                            "/edit", "", $data["ocap"])))
                    return false;
            }
            return true;
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
