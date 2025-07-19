document.querySelectorAll(".like-btn").forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            const productBox = button.closest(".product-box");
            const product = {
                name: productBox.querySelector("strong").textContent,
                price: parseInt(productBox.querySelector(".price").textContent.replace("Rs. ", "")),
                image: productBox.querySelector("img").src,
            };

            const index = wishlist.findIndex(item => item.name === product.name);
            if (index !== -1) {
                wishlist.splice(index, 1); // Remove from wishlist
                button.innerHTML = `<i class="far fa-heart"></i>`; // Reset heart icon
            } else {
                wishlist.push(product); // Add to wishlist
                button.innerHTML = `<i class="fas fa-heart" style="color:red;"></i>`; // Mark as liked
            }

            localStorage.setItem("wishlist", JSON.stringify(wishlist));
            updateWishlistIcons();
        });
    });

    // Function to highlight liked items on page load
    function updateWishlistIcons() {
        document.querySelectorAll(".like-btn").forEach(button => {
            const productBox = button.closest(".product-box");
            const productName = productBox.querySelector("strong").textContent;
            if (wishlist.some(item => item.name === productName)) {
                button.innerHTML = `<i class="fas fa-heart" style="color:red;"></i>`;
            }
        });
    }

    updateWishlistIcons();