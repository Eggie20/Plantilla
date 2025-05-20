<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Official website of the Municipality of Magallanes">
    <title>Municipality of Magallanes - Welcome</title>
    
    <!-- Styles -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        /* Override default image styling */
        img, video {
            max-width: unset;
            width: auto;
            height: auto;
        }

        /* Custom Form Styles */
        .login-container {
            width: 372px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .login-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-field {
            width: 100%;
            height: 48px;
            padding: 12px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .input-label {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.3s ease;
            pointer-events: none;
            color: #64748b;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .input-field:focus ~ .input-label,
        .input-field:not(:placeholder-shown) ~ .input-label {
            transform: translateY(-120%) scale(0.8);
            color: #3b82f6;
            background: white;
            padding: 0 4px;
        }
        
        .error-message {
            font-size: 0.875rem;
            color: #ef4444;
            margin-top: 0.5rem;
            padding-left: 20px;
        }
        
        .login-button {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .remember-me {
            color: #64748b;
            font-size: 0.875rem;
        }
        
        .remember-me input[type="checkbox"] {
            margin-right: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
        }
        
        .remember-me input[type="checkbox"]:checked {
            border-color: #3b82f6;
        }
        
        .forgot-password {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .forgot-password:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-cover bg-center bg-no-repeat" style="background-image: url('<?php echo e(asset('images/BG-Enhance.jpg')); ?>')">
    <!-- Main Container -->
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="max-w-2xl w-full text-center">
            <!-- Login Form Section -->
            <div class="mt-12">
                <div class="login-container rounded-xl p-6 max-w-xs mx-auto">
                    <form method="POST" action="<?php echo e(route('login')); ?>">
                        <?php echo csrf_field(); ?>

                        <!-- Logo Section -->
                        <div class="mb-3 flex justify-center">
                            <img 
                                src="<?php echo e(asset('images/Municipal Logo of Magallanes.png')); ?>" 
                                alt="Municipality Logo" 
                                class="h-auto"
                                style="width: 109px;"
                            >
                        </div>
                        
                        <!-- Subtitle Section -->
                        <div class="mb-4 text-center space-y-1">
                            <h4 class="text-xl font-semibold text-gray-800">Plantilla of Personnel</h4>
                            <h4 class="text-gray-600">for Fiscal Year: 2025</h4>
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <div class="space-y-1">
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" 
                                       name="username" 
                                       id="username" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 focus:outline-none transition-all duration-200"
                                       value="<?php echo e(old('username')); ?>" 
                                       required 
                                       autocomplete="username" 
                                       autofocus>
                                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error-message"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <div class="space-y-1">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <div class="relative">
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           class="w-full px-4 py-3 border border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 focus:outline-none transition-all duration-200 pr-10"
                                           required 
                                           autocomplete="current-password">
                                    <button type="button" 
                                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-all duration-200 bg-white rounded-full p-1.5 shadow-sm z-10"
                                            onclick="togglePassword()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error-message"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>



                        <!-- Login Button -->
                        <div class="flex items-center justify-center">
                            <button type="submit" 
                                    class="login-button w-full flex justify-center py-2.5 px-3 border border-transparent 
                                           text-xs font-medium rounded-lg text-white 
                                           focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2 
                                           transition-all duration-200">
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password button');
            const eyeIcon = toggleButton.querySelector('svg');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('d', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M12 2a1.993 1.993 0 012.83 1.09l.81.81a2 2 0 11-2.83 2.83l-.81-.81a1.993 1.993 0 010-2.83zM10 12a2 2 0 100 4 2 2 0 000-4z');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('d', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z');
            }
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\plantilla versions\plantilla v1.9 - Copy\resources\views/Plantilla/welcome.blade.php ENDPATH**/ ?>