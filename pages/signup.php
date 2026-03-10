<?php
// Signup page handler - API calls are handled via JavaScript
// This page doesn't process form submission directly anymore
?>

<section class="py-12 md:py-20">
    <div class="max-w-md mx-auto px-4">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-200">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h1>
                <p class="text-gray-600">Join RoomMate and find your perfect space</p>
            </div>

            <!-- Error Messages -->
            <div id="errorContainer" class="hidden bg-red-50 border border-red-200 rounded-lg mb-6 p-4"></div>

            <!-- Success Message -->
            <div id="successContainer" class="hidden bg-green-50 border border-green-200 rounded-lg mb-6 p-4">
                <p class="text-green-700 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    Account created successfully! Redirecting...
                </p>
            </div>

            <form id="signupForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100" placeholder="+91 XXXXX XXXXX">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">I am a...</label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="role" value="user" checked class="w-4 h-4 text-purple-600">
                            <span class="ml-2 text-gray-700">Room Seeker</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="role" value="owner" class="w-4 h-4 text-purple-600">
                            <span class="ml-2 text-gray-700">PG Owner</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    <p class="text-xs text-gray-500 mt-1">At least 6 characters</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>

                <div class="flex items-start">
                    <input type="checkbox" id="terms" name="terms" required class="w-4 h-4 text-purple-600 rounded mt-1">
                    <span class="ml-2 text-sm text-gray-600">I agree to the <a href="#" class="text-purple-600 hover:text-purple-700 font-semibold">Terms of Service</a> and <a href="#" class="text-purple-600 hover:text-purple-700 font-semibold">Privacy Policy</a></span>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg transition">
                    Create Account
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-600">Already have an account? 
                    <a href="index.php?page=login" class="text-purple-600 font-semibold hover:text-purple-700">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('signupForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const errorContainer = document.getElementById('errorContainer');
    const successContainer = document.getElementById('successContainer');
    errorContainer.classList.add('hidden');
    successContainer.classList.add('hidden');
    
    const data = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        password: document.getElementById('password').value,
        confirm_password: document.getElementById('confirm_password').value,
        role: document.querySelector('input[name="role"]:checked').value
    };
    
    try {
        const response = await fetch('api/auth.php?action=register', {
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
            }, 2000);
        } else {
            const errors = result.errors || [result.error];
            errorContainer.innerHTML = errors.map(err => 
                `<p class="text-red-700 text-sm mb-2"><i class="fas fa-exclamation-circle mr-2"></i>${err}</p>`
            ).join('');
            errorContainer.classList.remove('hidden');
        }
    } catch (err) {
        errorContainer.innerHTML = `<p class="text-red-700 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>Error: ${err.message}</p>`;
        errorContainer.classList.remove('hidden');
    }
});
</script>

