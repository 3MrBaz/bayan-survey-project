    <script src="//unpkg.com/alpinejs" defer></script>

    <x-layout>

        <div class="bg-[#262626] pt-12 lg:pt-0">
            <div class=""><svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                        fill="#F9B800"></path>
                </svg></div>

            <form action="{{ route('survey.submit', $survey->id) }}" method="POST">
                @csrf

                {{-- Hidden list of shown questions --}}
                @foreach ($questions as $q)
                    <input type="hidden" name="shown_questions[]" value="{{ $q->id }}">
                @endforeach

                <div class="bg-[#ffffe4] border border-amber-300 rounded-lg p-4 w-[90%] md:w-[60%] mx-auto">

                    <h2 class="text-2xl font-semibold text-amber-400 mb-4 text-center">{{ $survey->name }}</h2>

                    <input type="hidden" name="start_time" value="{{ time() }}">

                    <ul class="space-y-3">
                        @foreach ($questions as $q)
                            <li class="bg-white p-4 rounded-md shadow border border-amber-200">

                                <p class="font-bold text-lg text-[#262626]">{{ $q->question }}</p>

                                {{-- MULTIPLE --}}
                                @if ($q->type === 'multiple')
                                    <div x-data="{ selected: '' }" class="space-y-2 mt-2 flex flex-col">
                                        <input type="hidden" name="answer_{{ $q->id }}" x-model="selected">

                                        <div x-data="{ selected: '' }" class="space-y-2 mt-2 flex flex-col">
                                            <input type="hidden" name="answer_{{ $q->id }}" x-model="selected">

                                            @foreach (json_decode($q->options, true) as $index => $opt)
                                                <button type="button" @click="selected = 'option_{{ $index + 1 }}'"
                                                    :class="selected === 'option_{{ $index + 1 }}'
                                                        ?
                                                        'bg-amber-400 text-[#262626]' :
                                                        'bg-[#262626] text-white'"
                                                    class="text-center px-4 py-2 rounded-md transition my-2">
                                                    {{ $opt }}
                                                </button>
                                            @endforeach

                                        </div>
                                    </div>
                                @endif

                                {{-- TRUE/FALSE --}}
                                @if ($q->type === 'truefalse')
                                    <div class="flex space-x-6 mt-3">
                                        <label class="flex items-center space-x-2 text-[#262626] text-lg p-3">
                                            <input type="radio" class="text-amber-400"
                                                name="answer_{{ $q->id }}" value="true">
                                            <span>صح</span>
                                        </label>
                                        <label class="flex items-center space-x-2 text-[#262626] text-lg p-3">
                                            <input type="radio" class="text-amber-400"
                                                name="answer_{{ $q->id }}" value="false">
                                            <span>خطأ</span>
                                        </label>
                                    </div>
                                @endif

                                {{-- TEXT --}}
                                @if ($q->type === 'text')
                                    <textarea name="answer_{{ $q->id }}" rows="3" class="textarea w-full text-[#262626]"></textarea>
                                @endif

                            </li>
                        @endforeach
                    </ul>

                    <button type="submit"
                        class="btn bg-[#262626] text-white text-lg w-full hover:bg-amber-400 hover:text-[#262626] mt-5">
                        تسليم
                    </button>

                </div>
            </form>

            <div class="rotate-180"><svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                        fill="#F9B800"></path>
                </svg></div>
        </div>
    </x-layout>
