document.addEventListener("DOMContentLoaded", function () {
    const otpForm = document.getElementById("otp-form");
    const emailInput = document.getElementById("email");
    const otpInput = document.getElementById("otp");
    const errorMessage = document.getElementById("error-message");
    const sendOtpBtn = document.getElementById("send-otp-btn");
    const resendOtpBtn = document.getElementById("resend-otp-btn");
    const emailWarning = document.getElementById("email-warning");
    const otpContainer = document.querySelector(".otp-container");

    let otpSent = false;
    let countdown = 30;

    function startResendTimer() {
    resendOtpBtn.classList.remove("enabled");
    resendOtpBtn.disabled = true;
    resendOtpBtn.textContent = `Resend OTP (${countdown})`;

    const timer = setInterval(() => {
        countdown--;
        resendOtpBtn.textContent = `Resend OTP (${countdown})`;
        if (countdown <= 0) {
            clearInterval(timer);
            resendOtpBtn.disabled = false;
            resendOtpBtn.textContent = "Resend OTP";
            resendOtpBtn.classList.add("enabled");

            // ✅ REMOVE inline styles manually if they exist
            resendOtpBtn.removeAttribute("style");

            countdown = 30;
        }
    }, 1000);
}


    function showSpinner(button, text) {
    button.disabled = true;
    button.innerHTML = `<span class="spinner"></span> ${text}`;
    button.classList.add("loading");
}

function hideSpinner(button, defaultText = "Send OTP") {
    button.disabled = false;
    button.innerHTML = `<span>${defaultText}</span>`;
    button.classList.remove("loading");
}

function sendOTP(email, isResend = false) {
    const button = isResend ? resendOtpBtn : sendOtpBtn;
    const label = isResend ? "Resending..." : "Sending...";

    showSpinner(button, label);

    fetch("send_email_otp.php", {
        method: "POST",
        body: new URLSearchParams({ email: email })
    })
    .then(res => res.text())
    .then(response => {
        if (response.includes("OTP Sent")) {
            errorMessage.style.color = "green";
            errorMessage.textContent = isResend ? "OTP resent successfully." : "OTP sent successfully.";
            startResendTimer();
            otpSent = true;

            sendOtpBtn.disabled = true;
            resendOtpBtn.style.display = "inline-block";
            emailWarning.style.display = "block";
        } else {
            errorMessage.style.color = "red";
            errorMessage.textContent = "Failed to send OTP. Please try again.";
        }
    })
    .catch(() => {
        errorMessage.style.color = "red";
        errorMessage.textContent = "Network error. Try again.";
    })
    .finally(() => {
        hideSpinner(button, isResend ? "Resend OTP" : "Send OTP");
        setTimeout(() => {
            errorMessage.textContent = "";
        }, 5000);
    });
}


    sendOtpBtn.addEventListener("click", function () {
        if (otpSent) return;

        const email = emailInput.value.trim();
        if (!/^[\w-.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email)) {
            errorMessage.style.color = "red";
            errorMessage.textContent = "Please enter a valid email address.";
            return;
        }

        sendOTP(email);
    });

    resendOtpBtn.addEventListener("click", function () {
        const email = emailInput.value.trim();
        if (!/^[\w-.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email)) {
            errorMessage.style.color = "red";
            errorMessage.textContent = "Please enter a valid email address.";
            return;
        }

        sendOTP(email, true);
    });

    otpForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const otpValue = otpInput.value.trim();
        const email = emailInput.value.trim();

        if (!/^\d{6}$/.test(otpValue)) {
            errorMessage.style.color = "red";
            errorMessage.textContent = "Please enter a valid 6-digit OTP.";
            return;
        }

        fetch("verify_email_otp.php", {
            method: "POST",
            body: new URLSearchParams({ email: email, otp: otpValue })
        })
        .then(res => res.text())
        .then(response => {
            if (response.includes("OTP Verified")) {
                showSuccessMessage();
            } else {
                errorMessage.style.color = "red";
                errorMessage.textContent = "Invalid or expired OTP.";
            }
        });
    });

    console.log("hey");

    function showSuccessMessage() {
    otpContainer.innerHTML = `
        <div class="success-box">
            <div class="check-icon">&#10004;</div>
            <h2>Thank You for Your Order!</h2>
            <p>Your OTP has been verified successfully and your payment is confirmed.</p>
            <a href="homepage.html" class="continue-btn">Continue Shopping</a>
        </div>
    `;
}
});
