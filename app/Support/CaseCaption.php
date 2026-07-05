<?php

namespace App\Support;

class CaseCaption
{
    /**
     * Case names in this module follow the convention "Republic vs
     * {defendant}". When an appeal (or court of appeal) was filed by the
     * defendant rather than by the court/prosecution, the defendant is the
     * appellant and standard appellate practice names the appellant first —
     * so the caption reverses to "{defendant} vs Republic".
     */
    public static function forAppeal(?string $caseName, ?string $filingDateSource): ?string
    {
        if ($filingDateSource !== 'defendant' || !$caseName || !str_contains($caseName, ' vs ')) {
            return $caseName;
        }

        [$prosecution, $defendant] = explode(' vs ', $caseName, 2);

        return trim($defendant) . ' vs ' . trim($prosecution);
    }
}
