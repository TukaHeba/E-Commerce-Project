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
        <!-- Verification Code -->
        <div class="mb-4">
            <label for="token" class="block text-sm font-medium text-gray-600">Code</label>
            <input type="text" id="email" name="token"
                   class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Enter Verification code" required>
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


