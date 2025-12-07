<x-layout>

    <section class="bg-[#262626] pb-56 pt-12 lg:pt-0">

        <div class="">
            <svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg>
        </div>
        <div
            class=" w-[90%] md:w-[60%] mx-auto mt-10 bg-white p-4 rounded-md shadow text-[#262626] border-4 border-amber-200">

            <h2 class="mb-3 text-3xl text-center p-4 text-amber-400">{{ $survey->name }}</h2>
            <p class="py-3 text-[#262626]">هذا الاستطلاع محمي بكلمة مرور.</p>

            <form method="POST" action="{{ route('survey.check', $survey->id) }}">
                @csrf

                <div class="mb-3">
                    <label>كلمة المرور:</label>
                    <input type="password" name="password" class="form-control rounded-md border-amber-200">

                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary bg-[#262626] text-white w-full mt-6">دخول</button>
            </form>

        </div>

    </section>
</x-layout>
