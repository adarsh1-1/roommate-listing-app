<?php
// Listing details page - fetches listing from API via JavaScript
$listing_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<section class="py-8 md:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Loading State -->
        <div id="loadingState" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-purple-600 mb-4"></i>
            <p class="text-gray-600">Loading listing details...</p>
        </div>

        <!-- Error State -->
        <div id="errorState" class="bg-red-50 border border-red-200 rounded-lg p-6 hidden">
            <p class="text-red-700"><i class="fas fa-exclamation-circle mr-2"></i><span id="errorMessage"></span></p>
            <a href="index.php?page=landing" class="text-red-600 hover:text-red-700 font-semibold mt-4 inline-block">← Back to listings</a>
        </div>

        <!-- Content State -->
        <div id="contentState" class="hidden">
            <!-- Gallery Section -->
            <div class="mb-8">
                <div id="main-image" class="w-full h-96 md:h-[500px] rounded-3xl overflow-hidden bg-gray-200 mb-4">
                    <img id="mainImg" src="" alt="Main" class="w-full h-full object-cover">
                </div>
                
                <div id="thumbnailContainer" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Listing Info -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm mb-6">
                        <h1 id="title" class="text-3xl md:text-4xl font-bold text-gray-900 mb-4"></h1>
                        
                        <div class="flex flex-wrap gap-4 mb-6">
                            <div>
                                <p class="text-gray-600 text-sm">Rent</p>
                                <p id="rent" class="text-2xl font-bold text-purple-600"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Available Rooms</p>
                                <p id="available_rooms" class="text-xl font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Furnishing</p>
                                <p id="furnishing" class="text-xl font-semibold text-gray-900"></p>
                            </div>
                        </div>

                        <div class="flex items-center text-sm text-gray-600 mb-6">
                            <i class="fas fa-map-marker-alt mr-2 text-purple-600"></i>
                            <span id="location"></span>
                        </div>

                        <!-- Key Details Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 py-6 border-t border-b border-gray-200">
                            <div>
                                <p class="text-gray-600 text-sm">Area</p>
                                <p id="area" class="font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Created</p>
                                <p id="created_at" class="font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Owner</p>
                                <p id="owner_name" class="font-semibold text-gray-900"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Property</h2>
                        <p id="description" class="text-gray-700 leading-relaxed"></p>
                    </div>

                    <!-- Amenities -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Amenities</h2>
                        <div id="amenitiesContainer" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Contact Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Contact Owner</h3>
                        
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-user text-white text-2xl"></i>
                            </div>
                            <p id="contactName" class="font-semibold text-gray-900 text-lg"></p>
                            <p class="text-sm text-gray-600">Property Owner</p>
                        </div>

                        <div class="space-y-3 mb-6">
                            <a id="callBtn" href="tel:" class="w-full flex items-center justify-center space-x-2 bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg font-semibold transition">
                                <i class="fas fa-phone"></i>
                                <span>Call Now</span>
                            </a>
                            <a id="whatsappBtn" href="https://wa.me/" class="w-full flex items-center justify-center space-x-2 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition" target="_blank">
                                <i class="fab fa-whatsapp"></i>
                                <span>WhatsApp</span>
                            </a>
                            <a id="emailBtn" href="mailto:" class="w-full flex items-center justify-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg font-semibold transition">
                                <i class="fas fa-envelope"></i>
                                <span>Email</span>
                            </a>
                        </div>

                        <button onclick="saveListing()" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition">
                            <i class="fas fa-heart mr-2"></i> Save Listing
                        </button>

                        <!-- Info Box -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm text-gray-700">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <strong>Tip:</strong> Always visit before making a decision and verify ownership.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const listingId = <?php echo $listing_id; ?>;
const loadingState = document.getElementById('loadingState');
const errorState = document.getElementById('errorState');
const contentState = document.getElementById('contentState');
const errorMessage = document.getElementById('errorMessage');

