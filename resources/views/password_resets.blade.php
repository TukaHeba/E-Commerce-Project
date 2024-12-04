{{--<!DOCTYPE html>--}}
{{--<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">--}}
{{--<head>--}}
{{--    <meta charset="utf-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1">--}}
{{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}

{{--    <title>{{ config('app.name', 'Laravel') }}</title>--}}

{{--    <!-- Fonts -->--}}
{{--    <link rel="preconnect" href="https://fonts.bunny.net">--}}
{{--    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>--}}

{{--    <!-- Scripts -->--}}
{{--    @vite(['resources/css/app.css', 'resources/js/app.js'])--}}
{{--    --}}
{{--</head>--}}
{{--<body>--}}
{{--<div class="font-sans text-gray-900 antialiased">--}}
{{--    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">--}}
{{--        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">--}}
{{--            @if ($errors->any())--}}
{{--                <div {{ $attributes }}>--}}
{{--                    <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>--}}

{{--                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">--}}
{{--                        @foreach ($errors->all() as $error)--}}
{{--                            <li>{{ $error }}</li>--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            @endif--}}

{{--            <form method="POST" action="{{url('/api/password/reset')}}">--}}
{{--                @csrf--}}

{{--                <input type="hidden" name="token" value="{{ $token }}">--}}

{{--                <div class="block">--}}
{{--                    <label for="email">Email</label>--}}
{{--                    <input id="email" class="block mt-1 w-full" type="email" name="email" required autofocus--}}
{{--                           autocomplete="username"/>--}}
{{--                </div>--}}

{{--                <div class="mt-4">--}}
{{--                    <label for="password">Password</label>--}}
{{--                    <input id="password" class="block mt-1 w-full" type="password" name="password" required--}}
{{--                           autocomplete="new-password"/>--}}
{{--                </div>--}}

{{--                <div class="mt-4">--}}
{{--                    <label for="password_confirmation">Confirm Password</label>--}}
{{--                    <input id="password_confirmation" class="block mt-1 w-full" type="password"--}}
{{--                           name="password_confirmation" required autocomplete="new-password"/>--}}
{{--                </div>--}}

{{--                <div class="flex items-center justify-end mt-4">--}}
{{--                    <button type='submit'--}}
{{--                            class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150'--}}
{{--                    >--}}
{{--                        Reset Password--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </form>--}}

{{--        </div>--}}
{{--    </div>--}}

{{--</div>--}}

{{--</body>--}}
{{--</html>--}}


    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
    <h2 class="text-2xl font-bold mb-4 text-center text-gray-700">Set Your Password</h2>

    <form method="POST" action="{{url('/api/password/reset')}}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
            <input type="email" id="email" name="email"
                   class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Enter your email" required>
        </div>

        <!-- Password field -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-600">New Password</label>
            <input type="password" id="password" name="password"
                   class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Enter your password" required>
        </div>

        <!-- Confirm password field -->
        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-600">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Confirm your password" required>
        </div>

        <!-- Submit button -->
        <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
            Save Password
        </button>
    </form>
</div>
</body>
</html>


