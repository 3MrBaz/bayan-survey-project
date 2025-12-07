@forelse ($surveys as $survey)
    <div class="card bg-white border-4 border-amber-200 shadow-md text-primary-content w-full max-w-sm">
        <div class="card-body">

            <div class="flex justify-between">
                <h2 class="card-title text-2xl text-[#262626] font-bold">
                    {{ $survey->name }}
                </h2>
                <p class="text-[#262626] text-end">
                    {{ $survey->number_of_answers ?? 0 }} / {{ $survey->total_answers ?? 0 }}
                </p>
            </div>

            <p class="text-[#262626] mt-2">{{ $survey->discription }}</p>

            <div class="card-actions justify-between mt-5 items-center">
                <p class="text-[#262626] text-sm">{{ $survey->user->name ?? 'مجهول' }}</p>

                <a href="{{ route('survey.access', $survey->id) }}"
                    class="btn bg-[#262626] text-white hover:bg-amber-400 hover:text-[#262626]">
                    بدأ الاستبيان
                </a>
            </div>

        </div>
    </div>
@empty
    <p class="text-white text-center col-span-full">لا توجد استبيانات متاحة.</p>
@endforelse
