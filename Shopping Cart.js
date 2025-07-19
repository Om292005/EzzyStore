document.addEventListener("DOMContentLoaded", () => {
  const cartItemsContainer = document.getElementById("cart-items");
  const totalItemsElement = document.getElementById("total-items");
  const totalPriceElement = document.getElementById("total-price");
  const cartTotalSection = document.querySelector(".cart-total");

  function loadCart() {
    fetch("get_cart.php", { credentials: "include" })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          renderCart(data.cart);
        } else {
          cartItemsContainer.innerHTML = "<p>Please log in to view your cart.</p>";
          cartTotalSection.style.display = "none";
        }
      })
      .catch(err => {
        console.error("Error loading cart:", err);
        cartItemsContainer.innerHTML = "<p>Error loading cart.</p>";
        cartTotalSection.style.display = "none";
      });
  }

  function renderCart(cart) {
    cartItemsContainer.innerHTML = "";

    if (cart.length === 0) {
      cartItemsContainer.innerHTML = "<center><p>Your cart is empty.</p></center>";
      cartTotalSection.style.display = "none";
      return;
    }

    cartTotalSection.style.display = "block";

    let totalItems = 0;
    let totalPrice = 0;

    cart.forEach(product => {
      totalItems += product.quantity;
      totalPrice += product.price * product.quantity;

      const productElement = document.createElement("div");
      productElement.classList.add("product");
      productElement.innerHTML = `
        <img src="${product.image}" alt="${product.product_name}">
        <div class="product-info">
          <h3 class="product-name">${product.product_name}</h3>
          <p class="product-price">Price: Rs ${parseFloat(product.price).toFixed(2)}</p>
          <p class="product-offer">Offer: ${product.offer}</p>
          <p class="product-total">Total: Rs ${(product.price * product.quantity).toFixed(2)}</p>
          <p class="product-quantity">Quantity:
            <input type="number" min="1" value="${product.quantity}" data-name="${product.product_name}" class="quantity-input">
          </p>
          <button class="product-remove" data-name="${product.product_name}">Remove</button>
        </div>
      `;
      cartItemsContainer.appendChild(productElement);
    });

    totalItemsElement.textContent = totalItems;
    totalPriceElement.textContent = totalPrice.toFixed(2);
  }

  cartItemsContainer.addEventListener("change", (event) => {
    if (event.target.classList.contains("quantity-input")) {
      const name = event.target.dataset.name;
      const quantity = parseInt(event.target.value);

      fetch("update_quantity.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ name, quantity })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) loadCart();
        });
    }
  });

  cartItemsContainer.addEventListener("click", (event) => {
    if (event.target.classList.contains("product-remove")) {
      const name = event.target.dataset.name;

      fetch("remove_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ name })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) loadCart();
        });
    }
  });

  loadCart();
});
