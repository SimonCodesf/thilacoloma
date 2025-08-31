<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class DynamicAgeRange extends Modifier
{
    /**
     * Calculate birth year range based on current age range
     * Switches to new scout year on August 15th
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
        $currentMonth = date('n'); // 1-12
        $currentDay = date('j'); // 1-31
        
        // If we're before August 15th, use previous scout year
        if ($currentMonth < 8 || ($currentMonth == 8 && $currentDay < 15)) {
            $scoutYear = $currentYear - 1;
        } else {
            $scoutYear = $currentYear;
        }
        
        // Parse age range like "6-8" or single age like "18"
        if (strpos($value, '-') !== false) {
            [$minAge, $maxAge] = explode('-', $value);
            $maxBirthYear = $scoutYear - intval($minAge);
            $minBirthYear = $scoutYear - intval($maxAge);
            return "Geboortejaar {$minBirthYear}-{$maxBirthYear}";
        } else {
            // Single age
            $age = intval($value);
            $birthYear = $scoutYear - $age;
            return "Geboortejaar {$birthYear}";
        }
    }
}
