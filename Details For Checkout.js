document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".address_form");
    const messageBox = document.getElementById("error_message");

    const pincodeInput = document.getElementById("pincode");
    const phoneInput = document.getElementById("phone");

    // 🔒 Restrict PINCODE to 6 digits only, first digit must be non-zero
    pincodeInput.addEventListener("input", function () {
        let value = this.value.replace(/\D/g, ''); // Remove non-digits

        if (value.length > 6) {
            value = value.slice(0, 6);
        }

        // Ensure first digit is not 0
        if (value.length >= 1 && value.charAt(0) === '0') {
            value = value.slice(1);
        }

        this.value = value;
    });

    // 🔒 Restrict PHONE to 10 digits only and must start with 6/7/8/9
    phoneInput.addEventListener("input", function () {
        let value = this.value.replace(/\D/g, ''); // Remove non-digits

        if (value.length > 10) {
            value = value.slice(0, 10);
        }

        // Prevent entry if first digit is not 6, 7, 8 or 9
        if (value.length === 1 && !/[6-9]/.test(value.charAt(0))) {
            value = '';
        }

        this.value = value;
    });

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        messageBox.innerHTML = "";
        messageBox.style.color = "black";

        const formData = new FormData(form);

        fetch("submit_delivery.php", {
            method: "POST",
            body: formData,
            credentials: "include"
        })
        .then(res => res.text())
        .then(text => {
            console.log("Raw Response:", text);
            let data;

            try {
                data = JSON.parse(text);
            } catch (e) {
                messageBox.innerHTML = "Invalid server response.";
                messageBox.style.color = "red";
                return;
            }

            messageBox.innerHTML = data.message;
            messageBox.style.color = data.success ? "green" : "red";

            if (data.success) {
                const btn = document.getElementById("submitbtn");
                btn.disabled = true;
                setTimeout(() => {
                    window.location.href = "payment.html";
                }, 2000);
            } else {
                if (data.message.includes("You can submit again in")) {
                    messageBox.style.fontWeight = "bold";
                }
            }
        })
        .catch(err => {
            console.error("Fetch failed:", err);
            messageBox.innerHTML = "Something went wrong. Please try again.";
            messageBox.style.color = "red";
        });
    });
});
