document.addEventListener("DOMContentLoaded", () => {
    const forgotForm = document.getElementById("forgotForm");
    const forgotMessage = document.getElementById("forgotMessage");
    const forgotResetLink = document.getElementById("forgotResetLink");

    if (!forgotForm) {
        return;
    }

    forgotForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        forgotMessage.textContent = "Sending reset link...";
        forgotMessage.style.color = "";

        if (forgotResetLink) {
            forgotResetLink.hidden = true;
            forgotResetLink.textContent = "";
        }

        try {
            const formData = new FormData(forgotForm);
            const response = await fetch("handle_forgot_password.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            forgotMessage.textContent = result.message;
            forgotMessage.style.color = result.success ? "green" : "red";

            if (result.success) {
                forgotForm.reset();

                if (result.reset_link && forgotResetLink) {
                    forgotResetLink.hidden = false;
                    forgotResetLink.innerHTML =
                        'Development reset link: <a href="' +
                        result.reset_link +
                        '">Open reset page</a>';
                }
            }
        } catch (error) {
            forgotMessage.textContent = "Unable to reach the password reset service. Please try again.";
            forgotMessage.style.color = "red";
        }
    });
});
