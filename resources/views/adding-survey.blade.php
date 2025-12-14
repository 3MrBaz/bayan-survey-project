<x-layout>
    <section class="bg-[#262626] pb-56 pt-12 lg:pt-0">


        <div class=""><svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg></div>

        <h2 class="text-center text-4xl text-white m-20 md:mt-[-60px]">اضافة استبيان</h2>

        <form action="{{ route('addingSurvey') }}" method="POST">

            @csrf

            <fieldset class="fieldset bg-white border-amber-200 rounded-box w-[90%] md:w-[60%] border p-4 mx-auto">
                <label class="label text-amber-400 text-lg font-semibold">اسم الاستبيان</label>
                <input type="text" name="name" class="input w-full rounded-md text-[#262626] font-semibold"
                    placeholder="اسم الاستبيان" required />

                <label class="label text-amber-400 text-lg font-semibold">وصف الاستبيان</label>
                <input type="text" name="discription" class="input w-full rounded-md text-[#262626] font-semibold"
                    placeholder="وصف الاستبيان" />

                <label class="label text-amber-400 text-lg font-semibold">اجمالي الحلول</label>
                <input type="number" name="total_answers" min="1"
                    class="input w-full rounded-md text-[#262626] font-semibold" placeholder="عدد الحلول"
                    value="0" />

                <div x-data="{ showCodeInput: false }" class="flex flex-col space-y-3">
                    <div class="flex flex-row items-center">
                        <label class="label text-amber-400 text-lg font-semibold">اضافة رمز للاستبيان</label>
                        <input type="checkbox" class="rounded-md text-amber-400 font-semibold m-3"
                            x-model="showCodeInput" />
                    </div>

                    <div x-show="showCodeInput" x-transition>
                        <label class="label text-amber-400 text-lg font-semibold mb-2">رمز الاستبيان</label>
                        <input type="text" name="password" x-bind:disabled="!showCodeInput"
                            x-bind:value="showCodeInput ? password : ''" x-model="password"
                            class="input w-full rounded-md text-[#262626] font-semibold" placeholder="أدخل الرمز هنا" />
                    </div>
                </div>

                <div class="flex flex-col space-y-2">
                    <label class="label text-amber-400 text-lg font-semibold">اظهار الاستبيان</label>

                    <div class="flex items-center space-x-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="view_survey" value="1"
                                class="rounded-md text-amber-400 font-semibold" />
                            <span class="text-[#262626] p-5 text-lg">عرض الاستبيان</span>
                        </label>

                        <label class="flex items-center space-x-2">
                            <input type="radio" name="view_survey" value="0"
                                class="rounded-md text-amber-400 font-semibold" />
                            <span class="text-[#262626] p-5 text-lg">إخفاء الاستبيان</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn bg-[#262626] text-white">استمرار</button>
            </fieldset>
        </form>


    </section>
    <script src="//unpkg.com/alpinejs" defer></script>

</x-layout>
