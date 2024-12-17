document.getElementById('filterBtn').addEventListener('click', function () {
    // Get filter values
    const searchQuery = document.getElementById('searchBar').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value.toLowerCase(); // Ensure category is lowercase
    const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
    const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;

    // Get product items
    const products = document.querySelectorAll('.product-item');

    // Filter products
    products.forEach(product => {
        const productName = product.querySelector('h3').innerText.toLowerCase();
        const productPrice = parseFloat(product.querySelector('p').innerText.replace(/[^0-9.-]+/g,"")) || 0;
        const productCategory = product.getAttribute('data-category').toLowerCase(); // Ensure category is lowercase

        // Check if product matches search query and filters
        const matchesSearch = searchQuery ? productName.includes(searchQuery) : true; // Show all if search is empty
        const matchesCategory = category ? productCategory === category : true;
        const matchesPrice = productPrice >= minPrice && productPrice <= maxPrice;

        // Show or hide product based on matches
        if (matchesSearch && matchesCategory && matchesPrice) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
});

// Sort functionality can be implemented here
document.getElementById('sortBy').addEventListener('change', function () {
    const sortBy = this.value;
    const productsArray = Array.from(document.querySelectorAll('.product-item'));

    productsArray.sort((a, b) => {
        let valueA, valueB;

        if (sortBy === 'price') {
            valueA = parseFloat(a.querySelector('p').innerText.replace(/[^0-9.-]+/g,""));
            valueB = parseFloat(b.querySelector('p').innerText.replace(/[^0-9.-]+/g,""));
            return valueA - valueB; // Sort by price ascending
        } else if (sortBy === 'location') {
            valueA = a.querySelector('p:nth-of-type(2)').innerText.toLowerCase(); // Assuming location is the second <p>
            valueB = b.querySelector('p:nth-of-type(2)').innerText.toLowerCase();
            return valueA.localeCompare(valueB); // Sort by location alphabetically
        }
        // Additional sorting criteria can be added here

        return 0; // Default case (no change)
    });

    // Append sorted products back to the grid
    const listingsGrid = document.getElementById('listingsGrid');
    listingsGrid.innerHTML = ''; // Clear existing products
    productsArray.forEach(product => listingsGrid.appendChild(product)); // Append sorted products
});