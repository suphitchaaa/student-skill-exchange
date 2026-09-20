<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $normalizedNames = [];
        $seen = [];

        // ตรวจทุกแถวรวม Soft Delete ก่อนแก้ Schema เพราะ MariaDB ไม่ย้อน DDL เมื่อเกิดข้อผิดพลาด
        foreach (DB::table('skills')->select('id', 'name')->orderBy('id')->get() as $skill) {
            $normalizedName = $this->normalizeName($skill->name);

            if ($normalizedName === '' || mb_strlen($normalizedName, 'UTF-8') > 765) {
                throw new RuntimeException("Invalid normalized skill name at ID {$skill->id}");
            }

            if (isset($seen[$normalizedName])) {
                throw new RuntimeException("Normalized skill name collision at IDs {$seen[$normalizedName]} and {$skill->id}");
            }

            $seen[$normalizedName] = $skill->id;
            $normalizedNames[$skill->id] = $normalizedName;
        }

        Schema::table('skills', function (Blueprint $table) {
            $column = $table->string('normalized_name', 765)->nullable()->after('name');

            if (DB::getDriverName() === 'mysql') {
                $column->collation('utf8mb4_bin');
            }
        });

        foreach ($normalizedNames as $id => $normalizedName) {
            DB::table('skills')->where('id', $id)->update(['normalized_name' => $normalizedName]);
        }

        Schema::table('skills', function (Blueprint $table) {
            $column = $table->string('normalized_name', 765)->nullable(false)->change();

            if (DB::getDriverName() === 'mysql') {
                $column->collation('utf8mb4_bin');
            }

            $table->unique('normalized_name');
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropUnique(['normalized_name']);
            $table->dropColumn('normalized_name');
        });
    }

    private function normalizeName(string $name): string
    {
        $collapsedWhitespace = trim(preg_replace('/[\s\p{Z}]+/u', ' ', $name));

        return mb_convert_case($collapsedWhitespace, MB_CASE_FOLD, 'UTF-8');
    }
};
