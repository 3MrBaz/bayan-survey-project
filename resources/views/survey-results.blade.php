<x-layout>
    <script src="//unpkg.com/alpinejs" defer></script>

    @php
        // Convert correct answer to readable text
        function getCorrectAnswerText($question)
        {
            if ($question->type !== 'multiple') {
                return $question->correct_answer;
            }

            $options = json_decode($question->options, true) ?? [];
            $index = intval(str_replace('option_', '', $question->correct_answer)) - 1;

            return $options[$index] ?? '';
        }

        // Convert user's answer to readable text
function getUserAnswerText($question, $userAnswer)
{
    if ($question->type !== 'multiple') {
        return $userAnswer;
    }

    $options = json_decode($question->options, true) ?? [];
    $index = intval(str_replace('option_', '', $userAnswer)) - 1;

    return $options[$index] ?? '';
}

// Compare answers
function answersMatch($question, $userAnswer)
{
    if ($question->type === 'text') {
        return false;
    }

    if ($question->type === 'truefalse') {
        return strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer));
    }

    if ($question->type === 'multiple') {
                // We store option_1, option_2... in BOTH places
                return trim($userAnswer) === trim($question->correct_answer);
            }

            return false;
        }
    @endphp

    <div class="bg-[#262626] pt-12 lg:pt-0">

        <!-- Top Yellow Shape -->
        <div>
            <svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800" />
            </svg>
        </div>

        <div class="w-[90%] md:w-[60%] mx-auto pt-10 mt-[-10px]">

            <!-- Survey Title -->
            <h2 class="text-3xl font-bold text-center text-amber-400 mb-5">
                نتائج الاستبيان: {{ $survey->name }}
            </h2>

            <!-- Score -->
            <p class="text-xl font-semibold text-center mb-10">
                درجتك: {{ $score }} / {{ $totalQuestions }}
            </p>

            <!-- Answers Loop -->
            @forelse ($answers as $answerRecord)
                @php
                    $q = $answerRecord->question;
                @endphp

                @if ($q)
                    <div class="bg-white p-5 rounded-lg shadow mb-5 border border-amber-300">

                        <!-- Question -->
                        <p class="font-bold text-lg text-[#262626]">
                            {{ $q->question }}
                        </p>

                        <!-- User Answer -->
                        <p class="mt-2 text-[#262626]">
                            <span class="font-semibold">إجابتك:</span>
                            {{ getUserAnswerText($q, $answerRecord->answer) ?: 'لم يتم الإجابة' }}
                        </p>

                        <!-- Correct Answer -->
                        @if ($q->type !== 'text')
                            <p class="mt-1 text-[#262626]">
                                <span class="font-semibold">الإجابة الصحيحة:</span>
                                {{ getCorrectAnswerText($q) }}
                            </p>
                        @endif

                        @php
                            $isCorrect = answersMatch($q, $answerRecord->answer);
                        @endphp

                        <!-- Grading Display -->
                        @if ($q->type === 'text')
                            <p class="text-[#262626] mt-2">
                                هذا السؤال لا يتم تصحيحه تلقائياً
                            </p>
                        @else
                            @if ($isCorrect)
                                <p class="text-green-600 font-semibold mt-2">✔ إجابة صحيحة</p>
                            @else
                                <p class="text-red-600 font-semibold mt-2">✘ إجابة خاطئة</p>
                            @endif
                        @endif

                    </div>
                @endif
            @empty
                <p class="text-center text-white text-lg mt-10">
                    لا توجد إجابات مسجلة لهذا الاستبيان حتى الآن.
                </p>
            @endforelse

            <!-- Home Button -->
            <a href="/"
                class="btn text-center w-full bg-white hover:bg-amber-400 text-[#262626] font-semibold text-lg mt-6">
                الرئيسية
            </a>
        </div>

        <!-- Bottom Yellow Shape -->
        <div class="rotate-180">
            <svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800" />
            </svg>
        </div>
    </div>
</x-layout>
