document.addEventListener("DOMContentLoaded", () => {
    const resetForm = document.getElementById("resetForm");
    const resetMessage = document.getElementById("resetMessage");
    const resetTokenInput = document.getElementById("resetToken");
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get("token");

    if (resetTokenInput && token) {
        resetTokenInput.value = token;
    }

    if (!token && resetMessage) {
        resetMessage.textContent = "Reset token is missing. Request a new link from the forgot password page.";
        resetMessage.style.color = "red";
    }

    if (!resetForm) {
        return;
    }

    resetForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        if (!token) {
            resetMessage.textContent = "Reset token is missing. Request a new link from the forgot password page.";
            resetMessage.style.color = "red";
            return;
        }

        resetMessage.textContent = "Updating password...";
        resetMessage.style.color = "";

        try {
            const formData = new FormData(resetForm);
            formData.set("token", token);

            const response = await fetch("handle_reset_password.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            resetMessage.textContent = result.message;
            resetMessage.style.color = result.success ? "green" : "red";

            if (result.success) {
                resetForm.reset();
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 1200);
            }
        } catch (error) {
            resetMessage.textContent = "Unable to reach the password reset service. Please try again.";
            resetMessage.style.color = "red";
        }
    });
});
