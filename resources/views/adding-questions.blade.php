<x-layout>

    <section class="bg-[#262626] pb-56 pt-12 lg:pt-0">

        <div class="">
            <svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg>
        </div>

        <h2 class="text-center text-4xl text-white m-20 md:mt-[-60px]">
            اضافة تفاصيل الاستبيان
        </h2>

        <!-- ============================
            CATEGORY SECTION
        ============================= -->
        <div class="w-[90%] md:w-[60%] mx-auto mt-10 bg-white p-4 rounded-md shadow text-[#262626]">

            <label class="label text-amber-400 text-lg font-semibold">اختر الفئة</label>

            @foreach ($categories as $cat)
                <div class="flex justify-between items-center bg-amber-50 p-2 mb-2 rounded">
                    <span>{{ $cat }}</span>

                    <!-- Delete category -->
                    <form action="{{ route('delete-category') }}" method="POST"
                        onsubmit="return confirm('هل أنت متأكد من حذف جميع الأسئلة داخل هذه الفئة؟');">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="category" value="{{ $cat }}">
                        <button type="submit" class="text-red-600 font-bold hover:text-red-800">حذف التصنيف</button>
                    </form>

                </div>
            @endforeach

            <select id="categorySelect" class="select w-full rounded-md border-amber-300">
                <option value="">-- اختر فئة --</option>
                <option value="__all__" class="font-bold text-blue-700">عرض جميع الأسئلة</option>

                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            <!-- Load QUESTIONS BY CATEGORY -->
            <form action="{{ route('add-selected-questions') }}" method="POST">
                @csrf
                <input type="hidden" name="survey_id" value="{{ $survey->id }}">

                <div id="questionsContainer"></div>

                <button id="addSelectedBtn" type="submit" class="btn bg-[#262626] text-white mt-4 w-full p-2 hidden">
                    إضافة الأسئلة المختارة
                </button>
            </form>

        </div>

        <!-- ============================
            MANUAL ADD QUESTION FORM
        ============================= -->
        <div class="w-full mx-auto mt-10">
            <form action="{{ route('store-question') }}" method="POST" class="space-y-10" autocomplete="off">
                @csrf
                <input type="hidden" name="survey_id" value="{{ $survey->id }}">

                <fieldset x-data="{ type: '' }"
                    class="fieldset bg-white border-amber-200 rounded-box w-[90%] md:w-[60%] border p-4 mx-auto">

                    <label class="label text-amber-400 text-lg font-semibold">نوع السؤال</label>

                    <div class="flex space-x-4 mb-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="question_type" value="multiple" x-model="type"
                                class="text-amber-400 focus:ring-amber-400">
                            <span class="text-[#262626] font-semibold p-2 text-lg">اختيار من متعدد</span>
                        </label>

                        <label class="flex items-center space-x-2">
                            <input type="radio" name="question_type" value="truefalse" x-model="type"
                                class="text-amber-400 focus:ring-amber-400">
                            <span class="text-[#262626] font-semibold p-2 text-lg">صح / خطأ</span>
                        </label>

                        <label class="flex items-center space-x-2">
                            <input type="radio" name="question_type" value="text" x-model="type"
                                class="text-amber-400 focus:ring-amber-400">
                            <span class="text-[#262626] font-semibold p-2 text-lg">إجابة نصية</span>
                        </label>
                    </div>

                    <label class="label text-amber-400 text-lg font-semibold">المهمة</label>
                    <input type="text" name="task" class="input w-full rounded-md text-[#262626] font-semibold"
                        placeholder="اكتب المهمة هنا" required />

                    <label class="label text-amber-400 text-lg font-semibold">السؤال</label>
                    <input type="text" name="question" class="input w-full rounded-md text-[#262626] font-semibold"
                        placeholder="اكتب السؤال هنا" required />

                    <!-- Multiple Choice -->
                    <div x-show="type === 'multiple'" x-transition class="space-y-2 mt-4">
                        <label class="label text-amber-400 text-lg font-semibold">خيارات الإجابة</label>

                        <template x-for="i in 4" :key="i">
                            <div class="flex items-center space-x-3">
                                <input type="text" :name="'option_' + i"
                                    class="input w-full rounded-md text-[#262626] font-semibold"
                                    :placeholder="'الخيار ' + i">
                                <input :required="type === 'multiple'" type="radio" name="correct_answer"
                                    :value="'option_' + i" class="text-amber-400 focus:ring-amber-400">
                            </div>
                        </template>
                    </div>

                    <!-- True/False -->
                    <div x-show="type === 'truefalse'" x-transition class="space-y-2 mt-4">
                        <label class="label text-amber-400 text-lg font-semibold">الإجابة الصحيحة</label>

                        <label class="text-[#262626] text-lg p-3">
                            <input type="radio" name="true_false_answer" value="true" class="text-amber-400">
                            صح
                        </label>

                        <label class="text-[#262626] text-lg p-3">
                            <input type="radio" name="true_false_answer" value="false" class="text-amber-400">
                            خطأ
                        </label>
                    </div>

                    <!-- Text -->
                    <div x-show="type === 'text'" x-transition class="space-y-2 mt-4">
                        <label class="label text-amber-400 text-lg font-semibold">الإجابة النصية</label>
                        <textarea name="text_answer" class="textarea w-full rounded-md text-[#262626] font-semibold"></textarea>
                    </div>

                    <button type="submit" class="btn bg-[#262626] text-white mt-6 text-lg">
                        اضافة
                    </button>

                </fieldset>
            </form>
        </div>

        <div class="h-10"></div>
        <div style="margin-top: 40px;"></div>

        <!-- ============================
            RANDOM COUNT FORM
        ============================= -->
        <div class="w-full mx-auto">
            <form action="{{ route('update-random-question-count') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="survey_id" value="{{ $survey->id }}">

                <div class="flex flex-col items-center mt-10">
                    <label class="text-amber-400 text-lg font-semibold">عدد الأسئلة العشوائية لكل مستخدم</label>

                    <div>
                        <input type="number" name="random_question_count" min="1"
                            value="{{ old('random_question_count', $survey->random_question_count) }}"
                            class="input w-full rounded-md m-0 text-[#262626] font-semibold w-[90%] md:w-[60%]"
                            placeholder="مثال: 10">

                        <button type="submit"
                            class="btn bg-amber-400 text-white p-3 rounded-md text-center">حفظ</button>
                    </div>
                </div>

            </form>
        </div>

        <!-- ============================
            LIST OF ADDED QUESTIONS
        ============================= -->
        @if (isset($questions) && $questions->count())
            <div class="mt-20 bg-[#ffffe4] border border-amber-300 rounded-lg p-4 w-[90%] md:w-[60%] mx-auto">

                <h2 class="text-2xl font-semibold text-amber-400 mb-4 text-center">الأسئلة المضافة</h2>

                <ul class="space-y-3">
                    @foreach ($questions as $q)
                        <li class="bg-white p-4 rounded-md shadow border border-amber-200">

                            <p class="font-bold text-lg text-[#262626]">{{ $q->question }}</p>

                            @if ($q->type === 'multiple')
                                <ul class="list-disc list-inside text-[#262626]">
                                    @foreach (json_decode($q->options) as $index => $opt)
                                        @if ($opt)
                                            <li @class([
                                                'font-bold text-[#262626]' =>
                                                    'option_' . ($index + 1) === $q->correct_answer,
                                            ])>
                                                {{ $opt }}
                                                @if ('option_' . ($index + 1) === $q->correct_answer)
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif

                            @if ($q->type === 'truefalse')
                                <p class="mt-2 text-[#262626]">
                                    <span class="font-semibold">الإجابة الصحيحة:</span>
                                    <span class="text-green font-bold">
                                        {{ $q->true_false_answer === 'true' ? 'خطأ' : 'صح' }}
                                    </span>
                                </p>
                            @endif

                            <!-- Delete question -->
                            <button type="button" onclick="deleteCategoryQuestion({{ $q->id }})"
                                class="text-red-600 hover:text-red-800 font-semibold text-sm mt-2 underline">
                                حذف
                            </button>

                        </li>
                    @endforeach
                </ul>

                <a href="/my-surveys" class="btn flex mt-5 text-center bg-[#262626] text-white">
                    استبياناتي
                </a>

            </div>
        @endif

    </section>

    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        document.getElementById('categorySelect').addEventListener('change', function() {
            let category = this.value;
            let container = document.getElementById('questionsContainer');
            let addBtn = document.getElementById('addSelectedBtn');

            container.innerHTML = '';
            addBtn.classList.add('hidden');

            if (!category) return;

            fetch(`/questions-by-category/${category}`)
                .then(response => response.json())
                .then(data => {

                    container.innerHTML = "";

                    if (!Array.isArray(data) || data.length === 0) {
                        container.innerHTML =
                            "<p class='text-center text-gray-500'>لا توجد أسئلة في هذه الفئة</p>";
                        return;
                    }

                    // Add Select All
                    container.insertAdjacentHTML("beforeend", `
                <div class="p-3 border rounded-md bg-amber-100 flex items-center my-3">
                    <label class="flex items-center space-x-3">
                        <input class="text-amber-400" type="checkbox" id="selectAllQuestions">
                        <span class="text-[#262626] font-semibold px-3">تحديد جميع الأسئلة</span>
                    </label>
                </div>
            `);

                    // List all questions
                    data.forEach(q => {
                        container.insertAdjacentHTML("beforeend", `
                    <div class="p-3 border rounded-md bg-amber-50 flex justify-between items-center my-3">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" class="text-amber-400 question-box" name="selected_questions[]" value="${q.id}">
                            <span class="text-[#262626] px-3">${q.question}</span>
                        </label>
                        <button onclick="deleteCategoryQuestion(${q.id})"
                                class="text-red-600 font-semibold hover:text-red-800">
                            حذف
                        </button>
                    </div>
                `);
                    });

                    addBtn.classList.remove('hidden');

                    // ⭐ ADD THIS INSIDE THE FETCH CALLBACK ONLY AFTER ELEMENT EXISTS
                    let selectAll = document.getElementById('selectAllQuestions');
                    selectAll.addEventListener('change', function() {
                        let checked = this.checked;
                        document.querySelectorAll(".question-box").forEach(cb => cb.checked = checked);
                    });

                })
                .catch(err => {
                    console.error("Error loading questions:", err);
                    container.innerHTML = "<p class='text-center text-red-600'>حدث خطأ أثناء تحميل الأسئلة</p>";
                });
        });
    </script>

</x-layout>
