<?php

namespace App\Models;

use Database\Factories\UserSkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    /** @use HasFactory<UserSkillFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'skill_id', 'skill_type', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function historicalSkill()
    {
        return $this->belongsTo(Skill::class, 'skill_id')->withTrashed();
    }

    public function sentExchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class, 'sender_user_skill_id');
    }

    public function receivedExchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class, 'receiver_user_skill_id');
    }
}
