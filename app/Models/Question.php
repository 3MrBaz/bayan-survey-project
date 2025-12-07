<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;


    protected $fillable = [
        'survey_id',
        'user_id',
        'category',
        'task',
        'question',
        'type',
        'options',
        'answer',
        'correct_answer',
    ];



    public function survey() {
        return $this->belongsTo(Survey::class, 'survey_id');
    }
    
    public function answers() {
    return $this->hasMany(SurveyorController::class);
    }

    protected $casts = [
        'options' => 'array',
    ];
}
