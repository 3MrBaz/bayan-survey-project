<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    // Explicitly set the table name since it's singular ("survey")
    protected $table = 'surveys';

    protected $fillable = [
        'name',
        'discription',
        'question_ids',
        'number_of_answers',
        'total_answers',
        'password',
        'view_survey',
        'user_id',
    ];


    protected $casts = [
        'question_ids' => 'array',   // converts JSON to array automatically
        'view_survey' => 'boolean',  // ensures true/false type
    ];

    // Relationship: each survey belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions() {
        return $this->hasMany(Question::class, 'survey_id');
    }

    public function answers() {
        return $this->hasMany(SurveyAnswer::class, 'survey_id');
    }

    public function surveyAnswers() {
        return $this->hasMany(SurveyAnswer::class);
    }
}
