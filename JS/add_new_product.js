// Add event listener to the form for submission
document.getElementById('addProductForm').addEventListener('submit', function(event) {
    //event.preventDefault(); // Prevent default form submission
    // Clear previous error messages
    clearErrors();

    // Gather input values from the form
    const productName = document.getElementById('productName').value.trim(); // Product name input
    const category = document.getElementById('category').value; // Selected category from dropdown
    const description = document.getElementById('description').value.trim(); // Product description
    const price = parseFloat(document.getElementById('price').value); // Parse price as a float
    const quantity = parseInt(document.getElementById('quantity').value); // Parse quantity as an integer
    const location = document.getElementById('location').value.trim(); // Location of the product
    const imageUpload = document.getElementById('imageUpload').files[0]; // Get uploaded image file

    // Initialize a variable to track validation status
    let isValid = true;

    // Perform validation checks on input fields
    if (!productName) {
        showError('nameError', 'Product name is required.'); // Error for missing product name
        isValid = false; // Set isValid to false
    }

    if (!category) {
        showError('categoryError', 'Please select a category.'); // Error for missing category
        isValid = false; // Set isValid to false
    }

    if (!description) {
        showError('descriptionError', 'Description is required.'); // Error for missing description
        isValid = false; // Set isValid to false
    }

    if (isNaN(price) || price <= 0) {
        showError('priceError', 'Price must be a positive number.'); // Error for invalid price
        isValid = false; // Set isValid to false
    }

    if (isNaN(quantity) || quantity < 1) {
        showError('quantityError', 'Quantity must be at least 1.'); // Error for invalid quantity
        isValid = false; // Set isValid to false
    }

    if (!location) {
        showError('locationError', 'Location is required.'); // Error for missing location
        isValid = false; // Set isValid to false
    }

    // Validate image size (max 2MB)
    if (imageUpload) {
        const maxSize = 2 * 1024 * 1024; // Maximum size of 2MB in bytes
        if (imageUpload.size > maxSize) {
            showError('imageError', 'Image size must not exceed 2MB.'); // Error for exceeding image size
            isValid = false; // Set isValid to false
        }
    } else {
        showError('imageError', 'Image upload is required.'); // Error for missing image upload
        isValid = false; // Set isValid to false
    }

    // If all validations pass, proceed with submission logic
    if (isValid) {
        const formData = new FormData(); // Create a FormData object for file upload

        formData.append("name", productName);
        formData.append("category", category);
        formData.append("description", description);
        formData.append("price", price);
        formData.append("quantity", quantity);
        formData.append("location", location);
        formData.append("imageUpload", imageUpload); // Append the image file
        

        // Send AJAX request to create new product using FormData
        fetch('../ACTIONS/create_product.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'multipart/form-data'
                }
            })
        .then(response => response.json())  // Parse JSON response from server
        .then(data => {
            alert(data.message); // Show success or error message from server response

            if (data.success) {  // If the response indicates success, redirect back to dashboard.
                window.location.href = '../VIEWS/farmer_dashboard.html'; 
            }
        })
        .catch(error => console.error('Error:', error));
    }
});

// Function to show error messages
function showError(elementId, message) {
    const errorElement = document.getElementById(elementId); // Get the error message element by ID
    errorElement.textContent = message; // Set the error message text
    errorElement.style.color = 'red'; // Set error message color to red
}

// Function to clear error messages
function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message'); // Select all elements with class 'error-message'
    errorElements.forEach(el => {
        el.textContent = ''; // Clear the error message text for each element
    });
}