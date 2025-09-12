// Basic header search functionality for Thila Coloma Statamic site
document.addEventListener('DOMContentLoaded', function() {
    console.log('Components.js loaded');
    
    // Basic search functionality placeholder
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');
    
    if (searchInput && searchButton) {
        console.log('Search elements found');
        
        // Basic search functionality - redirect to search page or show dropdown
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query) {
                // For now, just log the search
                console.log('Search query:', query);
                // You can implement actual search functionality here
                // e.g., redirect to a search results page or show dropdown results
            }
        });
        
        // Enter key support
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });
    } else {
        console.log('Search elements not found');
    }
});