<template>
  <div class="ai-admin-panel">
    <div class="panel-header">
      <h2>🤖 Quản lý AI Chatbot</h2>
      <p>Cấu hình quyền truy cập và theo dõi sử dụng</p>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button 
        :class="['tab', { active: activeTab === 'users' }]"
        @click="activeTab = 'users'"
      >
        👥 Quản lý User
      </button>
      <button 
        :class="['tab', { active: activeTab === 'statistics' }]"
        @click="activeTab = 'statistics'; loadStatistics()"
      >
        📊 Thống kê
      </button>
      <button 
        :class="['tab', { active: activeTab === 'knowledge' }]"
        @click="activeTab = 'knowledge'; loadKnowledge()"
      >
        📚 Tài liệu
      </button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
      <!-- Users Tab -->
      <div v-if="activeTab === 'users'" class="users-tab">
        <div class="search-bar">
          <input 
            v-model="searchQuery" 
            @input="searchUsers"
            type="text" 
            placeholder="Tìm kiếm user..."
          />
        </div>

        <div class="users-table">
          <table>
            <thead>
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th>Hạn mức ngày</th>
                <th>Hạn mức tháng</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td><strong>{{ user.name }}</strong></td>
                <td>{{ user.email }}</td>
                <td>
                  <span :class="['badge', user.is_enabled ? 'success' : 'danger']">
                    {{ user.is_enabled ? 'Đã bật' : 'Tắt' }}
                  </span>
                </td>
                <td>{{ user.daily_limit?.toLocaleString() || 0 }}</td>
                <td>{{ user.monthly_limit?.toLocaleString() || 0 }}</td>
                <td>
                  <button 
                    class="btn-edit" 
                    @click="editUser(user)"
                    title="Chỉnh sửa"
                  >
                    ✏️
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="pagination">
          <button 
            @click="loadUsers(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
          >
            ← Trước
          </button>
          <span>Trang {{ pagination.current_page }} / {{ pagination.last_page }}</span>
          <button 
            @click="loadUsers(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
          >
            Sau →
          </button>
        </div>
      </div>

      <!-- Statistics Tab -->
      <div v-if="activeTab === 'statistics'" class="statistics-tab">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value">{{ statistics.summary?.total_users || 0 }}</div>
            <div class="stat-label">Users đang dùng AI</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🔢</div>
            <div class="stat-value">{{ (statistics.summary?.total_tokens || 0).toLocaleString() }}</div>
            <div class="stat-label">Tổng tokens đã dùng</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value">${{ statistics.summary?.total_cost || 0 }}</div>
            <div class="stat-label">Tổng chi phí</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">📨</div>
            <div class="stat-value">{{ (statistics.summary?.total_requests || 0).toLocaleString() }}</div>
            <div class="stat-label">Tổng requests</div>
          </div>
        </div>

        <!-- Top Users -->
        <div class="top-users">
          <h3>🏆 Top Users</h3>
          <table>
            <thead>
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Tokens đã dùng</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in statistics.top_users" :key="index">
                <td>{{ user.user_name }}</td>
                <td>{{ user.user_email }}</td>
                <td>{{ user.total_tokens.toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Knowledge Tab -->
      <div v-if="activeTab === 'knowledge'" class="knowledge-tab">
        <button class="btn-primary" @click="showKnowledgeModal()">
          ➕ Thêm tài liệu mới
        </button>

        <div class="knowledge-list">
          <div v-for="doc in knowledgeList" :key="doc.id" class="knowledge-item">
            <div class="knowledge-header">
              <h4>{{ doc.title }}</h4>
              <span class="category-badge">{{ doc.category }}</span>
            </div>
            <p class="knowledge-content">{{ doc.content.substring(0, 150) }}...</p>
            <div class="knowledge-actions">
              <button class="btn-edit" @click="editKnowledge(doc)">✏️ Sửa</button>
              <button class="btn-delete" @click="deleteKnowledge(doc.id)">🗑️ Xóa</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div v-if="showEditModal" class="modal" @click.self="showEditModal = false">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Chỉnh sửa quyền AI - {{ editingUser.name }}</h3>
          <button @click="showEditModal = false">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>
              <input type="checkbox" v-model="editForm.is_enabled" />
              Bật quyền sử dụng AI
            </label>
          </div>
          <div class="form-group">
            <label>Hạn mức token/ngày</label>
            <input type="number" v-model.number="editForm.daily_token_limit" />
          </div>
          <div class="form-group">
            <label>Hạn mức token/tháng</label>
            <input type="number" v-model.number="editForm.monthly_token_limit" />
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" v-model="editForm.can_query_data" />
              Cho phép tra cứu dữ liệu
            </label>
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" v-model="editForm.can_generate_reports" />
              Cho phép tạo báo cáo Excel
            </label>
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" v-model="editForm.can_access_knowledge" />
              Cho phép truy cập tài liệu
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-secondary" @click="showEditModal = false">Hủy</button>
          <button class="btn-primary" @click="saveUserPermission">Lưu</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
 import axios from '../http/axios.js';

export default {
  name: 'AiAdminPanel',
  
  data() {
    return {
      activeTab: 'users',
      searchQuery: '',
      users: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
      },
      statistics: {
        summary: {},
        top_users: [],
        daily_usage: [],
      },
      knowledgeList: [],
      showEditModal: false,
      editingUser: null,
      editForm: {
        is_enabled: false,
        daily_token_limit: 10000,
        monthly_token_limit: 300000,
        can_query_data: true,
        can_generate_reports: true,
        can_access_knowledge: true,
      },
    };
  },

  mounted() {
    this.loadUsers();
  },

  methods: {
    async loadUsers(page = 1) {
      try {
        const response = await axios.g('/api/ai/admin/users', {
          params: {
            page,
            search: this.searchQuery,
          },
        });
        
        if (response.data.success) {
          this.users = response.data.data;
          this.pagination = response.data.pagination;
        }
      } catch (error) {
        console.error('Lỗi khi tải users:', error);
        alert('Không thể tải danh sách users');
      }
    },

    searchUsers() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.loadUsers(1);
      }, 500);
    },

    editUser(user) {
      this.editingUser = user;
      this.editForm = {
        is_enabled: user.is_enabled,
        daily_token_limit: user.daily_limit || 10000,
        monthly_token_limit: user.monthly_limit || 300000,
        can_query_data: user.permissions?.can_query_data ?? true,
        can_generate_reports: user.permissions?.can_generate_reports ?? true,
        can_access_knowledge: user.permissions?.can_access_knowledge ?? true,
      };
      this.showEditModal = true;
    },

    async saveUserPermission() {
      try {
        const response = await axios.p(
          `/api/ai/admin/users/${this.editingUser.id}/permission`,
          this.editForm
        );
        
        if (response.data.success) {
          alert('Đã cập nhật quyền thành công!');
          this.showEditModal = false;
          this.loadUsers(this.pagination.current_page);
        }
      } catch (error) {
        console.error('Lỗi khi lưu:', error);
        alert('Không thể lưu thay đổi');
      }
    },

    async loadStatistics() {
      try {
        const response = await axios.g('/api/ai/admin/statistics');
        if (response.data.success) {
          this.statistics = response.data.data;
        }
      } catch (error) {
        console.error('Lỗi khi tải thống kê:', error);
      }
    },

    async loadKnowledge() {
      try {
        const response = await axios.g('/api/ai/admin/knowledge');
        if (response.data.success) {
          this.knowledgeList = response.data.data.data;
        }
      } catch (error) {
        console.error('Lỗi khi tải knowledge:', error);
      }
    },

    async deleteKnowledge(id) {
      if (!confirm('Bạn có chắc muốn xóa tài liệu này?')) return;
      
      try {
        const response = await axios.d(`/api/ai/admin/knowledge/${id}`);
        if (response.data.success) {
          alert('Đã xóa tài liệu');
          this.loadKnowledge();
        }
      } catch (error) {
        console.error('Lỗi khi xóa:', error);
        alert('Không thể xóa tài liệu');
      }
    },

    showKnowledgeModal() {
      alert('Chức năng thêm tài liệu sẽ được bổ sung sau');
    },

    editKnowledge(doc) {
      alert('Chức năng sửa tài liệu sẽ được bổ sung sau');
    },
  },
};
</script>

