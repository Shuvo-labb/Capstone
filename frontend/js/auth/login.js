// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : login.js
// Description     : Authentication client script
// First Commit Date: Monday,18-May-2026
// Last Commit Date : Friday,19-Jun-2026
document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const loginMessage = document.getElementById("loginMessage");

    if (!loginForm) {
        return;
    }

    loginForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        loginMessage.textContent = "Signing in...";
        loginMessage.style.color = "";

        try {
            const formData = new FormData(loginForm);
            const response = await fetch("handle_login.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            loginMessage.textContent = result.message;
            loginMessage.style.color = result.success ? "green" : "red";

            if (result.success) {
                window.location.href = "../dashboard/index.php";
            }
        } catch (error) {
            loginMessage.textContent = "Unable to reach the login service. Please try again.";
            loginMessage.style.color = "red";
        }
    });
});