document.addEventListener('DOMContentLoaded', () => {
    if (listingId <= 0) {
        showError('Invalid listing ID');
        return;
    }
    loadListing();
});

async function loadListing() {
    try {
        const response = await fetch(`api/listings.php?action=get_listing&id=${listingId}`);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Failed to load listing');
        }

        if (!data.listing) {
            throw new Error('Listing not found');
        }

        displayListing(data.listing);
        loadingState.classList.add('hidden');
        contentState.classList.remove('hidden');
        
    } catch (err) {
        console.error('Error:', err);
        showError(err.message);
    }
}

function displayListing(listing) {
    // Basic info
    document.getElementById('title').textContent = listing.title;
    document.getElementById('rent').textContent = '₹' + (listing.rent ? listing.rent.toLocaleString('en-IN') : '0') + '/mo';
    document.getElementById('location').textContent = listing.location;
    document.getElementById('available_rooms').textContent = listing.available_rooms || '1';
    document.getElementById('furnishing').textContent = listing.furnishing || 'N/A';
    document.getElementById('area').textContent = listing.area || 'N/A';
    document.getElementById('description').textContent = listing.description || '';
    document.getElementById('owner_name').textContent = listing.owner_name || 'Owner';
    document.getElementById('contactName').textContent = listing.owner_name || 'Owner';
    
    // Format date
    if (listing.created_at) {
        const date = new Date(listing.created_at);
        document.getElementById('created_at').textContent = date.toLocaleDateString('en-IN');
    }

    // Contact info
    if (listing.owner_phone) {
        const phone = listing.owner_phone.replace(/\D/g, '');
        document.getElementById('callBtn').href = 'tel:' + listing.owner_phone;
        document.getElementById('whatsappBtn').href = 'https://wa.me/' + phone;
    }
    if (listing.owner_email) {
        document.getElementById('emailBtn').href = 'mailto:' + listing.owner_email;
    }

    // Gallery
    const images = listing.images && listing.images.length > 0 
        ? listing.images.map(img => img.image_url)
        : [listing.image_url || 'https://via.placeholder.com/800x600?text=No+Image'];

    if (images.length > 0) {
        document.getElementById('mainImg').src = images[0];
        document.getElementById('mainImg').onerror = function() {
            this.src = 'https://via.placeholder.com/800x600?text=No+Image';
        };
    }

    // Thumbnails
    const thumbnailContainer = document.getElementById('thumbnailContainer');
    images.forEach((image, index) => {
        const thumb = document.createElement('div');
        thumb.className = 'cursor-pointer rounded-xl overflow-hidden bg-gray-200 h-24 md:h-28 hover:shadow-lg transition';
        thumb.innerHTML = `<img src="${image}" alt="Thumbnail" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">`;
        thumb.addEventListener('click', () => changeImage(images, index));
        thumbnailContainer.appendChild(thumb);
    });

    // Amenities
    const amenitiesContainer = document.getElementById('amenitiesContainer');
    const amenities = listing.amenities && typeof listing.amenities === 'string' 
        ? JSON.parse(listing.amenities) 
        : listing.amenities || [];
    
    if (Array.isArray(amenities) && amenities.length > 0) {
        amenities.forEach(amenity => {
            const div = document.createElement('div');
            div.className = 'flex items-center p-3 bg-purple-50 rounded-lg';
            div.innerHTML = `<i class="fas fa-check-circle text-purple-600 mr-3"></i><span class="text-gray-700 font-medium">${amenity}</span>`;
            amenitiesContainer.appendChild(div);
        });
    } else {
        amenitiesContainer.innerHTML = '<p class="col-span-full text-gray-600">No amenities listed</p>';
    }
}

function changeImage(images, index) {
    document.getElementById('mainImg').src = images[index];
}

function showError(message) {
    loadingState.classList.add('hidden');
    contentState.classList.add('hidden');
    errorMessage.textContent = message;
    errorState.classList.remove('hidden');
}

function saveListing() {
    alert('Save listing feature coming soon!');
}
</script>
