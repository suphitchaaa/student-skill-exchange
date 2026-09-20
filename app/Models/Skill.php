<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'category', 'is_active'];

    protected static function booted(): void
    {
        static::saving(function (Skill $skill): void {
            $skill->normalized_name = self::normalizeName($skill->name);
        });
    }

    public static function normalizeName(string $name): string
    {
        return mb_convert_case(self::cleanName($name), MB_CASE_FOLD, 'UTF-8');
    }

    public static function cleanName(string $name): string
    {
        $collapsedWhitespace = preg_replace('/[\s\p{Z}]+/u', ' ', $name);

        return $collapsedWhitespace === null ? '' : trim($collapsedWhitespace);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function userSkills()
    {
        return $this->hasMany(UserSkill::class);
    }
}
