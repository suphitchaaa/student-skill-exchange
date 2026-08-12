<?php

namespace App\Models;

use Database\Factories\StudentProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    /** @use HasFactory<StudentProfileFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'faculty', 'major', 'year_level', 'bio', 'phone', 'contact_channel', 'profile_image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
