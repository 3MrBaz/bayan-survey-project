<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'survey_id',
        'question_id',
        'answer',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

public function question()
{
    return $this->belongsTo(Question::class, 'question_id');
}

}
