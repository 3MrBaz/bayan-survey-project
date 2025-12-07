<?php

namespace App\Models;

use App\Models\User;
use App\Models\Survey;
use Illuminate\Database\Eloquent\Model;

class SurveyResult extends Model
{
    protected $fillable = [
        'user_id',
        'survey_id',
        'time_spent',
        'score',
        'answered_questions',
    ];

    protected $casts = [
        'answered_questions' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function survey() {
        return $this->belongsTo(Survey::class);
    }

}
