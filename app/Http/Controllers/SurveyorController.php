<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Survey;
use App\Models\Question;
use App\Models\SurveyAnswer;
use App\Models\SurveyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyorController extends Controller
{

    public function startSurvey(Request $request, $surveyId) {
        session(['survey_start_time_' . $surveyId => now()]);

        return view('survey.take', compact('surveyId'));
    }
    
    public function delete($id) {
        $q = Question::find($id);

        if (!$q) {
            return response()->json(['success' => false]);
        }

        $q->delete();

        return response()->json(['success' => true]);
    }

    public function manualGrade(Request $request) {
        $request->validate([
            'answer_id'        => 'required|exists:survey_answers,id',
            'corrected_answer' => 'required|string',
            'is_correct'       => 'required|boolean',
        ]);

        $answer = SurveyAnswer::findOrFail($request->answer_id);
        $result = SurveyResult::where('survey_id', $answer->survey_id)
            ->where('user_id', $answer->user_id)
            ->first();

        $answer->answer = $request->corrected_answer;
        $answer->meta = [
            'selected_option' => $answer->meta['selected_option'] ?? null,
            'is_correct'      => (bool) $request->is_correct,
        ];
        $answer->save();

        $correctCount = SurveyAnswer::where('survey_id', $answer->survey_id)
            ->where('user_id', $answer->user_id)
            ->where('meta->is_correct', true)
            ->count();

        $result->score = $correctCount;
        $result->save();

        return back()->with('success', 'تم تحديث التصحيح والدرجة بنجاح');
    }

    public function exportPage() {
        $surveys = Survey::all();
        return view('export', compact('surveys'));
    }

    public function export(Request $request) {
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'export_type' => 'required|string',
        ]);

        $survey = Survey::with('questions')->findOrFail($request->survey_id);
        $type   = $request->export_type;

        switch ($type) {

            case 'questions_json':
                return response()->json($survey->questions)
                    ->header('Content-Disposition', 'attachment; filename="questions.json"');

            case 'answers_json':
                $answers = SurveyAnswer::where('survey_id', $survey->id)->get();

                return response()->json($answers)
                    ->header('Content-Disposition', 'attachment; filename="answers.json"');

            case 'questions_csv':

                $filename = "questions.csv";
                $headers = [
                    "Content-Type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$filename",
                ];

                $rows = [];
                $rows[] = ["Task", "Question", "Type", "Options", "Correct Answer"];

                foreach ($survey->questions as $q) {
                    $rows[] = [
                        $q->task,
                        $q->question,
                        $q->type,
                        $q->options,
                        $q->correct_answer
                    ];
                }

                return response()->stream(function () use ($rows) {
                    $file = fopen('php://output', 'w');
                    foreach ($rows as $line) fputcsv($file, $line);
                    fclose($file);
                }, 200, $headers);

            case 'answers_csv':

                $answers = SurveyAnswer::where('survey_id', $survey->id)->get();

                $filename = "answers.csv";
                $headers = [
                    "Content-Type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$filename",
                ];

                $rows = [];
                $rows[] = ["User ID", "Question ID", "Answer"];

                foreach ($answers as $a) {
                    $rows[] = [
                        $a->user_id,
                        $a->question_id,
                        $a->answer
                    ];
                }

                return response()->stream(function () use ($rows) {
                    $file = fopen('php://output', 'w');
                    foreach ($rows as $line) fputcsv($file, $line);
                    fclose($file);
                }, 200, $headers);
        }

        return back()->with('error', 'نوع التصدير غير صحيح.');
    }

    public function updateRandomCount(Request $request) {
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'random_question_count' => 'nullable|integer|min:1',
        ]);

        Survey::where('id', $request->survey_id)
            ->update(['random_question_count' => $request->random_question_count]);

        return back()->with('success', 'تم حفظ عدد الأسئلة العشوائية بنجاح!');
    }

    public function checkSurveyPassword(Request $request, $survey_id) {
        $survey = Survey::findOrFail($survey_id);

        $request->validate([
            'password' => 'required'
        ]);

        if ($request->password === $survey->password) {
            session(["survey_access_{$survey_id}" => true]);
            return redirect()->route('answering-questions', $survey_id);
        }

        return back()->withErrors(['password' => 'Incorrect password']);
    }

    public function surveyAccess($survey_id) {
        $survey = Survey::findOrFail($survey_id);

        if (empty($survey->password)) {
            return redirect()->route('answering-questions', $survey_id);
        }

        return view('survey-password', compact('survey'));
    }


    public function viewImportQuestions() {
        return view('import-questions');
    }

    public function importQuestions(Request $request) {
        $request->validate([
            'questions_csv' => 'required|file|mimes:csv,txt',
        ]);

        $userId = Auth::id();

        $file = $request->file('questions_csv');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);

        $header = array_map(function ($h) {
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
            return strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/', '', $h)));
        }, $header);

        while (($row = fgetcsv($handle)) !== false) {

            $row = array_map(function ($v) {
                return trim(preg_replace('/[\x00-\x1F\x7F]/', '', $v));
            }, $row);

            if (count(array_filter($row)) === 0) {
                continue;
            }

            $data = array_combine($header, $row);
            if (!$data) {
                continue;
            }

            $questionType = strtolower(trim($data['question_type'] ?? ''));

            // -----------------------------
            // 🔥 FIX: Convert 1 → option_1
            // -----------------------------
            $correct = null;

            if ($questionType === 'multiple') {

                // User enters 1,2,3,4 in CSV → convert:
                $correctNumber = intval($data['correct_answer'] ?? 0);

                if ($correctNumber >= 1 && $correctNumber <= 4) {
                    $correct = "option_" . $correctNumber;
                }

            } elseif ($questionType === 'truefalse') {

                $correct = strtolower(trim($data['true_false_answer'] ?? ''));

            }

            // Create the question
            Question::create([
                'user_id'        => $userId,
                'category'       => $data['question_category'] ?? null,
                'task'           => $data['task'] ?? null,
                'question'       => $data['question'],
                'type'           => $questionType,
                'options'        => json_encode([
                    $data['option_1'] ?? null,
                    $data['option_2'] ?? null,
                    $data['option_3'] ?? null,
                    $data['option_4'] ?? null,
                ]),
                'correct_answer' => $correct,
            ]);
        }

        fclose($handle);

        return back()->with('success', 'تم استيراد الأسئلة بنجاح!');
    }

    public function index($survey_id) {
        $results = SurveyResult::with(['user', 'survey'])
            ->where('survey_id', $survey_id)
            ->get();

        $scores = $results->pluck('score')->toArray();
        $times  = $results->pluck('time_spent')->toArray();

        // Completion over time
        $completion = $results->groupBy(fn($r) => $r->created_at->format('Y-m-d'));
        $completionLabels = $completion->keys()->toArray();
        $completionData   = $completion->map->count()->values()->toArray();

        // All survey answers
        $answers = SurveyAnswer::with(['user', 'question'])
            ->where('survey_id', $survey_id)
            ->get();

        // Most missed questions
        $incorrect = $answers->filter(fn($a) =>
            isset($a->meta['is_correct']) && !$a->meta['is_correct']
        )
        ->groupBy('question_id')
        ->map->count()
        ->sortDesc();

        $mostMissedLabels = Question::whereIn('id', $incorrect->keys())
            ->pluck('question')
            ->toArray();

        $mostMissedData = array_values($incorrect->toArray());

        // Per-question stats for chart 5
        $questionStats = [];
        $questions = Question::where('survey_id', $survey_id)->get();

        foreach ($questions as $q) {
            $stats = SurveyAnswer::where('question_id', $q->id)->get();

            if ($q->type === 'multiple') {
                $options = json_decode($q->options, true) ?? [];
                $labels = $options;

                $data = [];
                foreach ($options as $index => $opt) {
                    $key = "option_" . ($index + 1);
                    $data[] = $stats->where('meta.selected_option', $key)->count();
                }
            }
            elseif ($q->type === 'truefalse') {
                $labels = ['صح', 'خطأ'];
                $data = [
                    $stats->where('answer', 'true')->count(),
                    $stats->where('answer', 'false')->count(),
                ];
            }
            else {
                $labels = ['إجابات نصية'];
                $data = [$stats->count()];
            }

            $questionStats[] = [
                'id' => $q->id,
                'question' => $q->question,
                'labels' => $labels,
                'data' => $data,
            ];
        }

        // 🔥 FIXED: full answer records with IDs + correctness for manual grading
        $answerRecords = [];
        foreach ($answers as $a) {
            $answerRecords[] = (object)[
                'id'        => $a->id,  // <-- THIS FIXES YOUR ERROR
                'user'      => $a->user,
                'survey'    => $a->question->survey,
                'question'  => $a->question,
                'answer'    => $a->answer,
                'is_correct'=> $a->meta['is_correct'] ?? null, 
            ];
        }

        return view('analysis', [
            'scores'            => $scores,
            'times'             => $times,
            'completionLabels'  => $completionLabels,
            'completionData'    => $completionData,
            'mostMissedLabels'  => $mostMissedLabels,
            'mostMissedData'    => $mostMissedData,
            'questionStats'     => $questionStats,
            'answers'           => $answerRecords,
        ]);
    }




    public function results($survey_id) {
        $survey = Survey::findOrFail($survey_id);
        $user   = auth()->user();

        // Load all answers for this user + survey WITH their questions
        $answers = SurveyAnswer::with('question')
            ->where('survey_id', $survey_id)
            ->where('user_id', $user->id)
            ->get();

        $score          = 0;
        $totalQuestions = $answers->count();

        foreach ($answers as $answer) {
            $q = $answer->question;
            if (!$q) {
                continue; // safety check
            }

            $userAnswer = $answer->answer;

            // TEXT → skip scoring
            if ($q->type === 'text') {
                continue;
            }

            // TRUE/FALSE
            if ($q->type === 'truefalse') {
                $userAnswerNorm = strtolower(trim($userAnswer));
                $correctNorm    = strtolower(trim($q->correct_answer));

                if ($userAnswerNorm === $correctNorm) {
                    $score++;
                }

                continue;
            }

            // MULTIPLE CHOICE (we store "option_1", "option_2", ...)
            if ($q->type === 'multiple') {
                if (trim($userAnswer) === trim($q->correct_answer)) {
                    $score++;
                }

                continue;
            }
        }

        return view('survey-results', [
            'survey'         => $survey,
            'answers'        => $answers,   // collection of SurveyAnswer
            'score'          => $score,
            'totalQuestions' => $totalQuestions,
        ]);
    }

