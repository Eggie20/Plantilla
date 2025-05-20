<!-- Login Form Container -->
<div class="bg-white rounded-lg shadow-md p-8 max-w-md mx-auto">
    <form method="POST" action="{{ route('login') }}">
        @csrf



        <!-- Username -->
        <div class="mb-6">
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
            <input type="text" name="username" id="username" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                   value="{{ old('username') }}" required autocomplete="username" autofocus>
            @error('username')
                <p id="error-message" class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if(session('locked_until'))
                <div class="mt-4 bg-red-100 p-4 rounded-lg text-center">
                    <div class="space-y-2">
                        <div class="text-red-700 font-semibold text-lg">
                            Account locked
                        </div>
                        <div class="text-red-600 text-sm">
                            Please contact an administrator to unlock your account
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input type="password" name="password" id="password" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                   required autocomplete="current-password">
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <input id="remember_me" name="remember" type="checkbox" 
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="remember_me" class="ml-2 block text-sm text-gray-700">Remember me</label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" 
                   class="text-sm text-indigo-600 hover:text-indigo-500">Forgot your password?</a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="flex items-center justify-center">
            <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent 
                           text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Sign in
            </button>
        </div>
    </form>
</div>


