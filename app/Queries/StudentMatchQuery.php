<?php

namespace App\Queries;

use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StudentMatchQuery
{
    /**
     * @return Builder<User>
     */
    public function eligibleStudentsFor(User $currentUser): Builder
    {
        return User::query()
            ->whereKeyNot($currentUser->getKey())
            ->where('role', 'student')
            ->where('status', 'active');
    }

    /**
     * @param  Builder<User>  $students
     * @return Builder<User>
     */
    public function applyRecommendation(Builder $students, User $currentUser): Builder
    {
        $currentWantedSkillIds = $this->activeSkillIdsFor($currentUser, 'wanted');
        $currentOfferedSkillIds = $this->activeSkillIdsFor($currentUser, 'offered');

        $students->whereHas('userSkills', function (Builder $userSkills) use ($currentWantedSkillIds): void {
            $userSkills
                ->where('skill_type', 'offered')
                ->whereIn('skill_id', $currentWantedSkillIds)
                ->whereHas('skill', function (Builder $skills): void {
                    $skills->where('is_active', true);
                });
        });

        $students->withExists([
            'userSkills as is_mutual_match' => function (Builder $userSkills) use ($currentOfferedSkillIds): void {
                $userSkills
                    ->where('skill_type', 'wanted')
                    ->whereIn('skill_id', $currentOfferedSkillIds)
                    ->whereHas('skill', function (Builder $skills): void {
                        $skills->where('is_active', true);
                    });
            },
        ]);

        return $students
            ->orderByDesc('is_mutual_match')
            ->orderBy('users.name')
            ->orderBy('users.id');
    }

    /**
     * @return Collection<int, User>
     */
    public function dashboardRecommendationsFor(User $currentUser, int $limit = 3): Collection
    {
        $matchingWantedSkillIds = $this->activeSkillIdsFor($currentUser, 'wanted');

        return $this
            ->applyRecommendation($this->eligibleStudentsFor($currentUser), $currentUser)
            ->with([
                'studentProfile',
                'userSkills' => function ($userSkills) use ($matchingWantedSkillIds): void {
                    $userSkills
                        ->where('skill_type', 'offered')
                        ->whereIn('skill_id', $matchingWantedSkillIds)
                        ->whereHas('skill', function (Builder $skills): void {
                            $skills->where('is_active', true);
                        })
                        ->with('skill')
                        ->orderBy('user_skills.id');
                },
            ])
            ->limit($limit)
            ->get();
    }

    /**
     * @return array{offered: array<int, bool>, wanted: array<int, bool>}
     */
    public function profileMatchSkillIds(User $currentUser, User $viewedStudent): array
    {
        if ($currentUser->is($viewedStudent)) {
            return [
                'offered' => [],
                'wanted' => [],
            ];
        }

        $matchedOfferedSkillIds = UserSkill::query()
            ->where('user_id', $viewedStudent->getKey())
            ->where('skill_type', 'offered')
            ->whereIn('skill_id', $this->activeSkillIdsFor($currentUser, 'wanted'))
            ->whereHas('skill', function (Builder $skills): void {
                $skills->where('is_active', true);
            })
            ->pluck('skill_id')
            ->mapWithKeys(fn ($skillId): array => [(int) $skillId => true])
            ->all();

        $matchedWantedSkillIds = UserSkill::query()
            ->where('user_id', $viewedStudent->getKey())
            ->where('skill_type', 'wanted')
            ->whereIn('skill_id', $this->activeSkillIdsFor($currentUser, 'offered'))
            ->whereHas('skill', function (Builder $skills): void {
                $skills->where('is_active', true);
            })
            ->pluck('skill_id')
            ->mapWithKeys(fn ($skillId): array => [(int) $skillId => true])
            ->all();

        return [
            'offered' => $matchedOfferedSkillIds,
            'wanted' => $matchedWantedSkillIds,
        ];
    }

    /**
     * @return Builder<UserSkill>
     */
    private function activeSkillIdsFor(User $user, string $skillType): Builder
    {
        return UserSkill::query()
            ->select('user_skills.skill_id')
            ->where('user_skills.user_id', $user->getKey())
            ->where('user_skills.skill_type', $skillType)
            ->whereHas('skill', function (Builder $skills): void {
                $skills->where('is_active', true);
            });
    }
}
