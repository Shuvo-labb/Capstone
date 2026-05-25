const resetForm = document.getElementById('resetForm');
const resetMessage = document.getElementById('resetMessage');

function setResetMessage(text, type){
  resetMessage.textContent = text;
  resetMessage.className = `message ${type}`.trim();
}

function getQueryParam(name){
  const params = new URLSearchParams(window.location.search);
  return params.get(name);
}

resetForm.addEventListener('submit', async function(e){
  e.preventDefault();
  const password = document.getElementById('password').value;
  const confirm = document.getElementById('confirm_password').value;
  if(!password || !confirm){
    setResetMessage('Please fill both password fields.', 'error');
    return;
  }
  if(password !== confirm){
    setResetMessage('Passwords do not match.', 'error');
    return;
  }
  const token = getQueryParam('token');
  if(!token){
    setResetMessage('Missing reset token.', 'error');
    return;
  }

  setResetMessage('Submitting new password...', '');
  try{
    const res = await fetch('../../../backend/api/reset_password.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({token, password})
    });
    if(!res.ok){
      throw new Error('Request failed');
    }
    setResetMessage('Password reset successful. Redirecting to login...', 'success');
    setTimeout(()=> window.location.href = 'login.php', 1500);
  }catch(err){
    setResetMessage('Unable to contact server — try again later.', 'error');
  }
});
