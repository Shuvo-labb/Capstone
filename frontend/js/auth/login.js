const loginForm = document.getElementById("loginForm");
const loginMessage = document.getElementById("loginMessage");

function setMessage(text, type) {
  loginMessage.textContent = text;
  loginMessage.className = `message ${type}`.trim();
}

async function submitLogin(event) {
  event.preventDefault();

  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value;

  if (!username || !password) {
    setMessage("Please enter both username and password.", "error");
    return;
  }

  setMessage("Checking credentials...", "");

  try {
    const response = await fetch("../../../backend/api/login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ username, password })
    });

    if (!response.ok) {
      throw new Error("Login failed.");
    }

    setMessage("Login request sent successfully.", "success");
    window.location.href = "../dashboard/index.php";
  } catch (error) {
    setMessage("Backend login endpoint is not ready yet.", "error");
  }
}

loginForm.addEventListener("submit", submitLogin);
