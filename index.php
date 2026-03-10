<?php
session_start();
$page = isset($_GET['page']) ? $_GET['page'] : 'landing';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomMate - Find Your Perfect Living Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="index.php?page=landing" class="text-2xl font-bold gradient-bg bg-clip-text text-transparent">
                        RoomMate
                    </a>
                    <div class="hidden md:flex space-x-4">
                        <a href="index.php?page=landing" class="text-gray-600 hover:text-gray-900 transition">Browse</a>
                        <a href="index.php?page=add-listing" class="text-gray-600 hover:text-gray-900 transition">Add Listing</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <span class="text-gray-700"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="logout.php" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Logout</a>
                    <?php else: ?>
                        <a href="index.php?page=login" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Login</a>
                        <a href="index.php?page=signup" class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium hover:shadow-lg transition">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php
        switch($page) {
            case 'landing':
                include 'pages/landing.php';
                break;
            case 'details':
                include 'pages/listing-details.php';
                break;
            case 'login':
                include 'pages/login.php';
                break;
            case 'signup':
                include 'pages/signup.php';
                break;
            case 'add-listing':
                include 'pages/add-listing.php';
                break;
            default:
                include 'pages/landing.php';
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">RoomMate</h3>
                    <p class="text-sm">Find your perfect living space with ease.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Browse Listings</a></li>
                        <li><a href="#" class="hover:text-white transition">Post a Listing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                <p>&copy; 2026 RoomMate. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
