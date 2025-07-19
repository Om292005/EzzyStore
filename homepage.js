document.addEventListener("DOMContentLoaded", () => {
  // --- Sync Wishlist Heart Icons ---
  fetch("wishlist.php", { credentials: "include" })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const wishlistItems = data.wishlist;
        document.querySelectorAll(".like-btn").forEach((btn) => {
          const productBox = btn.closest(".product-box");
          const productName = productBox.querySelector("strong").textContent;
          const icon = btn.querySelector("i");

          const isInWishlist = wishlistItems.some(item => item.name === productName);
          if (isInWishlist) {
            icon.classList.replace("far", "fas");
            icon.style.color = "red";
          } else {
            icon.classList.replace("fas", "far");
            icon.style.color = "";
          }
        });
      }
    })
    .catch(err => console.error("Error syncing wishlist icons:", err));

  // --- Wishlist Logic ---
  updateWishlistCounter();

  document.querySelectorAll(".like-btn").forEach((btn) => {
    const productBox = btn.closest(".product-box");
    const productName = productBox.querySelector("strong").textContent;
    const icon = btn.querySelector("i");

    btn.addEventListener("click", () => {
      const product = {
        name: productName,
        price: parseFloat(productBox.querySelector(".price").textContent.replace("Rs. ", "")),
        image: productBox.querySelector("img").src
      };

      fetch("update_wishlist.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(product),
        credentials: "include"
      })
        .then(res => res.json())
        .then(data => {
          if (data.added) {
            icon.classList.replace("far", "fas");
            icon.style.color = "red";
          } else if (data.removed) {
            icon.classList.replace("fas", "far");
            icon.style.color = "";
          } else {
            alert(data.error || "Something went wrong.");
          }
          updateWishlistCounter();
        })
        .catch(err => {
          console.error(err);
          alert("Please log in to add to wishlist.");
        });
    });
  });

  function updateWishlistCounter() {
    fetch("wishlist_count.php", { credentials: "include" })
      .then(res => res.json())
      .then(data => {
        const counter = document.querySelector(".like span");
        if (counter) {
          counter.textContent = data.count;
        }
      })
      .catch(() => {
        const counter = document.querySelector(".like span");
        if (counter) counter.textContent = "0";
      });
  }

  // --- Cart Logic ---
  updateCartCounter();

  document.querySelectorAll(".cart-btn").forEach((btn) => {
    btn.addEventListener("click", (event) => {
      event.preventDefault(); // prevent redirection

      const productBox = btn.closest(".product-box");
      const product = {
        name: productBox.querySelector("strong").textContent,
        price: parseFloat(productBox.querySelector(".price").textContent.replace("Rs. ", "")),
        image: productBox.querySelector("img").src,
        quantity: 1,
        offer: productBox.querySelector(".product-offer")?.textContent || "No offer"
      };

      fetch("update_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(product),
        credentials: "include"
      })
        .then(response => {
          if (!response.ok) {
            throw new Error("Cart update failed");
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            updateCartCounter();
            showNotification(`${product.name} added to cart!`);
          } else {
            showNotification(data.error || "Error adding to cart");
          }
        })
        .catch(error => {
          console.error("Error:", error);
          showNotification("Please log in to add items to cart.");
        });
    });
  });

  function updateCartCounter() {
    const counter = document.querySelector(".cart span");

    fetch("get_cart.php", {
      credentials: "include"
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const total = data.cart.reduce((sum, item) => sum + parseInt(item.quantity), 0);
          if (counter) counter.textContent = total;
        } else {
          if (counter) counter.textContent = "0";
        }
      })
      .catch(() => {
        if (counter) counter.textContent = "0";
      });
  }

  function showNotification(message) {
    let badge = document.getElementById("notification-badge");

    if (!badge) {
      badge = document.createElement("div");
      badge.id = "notification-badge";
      badge.className = "badge";
      document.body.appendChild(badge);
    }

    badge.textContent = message;
    badge.classList.add("show");

    setTimeout(() => {
      badge.classList.remove("show");
      setTimeout(() => badge.style.display = "none", 400);
    }, 2500);

    badge.style.display = "block";
  }
});
