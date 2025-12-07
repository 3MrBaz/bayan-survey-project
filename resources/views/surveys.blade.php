<x-layout>
    <section class="bg-[#262626] pt-12 lg:pt-0">

        {{-- Top SVG Shape --}}
        <div>
            <svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg>
        </div>

        {{-- Title --}}
        <h2 class="text-center text-5xl font-bold mt-20 md:mt-12 lg:mt-[-15px] text-white">
            الاستبيانات
        </h2>

        {{-- Search Bar --}}
        <div class="flex justify-center mt-10 mb-10">
            <input id="searchInput" type="text"
                class="w-[90%] max-w-md px-4 py-2 border border-gray-300 text-black rounded-md"
                placeholder="ابحث باسم الاستطلاع أو اسم الناشر">
        </div>

        {{-- Surveys Grid --}}
        <div id="surveyGrid" class="mt-20 xl:mx-48 mx-3 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 px-4">
            @include('survey-cards', ['surveys' => $surveys])
        </div>

        {{-- Bottom SVG --}}
        <div class="rotate-180 mt-20">
            <svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg>
        </div>

    </section>

    {{-- AJAX Live Search --}}
    <script>
        const searchInput = document.getElementById('searchInput');
        const surveyGrid = document.getElementById('surveyGrid');
        let timer = null;

        searchInput.addEventListener('keyup', function() {
            clearTimeout(timer);

            timer = setTimeout(() => {
                const query = searchInput.value;

                fetch(`/surveys-ajax?search=${encodeURIComponent(query)}`)
                    .then(res => res.text())
                    .then(html => {
                        surveyGrid.innerHTML = html;
                    })
                    .catch(err => console.error(err));
            }, 300);
        });
    </script>
</x-layout>
