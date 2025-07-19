document.addEventListener("DOMContentLoaded", function () {
  fetchWishlistFromServer();
});

function fetchWishlistFromServer() {
  fetch("wishlist.php", {
    credentials: "include"
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        renderWishlist(data.wishlist);
      } else {
        document.querySelector("#Wishlist-items").innerHTML =
          "<p>Your wishlist is empty.</p>";
      }
    })
    .catch((err) => {
      console.error("Error fetching wishlist:", err);
      document.querySelector("#Wishlist-items").innerHTML =
        "<p>Error loading wishlist. Please try again.</p>";
    });
}

function renderWishlist(wishlistItems) {
  const wishlistContainer = document.querySelector("#Wishlist-items");
  wishlistContainer.innerHTML = ""; // Clear previous items

  if (wishlistItems.length === 0) {
    wishlistContainer.innerHTML = "<p>Your wishlist is empty.</p>";
    return;
  }

  wishlistItems.forEach((item) => {
    const productElement = document.createElement("div");
    productElement.classList.add("product");

    productElement.innerHTML = `
      <img src="${item.image}" alt="${item.name}">
      <div class="product-info">
        <h3 class="product-name">${item.name}</h3>
        <p class="product-price">Price: Rs ${item.price}</p>
        <button class="product-remove" onclick='removeFromWishlist(${JSON.stringify(JSON.stringify(item))})'>Remove</button>
      </div>
    `;

    wishlistContainer.appendChild(productElement);
  });
}

// Receives a stringified item, parses it back to object
function removeFromWishlist(itemStr) {
  const item = JSON.parse(itemStr);

  if (!item || !item.name || !item.price || !item.image) {
    alert("Invalid product data.");
    return;
  }

  fetch("update_wishlist.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify(item)
  })
    .then(res => res.json())
    .then(data => {
      if (data.removed) {
        fetchWishlistFromServer(); // Refresh UI
      } else {
        alert(data.error || "Something went wrong");
      }
    })
    .catch(err => {
      console.error(err);
      alert("Error removing item. Try again.");
    });
}
