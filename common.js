document.addEventListener("DOMContentLoaded", () => {
    // Always load the latest cart and wishlist data
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    
    // Add to cart functionality
    document.querySelectorAll(".cart-btn").forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            
            const productBox = button.closest(".product-box");
            const product = {
                name: productBox.querySelector("strong").textContent,
                quantity: 1,
                price: parseInt(productBox.querySelector(".price").textContent.replace("Rs. ", "")),
                image: productBox.querySelector("img").src,
            };

            // Check if product already exists in the cart
            const existingItem = cart.find(item => item.name === product.name);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push(product);
            }

            // Update cart in localStorage
            localStorage.setItem("cart", JSON.stringify(cart));
            
            // Update cart counter on the fruits page
            updateCartCounter();
        });
    });

    function updateCartCounter() {
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        document.querySelector(".cart span").textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    }

    // Update the cart counter when the page loads
    updateCartCounter();

    // Wishlist functionality
    document.querySelectorAll(".like-btn").forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            
            const productBox = button.closest(".product-box");
            const product = {
                name: productBox.querySelector("strong").textContent,
                price: parseInt(productBox.querySelector(".price").textContent.replace("Rs. ", "")),
                image: productBox.querySelector("img").src,
            };
            
            const existingIndex = wishlist.findIndex(item => item.name === product.name);
            if (existingIndex !== -1) {
                wishlist.splice(existingIndex, 1);
                button.innerHTML = '<i class="far fa-heart"></i>'; // Unfilled heart
            } else {
                wishlist.push(product);
                button.innerHTML = '<i class="fas fa-heart" style="color:red;"></i>'; // Filled heart
            }
            
            localStorage.setItem("wishlist", JSON.stringify(wishlist));
        });
    });

    // Highlight wishlist items on page load
    document.querySelectorAll(".like-btn").forEach(button => {
        const productBox = button.closest(".product-box");
        const productName = productBox.querySelector("strong").textContent;
        
        if (wishlist.some(item => item.name === productName)) {
            button.innerHTML = '<i class="fas fa-heart" style="color:red;"></i>'; // Filled heart
        }
    });
});
