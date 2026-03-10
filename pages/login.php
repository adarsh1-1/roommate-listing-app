<?php
// Login page handler - API calls are handled via JavaScript
?>

<section class="py-12 md:py-20">
    <div class="max-w-md mx-auto px-4">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-200">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-gray-600">Sign in to your RoomMate account</p>
            </div>

            <!-- Error Messages -->
            <div id="errorContainer" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm"></div>

            <!-- Success Message -->
            <div id="successContainer" class="hidden bg-green-50 border border-green-200 rounded-lg mb-6 p-4">
                <p class="text-green-700 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    Login successful! Redirecting...
                </p>
            </div>

            <form id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 text-purple-600 rounded">
                        <span class="ml-2 text-gray-700">Remember me</span>
                    </label>
                    <a href="#" class="text-purple-600 hover:text-purple-700">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg transition">
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-600">Don't have an account? 
                    <a href="index.php?page=signup" class="text-purple-600 font-semibold hover:text-purple-700">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const errorContainer = document.getElementById('errorContainer');
    const successContainer = document.getElementById('successContainer');
    errorContainer.classList.add('hidden');
    successContainer.classList.add('hidden');
    
    const data = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };
    
    try {
        const response = await fetch('api/auth.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            successContainer.classList.remove('hidden');
            setTimeout(() => {
                window.location.href = 'index.php?page=landing';
            }, 1500);
        } else {
            errorContainer.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${result.error}`;
            errorContainer.classList.remove('hidden');
        }
    } catch (err) {
        errorContainer.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>Error: ${err.message}`;
        errorContainer.classList.remove('hidden');
    }
});
</script>

