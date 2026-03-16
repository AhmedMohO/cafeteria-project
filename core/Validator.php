<?php

namespace Core;

class Validator
{
    public static function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {

            $value = trim($data[$field] ?? '');

            foreach ($fieldRules as $rule) {

                if ($rule === 'required' && empty($value)) {
                    $errors[$field][] = "$field is required";
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "Invalid email format";
                }

                if (str_starts_with($rule, 'min:')) {

                    $min = explode(':', $rule)[1];

                    if (strlen($value) < $min) {
                        $errors[$field][] = "$field must be at least $min characters";
                    }
                }

                if (str_starts_with($rule, 'max:')) {

                    $max = explode(':', $rule)[1];

                    if (strlen($value) > $max) {
                        $errors[$field][] = "$field must be less than $max characters";
                    }
                }
            }
        }

        return $errors;
    }
}
