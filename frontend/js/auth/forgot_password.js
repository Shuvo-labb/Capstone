const forgotForm = document.getElementById('forgotForm');
const forgotMessage = document.getElementById('forgotMessage');

function setForgotMessage(text, type){
  forgotMessage.textContent = text;
  forgotMessage.className = `message ${type}`.trim();
}

forgotForm.addEventListener('submit', async function(e){
  e.preventDefault();
  const email = document.getElementById('email').value.trim();
  if(!email){
    setForgotMessage('Please enter your email address.', 'error');
    return;
  }

  setForgotMessage('Sending reset link...', '');
  try{
    const res = await fetch('../../../backend/api/forgot_password.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({email})
    });
    if(!res.ok){
      throw new Error('Request failed');
    }
    setForgotMessage('If the email exists, a reset link was sent.', 'success');
  }catch(err){
    setForgotMessage('Unable to contact server — try again later.', 'error');
  }
});
