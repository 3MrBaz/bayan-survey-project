<x-layout>
    <section class="bg-[#262626] pb-56 pt-12 lg:pt-0">


        <div class=""><svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg></div>

        <h2 class="text-center text-4xl text-white m-20 md:mt-[-60px]">استبياناتي</h2>
        <div class="flex justify-center">
            <div
                class="w-full p-4 mx-10 md:mx-40 lg:mx-60 overflow-x-auto rounded-lg border-4 border-amber-200 bg-[#ffffe4]">
                <table class="min-w-[800px] w-full text-[#262626] text-lg">
                    <!-- head -->
                    <thead class="bg-[#ffffe4]">
                        <tr>
                            <th></th>
                            <th>اسم الاستبيان</th>
                            <th>وصف الاستبيان</th>
                            <th>عدد الحلول</th>
                            <th>اظهار الاحصائيات</th>
                            <th>تعديل</th>
                            <th>اظهار الاستبيان</th>
                            <th>حذف الاستبيان</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surveys as $index => $survey)
                            <tr>
                                <th class="text-center pt-3">{{ $survey->id }}</th>
                                <td class="text-center pt-3">{{ $survey->name }}</td>
                                <td class="text-center pt-3">{{ $survey->discription }}</td>
                                <td class="text-center pt-3">{{ $survey->number_of_answers }}</td>

                                <th class="text-center">
                                    <a href="{{ route('analysis', ['survey_id' => $survey->id]) }}">
                                        <i class="fa-solid fa-chart-pie text-[#262626] hover:text-amber-400"></i>
                                    </a>
                                </th>

                                <td class="text-center">
                                    <a href="{{ route('adding-questions', ['survey_id' => $survey->id]) }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </td>

                                <th class="text-center">
                                    <form action="{{ route('survey.toggle_visibility', $survey->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit">
                                            @if ($survey->view_survey)
                                                <i class="fa-solid fa-eye text-amber-400"></i>
                                            @else
                                                <i class="fa-solid fa-eye-slash text-[#262626]"></i>
                                            @endif
                                        </button>
                                    </form>
                                </th>

                                <td class="text-center">
                                    <form action="{{ route('surveys.destroy', $survey->id) }}" method="POST"
                                        onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا الاستبيان؟');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit">
                                            <i class="fa-solid fa-trash-can text-[#262626] hover:text-amber-400"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>


        </div>
        <br>
        <br>
        <br>
    </section>
</x-layout>
