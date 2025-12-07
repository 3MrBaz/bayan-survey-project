<x-layout>
    <style>
        .auth-container {
            width: 90%;
            max-width: 1100px;
            height: 650px;
            display: flex;
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .auth-panel-left,
        .auth-panel-right {
            width: 50%;
            height: 100%;
            transition: transform 0.7s cubic-bezier(.25, .8, .25, 1);
            position: relative;
        }

        /* DESKTOP (your original logic — unchanged) */
        .slide-left {
            transform: translateX(-100%);
        }

        .slide-right {
            transform: translateX(0);
        }

        .slide-right-register {
            transform: translateX(235%);
        }

        .inner-slide {
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        [x-cloak] {
            display: none !important;
        }

        /* ============================================
            MOBILE MODE (sm + md) UP/DOWN TRANSITIONS
           ============================================ */
        @media (max-width: 1023px) {

            .auth-container {
                flex-direction: column;
                height: auto !important;
            }

            .auth-panel-left,
            .auth-panel-right {
                width: 100% !important;
                height: auto !important;
                transition: transform 0.6s ease !important;
            }

            /* Slide UP/DOWN */
            .mobile-up {
                transform: translateY(-75%) !important;
            }

            .mobile-down {
                transform: translateY(135%) !important;
            }

            .mobile-up-register {
                transform: translateY(0%) !important;
            }

            .mobile-down-register {
                transform: translateY(300%) !important;
            }

            .mobile-up-form {
                transform: translateY(-150%) !important;
            }

            .mobile-show {
                transform: translateY(0) !important;
            }

            /* Container padding fix */
            .auth-panel-right,
            .auth-panel-left {
                padding: 30px !important;
            }
        }
    </style>

    <section class="bg-[#262626] min-h-screen flex items-center justify-center py-12 lg:py-0 lg:pt-0 pt-28">

        <div x-data="{
            mode: 'login',
            mobile: window.innerWidth < 1024
        }" x-init="window.addEventListener('resize', () => {
            mobile = window.innerWidth < 1024
        })" class="auth-container">

            {{-- ==========================================
                 LEFT PANEL (AMBER)
                 Desktop: left / right slide
                 Mobile: up/down
            =========================================== --}}
            <div class="auth-panel-left bg-amber-400 text-white p-10 flex flex-col items-center justify-center"
                :class="mobile
                    ?
                    (mode === 'login' ? 'mobile-down' : 'mobile-up-register') :
                    (mode === 'login' ? 'slide-right' : 'slide-left')">

                {{-- LOGIN TEXT --}}
                <div x-show="mode === 'login'" x-transition.opacity.duration.700ms x-cloak
                    class="inner-slide text-center space-y-4">
                    <h2 class="text-4xl font-bold">جديد هنا !</h2>
                    <p class="text-lg leading-relaxed">أنشئ حسابًا جديدًا وابدأ في إنشاء وإدارة الاستبيانات بكل سهولة.
                    </p>

                    <button type="button" @click="mode = 'register'"
                        class="mt-4 px-8 py-2 border border-white rounded-full text-white text-lg
                        hover:bg-white hover:text-amber-500 transition">
                        إنشاء حساب
                    </button>
                </div>

                {{-- REGISTER TEXT --}}
                <div x-show="mode === 'register'" x-transition.opacity.duration.700ms x-cloak
                    class="inner-slide text-center space-y-4">
                    <h2 class="text-4xl font-bold">أهلاً بعودتك !</h2>
                    <p class="text-lg leading-relaxed">إذا كان لديك حساب بالفعل، قم بتسجيل الدخول للمتابعة.</p>

                    <button type="button" @click="mode = 'login'"
                        class="mt-4 px-8 py-2 border border-white rounded-full text-white text-lg
                        hover:bg-white hover:text-amber-500 transition">
                        تسجيل الدخول
                    </button>
                </div>

            </div>

            {{-- ==========================================
                 RIGHT PANEL (FORMS)
                 Desktop: unchanged behavior
                 Mobile: up/down switch
            =========================================== --}}
            <div class="auth-panel-right bg-white p-10 flex flex-col justify-center" dir="rtl"
                :class="mobile
                    ?
                    (mode === 'login' ? 'mobile-up' : 'mobile-down') :
                    (mode === 'login' ? 'slide-right' : 'slide-left')">

                {{-- LOGIN FORM --}}
                <div x-show="mode === 'login'" x-transition.opacity.duration.500ms x-cloak
                    class="inner-slide space-y-6">
                    <h2 class="text-3xl font-bold text-center text-[#262626] mb-4">تسجيل الدخول</h2>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('البريد الالكتروني')" />
                            <x-text-input dir="rtl" id="email" class="block mt-1 w-full text-[#262626]"
                                type="email" name="email" :value="old('email')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password" :value="__('الرقم السري')" />
                            <x-text-input dir="rtl" id="password" class="block mt-1 w-full text-[#262626]"
                                type="password" name="password" required />
                        </div>

                        <div class="flex items-center mt-4 justify-between">
                            <x-primary-button class="w-full text-center hover:bg-amber-400">تسجيل
                                الدخول</x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- REGISTER FORM --}}
                <div x-show="mode === 'register'" x-transition.opacity.duration.500ms x-cloak
                    class="inner-slide space-y-6"
                    :class="mobile
                        ?
                        (mode === 'login' ? 'mobile-down-register' : 'mobile-up-form') :
                        (mode === 'login' ? 'slide-left' : 'slide-right-register')">


                    <h2 class="text-3xl font-bold text-center text-[#262626] mb-4">إنشاء حساب جديد</h2>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div>
                            <x-input-label for="name" value="الاسم" />
                            <x-text-input id="name" class="block text-[#262626] mt-1 w-full" type="text"
                                name="name" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="email" value="البريد الالكتروني" />
                            <x-text-input id="email" class="block mt-1 w-full text-[#262626]" type="email"
                                name="email" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="role" class="text-262626" value="نوع الحساب" />
                            <select id="role" name="role"
                                class="block mt-1 w-full text-[#262626] border-gray-300 rounded-md" required>
                                <option class="text-[#262626]" value="surveyor">مضيف استبيانات</option>
                                <option class="text-[#262626]" value="user">حساب لحل الاستبيان</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password" value="الرقم السري" />
                            <x-text-input id="password" class="block mt-1 w-full text-[#262626]" type="password"
                                name="password" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password_confirmation" value="تأكيد الرقم السري" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full text-[#262626]"
                                type="password" name="password_confirmation" required />
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <x-primary-button class="w-full text-center hover:bg-amber-400">انشاء
                                الحساب</x-primary-button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </section>
</x-layout>