<style scoped>
.ai-admin-panel {
  padding: 24px;
  background: #f7f8fc;
  min-height: 100vh;
}

.panel-header {
  margin-bottom: 32px;
}

.panel-header h2 {
  margin: 0 0 8px 0;
  color: #1a202c;
  font-size: 28px;
}

.panel-header p {
  margin: 0;
  color: #718096;
}

.tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  border-bottom: 2px solid #e2e8f0;
}

.tab {
  padding: 12px 24px;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  font-size: 15px;
  font-weight: 500;
  color: #718096;
  transition: all 0.2s;
}

.tab:hover {
  color: #667eea;
}

.tab.active {
  color: #667eea;
  border-bottom-color: #667eea;
}

.tab-content {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Users Tab */
.search-bar {
  margin-bottom: 20px;
}

.search-bar input {
  width: 100%;
  max-width: 400px;
  padding: 10px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
}

.users-table table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th {
  text-align: left;
  padding: 12px;
  background: #f7fafc;
  font-weight: 600;
  color: #4a5568;
  border-bottom: 2px solid #e2e8f0;
}

.users-table td {
  padding: 12px;
  border-bottom: 1px solid #e2e8f0;
}

.badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.badge.success {
  background: #c6f6d5;
  color: #22543d;
}

.badge.danger {
  background: #fed7d7;
  color: #742a2a;
}

.btn-edit {
  background: #edf2f7;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-edit:hover {
  background: #e2e8f0;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 20px;
}

.pagination button {
  padding: 8px 16px;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 6px;
  cursor: pointer;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Statistics Tab */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
}

.stat-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 24px;
  border-radius: 12px;
  text-align: center;
}

.stat-icon {
  font-size: 32px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 13px;
  opacity: 0.9;
}

.top-users {
  margin-top: 32px;
}

.top-users h3 {
  margin-bottom: 16px;
}

.top-users table {
  width: 100%;
  border-collapse: collapse;
}

.top-users th,
.top-users td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

/* Knowledge Tab */
.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  margin-bottom: 20px;
}

.knowledge-list {
  display: grid;
  gap: 16px;
}

.knowledge-item {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
}

.knowledge-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.knowledge-header h4 {
  margin: 0;
}

.category-badge {
  background: #edf2f7;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
}

.knowledge-content {
  color: #718096;
  margin: 8px 0;
}

.knowledge-actions {
  display: flex;
  gap: 8px;
}

.btn-delete {
  background: #fed7d7;
  color: #742a2a;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}

/* Modal */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: 20px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
}

.modal-header button {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #a0aec0;
}

.modal-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #4a5568;
}

.form-group input[type="number"] {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
}

.form-group input[type="checkbox"] {
  margin-right: 8px;
}

.modal-footer {
  padding: 20px;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.btn-secondary {
  background: #edf2f7;
  color: #4a5568;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}
</style>
