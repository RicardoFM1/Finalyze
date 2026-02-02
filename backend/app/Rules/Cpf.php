<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove non-numeric characters
        $c = preg_replace('/\D/', '', $value);

        // Check if length is 11
        if (strlen($c) != 11) {
            $fail('O CPF deve ter 11 dígitos.');
            return;
        }

        // Check for repeated digits (blacklist)
        if (preg_match("/^{$c[0]}{11}$/", $c)) {
            $fail('O CPF informado é inválido.');
            return;
        }

        // Validate first digit
        for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);

        if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            $fail('O CPF informado é inválido.');
            return;
        }

        // Validate second digit
        for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);

        if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            $fail('O CPF informado é inválido.');
            return;
        }
    }
}
