document.addEventListener("DOMContentLoaded", function () {
    const loginFormBox = document.getElementById("one");
    const signupFormBox = document.getElementById("four");
    const switchToSignup = document.getElementById("b1");
    const switchToLogin = document.getElementById("b2");
    const rightPanel = document.getElementById("two");
    const leftPanel = document.getElementById("three");

    switchToSignup.onclick = () => {
        signupFormBox.classList.add("mover");
        loginFormBox.classList.add("hide");
        loginFormBox.classList.remove("show");
        leftPanel.classList.add("movel2");
        rightPanel.classList.add("hide");
        rightPanel.classList.remove("show");
        leftPanel.classList.remove("hide");
        leftPanel.classList.add("show");
        signupFormBox.classList.remove("hide");
        signupFormBox.classList.add("show");
    };

    switchToLogin.onclick = () => {
        rightPanel.classList.add("mover2");
        leftPanel.classList.add("hide");
        leftPanel.classList.remove("show");
        loginFormBox.classList.add("movel");
        signupFormBox.classList.add("hide");
        signupFormBox.classList.remove("show");
        rightPanel.classList.remove("hide");
        rightPanel.classList.add("show");
        loginFormBox.classList.remove("hide");
        loginFormBox.classList.add("show");
    };

    // === Handle Signup Form ===
    const signupForm = document.querySelector("form[action='signup.php']");
    const signupError = signupForm.querySelector("#signup-error");

    signupForm.addEventListener("submit", function (e) {
        e.preventDefault();
        signupError.textContent = "";

        const formData = new FormData(signupForm);

        fetch("signup.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = "content.php";
            } else {
                signupError.textContent = data.message;
            }
        })
        .catch(() => {
            signupError.textContent = "Something went wrong. Please try again.";
        });
    });

    // === Handle Login Form ===
    const loginForm = document.querySelector("form[action='login.php']");
    const loginError = loginForm.querySelector("#login-error");

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();
        loginError.textContent = "";

        const formData = new FormData(loginForm);

        fetch("login.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = "content.php";
            } else {
                loginError.textContent = data.message;
            }
        })
        .catch(() => {
            loginError.textContent = "Something went wrong. Please try again.";
        });
    });
});
