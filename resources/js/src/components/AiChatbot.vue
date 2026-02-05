<template>
  <div class="ai-chatbot">
    <!-- Button mở chat -->
    <button 
      v-if="!isOpen" 
      class="chat-toggle-btn"
      @click="toggleChat"
      title="Trợ lý AI"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      </svg>
    </button>

    <!-- Chat window -->
    <div v-if="isOpen" class="chat-window">
      <!-- Header -->
      <div class="chat-header">
        <div class="header-content">
          <div class="ai-avatar">🤖</div>
          <div>
            <h3>Trợ lý AI</h3>
            <p class="status">{{ isTyping ? 'Đang soạn tin...' : 'Trực tuyến' }}</p>
          </div>
        </div>
        <div class="header-actions">
          <button @click="newConversation" title="Cuộc hội thoại mới">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 5v14M5 12h14"></path>
            </svg>
          </button>
          <button @click="toggleChat" title="Đóng">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>

      <!-- Messages -->
      <div class="chat-messages" ref="messagesContainer">
        <!-- Welcome Message -->
        <div v-if="messages.length === 0 && !isLoading" class="welcome-message">
          <h3>👋 Xin chào!</h3>
          <p>Tôi là trợ lý ảo ERP. Bạn cần giúp gì hôm nay?</p>
          <div class="suggestion-chips">
            <button @click="setInput('Doanh thu tháng này thế nào?')">Doanh thu tháng này</button>
            <button @click="setInput('Danh sách học viên mới?')">Học viên mới</button>
            <button @click="setInput('Hướng dẫn tạo hợp đồng')">Tạo hợp đồng</button>
          </div>
        </div>

        <!-- Loading History -->
        <div v-if="isLoading" class="loading-history">
          <div class="typing-indicator">
            <span></span><span></span><span></span>
          </div>
          <p>Đang tải lịch sử trò chuyện...</p>
        </div>

        <!-- Messages -->
        <div v-for="(msg, index) in messages" :key="index" :class="['message', msg.role]">
          <div class="message-content" v-html="formatMessage(msg.content)"></div>
          <div class="message-time">{{ formatTime(msg.created_at) }}</div>
        </div>

        <!-- Typing Indicator -->
        <div v-if="isTyping" class="message assistant typing">
          <div class="typing-indicator">
            <span></span><span></span><span></span>
          </div>
        </div>
      </div>

      <!-- Input -->
      <div class="chat-input">
        <div v-if="quota" class="quota-info">
          Token còn lại: {{ quota.daily_remaining.toLocaleString() }} / {{ quota.daily_limit.toLocaleString() }}
        </div>
        <div class="input-wrapper">
          <textarea
            ref="userInput"
            v-model="inputMessage"
            @keydown.enter.exact.prevent="sendMessage"
            placeholder="Nhập tin nhắn..."
            rows="1"
            :disabled="isTyping"
          ></textarea>
          <button 
            @click="sendMessage" 
            :disabled="!inputMessage.trim() || isTyping"
            class="send-btn"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
 import axios from '../http/axios.js';

