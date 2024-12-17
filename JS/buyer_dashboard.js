// Select elements for interactions
const editProfileBtn = document.getElementById('editProfileBtn');
const profileName = document.getElementById('profileName');
const profileEmail = document.getElementById('profileEmail');
const profileLocation = document.getElementById('profileLocation');
const addFavoriteBtn = document.getElementById('addFavoriteBtn');
const viewHistoryBtn = document.getElementById('viewHistoryBtn');
const purchaseHistoryModal = document.getElementById('purchaseHistoryModal');
const closeModalBtn = document.querySelector('.close-btn');
const clearNotificationsBtn = document.getElementById('clearNotificationsBtn');

// Profile Edit Handler
editProfileBtn.addEventListener('click', () => {
    const newName = prompt('Enter your new name:', profileName.textContent);
    const newLocation = prompt('Enter your new location:', profileLocation.textContent);
    
    if (newName && newLocation) {
        // Send AJAX request to update profile
        fetch('edit_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `name=${encodeURIComponent(newName)}&location=${encodeURIComponent(newLocation)}`
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Show success or error message from PHP
            location.reload(); // Refresh the page to show updated info
        })
        .catch(error => console.error('Error:', error));
    }
});

// // Add New Favorite Handler
// addFavoriteBtn.addEventListener('click', () => {
//     const productName = prompt('Enter the name of the product to add to favorites:');
//     if (productName) {
//         // Send AJAX request to add favorite
//         fetch('add_favorite.php', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/x-www-form-urlencoded'
//             },
//             body: `product_id=${encodeURIComponent(productName)}` // Assuming product_id is passed
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 const favoriteSection = document.querySelector('.favorites-section #favoritesList');
//                 const newFavorite = document.createElement('div');
//                 newFavorite.classList.add('favorite-item');
//                 newFavorite.innerHTML = `
//                     <p><strong>Product:</strong> ${productName}</p>
//                     <button class="remove-btn">Remove from Favorites</button>
//                 `;
//                 favoriteSection.appendChild(newFavorite);

//                 // Add event listener to new remove button
//                 newFavorite.querySelector('.remove-btn').addEventListener('click', function () {
//                     // Send AJAX request to remove favorite
//                     fetch('remove_favorite.php', {
//                         method: 'POST',
//                         headers: {
//                             'Content-Type': 'application/x-www-form-urlencoded'
//                         },
//                         body: `product_id=${encodeURIComponent(productName)}` // Assuming product_id is passed
//                     })
//                     .then(response => response.json())
//                     .then(data => {
//                         if (data.success) {
//                             newFavorite.remove();
//                             alert('Item removed from favorites.');
//                         }
//                     });
//                 });
//             } else {
//                 alert(data.error || "Failed to add favorite.");
//             }
//         });
//     }
// });

// // View Purchase History Modal Handler
// viewHistoryBtn.addEventListener('click', () => {
//     fetch('get_purchase_history.php')
//         .then(response => response.json())
//         .then(data => {
//             const purchaseHistoryContent = document.getElementById("purchaseHistoryContent");
//             purchaseHistoryContent.innerHTML = ""; // Clear previous content
            
//             data.forEach(item => {
//                 purchaseHistoryContent.innerHTML += `<p><strong>Product:</strong> ${item.product}</p><p><strong>Date:</strong> ${item.date}</p>`;
//             });

//             purchaseHistoryModal.style.display = 'block'; // Show modal
//         });
// });

// // Close modal functionality
// closeModalBtn.addEventListener("click", () => {
//     purchaseHistoryModal.style.display = "none";
// });

// window.addEventListener("click", (event) => {
//     if (event.target == purchaseHistoryModal) {
//         purchaseHistoryModal.style.display = "none";
//     }
// });

// // Clear Notifications Handler
// clearNotificationsBtn.addEventListener("click", () => {
//     fetch('clear_notifications.php')
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 const notificationsSection = document.querySelector('.notifications p');
//                 notificationsSection.textContent = 'No new notifications';
//                 alert('Notifications cleared.');
//             } else {
//                 alert(data.error || "Failed to clear notifications.");
//             }
//         });
// });