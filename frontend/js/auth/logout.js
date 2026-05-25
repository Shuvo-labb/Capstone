const logoutButton = document.getElementById("logoutButton");
const logoutMessage = document.getElementById("logoutMessage");

function setLogoutMessage(text, type) {
  logoutMessage.textContent = text;
  logoutMessage.className = `message ${type}`.trim();
}

async function submitLogout() {
  setLogoutMessage("Signing out...", "");

  try {
    const response = await fetch("../../../backend/api/logout.php", {
      method: "POST"
    });

    if (!response.ok) {
      throw new Error("Logout failed.");
    }

    setLogoutMessage("You have been logged out.", "success");
    window.location.href = "login.php";
  } catch (error) {
    setLogoutMessage("Backend logout endpoint is not ready yet.", "error");
  }
}

logoutButton.addEventListener("click", submitLogout);
