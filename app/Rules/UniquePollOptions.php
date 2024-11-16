<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniquePollOptions implements Rule
{
    public function passes($attribute, $value)
    {
        // Check if all values in the array are unique
        return count($value) === count(array_unique($value));
    }

    public function message()
    {
        // Custom validation message
        return 'The :attribute must have unique options.';
    }
}
