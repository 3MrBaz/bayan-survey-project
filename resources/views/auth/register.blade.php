<x-layout>
    <section class="bg-[#262626] pt-12 lg:pt-0">
        <div class=""><svg viewBox="0 0 1925 375" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0021713 0L0.000976562 375.003L1925 0H0.0021713Z"
                    fill="#F9B800"></path>
            </svg></div>
        <!-- Session Status -->
        <div class="bg-white border-amber-200 rounded-box w-[90%] md:w-[60%] border p-4 mx-auto">
            <form method="POST" action="{{ route('register') }}" dir="rtl">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('الاسم')" />
                    <x-text-input dir="ltr" id="name" class="block mt-1 w-full" type="text" name="name"
                        :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('البريد الالكتروني')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                        :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="role" :value="__('نوع الحساب')" />

                    <select id="role" name="role"
                        class="block mt-1 w-full border-gray-300 focus:border-amber-400 focus:ring-amber-400 rounded-md shadow-sm"
                        required>
                        <option value="surveyor">مضيف استبيانات</option>
                        <option value="user">حساب لحل الاستبيان</option>
                    </select>

                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('الرقم السري')" />

                    <x-text-input dir="ltr" id="password" class="block mt-1 w-full" type="password"
                        name="password" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('تأكيد الرقم السري')" />

                    <x-text-input dir="ltr" id="password_confirmation" class="block mt-1 w-full" type="password"
                        name="password_confirmation" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('login') }}">
                        {{ __('تسجيل الدخول') }}
                    </a>

                    <x-primary-button class="ms-4">
                        {{ __('انشاء الحساب') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
</x-layout>
