<?php

namespace App\Models;

use Database\Factories\ExchangeRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRequest extends Model
{
    /** @use HasFactory<ExchangeRequestFactory> */
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'sender_user_skill_id', 'receiver_user_skill_id', 'learning_format', 'preferred_schedule', 'message', 'status', 'responded_at', 'completed_at'];

    protected function casts(): array
    {
        return ['responded_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function senderUserSkill()
    {
        return $this->belongsTo(UserSkill::class, 'sender_user_skill_id');
    }

    public function receiverUserSkill()
    {
        return $this->belongsTo(UserSkill::class, 'receiver_user_skill_id');
    }
}
