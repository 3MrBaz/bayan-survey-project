<x-layout>

    <div x-data="{ mode: 'login' }" class="min-h-screen flex items-center justify-center bg-[#262626] py-20">

        <div class="auth-container">

            <!-- LEFT PANEL (Forms) -->
            <div class="auth-panel bg-white p-10 fade" :class="mode === 'register' ? 'slide-left' : 'slide-right'">

                <!-- LOGIN FORM -->
                <div x-show="mode === 'login'" x-transition.opacity>
                    <h2 class="text-3xl font-bold mb-6 text-center">Welcome back,</h2>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <label class="text-gray-600">Email</label>
                        <input type="email" name="email" class="input w-full mb-4" required>

                        <label class="text-gray-600">Password</label>
                        <input type="password" name="password" class="input w-full mb-2" required>

                        <a href="#" class="text-sm text-gray-500">Forgot password?</a>

                        <button class="btn bg-amber-400 text-white w-full mt-4">SIGN IN</button>
                    </form>

                    <button class="btn border mt-4 w-full" @click="mode = 'register'">
                        New here? SIGN UP →
                    </button>
                </div>


                <!-- REGISTER FORM -->
                <div x-show="mode === 'register'" x-transition.opacity>
                    <h2 class="text-3xl font-bold mb-6 text-center">Create an account</h2>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <label class="text-gray-600">Name</label>
                        <input type="text" name="name" class="input w-full mb-4" required>

                        <label class="text-gray-600">Email</label>
                        <input type="email" name="email" class="input w-full mb-4" required>

                        <label class="text-gray-600">Password</label>
                        <input type="password" name="password" class="input w-full mb-4" required>

                        <label class="text-gray-600">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="input w-full mb-4" required>

                        <button class="btn bg-amber-400 text-white w-full mt-2">SIGN UP</button>
                    </form>

                    <button class="btn border mt-4 w-full" @click="mode = 'login'">
                        Already have an account? SIGN IN →
                    </button>
                </div>

            </div>


            <!-- RIGHT PANEL (Image + CTA) -->
            <div class="auth-panel auth-panel-right" :class="mode === 'register' ? 'slide-left' : 'slide-right'"
                style="background-image: url('/images/auth-bg.jpg');">

                <!-- LOGIN MODE TEXT -->
                <div x-show="mode === 'login'" x-transition>
                    <h2 class="text-4xl font-bold mb-4">New here?</h2>
                    <p class="mb-6 text-lg">Sign up and discover great opportunities!</p>
                    <button class="btn border-white text-white" @click="mode = 'register'">
                        SIGN UP
                    </button>
                </div>

                <!-- REGISTER MODE TEXT -->
                <div x-show="mode === 'register'" x-transition>
                    <h2 class="text-4xl font-bold mb-4">Welcome back!</h2>
                    <p class="mb-6 text-lg">Login and continue your journey.</p>
                    <button class="btn border-white text-white" @click="mode = 'login'">
                        SIGN IN
                    </button>
                </div>
            </div>

        </div>

    </div>

</x-layout>
