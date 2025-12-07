<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function ajaxSearch(Request $request) {
        $search = $request->input('search');

        $surveys = Survey::where('view_survey', true)
            ->with('user')
            ->whereRaw('(
                SELECT COUNT(*) 
                FROM survey_answers 
                WHERE survey_answers.survey_id = surveys.id
            ) < surveys.total_answers') // 🔥 hide full surveys
            ->when($search, function ($query, $search) {

                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
                });

            })
            ->latest()
            ->get();

        return view('survey-cards', compact('surveys'))->render();
    }

    public function viewSurveys(Request $request) {
        $userId = auth()->id();

        // Get surveys the user already completed
        $solvedSurveyIds = SurveyAnswer::where('user_id', $userId)
            ->pluck('survey_id')
            ->unique();

        $surveys = Survey::where('view_survey', true)
            ->whereNotIn('id', $solvedSurveyIds)

            // ⬇️ Hide surveys that reached their total answer limit
            ->whereRaw('(
                SELECT COUNT(*) 
                FROM survey_answers 
                WHERE survey_answers.survey_id = surveys.id
            ) < surveys.total_answers')

            ->latest()
            ->get();

        return view('surveys', compact('surveys'));
    }

}
