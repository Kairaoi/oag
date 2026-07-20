<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Case Review "Offences Charged" section needs this fixed set of
     * categories available in its dropdown. Existing categories (Theft,
     * Vandalism, Burglary, Murder, ...) already have offences/case_offence
     * rows pointing at them, so this only adds whatever from the required
     * list is missing (matched case-insensitively against category_name)
     * rather than replacing the table's contents.
     */
    public function up(): void
    {
        $required = [
            'SGBV',
            'Arson',
            'Fraud',
            'Drug Dealings',
            'Homicide',
            'Assault',
            'Damaging Property',
            'Statutory Offences',
            'Embezzlement',
        ];

        $existing = DB::table('offence_categories')
            ->whereNull('deleted_at')
            ->pluck('category_name')
            ->map(fn ($name) => strtolower(trim($name)))
            ->all();

        $creatorId = DB::table('users')->orderBy('id')->value('id');

        if (!$creatorId) {
            // No users yet (fresh install before the first migrate-and-seed
            // pass) — nothing sensible to set created_by to, so skip. The
            // dropdown will just show whatever's already there until this
            // runs somewhere a user already exists.
            return;
        }

        $now = now();

        foreach ($required as $categoryName) {
            if (in_array(strtolower($categoryName), $existing, true)) {
                continue;
            }

            DB::table('offence_categories')->insert([
                'category_name' => $categoryName,
                'created_by' => $creatorId,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Intentionally left as a no-op: rolling this back could delete category
     * rows that offences or case_offence rows now depend on.
     */
    public function down(): void
    {
    }
};
