<?php
/**
 * RoomMate API Documentation
 * 
 * This file serves as a reference for API endpoints and their usage.
 * Currently includes example endpoints ready for implementation.
 * 
 * Base URL: http://localhost:8000/api/
 * Version: v1
 */

/**
 * API ENDPOINTS REFERENCE
 */

/**
 * GET /api/listings.php?action=get_listings
 * 
 * Description: Retrieve all listings
 * 
 * Response:
 * {
 *   "status": "success",
 *   "data": [
 *     {
 *       "id": 1,
 *       "title": "2BHK Apartment",
 *       "location": "Bandra",
 *       "rent": 45000,
 *       "room_type": "2 Bed",
 *       "rating": 4.5
 *     }
 *   ]
 * }
 */

/**
 * GET /api/listings.php?action=get_listing&id=1
 * 
 * Description: Get single listing by ID
 * 
 * Parameters:
 * - id (required): Listing ID
 * 
 * Response:
 * {
 *   "status": "success",
 *   "data": {
 *     "id": 1,
 *     "title": "2BHK Apartment in Bandra",
 *     "location": "Bandra East, Mumbai",
 *     "rent": 45000,
 *     "amenities": ["WiFi", "AC", "Parking"]
 *   }
 * }
 */

/**
 * GET /api/listings.php?action=search_listings&q=bandra&location=Mumbai
 * 
 * Description: Search listings
 * 
 * Parameters:
 * - q (optional): Search query
 * - location (optional): Location filter
 * - min_rent (optional): Minimum rent
 * - max_rent (optional): Maximum rent
 * - room_type (optional): Room type filter
 * 
 * Response:
 * {
 *   "status": "success",
 *   "data": [...],
 *   "count": 5
 * }
 */

/**
 * FUTURE API ENDPOINTS
 */

// POST /api/listings.php?action=create_listing
// Create new listing
// Required: title, location, rent, room_type, description, image

// PUT /api/listings.php?action=update_listing
// Update existing listing
// Required: id, fields to update

// DELETE /api/listings.php?action=delete_listing
// Delete listing
// Required: id

// GET /api/users.php?action=get_user&id=1
// Get user profile
// Required: id

// POST /api/auth.php?action=login
// User login
// Required: email, password

// POST /api/auth.php?action=register
// User registration
// Required: name, email, phone, password

// POST /api/favorites.php?action=add
// Add listing to favorites
// Required: user_id, listing_id

// GET /api/favorites.php?action=get_user_favorites&user_id=1
// Get user's favorite listings
// Required: user_id

// POST /api/reviews.php?action=create_review
// Add review to listing
// Required: user_id, listing_id, rating, comment

// GET /api/reviews.php?action=get_listing_reviews&listing_id=1
// Get reviews for listing
// Required: listing_id

?>
<!-- 

API IMPLEMENTATION GUIDE

1. Authentication
   - Implement JWT tokens for API authentication
   - Add API key validation
   - Implement rate limiting

2. Response Format
   All API responses should follow this format:
   {
     "status": "success|error",
     "message": "Description",
     "data": {...},
     "timestamp": "2024-01-01T12:00:00Z"
   }

3. Error Handling
   400 Bad Request - Invalid parameters
   401 Unauthorized - Auth required
   403 Forbidden - Access denied
   404 Not Found - Resource not found
   500 Internal Server Error - Server error

4. Pagination
   Use ?page=1&limit=10 for pagination
   Response includes: current_page, total_pages, total_items

5. Filtering
   - Support multiple filter parameters
   - Allow sorting (sort=rent_asc, sort=rating_desc)
   - Support search across fields

6. Versioning
   Use /api/v1/, /api/v2/ for versions
   Support backwards compatibility

7. Documentation
   - Document all endpoints
   - Provide request/response examples
   - Include error codes
   - Add rate limit info

8. Security
   - Validate all inputs
   - Escape output properly
   - Use HTTPS in production
   - Implement CORS if needed
   - Use prepared statements
   - Prevent SQL injection
   - Validate file uploads

-->
