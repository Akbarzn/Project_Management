{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center p-6">

    <div class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl overflow-hidden flex">

        {{-- LEFT SIDE --}}
        <div class="hidden md:flex w-1/2  items-center justify-center p-14">
            <div class="text-center text-white">
                <img src="{{ asset('storage/images/logo-aerospace.png') }}" class="w-72 mx-auto mb-8 drop-shadow-lg">

                {{-- <h3 class="text-3xl font-bold mb-2">Aerospace Project</h3>
                <p class="text-blue-100 text-sm">
                    Manage your project, tasks, and workflow with ease.
                </p> --}}
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="w-full md:w-1/2 p-14 flex flex-col justify-center">

            <h2 class="text-4xl font-extrabold text-gray-900 text-center mb-2">
                Welcome Back!
            </h2>

            <p class="text-gray-500 text-center mb-8 text-sm">
                Login to continue to your dashboard
            </p>

            {{-- Session Status --}}
            @if(session('status'))
                <div class="mb-4 text-green-600 text-sm text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-5">
                    <input type="email" name="email" required autofocus
                        class="w-full px-5 py-3 border border-gray-300 rounded-full focus:border-blue-600 focus:ring focus:ring-blue-200"
                        placeholder="Enter Email Address..." value="{{ old('email') }}">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <input type="password" name="password" required
                        class="w-full px-5 py-3 border border-gray-300 rounded-full focus:border-blue-600 focus:ring focus:ring-blue-200"
                        placeholder="Password">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Remember Me --}}
                <label class="flex items-center gap-2 mb-5 text-sm text-gray-600">
                    <input type="checkbox" name="remember"
                        class="rounded border-gray-300 text-blue-600">
                    Remember Me
                </label>

                {{-- Login Button --}}
                <button type="submit"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-lg transform hover:scale-[1.01] transition">
                    Login
                </button>

                <p class="text-center text-sm text-gray-600 mt-6">
                    <a href="{{ route('register') }}" class="text-blue-600 hover:underline">
                        Create an Account!
                    </a>
                </p>
            </form>

        </div>
    </div>

</body>

</html>
