<x-layout>
    <section class="bg-[#262626] pt-12 lg:pt-0">
        <div class=""><svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg></div>

        <div class="bg-[#262626] min-h-screen py-20">

            <h2 class="text-center text-4xl text-white mb-10">
                تصدير بيانات الاستبيانات
            </h2>

            <div class="w-[90%] md:w-[60%] mx-auto bg-white p-6 rounded shadow">

                <!-- SURVEY SELECTION -->
                <form action="{{ route('export.file') }}" method="GET">

                    <label class="text-amber-400 font-semibold text-lg">اختر الاستبيان</label>

                    <select name="survey_id" class="select w-full rounded-md border-amber-300 mt-3 text-[#262626]"
                        required>
                        <option class="text[#262626]" value="">-- اختر الاستبيان --</option>

                        @foreach ($surveys as $survey)
                            <option value="{{ $survey->id }}">{{ $survey->name }}</option>
                        @endforeach
                    </select>

                    <!-- EXPORT TYPE -->
                    <label class="text-amber-400 font-semibold text-lg mt-6 block">
                        اختر نوع التصدير
                    </label>

                    <select name="export_type" class="select w-full rounded-md border-amber-300 mt-3 text-[#262626]"
                        required>
                        <option value="">-- اختر نوع التصدير --</option>

                        <option class="text-[#262626]" value="questions_json">تصدير الأسئلة (JSON)</option>
                        <option class="text-[#262626]" value="answers_json">تصدير الإجابات (JSON)</option>
                        <option class="text-[#262626]" value="questions_csv">تصدير الأسئلة (CSV)</option>
                        <option class="text-[#262626]" value="answers_csv">تصدير الإجابات (CSV)</option>
                    </select>

                    <button type="submit"
                        class="btn bg-[#262626] text-white w-full mt-6 hover:bg-amber-400 hover:text-[#262626]">
                        تحميل الملف
                    </button>

                </form>

            </div>
        </div>
    </section>
</x-layout>
