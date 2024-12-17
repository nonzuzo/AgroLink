document.addEventListener('DOMContentLoaded', function() {
    loadCartItems(); // Load cart items when DOM is ready
});

// Function to load cart items into the HTML
function loadCartItems() {
    const cartContainer = document.getElementById('cart');
    let subtotal = 0;

    // Fetch cart items from the server
    fetch('get_cart_items.php') // This PHP script should return the user's cart items in JSON format
        .then(response => response.json())
        .then(cartItems => {
            // Clear existing items
            cartContainer.innerHTML = '';

            // Check if there are any items in the cart
            if (cartItems.length === 0) {
                cartContainer.innerHTML = '<p>Your cart is empty.</p>';
                updateTotals(0); // Update totals if empty
                return;
            }

            // Populate the cart with items
            cartItems.forEach(item => {
                const price = parseFloat(item.price); // Ensure price is a float
                const quantity = parseInt(item.quantity); // Ensure quantity is an integer

                if (isNaN(price) || isNaN(quantity)) {
                    console.error('Invalid price or quantity for item:', item);
                    return; // Skip this item if invalid
                }

                const itemTotal = price * quantity;
                subtotal += itemTotal;

                const cartItemDiv = document.createElement('div');
                cartItemDiv.className = 'cart-item';
                cartItemDiv.id = `item-${item.cart_item_id}`;
                console.log(item);

                cartItemDiv.innerHTML = `
                    <button class="remove-button" onclick="removeItem(${item.cart_item_id})">Remove</button>
                    <img src="../uploads/${item.image_url}" alt="${item.product_name}" style="width: 80px; height: auto;">
                    <div class="cart-item-details">
                        <p>${item.product_name} - Quantity: 
                            <input type="number" value="${quantity}" min="1" onchange="updateQuantity(${item.cart_item_id}, this.value)">
                        </p>
                        <p>Price: $<span class="item-price">${price.toFixed(2)}</span></p>
                    </div>
                `;

                cartContainer.appendChild(cartItemDiv);
            });

            // Update totals after loading items
            updateTotals(subtotal);
        })
        .catch(error => console.error('Error fetching cart items:', error));
}

// Function to remove an item from the cart
function removeItem(cartItemId) {
    console.log(`Attempting to remove item with ID: ${cartItemId}`); // Log the ID being removed

    fetch(`../ACTIONS/remove_from_cart.php`, {
        method: 'DELETE', // Assuming you implement a DELETE method on your server,
        header: {'Content-Type':'application/json'},
        body:JSON.stringify({cart_item_id:cartItemId})
    })
    .then(response => {
        console.log(response);
        if (response.ok) {
            document.getElementById(`item-${cartItemId}`).remove(); // Remove item from UI
            loadCartItems(); // Reload items if needed
        } else {
            console.error('Failed to remove item');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Function to update quantity and recalculate totals
function updateQuantity(cartItemId, newQuantity) {
    fetch(`update_cart.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ cart_item_id: cartItemId, quantity: newQuantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCartItems(); // Reload items or update totals as needed
        } else {
            console.error('Failed to update quantity');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Function to update grand total display
function updateTotals(grandTotal) {
    document.getElementById('grand-total').innerText = `$${grandTotal.toFixed(2)}`;
}