public function submit(Request $request, $survey_id)
{
    $survey = Survey::with('questions')->findOrFail($survey_id);
    $user = auth()->user();

    $shownQuestions = $request->shown_questions ?? [];

    $score = 0;
    $answeredQuestions = [];
    $startTime = $request->input('start_time');

    $timeSpent = 0;

    if ($startTime) {
        $start = Carbon::createFromTimestamp($startTime);
        $timeSpent = $start->diffInSeconds(now());
    }
        
    foreach ($shownQuestions as $questionId) {

        $question = $survey->questions->firstWhere('id', $questionId);
        if (!$question) continue;

        $field = "answer_" . $questionId;
        $rawAnswer = $request->input($field);

        if ($rawAnswer === null) continue;

        // Save option number for multiple (option_1, option_2)
        $selectedOption = $rawAnswer;

        // Convert MCQ into text for `answer` column
        if ($question->type === "multiple") {
            $options = json_decode($question->options, true);
            $index = intval(str_replace("option_", "", $rawAnswer)) - 1;
            $answer = $options[$index] ?? null;
        } else {
            $answer = $rawAnswer;
        }

        // Check correctness
        $isCorrect = false;

        if ($question->type === "multiple") {
            $selectedOption = $rawAnswer;

            // convert to text
            $options = json_decode($question->options, true);
            $index = intval(str_replace("option_", "", $rawAnswer)) - 1;
            $answerText = $options[$index] ?? null;

            $answer = $selectedOption; // <-- store option_1 in 'answer'

            $isCorrect = ($selectedOption === $question->correct_answer);
        }

        if ($question->type === "truefalse") {
            $isCorrect = strtolower($rawAnswer) === strtolower($question->correct_answer);
        }

        if ($isCorrect) {
            $score++;
        }

        // Save individual answer
        SurveyAnswer::updateOrCreate(
            [
                "user_id"     => $user->id,
                "survey_id"   => $survey->id,
                "question_id" => $questionId,
            ],
            [
                "answer" => $answer, // now stores "option_1"
                "meta"   => [
                    "answer_text"    => $answerText ?? $rawAnswer,
                    "selected_option" => $selectedOption,
                    "is_correct"      => $isCorrect,
                ]
            ]
        );

        // For analysis chart
        $answeredQuestions[] = [
            "question_id" => $questionId,
            "answer"      => $answer,
            "correct"     => $isCorrect,
        ];
    }

    
    // ==============================
    // SAVE SURVEY RESULT FOR ANALYSIS
    // ==============================
    SurveyResult::create([
        "user_id"            => $user->id,
        "survey_id"          => $survey->id,
        "score"              => $score,
        "time_spent"         => $timeSpent,
        "answered_questions" => $answeredQuestions, // JSON for stats
    ]);

    return redirect()->route("survey.results", $survey_id);
}





    public function viewAnsweringPage($survey_id){
        $survey = Survey::with('questions')->findOrFail($survey_id);

        // ===========================
        // 1. Check survey password
        // ===========================
        if (!empty($survey->password)) {
            if (!session()->has("survey_access_{$survey_id}")) {
                return redirect()->route('survey.access', $survey_id);
            }
        }

        // ===========================
        // 2. Increment answer counter once
        // ===========================
        $userId = auth()->id();

        $alreadySolved = SurveyAnswer::where('user_id', $userId)
            ->where('survey_id', $survey_id)
            ->exists();

        if (!$alreadySolved) {
            $survey->increment('number_of_answers');
        }

        // ===========================
        // 3. Random Questions Logic
        // ===========================
        $sessionKey = "random_questions_{$survey_id}_{$userId}";

        if ($survey->random_question_count && $survey->random_question_count > 0) {

            // Generate random set for this user ONLY ONCE
            if (!session()->has($sessionKey)) {
                $randomIds = $survey->questions
                    ->pluck('id')
                    ->shuffle()
                    ->take($survey->random_question_count)
                    ->toArray();

                session([$sessionKey => $randomIds]);
            }

            // Load selected random questions
            $questions = Question::whereIn('id', session($sessionKey))->get();

        } else {
            // NO random question limit → return ALL questions
            $questions = $survey->questions;
        }

        $count = $survey->random_question_count ?? $survey->questions->count();
        $questionsShownToUser = $survey->questions->shuffle()->take($count);

        return view('answering-questions', [
            'survey' => $survey,
            'questionsShownToUser' => $questionsShownToUser, // hidden inputs
            'questions' => $questionsShownToUser, // questions displayed
        ]);
    }


    public function destroy($id) {
        $survey = Survey::findOrFail($id);
        $survey->delete();
        return redirect()->route('my-surveys');
    }
        
    public function deleteSurveyQuestion($id) {
        $q = Question::where('id', $id)
                    ->whereNotNull('survey_id')
                    ->where('user_id', Auth::id())
                    ->first();

        if (!$q) {
            return response()->json(['error' => 'not found'], 404);
        }

        $q->delete();
        return response()->json(['success' => true]);
    }

    public function deleteQuestion($id) {
        $q = Question::find($id);

        if (!$q) {
            return response()->json(['error' => 'not found'], 404);
        }

        $q->delete();

        return response()->json(['success' => true], 200);
    }

    public function removeQuestionFromSurvey($id) {
        $surveyId = request('survey_id');

        DB::table('survey_question')
            ->where('survey_id', $surveyId)
            ->where('question_id', $id)
            ->delete();

        return response()->json(['success' => true], 200);
    }


    public function deleteCategory(Request $request) {
        $request->validate([
            'category' => 'required|string',
        ]);

        // Delete all questions that belong to this category
        Question::where('user_id', Auth::id())
            ->where('category', $request->category)
            ->delete();

        return back()->with('success', 'تم حذف جميع الأسئلة في هذه الفئة بنجاح!');
    }

    public function deleteTemplate($id) {
        $q = Question::where('id', $id)
                    ->whereNull('survey_id')
                    ->where('user_id', Auth::id())
                    ->first();

        if (!$q) {
            return response()->json(['error' => 'not found'], 404);
        }

        $q->delete();
        return response()->json(['success' => true]);
    }


    public function deleteCategoryQuestions(Request $request) {
        $request->validate([
            'category' => 'required|string',
        ]);

        Question::where('user_id', Auth::id())
            ->where('category', $request->category)
            ->delete();

        return back()->with('success', 'تم حذف جميع الأسئلة داخل الفئة بنجاح!');
    }

    public function questionsByCategory($category) {
        if ($category === "__all__") {
            return Question::where('user_id', Auth::id())->get();
        }

        return Question::where('user_id', Auth::id())
            ->where('category', $category)
            ->get();
    }

    public function getQuestionsByCategory($category) {
        if ($category === "__all__") {
            return response()->json(
                Question::where('user_id', Auth::id())
                    ->whereNull('survey_id') // show only template questions
                    ->get()
            );
        }

        return response()->json(
            Question::where('user_id', Auth::id())
                ->where('category', $category)
                ->whereNull('survey_id') // still exclude cloned survey questions
                ->get()
        );
    }

    public function addSelectedQuestions(Request $request) {
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'selected_questions' => 'required|array',
        ]);

        $surveyId = $request->survey_id;

        foreach ($request->selected_questions as $questionId) {

            // Get the template question
            $template = Question::where('id', $questionId)
                ->whereNull('survey_id') // must be a template
                ->first();

            if (!$template) {
                continue;
            }

            // 🔥 Check if this template question was already added before
            $alreadyExists = Question::where('survey_id', $surveyId)
                ->where('question', $template->question)
                ->where('task', $template->task)
                ->where('category', $template->category)
                ->exists();

            if ($alreadyExists) {
                continue; // Skip to prevent duplicates
            }

            // 🔥 Clone the template into the survey only once
            Question::create([
                'survey_id'      => $surveyId,
                'user_id'        => Auth::id(),
                'category'       => $template->category,
                'task'           => $template->task,
                'question'       => $template->question,
                'type'           => $template->type,
                'options'        => $template->options,
                'correct_answer' => $template->correct_answer,
            ]);
        }

        // Reload survey and view
        $survey = Survey::findOrFail($surveyId);
        $questions = Question::where('survey_id', $surveyId)->get();
        $categories = Question::where('user_id', Auth::id())
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return redirect()->route('adding-questions', $surveyId)->with('success', 'تمت إضافة الأسئلة المختارة بنجاح!');

    }

    public function storeQuestion(Request $request) {
        $validated = $request->validate([
            'survey_id' => 'required|exists:surveys,id',

            'category' => 'nullable|string|max:255',

            'question' => 'required|string|max:255',
            'question_type' => 'required|string|in:multiple,truefalse,text',
            'task' => 'required|string|max:255',

            'option_1' => 'nullable|string|max:255',
            'option_2' => 'nullable|string|max:255',
            'option_3' => 'nullable|string|max:255',
            'option_4' => 'nullable|string|max:255',
            'correct_answer' => 'nullable|string|max:255',
            'true_false_answer' => 'nullable|string|max:10',
            'text_answer' => 'nullable|string|max:255',

        ]);

        $correct = null;

        if ($validated['question_type'] === 'multiple') {
            $correct = $validated['correct_answer'];
        }

        if ($validated['question_type'] === 'truefalse') {
            $correct = $validated['true_false_answer'];
        }

        if ($validated['question_type'] === 'text') {
            $correct = $validated['text_answer'];
        }


        // Create the question
        Question::create([
            'survey_id'      => $validated['survey_id'],
            'user_id'        => Auth::id(),

            'category'       => $validated['category'] ?? null,
            'task'           => $validated['task'],
            'question'       => $validated['question'],
            'type'           => $validated['question_type'],

            'options'        => json_encode([
                $validated['option_1'] ?? null,
                $validated['option_2'] ?? null,
                $validated['option_3'] ?? null,
                $validated['option_4'] ?? null,
            ]),

            'correct_answer' => $correct,  // now includes text answer
        ]);

        return redirect()->route('adding-questions', $validated['survey_id']);
    }

    public function addingQuestions($survey_id) {
        $survey = Survey::findOrFail($survey_id);
        $questions = Question::where('survey_id', $survey->id)->get();

        
        $categories = Question::where('user_id', Auth::id())
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('adding-questions', compact('survey', 'questions', 'categories'));
    }

    public function addingSurvey(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discription' => 'string|max:150',
            'total_answers' => 'required|integer|min:1',
            'password' => 'nullable|string|max:50',
            'view_survey' => 'required|boolean',
        ]);

        // If password checkbox was removed, set password to null
        if (!$request->has('password') || $request->password === null || $request->password === '') {
            $validated['password'] = null;
        }

        $survey = Survey::create([
            'name' => $validated['name'],
            'discription' => $validated['discription'],
            'total_answers' => $validated['total_answers'],
            'password' => $validated['password'],
            'view_survey' => $validated['view_survey'],
            'user_id' => auth()->id(),
            'question_ids' => json_encode([]),
            'random_question_count' => $validated['random_question_count'] ?? null,
        ]);

        return redirect()->route('adding-questions', ['survey_id' => $survey->id]);
    }


    public function viewAddingSurvey() {
        return view('adding-survey');
    }

    public function toggleVisibility($id) {
        $survey = Survey::findOrFail($id);

        if ($survey->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $survey->view_survey = !$survey->view_survey;
        $survey->save();

        return redirect()->back();
    }

    public function viewSurveys(Request $request) {
        $userId = auth()->id();

        // Surveys the user already solved
        $solvedSurveyIds = SurveyAnswer::where('user_id', $userId)
            ->pluck('survey_id')
            ->unique();

        $surveys = Survey::where('view_survey', true)
            ->whereNotIn('id', $solvedSurveyIds)

            // ✔ Hide surveys that reached their limit
            ->whereColumn('number_of_answers', '<', 'total_answers')

            ->latest()
            ->get();

        return view('surveys', compact('surveys'));
    }



    public function viewMySurveys() {
        $surveys = Survey::where('user_id', auth()->id())->get();

        return view('my-surveys', compact('surveys'));
    }

}
