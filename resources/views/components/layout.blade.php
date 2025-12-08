<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400..800&family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=Noto+Sans+Arabic:wght@100..900&family=Rubik:ital,wght@0,300..900;1,300..900&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
        rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const page = document.querySelector(".page");

        const dir = sessionStorage.getItem("transition");

        if (dir === "login→register") page.classList.add("slide-in-right");
        if (dir === "register→login") page.classList.add("slide-in-left");

        sessionStorage.removeItem("transition");
    });

    function goRegister() {
        const page = document.querySelector(".page");
        page.classList.add("slide-out-left");
        sessionStorage.setItem("transition", "login→register");
        setTimeout(() => window.location.href = "/register", 480);
    }

    function goLogin() {
        const page = document.querySelector(".page");
        page.classList.add("slide-out-right");
        sessionStorage.setItem("transition", "register→login");
        setTimeout(() => window.location.href = "/login", 480);
    }
</script>

<body dir="rtl" class="tajawal-regular font-semibold">

    <div class="navbar bg-[#ffffe4] shadow-sm fixed z-50 text-[#262626]">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h8m-8 6h16" />
                    </svg>
                </div>
                <ul tabindex="-1"
                    class="menu no-arrow menu-sm dropdown-content bg-[#ffffe4] rounded-box z-1 mt-5 w-52 p-2 shadow text-lg">
                    <li class=""><a href="/">الرئيسية</a></li>
                    <li class=""><a href="/surveys">الاستبيانات</a></li>
                    <!--<li>
                        <a class="">طريقة الاستخدام</a>
                        <ul class="p-2">
                            <li><a>Submenu 1</a></li>
                            <li><a>Submenu 2</a></li>
                        </ul>
                    </li>-->

                    @php
                        $user = Auth::user();
                    @endphp

                    @if ($user && $user->role === 'surveyor')
                        <!-- Content for Surveyors -->
                        <li><a href="/my-surveys">استبياناتي</a></li>
                        <li class="text-[#262626]"><a href="/adding-survey">اضافة استبيان</a></li>
                        <li class="text-[#262626]"><a href="/export">تصدير الاستبيان</a></li>
                        <li class="text-[#262626]"><a href="/import-questions">اضافة ملف اسئلة</a></li>
                    @elseif ($user && $user->role === 'surveyUser')
                        <!-- Content for Survey Users -->
                        <li><a>حل استبيان</a></li>
                    @else
                        <!-- Optional fallback -->
                        <li class="hidden"><a></a></li>
                    @endif

                </ul>
            </div>
            <a href="/">
                <img class="md:mr-5 mr-0" src="{{ asset('logo.svg') }}" alt="" width="40px">
                <a class="btn btn-ghost text-4xl">بيان</a>
            </a>

        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 text-xl">
                <li class=""><a href="/">الرئيسية</a></li>
                <li class="mx-3"><a href="/surveys">الاستبيانات</a></li>
                <li>
                    <!--<details>
                        <summary class="mx-3">طريقة الاستخدام</summary>
                        <ul class="p-2 w-44 mt-3 bg-white">
                            <li><a>Submenu 1</a></li>
                            <li><a>Submenu 2</a></li>
                        </ul>
                    </details>-->
                </li>
                @php
                    $user = Auth::user();
                @endphp

                @if ($user && $user->role === 'surveyor')
                    <!-- Content for Surveyors -->
                    <li><a href="/my-surveys">استبياناتي</a></li>
                    <li>
                        <details>
                            <summary class="mx-3">ادارة الاستبيانات</summary>
                            <ul class="p-2 w-60 no-arrow mt-3 bg-white">
                                <li class="text-[#262626]"><a href="/adding-survey">اضافة استبيان</a></li>
                                <li class="text-[#262626]"><a href="/export">تصدير الاستبيان</a></li>
                                <li class="text-[#262626]"><a href="/import-questions">اضافة ملف اسئلة</a></li>
                            </ul>
                        </details>
                    </li>
                @elseif ($user && $user->role === 'surveyUser')
                    <!-- Content for Survey Users -->
                    <li><a>حل استبيان</a></li>
                @else
                    <!-- Optional fallback -->
                    <li class="hidden"><a></a></li>
                @endif
            </ul>
        </div>
        <div class="navbar-end text-[#ffffe4]">

            @php
                $user = Auth::user();
            @endphp

            @if ($user)
                <!-- Logged-in user -->
                <a href="/profile" class="btn bg-[#262626] text-sm hover:text-[#262626] hover:bg-[#ffffd7] py-3">
                    الحساب
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="btn bg-[#262626] text-sm hover:text-[#262626] hover:bg-[#ffffd7] mr-2 px-2 py-3">
                        تسجيل الخروج
                    </button>
                </form>
            @else
                <!-- Guest user -->
                <a href="/login" class="btn text-sm bg-[#262626] hover:text-[#262626] hover:bg-[#ffffd7] px-3">
                    تسجيل الدخول
                </a>
            @endif


        </div>
    </div>


    {{ $slot }}

    <footer class="footer footer-horizontal footer-center bg-[#111111] text-base-content rounded p-10 ">
        <nav class="grid grid-flow-col gap-4">
            <a href="/" class="link link-hover  text-white">الرئيسية</a>
            <a href="/surveys" class="link link-hover  text-white">الاستبيانات</a>
        </nav>
        <nav>
            <div class="grid grid-flow-col gap-4">
                <a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        class="fill-current text-white">
                        <path
                            d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z">
                        </path>
                    </svg>
                </a>
                <a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        class="fill-current text-white">
                        <path
                            d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z">
                        </path>
                    </svg>
                </a>
                <a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        class="fill-current text-white">
                        <path
                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z">
                        </path>
                    </svg>
                </a>
            </div>
        </nav>
        <aside>
            <p class="text-white">Copyright BAYAN © 2025</p>
        </aside>
    </footer>

    <script src="https://kit.fontawesome.com/1adcaefdde.js" crossorigin="anonymous"></script>
</body>

</html>
