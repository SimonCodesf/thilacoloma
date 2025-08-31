<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class DynamicAgeRange extends Modifier
{
    /**
     * Calculate birth year range based on current age range
     * 
     * @param mixed $value The base age range (e.g., "6-8")
     * @param array $params
     * @param array $context
     * @return string
     */
    public function index($value, $params, $context): string
    {
        if (!$value) {
            return '';
        }

        $currentYear = date('Y');
        
        // Parse age range like "6-8" or single age like "18"
        if (strpos($value, '-') !== false) {
            [$minAge, $maxAge] = explode('-', $value);
            $maxBirthYear = $currentYear - intval($minAge);
            $minBirthYear = $currentYear - intval($maxAge);
            return "Geboortejaar {$minBirthYear}-{$maxBirthYear}";
        } else {
            // Single age
            $age = intval($value);
            $birthYear = $currentYear - $age;
            return "Geboortejaar {$birthYear}";
        }
    }
}
