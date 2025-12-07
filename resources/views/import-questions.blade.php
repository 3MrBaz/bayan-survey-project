<x-layout>
    <section class="bg-[#262626] pt-12 lg:pt-0">
        <div class=""><svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg></div>

        <div class="bg-white border-4 border-amber-200 rounded-md w-[90%] md:w-[50%] mx-auto mb-20 pb-10">
            <form action="{{ route('import-questions') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6 w-[90%] md:w-[60%] mx-auto mt-10">

                @csrf

                <label class="label text-amber-400 text-lg font-semibold">رفع ملف CSV</label>

                <!-- Hidden File Input -->
                <input id="csvFile" type="file" name="questions_csv" accept=".csv" class="hidden" required>

                <!-- Custom Full-Width Button -->
                <label for="csvFile"
                    class="block w-full bg-gray-700 text-white text-center py-3 rounded-md cursor-pointer hover:bg-gray-800">
                    اختر ملف CSV
                </label>

                <!-- Show filename after choosing -->
                <p id="fileName" class="text-center text-[#262626] font-semibold mt-2"></p>

                <div>
                    <button type="submit" class="btn bg-[#262626] text-white mt-4 w-full p-2">
                        رفع الملف وإضافة الأسئلة
                    </button>
                </div>
            </form>
        </div>

        <div>
            <h2 class="text-center text-3xl p-12">طريقة الاستخدام</h2>

            <div
                class="bg-white border-4 border-amber-200 rounded-md w-[90%] md:w-[50%] mx-auto mb-20 p-10 text-[#262626]">
                <p>يجب تحميل ملف الـCSV المرفق وكتابة الاسئلة على القالب المحدد</p>
                <br>
                <p>يجب تحديد نوع السوال من احد الانواع الثلاثة</p>
                <ul class="list-disc pr-8">
                    <li>multiple</li>
                    <li>truefalse</li>
                    <li>text</li>
                </ul>

                <br>

                <ul class="list-disc">
                    <li>اذا كان السؤال اختيار متعدد يجب كتابة الخيارات وكتابة الخيار الصحيح أ-ب-ج-د في خانتي
                        الـcorrect_answer - answer</li>
                    <li>اذا كان السوال صح وخطا يجب كتابة السؤال وكتابة الخيار الصحيح اما true او false في خانة
                        correct_answer
                    </li>
                    <li>اذا كان السؤال اكمال فراغ يمكن تجاهل الخانات correct_answer - answer</li>
                </ul>
                <br>
                <p>يمكن اضافة تصنيف للسوال في خانة question_category ولكنها غير الزامية</p>

                <br>
                <br>

                <a href="{{ asset('template.csv') }}" download class="text-amber-400 underline font-semibold">
                    اضغط هنا لتحميل قالب الـCSV
                </a>
            </div>
        </div>


        <script>
            document.getElementById('csvFile').addEventListener('change', function() {
                document.getElementById('fileName').textContent = this.files[0]?.name || "";
            });
        </script>
</x-layout>
</section>
