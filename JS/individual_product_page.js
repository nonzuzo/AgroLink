// Elements
const contactBtn = document.querySelector('.contact-btn');
const modal = document.getElementById('contactModal');
const closeModalBtn = document.querySelector('.close-modal');
const submitMessageBtn = document.querySelector('.submit-message');
const messageField = document.getElementById('messageField');
const relatedProductsGrid = document.querySelector('.related-products-grid');

// Show Contact Modal
contactBtn.addEventListener('click', () => {
    modal.style.display = 'block';
});

// Close Contact Modal
closeModalBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

window.addEventListener('click', (event) => {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
});

// Send Message
submitMessageBtn.addEventListener('click', () => {
    const message = messageField.value.trim();
    if (message) {
        alert(`Message sent to the seller: "${message}"`);
        messageField.value = '';
        modal.style.display = 'none';
    } else {
        alert("Please enter a message.");
    }
});

// Related Products (Simulated Data)
document.addEventListener("DOMContentLoaded", () => {
    const relatedProducts = [
        { name: "Organic Carrots", price: "$8", image: "related1.jpg" },
        { name: "Whole Grain Rice", price: "$5", image: "related2.jpg" },
        { name: "Fresh Milk", price: "$3", image: "related3.jpg" }
    ];

    relatedProducts.forEach(product => {
        const productDiv = document.createElement('div');
        productDiv.classList.add('related-product');
        productDiv.innerHTML = `
            <img src="${product.image}" alt="${product.name}">
            <h3>${product.name}</h3>
            <p>Price: ${product.price}</p>
        `;
        relatedProductsGrid.appendChild(productDiv);
    });
});
