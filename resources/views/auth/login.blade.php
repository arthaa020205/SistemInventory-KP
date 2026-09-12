<x-guest-layout>

```
<div class="min-h-screen flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-md">

        <!-- Logo -->
        <div class="text-center mb-8">

            <div class="inline-flex items-center justify-center
                        w-16 h-16
                        rounded-2xl
                        bg-indigo-600
                        shadow-lg shadow-indigo-200">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-8 h-8 text-white"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>

                </svg>

            </div>

            <h1 class="mt-5 text-2xl font-bold text-gray-900">
                Inventory System
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                CV Cahaya Khanza Plastik
            </p>

        </div>


        <!-- Login Card -->
        <div class="bg-white rounded-2xl
                    border border-gray-100
                    shadow-xl shadow-gray-200/50
                    p-7 sm:p-8">

            <div class="mb-7">

                <h2 class="text-xl font-semibold text-gray-900">
                    Selamat Datang
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Silakan masuk untuk mengakses sistem inventory.
                </p>

            </div>


            <!-- Session Status -->
            <x-auth-session-status
                class="mb-5"
                :status="session('status')" />


            <form method="POST" action="{{ route('login') }}">
                @csrf


                <!-- Email -->
                <div>

                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700">

                        Email

                    </label>

                    <div class="relative mt-2">

                        <div class="absolute inset-y-0 left-0
                                    flex items-center pl-3
                                    pointer-events-none">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 text-gray-400"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M3 8l9 6 9-6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>

                            </svg>

                        </div>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan email"

                            class="w-full
                                   rounded-xl
                                   border-gray-200
                                   bg-gray-50
                                   pl-10
                                   pr-4
                                   py-3
                                   text-sm
                                   focus:border-indigo-500
                                   focus:bg-white
                                   focus:ring-2
                                   focus:ring-indigo-100
                                   transition">

                    </div>

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-2" />

                </div>


                <!-- Password -->
                <div class="mt-5">

                    <div class="flex items-center justify-between">

                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700">

                            Password

                        </label>

                    </div>


                    <div class="relative mt-2">

                        <div class="absolute inset-y-0 left-0
                                    flex items-center pl-3
                                    pointer-events-none">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 text-gray-400"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm10-9V7a4 4 0 00-8 0v3h8z"/>

                            </svg>

                        </div>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan password"

                            class="w-full
                                   rounded-xl
                                   border-gray-200
                                   bg-gray-50
                                   pl-10
                                   pr-4
                                   py-3
                                   text-sm
                                   focus:border-indigo-500
                                   focus:bg-white
                                   focus:ring-2
                                   focus:ring-indigo-100
                                   transition">

                    </div>

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2" />

                </div>


                <!-- Remember -->
                <div class="mt-5">

                    <label
                        for="remember_me"
                        class="inline-flex items-center cursor-pointer">

                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="rounded-md
                                   border-gray-300
                                   text-indigo-600
                                   shadow-sm
                                   focus:ring-indigo-500">

                        <span class="ms-2 text-sm text-gray-500">
                            Ingat saya
                        </span>

                    </label>

                </div>


                <!-- Login Button -->
                <button
                    type="submit"

                    class="w-full
                           mt-6
                           rounded-xl
                           bg-indigo-600
                           px-4
                           py-3
                           text-sm
                           font-semibold
                           text-white
                           shadow-lg
                           shadow-indigo-200
                           hover:bg-indigo-700
                           hover:shadow-indigo-300
                           focus:outline-none
                           focus:ring-2
                           focus:ring-indigo-500
                           focus:ring-offset-2
                           transition">

                    Masuk ke Sistem

                </button>

            </form>

        </div>


        <!-- Footer -->
        <div class="text-center mt-6">

            <p class="text-xs text-gray-400">
                © {{ date('Y') }} CV Cahaya Khanza Plastik
            </p>

        </div>

    </div>

</div>
```

</x-guest-layout>
