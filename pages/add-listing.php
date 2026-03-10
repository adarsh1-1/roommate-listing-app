<?php
// Add listing page - API calls handled via JavaScript
// Requires authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
if ($_SESSION['user_role'] !== 'owner') {
    header('Location: index.php?page=landing');
    exit;
}
?>

<section class="py-12 md:py-20">
    <div class="max-w-3xl mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Post a New Listing</h1>
            <p class="text-gray-600">Fill in the details to list your property</p>
        </div>

        <div class="bg-white rounded-3xl p-6 md:p-10 shadow-sm border border-gray-200">
            <?php if(isset($success)): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <!-- Error Messages -->
            <div id="errorContainer" class="hidden bg-red-50 border border-red-200 rounded-lg mb-6 p-4"></div>

            <!-- Success Message -->
            <div id="successContainer" class="hidden bg-green-50 border border-green-200 rounded-lg mb-6 p-4">
                <p class="text-green-700 text-sm">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span id="successMessage">Listing created successfully! Redirecting...</span>
                </p>
            </div>

            <form id="listingForm" class="space-y-6" enctype="multipart/form-data">
                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Property Image<span class="text-red-600">*</span></label>
                    <div id="drop-zone" class="border-2 border-dashed border-purple-300 rounded-2xl p-8 text-center cursor-pointer hover:border-purple-600 hover:bg-purple-50 transition">
                        <i class="fas fa-cloud-upload-alt text-3xl text-purple-600 mb-4"></i>
                        <p class="text-gray-700 font-semibold mb-1">Drag and drop your image here</p>
                        <p class="text-gray-600 text-sm mb-4">or</p>
                        <input type="file" name="image" id="image-input" accept="image/*" required class="hidden">
                        <button type="button" onclick="document.getElementById('image-input').click()" class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                            Select Image
                        </button>
                        <p class="text-gray-500 text-xs mt-3">JPG, PNG, GIF, WebP up to 5MB</p>
                    </div>
                    <div id="image-preview" class="mt-4 hidden">
                        <img id="preview-img" src="" alt="Preview" class="max-h-48 rounded-lg">
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Property Title<span class="text-red-600">*</span></label>
                        <input type="text" id="title" name="title" required placeholder="e.g., 2BHK Apartment in Bandra" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Location<span class="text-red-600">*</span></label>
                        <input type="text" id="location" name="location" required placeholder="e.g., Bandra East, Mumbai" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    </div>
                </div>

                <!-- Rent and Room Type -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Monthly Rent (₹)<span class="text-red-600">*</span></label>
                        <input type="number" id="rent" name="rent" required placeholder="45000" min="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Available Rooms<span class="text-red-600">*</span></label>
                        <input type="number" id="available_rooms" name="available_rooms" required placeholder="1" min="1" value="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Area (sqft)</label>
                        <input type="text" id="area" name="area" placeholder="e.g., 850" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    </div>
                </div>

                <!-- Furnishing -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Furnishing<span class="text-red-600">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="furnishing" value="unfurnished" checked class="w-4 h-4 text-purple-600">
                            <span class="ml-2 text-gray-700">Unfurnished</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="furnishing" value="semi-furnished" class="w-4 h-4 text-purple-600">
                            <span class="ml-2 text-gray-700">Semi-Furnished</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="furnishing" value="furnished" class="w-4 h-4 text-purple-600">
                            <span class="ml-2 text-gray-700">Furnished</span>
                        </label>
                    </div>
                </div>

                <!-- Amenities -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="amenity-checkbox w-4 h-4 text-purple-600" value="WiFi">
                            <span class="ml-2 text-gray-700">WiFi</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="amenity-checkbox w-4 h-4 text-purple-600" value="AC">
                            <span class="ml-2 text-gray-700">Air Conditioning</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="amenity-checkbox w-4 h-4 text-purple-600" value="Parking">
                            <span class="ml-2 text-gray-700">Parking</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="amenity-checkbox w-4 h-4 text-purple-600" value="Gym">
                            <span class="ml-2 text-gray-700">Gym</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="amenity-checkbox w-4 h-4 text-purple-600" value="Security">
                            <span class="ml-2 text-gray-700">24/7 Security</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="amenity-checkbox w-4 h-4 text-purple-600" value="Kitchen">
                            <span class="ml-2 text-gray-700">Equipped Kitchen</span>
                        </label>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description<span class="text-red-600">*</span></label>
                    <textarea id="description" name="description" required rows="6" placeholder="Describe your property in detail..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-100 resize-none"></textarea>
                </div>

                <!-- Submit -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" id="submitBtn" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-upload mr-2"></i> Publish Listing
                    </button>
                    <a href="index.php?page=landing" class="flex-1 bg-gray-200 text-gray-900 font-semibold py-3 rounded-lg text-center hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
const dropZone = document.getElementById('drop-zone');
const imageInput = document.getElementById('image-input');
const imagePreview = document.getElementById('image-preview');
const previewImg = document.getElementById('preview-img');
const listingForm = document.getElementById('listingForm');
const errorContainer = document.getElementById('errorContainer');
const successContainer = document.getElementById('successContainer');
const submitBtn = document.getElementById('submitBtn');

// Image preview function
function handleImageSelect() {
    const file = imageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

// Drag and drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-purple-600', 'bg-purple-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-purple-600', 'bg-purple-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-purple-600', 'bg-purple-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        imageInput.files = files;
        handleImageSelect();
    }
});

// File input change
imageInput.addEventListener('change', handleImageSelect);

// Form submission
listingForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    errorContainer.classList.add('hidden');
    successContainer.classList.add('hidden');
    submitBtn.disabled = true;
    
    try {
        // Collect form data
        const formData = new FormData();
        formData.append('title', document.getElementById('title').value);
        formData.append('location', document.getElementById('location').value);
        formData.append('rent', document.getElementById('rent').value);
        formData.append('available_rooms', document.getElementById('available_rooms').value);
        formData.append('furnishing', document.querySelector('input[name="furnishing"]:checked').value);
        formData.append('area', document.getElementById('area').value);
        formData.append('description', document.getElementById('description').value);
        
        // Collect amenities
        const amenities = [];
        document.querySelectorAll('.amenity-checkbox:checked').forEach(checkbox => {
            amenities.push(checkbox.value);
        });
        formData.append('amenities', JSON.stringify(amenities));
        
        // Add image file
        if (imageInput.files.length === 0) {
            throw new Error('Please select an image');
        }
        formData.append('image', imageInput.files[0]);
        
        // Send to API
        const response = await fetch('api/listings.php?action=create', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            document.getElementById('successMessage').textContent = 'Listing created successfully! Redirecting...';
            successContainer.classList.remove('hidden');
            setTimeout(() => {
                window.location.href = 'index.php?page=landing';
            }, 2000);
        } else {
            throw new Error(result.error || 'Failed to create listing');
        }
    } catch (err) {
        console.error('Error:', err);
        errorContainer.innerHTML = `<p class="text-red-700 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>${err.message}</p>`;
        errorContainer.classList.remove('hidden');
        submitBtn.disabled = false;
    }
});
</script>
imageInput.addEventListener('change', handleImageSelect);

function handleImageSelect() {
    const file = imageInput.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}
</script>
