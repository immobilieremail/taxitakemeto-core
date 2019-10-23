<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Http\Testing\MimeType;

class AudioRules implements Rule
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
        $mime = $value->getMimeType();
        if (!is_string($mime))
            return false;
        preg_match('#^([^/]+)/#', $mime, $matches);
        return $matches[0] == 'audio/';
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
