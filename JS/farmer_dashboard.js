document.addEventListener("DOMContentLoaded", () => {
    // Load and display products from the database
    displayProducts();
    
    // Load insights and notifications
    loadInsights();
    loadNotifications();

    // Show modal to edit profile
    const editProfileBtn = document.getElementById("editProfileBtn");
    const editProfileModal = document.getElementById("editProfileModal");
    const closeEditModalBtn = editProfileModal.querySelector(".close-btn");

    editProfileBtn.addEventListener("click", () => {
        editProfileModal.style.display = "block"; // Show modal
    });

    closeEditModalBtn.addEventListener("click", () => {
        editProfileModal.style.display = "none"; // Hide modal
    });

   // Handle profile editing
   document.getElementById("editProfileForm").addEventListener("submit", function(event) {
       event.preventDefault(); // Prevent default form submission

       const name = document.getElementById("editName").value;
       const email = document.getElementById("editEmail").value;

       // Send AJAX request to update profile
       fetch('update_profile.php', {
           method: 'POST',
           headers: {
               'Content-Type': 'application/x-www-form-urlencoded'
           },
           body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`
       })
       .then(response => response.text())
       .then(data => {
           alert(data); // Show success or error message
           location.reload(); // Refresh page to show updated info
       })
       .catch(error => console.error('Error:', error));

       editProfileModal.style.display = "none"; // Hide modal after submission
   });

   // Function to load and display products from the database
   function displayProducts() {
       fetch('read_products.php')
           .then(response => response.json())
           .then(products => {
               const productContainer = document.getElementById("productContainer");
               productContainer.innerHTML = ''; // Clear previous listings

               if (products.length === 0) {
                   productContainer.innerHTML = '<p>No products available. Please add new listings.</p>';
                   return;
               }

               products.forEach(product => {
                   const productDiv = document.createElement("div");
                   productDiv.classList.add("product");
                   productDiv.innerHTML = `
                       <img src="${product.image_url}" alt="${product.name}" class="product-image"/> <!-- Display product image -->
                       <p><strong>Product:</strong> ${product.name}</p>
                       <p><strong>Category:</strong> ${product.category}</p><!-- Display category -->
                       <p><strong>Description:</strong> ${product.description}</p><!-- Display description -->
                       <p><strong>Price:</strong> $${product.price}/kg</p><!-- Display price -->
                       <p><strong>Quantity:</strong> ${product.quantity} kg</p><!-- Display quantity -->
                       <!-- Edit button will trigger a function to populate the form with current data -->
                       <!-- Delete button will call the delete function -->
                       <button class='edit-btn' data-id='${product.product_id}'>Edit</button> 
                       <button class='delete-btn' data-id='${product.product_id}'>Delete</button>`;
                   productContainer.appendChild(productDiv);
               });

               // Add event listeners for edit and delete buttons
               document.querySelectorAll(".edit-btn").forEach(button => {
                   button.addEventListener("click", function() {
                       const productId = this.getAttribute("data-id");
                       editProduct(productId); // Call edit function (to be implemented)
                   });
               });

               document.querySelectorAll(".delete-btn").forEach(button => {
                   button.addEventListener("click", function() {
                       const productId = this.getAttribute("data-id");
                       deleteProduct(productId); // Call delete function
                   });
               });
           });
   }

   // Function to load insights from the database 
   function loadInsights() {
       const insightsContainer = document.getElementById('insightsContainer');
       insightsContainer.innerHTML = "<p>Loading insights...</p>";
       
       fetch('get_insights.php')  // Assuming you have a PHP script to get insights data.
           .then(response => response.json())
           .then(data => {
               insightsContainer.innerHTML = '';  // Clear loading message

               data.forEach(insight => {
                   const insightDiv = document.createElement('div');
                   insightDiv.innerHTML = `<p>${insight.message}</p>`;
                   insightsContainer.appendChild(insightDiv);
               });
           })
           .catch(error => console.error('Error loading insights:', error));
   }

   // Function to load notifications from the database
   function loadNotifications() {
       const notificationList = document.getElementById('notificationList');
       
       fetch('get_notifications.php')  // Assuming you have a PHP script to get notifications.
           .then(response => response.json())
           .then(notifications => {
               notificationList.innerHTML = '';  // Clear previous notifications

               notifications.forEach(notification => {
                   const li = document.createElement('li');
                   li.textContent = notification.message;
                   notificationList.appendChild(li);
               });
           })
           .catch(error => console.error('Error loading notifications:', error));
   }

   // Function to delete a product
   function deleteProduct(productId) {
       if (confirm("Are you sure you want to delete this product?")) {
           fetch(`delete_product.php?id=${productId}`, { method: 'GET' })
               .then(response => response.text())
               .then(data => {
                   alert(data); // Show success or error message
                   location.reload(); // Refresh page to show updated product list
               })
               .catch(error => console.error('Error:', error));
       }
   }

});