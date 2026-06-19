document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm");
    const registerMessage = document.getElementById("registerMessage");

    if (!registerForm) {
        return;
    }

    registerForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        registerMessage.textContent = "Creating account...";
        registerMessage.style.color = "";

        try {
            const formData = new FormData(registerForm);
            const response = await fetch("handle_register.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            registerMessage.textContent = result.message;
            registerMessage.style.color = result.success ? "green" : "red";

            if (result.success) {
                registerForm.reset();
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 1200);
            }
        } catch (error) {
            registerMessage.textContent = "Unable to reach the registration service. Please try again.";
            registerMessage.style.color = "red";
        }
    });
});
