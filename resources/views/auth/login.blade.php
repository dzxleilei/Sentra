<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sentra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6'
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        @media (min-width: 1024px) {
            .login-container {
                flex-direction: row;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="login-container" x-data="loginForm()">
        <!-- Left Side - Campus Image (Hidden on mobile, visible on large screens) -->
        <div class="hidden lg:block lg:w-1/2 bg-cover bg-center bg-blue-600" style="background-image: url('/images/campus.jpg')"></div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center min-h-screen lg:min-h-auto px-4 py-8 sm:py-12 lg:py-0">
            <div class="w-full max-w-sm">
                <!-- Logo Section -->
                <div class="text-center mb-8">
                    <img src="/images/logo.png" alt="Logo" class="mx-auto w-14 sm:w-20 mb-2 sm:mb-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Sentra ITBSS</h1>
                    <p class="text-gray-500 text-sm mt-1">Sistem Manajemen Peminjaman Sarana dan Prasarana</p>
                </div>

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" @submit.prevent="handleSubmit" class="space-y-4">
                    @csrf

                    @if(session('status'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-green-700 text-sm">{{ session('status') }}</p>
                        </div>
                    @endif
                    
                    <!-- Email Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            name="email"
                            x-model="email"
                            @input="emailError = false; error = ''"
                            value="{{ old('email') }}"
                            :class="emailError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'"
                            class="w-full border-2 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition"
                            placeholder="your@email.com"
                        />
                        <p x-show="emailError" class="text-red-500 text-xs mt-1.5">Email must be filled in</p>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                x-model="password"
                                @input="passwordError = false; error = ''"
                                :class="passwordError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'"
                                class="w-full border-2 rounded-lg px-4 py-2.5 pr-12 text-sm focus:outline-none focus:ring-2 transition"
                                placeholder="Enter your password"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none p-1"
                            >
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <p x-show="passwordError" class="text-red-500 text-xs mt-1.5">Password must be filled in</p>
                    </div>

                    <!-- Error Messages -->
                    <p x-show="error" x-text="error" class="text-red-600 text-sm bg-red-50 p-3 rounded-lg border border-red-200"></p>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded-lg font-semibold text-sm sm:text-base transition-colors duration-200 mt-6"
                    >
                        Login
                    </button>
                </form>

                <!-- Footer Info -->
                <p class="text-center text-gray-500 text-xs sm:text-sm mt-6">
                    Developed by Calvin 22100006 &copy; 2026
                </p>
            </div>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                email: '{{ old('email') }}',
                password: '',
                error: '',
                emailError: false,
                passwordError: false,
                showPassword: false,

                handleSubmit() {
                    this.error = '';
                    this.emailError = false;
                    this.passwordError = false;

                    // Validate empty inputs
                    if (!this.email.trim()) {
                        this.emailError = true;
                        return;
                    }

                    if (!this.password) {
                        this.passwordError = true;
                        return;
                    }

                    // If validation passes, submit the form
                    document.querySelector('form').submit();
                }
            }
        }
    </script>
</body>
</html>