export default {
  name: 'AiChatbot',
  
  data() {
    return {
      isOpen: false,
      isLoading: false,
      messages: [],
      inputMessage: '',
      isTyping: false,
      sessionId: null,
      quota: null,
    };
  },

  mounted() {
    this.loadQuota();
    
    // Tự động load hội thoại gần nhất
    this.loadLastConversation();
  },

  methods: {
    toggleChat() {
      this.isOpen = !this.isOpen;
      if (this.isOpen && this.messages.length === 0) {
        this.loadQuota();
      }
    },

    async loadQuota() {
      try {
        const response = await axios.g('/api/ai/quota');
        if (response.data.success) {
          this.quota = response.data.data;
        }
      } catch (error) {
        console.error('Lỗi khi tải quota:', error);
      }
    },

    async sendMessage() {
      if (!this.inputMessage.trim() || this.isTyping) return;

      const userMessage = this.inputMessage.trim();
      this.inputMessage = '';

      // Thêm tin nhắn user
      this.messages.push({
        role: 'user',
        content: userMessage,
        created_at: new Date(),
      });

      this.scrollToBottom();
      this.isTyping = true;

      try {
        const response = await axios.p('/api/ai/chat', {
          message: userMessage,
          session_id: this.sessionId,
        });

        if (response.data.success) {
          this.sessionId = response.data.session_id;
          
          // Thêm tin nhắn AI
          this.messages.push({
            role: 'assistant',
            content: response.data.message,
            created_at: new Date(),
          });

          // Cập nhật quota
          this.loadQuota();
        } else {
          this.messages.push({
            role: 'assistant',
            content: '❌ ' + response.data.message,
            created_at: new Date(),
          });
        }
      } catch (error) {
        this.messages.push({
          role: 'assistant',
          content: '❌ Đã xảy ra lỗi: ' + (error.response?.data?.message || error.message),
          created_at: new Date(),
        });
      } finally {
        this.isTyping = false;
        this.scrollToBottom();
      }
    },

    newConversation() {
      this.messages = [];
      this.sessionId = null;
      this.loadQuota();
      
      // Gọi API tạo session mới nếu cần, hoặc để chat tự tạo
      // Nhưng tốt nhất là reset session ID client side là đủ
    },

    async loadLastConversation() {
      this.isLoading = true;
      try {
        // 1. Lấy danh sách conversations
        const res = await axios.g('/api/ai/conversations');
        if (res.data.success && res.data.data.length > 0) {
          const lastConv = res.data.data[0];
          this.sessionId = lastConv.session_id;

          // 2. Lấy chi tiết tin nhắn
          const detailRes = await axios.g(`/api/ai/conversations/${this.sessionId}`);
          if (detailRes.data.success) {
            this.messages = detailRes.data.data.messages.map(msg => ({
              role: msg.role,
              content: msg.content,
              created_at: msg.created_at, // API trả về string format sẵn hoặc date object
            }));
            this.scrollToBottom();
          }
        }
      } catch (error) {
        console.error('Lỗi load history:', error);
      } finally {
        this.isLoading = false;
      }
    },

    formatMessage(content) {
      // Xử lý markdown tables trước
      content = this.parseMarkdownTable(content);

      // Xử lý code blocks (```language\ncode\n```)
      content = content.replace(/```(\w+)?\n([\s\S]*?)```/g, (match, lang, code) => {
        const language = lang || 'text';
        return `<pre><code class="language-${language}">${this.escapeHtml(code.trim())}</code></pre>`;
      });

      // Xử lý inline code (`code`)
      content = content.replace(/`([^`]+)`/g, '<code>$1</code>');

      // Xử lý bold (**text**)
      content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

      // Xử lý italic (*text*)
      content = content.replace(/\*([^*]+)\*/g, '<em>$1</em>');

      // Xử lý newline
      content = content.replace(/\n/g, '<br>');

      // Xử lý list items
      content = content.replace(/- (.*?)(<br>|$)/g, '• $1$2');

      return content;
    },

    parseMarkdownTable(content) {
      // Regex để tìm markdown table
      const tableRegex = /\|(.+)\|\n\|([:\-\s|]+)\|\n((\|.+\|\n?)+)/g;
      
      return content.replace(tableRegex, (match) => {
        const lines = match.trim().split('\n');
        if (lines.length < 3) return match;

        let html = '<table class="markdown-table">';
        
        // Header
        const headers = lines[0].split('|').filter(cell => cell.trim());
        html += '<thead><tr>';
        headers.forEach(header => {
          html += `<th>${header.trim()}</th>`;
        });
        html += '</tr></thead>';
        
        // Body
        html += '<tbody>';
        for (let i = 2; i < lines.length; i++) {
          const cells = lines[i].split('|').filter(cell => cell.trim());
          html += '<tr>';
          cells.forEach(cell => {
            html += `<td>${cell.trim()}</td>`;
          });
          html += '</tr>';
        }
        html += '</tbody></table>';
        
        return html;
      });
    },

    escapeHtml(text) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return text.replace(/[&<>"']/g, m => map[m]);
    },

    formatTime(date) {
      if (!date) return '';
      const d = new Date(date);
      return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },

    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.messagesContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },

    setInput(text) {
      this.inputMessage = text;
      this.$nextTick(() => {
        if (this.$refs.userInput) {
          this.$refs.userInput.focus();
        }
      });
    },
  },
};
</script>

<style scoped>
.ai-chatbot {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chat-toggle-btn {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  color: white;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chat-toggle-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.chat-window {
  width: 640px;
  height: 700px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: slideUp 0.3s ease;
}

@media (max-width: 768px) {
  .chat-window {
    width: 380px;
    height: 600px;
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Suggestion Chips */
.suggestion-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.suggestion-chips button {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 6px 12px;
  font-size: 12px;
  color: #4a5568;
  cursor: pointer;
  transition: all 0.2s;
}

.suggestion-chips button:hover {
  background: #ebf4ff;
  border-color: #667eea;
  color: #5a67d8;
}

/* Loading History */
.loading-history {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: #a0aec0;
  font-size: 13px;
}

.loading-history .typing-indicator {
  margin-bottom: 8px;
}

.chat-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.ai-avatar {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.chat-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.status {
  margin: 0;
  font-size: 12px;
  opacity: 0.9;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.header-actions button {
  background: rgba(255, 255, 255, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.4);
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.header-actions button:hover {
  background: rgba(255, 255, 255, 1);
  color: #667eea;
  transform: translateY(-2px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background: #f7f8fc;
}

.welcome-message {
  text-align: center;
  padding: 40px 20px;
}

.welcome-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.welcome-message h4 {
  margin: 0 0 8px 0;
  color: #333;
}

.welcome-message p {
  color: #666;
  margin: 16px 0 8px 0;
}

.welcome-message ul {
  list-style: none;
  padding: 0;
  text-align: left;
  max-width: 250px;
  margin: 0 auto;
}

.welcome-message li {
  padding: 8px 0;
  color: #555;
}

.message {
  margin-bottom: 16px;
  display: flex;
}

.message.user {
  justify-content: flex-end;
}

.message-content {
  max-width: 75%;
  padding: 12px 16px;
  border-radius: 12px;
  position: relative;
}

.message.user .message-content {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-bottom-right-radius: 4px;
}

.message.assistant .message-content {
  background: white;
  color: #333;
  border-bottom-left-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.message-text {
  line-height: 1.5;
  word-wrap: break-word;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
  margin-top: 4px;
}

.typing-indicator {
  display: flex;
  gap: 4px;
  padding: 8px 0;
}

.typing-indicator span {
  width: 8px;
  height: 8px;
  background: #999;
  border-radius: 50%;
  animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-10px);
  }
}

.chat-input {
  border-top: 1px solid #e5e7eb;
  background: white;
}

.quota-info {
  padding: 8px 16px;
  font-size: 11px;
  color: #666;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.input-wrapper {
  display: flex;
  padding: 12px;
  gap: 8px;
  align-items: flex-end;
}

.input-wrapper textarea {
  flex: 1;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 14px;
  resize: none;
  max-height: 100px;
  font-family: inherit;
}

.input-wrapper textarea:focus {
  outline: none;
  border-color: #667eea;
}

.send-btn {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.send-btn:hover:not(:disabled) {
  transform: scale(1.05);
}

.send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Scrollbar */
.chat-messages::-webkit-scrollbar {
  width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
  background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

/* Code Styling */
.message-content code {
  background: #f1f5f9;
  color: #e11d48;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Courier New', Courier, monospace;
  font-size: 13px;
}

.message-content pre {
  background: #1e293b;
  color: #e2e8f0;
  padding: 16px;
  border-radius: 8px;
  overflow-x: auto;
  margin: 8px 0;
  border-left: 4px solid #667eea;
}

.message-content pre code {
  background: transparent;
  color: inherit;
  padding: 0;
  font-size: 13px;
  line-height: 1.6;
  display: block;
}

.message-content strong {
  font-weight: 600;
  color: #1e293b;
}

.message-content em {
  font-style: italic;
  color: #64748b;
}

/* Markdown Table Styling */
.markdown-table {
  width: 100%;
  border-collapse: collapse;
  margin: 12px 0;
  font-size: 13px;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.markdown-table thead {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.markdown-table th {
  padding: 12px;
  text-align: left;
  font-weight: 600;
  border-bottom: 2px solid #e2e8f0;
}

.markdown-table td {
  padding: 10px 12px;
  border-bottom: 1px solid #e2e8f0;
}

.markdown-table tbody tr:hover {
  background: #f7fafc;
}

.markdown-table tbody tr:last-child td {
  border-bottom: none;
}
</style>
