<!-- AI Security Chatbot widget outer layout container -->
<div id="chatbot-widget">
  <!-- Render float button to toggle visibility of chatbot widget -->
  <button id="chatbot-toggle" onclick="toggleChatbot()">🤖 AI Assistant</button>
  <!-- Render conversation chat window default to hidden style display -->
  <div id="chatbot-window" style="display:none;">
    <!-- Render header block with chatbot title name -->
    <div id="chatbot-header">
      <!-- Render label text span for assistant -->
      <span>Security AI Assistant</span>
      <!-- Close action button with multiply sign representation -->
      <button onclick="toggleChatbot()">×</button>
    <!-- Close chatbot header container -->
    </div>
    <!-- Create message box container to append dialogue list -->
    <div id="chatbot-messages"></div>
    <!-- Create bottom message input form controls wrapper -->
    <div id="chatbot-input">
      <!-- Render text input box that triggers send on pressing Enter key -->
      <input type="text" id="chatbot-user-input" placeholder="Ask about security threats..." onkeypress="if(event.key==='Enter')sendMessage()">
      <!-- Render send dispatch button control -->
      <button onclick="sendMessage()">Send</button>
    <!-- Close chatbot input controls wrapper -->
    </div>
  <!-- Close conversation chat window wrapper -->
  </div>
<!-- Close chatbot widget outer container -->
</div>

<!-- Start chatbot CSS layout definitions -->
<style>
/* Position widget container fixed to bottom right corner */
#chatbot-widget { position:fixed; bottom:20px; right:20px; z-index:1000; font-family:Arial,sans-serif; }
/* Style float button with rounded circle and shadow borders */
#chatbot-toggle { padding:12px 20px; background:#4CAF50; color:#fff; border:none; border-radius:30px; cursor:pointer; font-size:14px; box-shadow:0 4px 12px rgba(0,0,0,0.3); }
/* Style outer chat box layout properties using flex column format */
#chatbot-window { position:absolute; bottom:60px; right:0; width:350px; height:450px; background:#1a1a2e; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.4); display:flex; flex-direction:column; }
/* Style top header bar with flex horizontal alignment */
#chatbot-header { padding:15px; background:#2d2d44; color:#fff; border-radius:12px 12px 0 0; display:flex; justify-content:space-between; align-items:center; }
/* Reset header close button outline border and background */
#chatbot-header button { background:none; border:none; color:#fff; font-size:20px; cursor:pointer; }
/* Style scrollable chat message log container area */
#chatbot-messages { flex:1; padding:15px; overflow-y:auto; color:#fff; }
/* Style input box bar alignment using horizontal flex spacing */
#chatbot-input { padding:15px; background:#2d2d44; border-radius:0 0 12px 12px; display:flex; gap:10px; }
/* Style text input field to consume remaining horizontal space */
#chatbot-input input { flex:1; padding:10px; border:none; border-radius:5px; outline:none; }
/* Style send button element */
#chatbot-input button { padding:10px 20px; background:#4CAF50; color:#fff; border:none; border-radius:5px; cursor:pointer; }
/* Define base structure spacing properties of bubbles */
.message { margin:10px 0; padding:10px; border-radius:8px; max-width:80%; }
/* Align user message bubbles to right side of chat container */
.user-message { background:#4CAF50; align-self:flex-end; margin-left:auto; }
/* Align assistant message bubbles to left side of chat container */
.bot-message { background:#2d2d44; align-self:flex-start; }
</style>

<!-- Start chatbot interaction script code -->
<script>
// Declare function to toggle chat window visibility
function toggleChatbot() {
  // Select chat window element by ID reference
  const windowEl = document.getElementById('chatbot-window');
  // Check and toggle between flex and none display modes
  windowEl.style.display = windowEl.style.display === 'none' ? 'flex' : 'none';
  // Check if window is open and conversation list is currently empty
  if (windowEl.style.display === 'flex' && document.getElementById('chatbot-messages').children.length === 0) {
    // Append introductory greeting message from chatbot
    addBotMessage("Hello! I'm your Security AI Assistant. Ask me about threats, recommendations, or security insights.");
  }
}

// Declare function to append chatbot message text into list
function addBotMessage(text) {
  // Create a new div element
  const div = document.createElement('div');
  // Set div class as bot-message bubble
  div.className = 'message bot-message';
  // Set content text safely
  div.textContent = text;
  // Append new bubble div to messages log container
  document.getElementById('chatbot-messages').appendChild(div);
  // Auto scroll messages container to bottom position
  document.getElementById('chatbot-messages').scrollTop = document.getElementById('chatbot-messages').scrollHeight;
}

// Declare function to append user message text into list
function addUserMessage(text) {
  // Create a new div element
  const div = document.createElement('div');
  // Set div class as user-message bubble
  div.className = 'message user-message';
  // Set content text safely
  div.textContent = text;
  // Append new bubble div to messages log container
  document.getElementById('chatbot-messages').appendChild(div);
  // Auto scroll messages container to bottom position
  document.getElementById('chatbot-messages').scrollTop = document.getElementById('chatbot-messages').scrollHeight;
}

// Declare asynchronous function to post user message to API
// Ponytail: chatbot endpoint does not support off-line caching.
// Upgrade path: store conversation in localStorage or handle offline network failures gracefully.
async function sendMessage() {
  // Select user text input element reference
  const input = document.getElementById('chatbot-user-input');
  // Extract and trim value string from input field
  const message = input.value.trim();
  // Return immediately if message is empty
  if (!message) return;
  
  // Render user text bubble in messages list
  addUserMessage(message);
  // Clear input box value
  input.value = '';
  
  // Render temporary thinking indicator bubble
  addBotMessage('Thinking...');
  
  // Try-catch block for API fetch request
  try {
    // Dispatch asynchronous POST request to backend API
    const response = await fetch('../includes/chatbot_api.php', {
      // Set method parameter to POST
      method: 'POST',
      // Declare request headers specifying application/json format
      headers: { 'Content-Type': 'application/json' },
      // Bind serialized message object to body parameter
      body: JSON.stringify({ message: message })
    });
    // Parse received response stream as JSON object
    const data = await response.json();
    
    // Remove "Thinking..." message placeholder
    document.getElementById('chatbot-messages').lastChild.remove();
    // Render actual API response text or fallback error message
    addBotMessage(data.reply || 'Sorry, I encountered an error.');
  // Intercept fetch or parsing exceptions
  } catch (e) {
    // Remove "Thinking..." message placeholder
    document.getElementById('chatbot-messages').lastChild.remove();
    // Render connection error notification text
    addBotMessage('Error: Unable to connect to AI service.');
  }
}
</script>
