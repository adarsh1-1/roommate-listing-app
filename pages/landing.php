<?php
// Landing page - shows all active PG listings
// Listings are fetched via JavaScript from the API
?>

<section class="py-12 md:py-20">
    <!-- Hero Section -->
    <div class="gradient-bg text-white rounded-3xl mx-4 md:mx-8 lg:mx-auto lg:max-w-6xl mb-12 px-6 md:px-12 py-12 md:py-20">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Find Your Perfect Living Space</h1>
            <p class="text-lg md:text-xl text-purple-100 mb-8">Discover verified roommate and PG listings in your city</p>
            
            <!-- Search Bar -->
            <form id="searchForm" class="flex flex-col md:flex-row gap-3 bg-white rounded-2xl p-2">
                <input type="text" id="searchQuery" placeholder="Search by location or keywords..." class="flex-1 px-4 py-3 text-gray-900 rounded-xl focus:outline-none" required>
                <input type="number" id="maxRent" placeholder="Max Rent" min="0" class="px-4 py-3 text-gray-900 rounded-xl focus:outline-none w-full md:w-32">
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-3 rounded-xl font-semibold hover:shadow-lg transition whitespace-nowrap">
                    Search
                </button>
            </form>
        </div>
    </div>

    <!-- Listings Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Featured Listings</h2>
            <a href="#" class="text-purple-600 hover:text-purple-700 font-semibold">View All →</a>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-purple-600 mb-4"></i>
            <p class="text-gray-600">Loading listings...</p>
        </div>

        <!-- Listings Container -->
        <div id="listingsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-12 hidden">
            <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 text-lg">No listings found. Try adjusting your search.</p>
        </div>

        <!-- Error State -->
        <div id="errorState" class="bg-red-50 border border-red-200 rounded-lg p-6 hidden">
            <p class="text-red-700"><i class="fas fa-exclamation-circle mr-2"></i><span id="errorMessage"></span></p>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="mt-8 flex justify-center gap-2 hidden">
        </div>
    </div>
</section>

<script>
let currentPage = 1;
let currentSearch = '';
let currentMaxRent = 0;

const listingsContainer = document.getElementById('listingsContainer');
const loadingState = document.getElementById('loadingState');
const emptyState = document.getElementById('emptyState');
const errorState = document.getElementById('errorState');
const errorMessage = document.getElementById('errorMessage');
const paginationContainer = document.getElementById('paginationContainer');
const searchForm = document.getElementById('searchForm');

// Load listings on page load
document.addEventListener('DOMContentLoaded', () => {
    loadListings(1);
});

// Search form submission
searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    currentSearch = document.getElementById('searchQuery').value;
    currentMaxRent = parseInt(document.getElementById('maxRent').value) || 0;
    currentPage = 1;
    loadListings(1);
});

async function loadListings(page = 1) {
    try {
        listingsContainer.classList.add('hidden');
        emptyState.classList.add('hidden');
        errorState.classList.add('hidden');
        loadingState.classList.remove('hidden');

        let url = 'api/listings.php?action=get_listings&page=' + page;
        
        // If search is active, use search endpoint
        if (currentSearch || currentMaxRent > 0) {
            url = 'api/listings.php?action=search&page=' + page;
            if (currentSearch) url += '&q=' + encodeURIComponent(currentSearch);
            if (currentMaxRent > 0) url += '&max_rent=' + currentMaxRent;
        }

        const response = await fetch(url);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Failed to load listings');
        }

        if (!data.listings || data.listings.length === 0) {
            showEmptyState();
            return;
        }

        displayListings(data.listings);
        displayPagination(data.pagination);
        
        loadingState.classList.add('hidden');
        listingsContainer.classList.remove('hidden');
        
    } catch (err) {
        console.error('Error:', err);
        errorMessage.textContent = err.message;
        errorState.classList.remove('hidden');
        loadingState.classList.add('hidden');
    }
}

function displayListings(listings) {
    listingsContainer.innerHTML = '';
    
    listings.forEach(listing => {
        const imageUrl = listing.image_url || 'https://via.placeholder.com/500x400?text=No+Image';
        const rent = listing.rent ? `₹${listing.rent.toLocaleString('en-IN')}` : 'Contact';
        const card = document.createElement('div');
        card.className = 'bg-white rounded-2xl overflow-hidden card-hover';
        card.innerHTML = `
            <div class="relative h-48 overflow-hidden bg-gray-200">
                <img src="${imageUrl}" alt="${listing.title}" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/500x400?text=No+Image'">
                <div class="absolute top-3 right-3 bg-white rounded-full p-2 shadow-md hover:shadow-lg transition cursor-pointer">
                    <i class="fas fa-heart text-gray-400 hover:text-red-500"></i>
                </div>
            </div>
            <div class="p-5">
                <h3 class="font-semibold text-gray-900 text-lg mb-2">${listing.title}</h3>
                <div class="flex items-center text-sm text-gray-600 mb-3">
                    <i class="fas fa-map-marker-alt mr-2 text-purple-600"></i>
                    ${listing.location}
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-2xl font-bold text-gray-900">${rent}/mo</span>
                </div>
                <a href="index.php?page=listing-details&id=${listing.id}" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold text-center hover:shadow-lg transition block">
                    View Details
                </a>
            </div>
        `;
        listingsContainer.appendChild(card);
    });
}

function displayPagination(pagination) {
    if (pagination.pages <= 1) {
        paginationContainer.classList.add('hidden');
        return;
    }
    
    paginationContainer.innerHTML = '';
    paginationContainer.classList.remove('hidden');
    
    for (let i = 1; i <= pagination.pages; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = i;
        btn.className = `px-4 py-2 rounded-lg font-semibold ${
            i === currentPage 
                ? 'bg-purple-600 text-white' 
                : 'bg-gray-200 text-gray-900 hover:bg-gray-300'
        }`;
        btn.addEventListener('click', () => {
            currentPage = i;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            loadListings(i);
        });
        paginationContainer.appendChild(btn);
    }
}

function showEmptyState() {
    loadingState.classList.add('hidden');
    listingsContainer.classList.add('hidden');
    emptyState.classList.remove('hidden');
}
</script>
