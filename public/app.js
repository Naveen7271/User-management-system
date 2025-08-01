// User Management System - Frontend JavaScript

// Global variables
let currentUser = null;
let accessToken = localStorage.getItem('access_token');
const API_BASE_URL = '/api';

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Check if user is already logged in
    if (accessToken) {
        validateToken();
    } else {
        showLoginSection();
    }

    // Setup event listeners
    setupEventListeners();
}

function setupEventListeners() {
    // Login form
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    
    // Create user form
    document.getElementById('createUserForm').addEventListener('submit', handleCreateUser);
    
    // Bulk create form
    document.getElementById('bulkCreateForm').addEventListener('submit', handleBulkCreate);
}

// Authentication Functions
async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    showLoading();
    
    try {
        const response = await fetch(`${API_BASE_URL}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Store token and user data
            accessToken = data.access_token;
            currentUser = data.user;
            localStorage.setItem('access_token', accessToken);
            localStorage.setItem('user_data', JSON.stringify(currentUser));
            
            showToast('Success', 'Login successful!', 'success');
            showDashboard();
            loadDashboardData();
        } else {
            showToast('Error', data.message || 'Login failed', 'error');
        }
    } catch (error) {
        console.error('Login error:', error);
        showToast('Error', 'Network error. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

async function validateToken() {
    try {
        const response = await fetch(`${API_BASE_URL}/user`, {
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            currentUser = await response.json();
            localStorage.setItem('user_data', JSON.stringify(currentUser));
            showDashboard();
            loadDashboardData();
        } else {
            // Token is invalid
            logout();
        }
    } catch (error) {
        console.error('Token validation error:', error);
        logout();
    }
}

async function logout() {
    try {
        if (accessToken) {
            await fetch(`${API_BASE_URL}/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${accessToken}`,
                    'Accept': 'application/json',
                }
            });
        }
    } catch (error) {
        console.error('Logout error:', error);
    } finally {
        // Clear local storage and show login
        localStorage.removeItem('access_token');
        localStorage.removeItem('user_data');
        accessToken = null;
        currentUser = null;
        showLoginSection();
        showToast('Info', 'Logged out successfully', 'info');
    }
}

// UI Navigation Functions
function showLoginSection() {
    document.getElementById('loginSection').classList.remove('d-none');
    document.getElementById('dashboardSection').classList.add('d-none');
}

function showDashboard() {
    document.getElementById('loginSection').classList.add('d-none');
    document.getElementById('dashboardSection').classList.remove('d-none');
    
    // Update user info in navbar
    document.getElementById('currentUserName').textContent = currentUser.name;
    document.getElementById('currentUserRole').textContent = currentUser.role;
    
    // Setup role-based navigation
    setupRoleBasedNavigation();
    
    // Show dashboard content
    showDashboardContent();
}

function setupRoleBasedNavigation() {
    const isSuperAdmin = currentUser.role === 'SuperAdmin';
    const isAdmin = currentUser.role === 'Admin';
    
    // Show/hide navigation items based on role
    document.getElementById('usersLink').style.display = 'block';
    document.getElementById('createUserLink').style.display = (isSuperAdmin || isAdmin) ? 'block' : 'none';
    document.getElementById('bulkCreateLink').style.display = (isSuperAdmin || isAdmin) ? 'block' : 'none';
    
    // Show/hide SuperAdmin options in forms
    const superAdminOptions = document.querySelectorAll('#superAdminOption, .super-admin-option, #editSuperAdminOption');
    superAdminOptions.forEach(option => {
        option.style.display = isSuperAdmin ? 'block' : 'none';
    });
}

function showDashboardContent() {
    hideAllContent();
    document.getElementById('dashboardContent').classList.remove('d-none');
    updateActiveNavItem('dashboard');
}

function showUsers() {
    hideAllContent();
    document.getElementById('usersContent').classList.remove('d-none');
    updateActiveNavItem('users');
    loadUsers();
}

function showCreateUser() {
    hideAllContent();
    document.getElementById('createUserContent').classList.remove('d-none');
    updateActiveNavItem('createUser');
}

function showBulkCreate() {
    hideAllContent();
    document.getElementById('bulkCreateContent').classList.remove('d-none');
    updateActiveNavItem('bulkCreate');
}

function hideAllContent() {
    const contents = ['dashboardContent', 'usersContent', 'createUserContent', 'bulkCreateContent'];
    contents.forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
}

function updateActiveNavItem(activeItem) {
    // Remove active class from all nav items
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to current item
    const navItems = {
        'dashboard': document.querySelector('.list-group-item[onclick="showDashboard()"]'),
        'users': document.getElementById('usersLink'),
        'createUser': document.getElementById('createUserLink'),
        'bulkCreate': document.getElementById('bulkCreateLink')
    };
    
    if (navItems[activeItem]) {
        navItems[activeItem].classList.add('active');
    }
}

// Dashboard Functions
async function loadDashboardData() {
    try {
        const users = await fetchUsers();
        
        // Update dashboard stats
        document.getElementById('totalUsers').textContent = users.length;
        document.getElementById('activeUsers').textContent = users.filter(user => !user.deleted_at).length;
        document.getElementById('userRoleDisplay').textContent = currentUser.role;
        
        // Update recent activity
        updateRecentActivity(users);
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function updateRecentActivity(users) {
    const recentActivity = document.getElementById('recentActivity');
    const recentUsers = users.slice(0, 5); // Show last 5 users
    
    if (recentUsers.length === 0) {
        recentActivity.innerHTML = '<p class="text-muted">No recent activity to display.</p>';
        return;
    }
    
    let activityHTML = '';
    recentUsers.forEach(user => {
        const status = user.deleted_at ? 'Deleted' : 'Active';
        const statusClass = user.deleted_at ? 'status-deleted' : 'status-active';
        const date = new Date(user.created_at).toLocaleDateString();
        
        activityHTML += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong>${user.name}</strong> (${user.email})
                    <br><small class="text-muted">Role: ${user.role}</small>
                </div>
                <div class="text-end">
                    <span class="badge ${statusClass}">${status}</span>
                    <br><small class="text-muted">${date}</small>
                </div>
            </div>
        `;
    });
    
    recentActivity.innerHTML = activityHTML;
}

// User Management Functions
async function fetchUsers() {
    const response = await fetch(`${API_BASE_URL}/users`, {
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Accept': 'application/json',
        }
    });
    
    if (!response.ok) {
        throw new Error('Failed to fetch users');
    }
    
    return await response.json();
}

async function loadUsers() {
    showLoading();
    
    try {
        const users = await fetchUsers();
        displayUsers(users);
    } catch (error) {
        console.error('Error loading users:', error);
        showToast('Error', 'Failed to load users', 'error');
    } finally {
        hideLoading();
    }
}

function displayUsers(users) {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '';
    
    users.forEach(user => {
        const row = document.createElement('tr');
        const status = user.deleted_at ? 'Deleted' : 'Active';
        const statusClass = user.deleted_at ? 'status-deleted' : 'status-active';
        const date = new Date(user.created_at).toLocaleDateString();
        
        row.innerHTML = `
            <td>${user.id}</td>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td><span class="badge role-${user.role.toLowerCase()}">${user.role}</span></td>
            <td><span class="${statusClass}">${status}</span></td>
            <td>${date}</td>
            <td>
                ${getActionButtons(user)}
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function getActionButtons(user) {
    const isOwnAccount = user.id === currentUser.id;
    const canEdit = currentUser.role === 'SuperAdmin' || 
                   (currentUser.role === 'Admin' && user.role === 'User') ||
                   isOwnAccount;
    const canDelete = currentUser.role === 'SuperAdmin' && !isOwnAccount;
    
    let buttons = '';
    
    if (canEdit) {
        buttons += `<button class="btn btn-sm btn-primary me-1" onclick="editUser(${user.id})">
            <i class="fas fa-edit"></i>
        </button>`;
    }
    
    if (canDelete) {
        buttons += `<button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
            <i class="fas fa-trash"></i>
        </button>`;
    }
    
    return buttons || '<span class="text-muted">No actions</span>';
}

// Create User Functions
async function handleCreateUser(e) {
    e.preventDefault();
    
    const userData = {
        users: [{
            name: document.getElementById('newUserName').value,
            email: document.getElementById('newUserEmail').value,
            password: document.getElementById('newUserPassword').value,
            role: document.getElementById('newUserRole').value
        }]
    };
    
    showLoading();
    
    try {
        const response = await fetch(`${API_BASE_URL}/users`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(userData)
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showToast('Success', 'User created successfully!', 'success');
            document.getElementById('createUserForm').reset();
            showUsers();
        } else {
            showToast('Error', data.message || 'Failed to create user', 'error');
        }
    } catch (error) {
        console.error('Create user error:', error);
        showToast('Error', 'Network error. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

// Bulk Create Functions
async function handleBulkCreate(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const users = [];
    let index = 0;
    
    while (formData.has(`users[${index}][name]`)) {
        users.push({
            name: formData.get(`users[${index}][name]`),
            email: formData.get(`users[${index}][email]`),
            password: formData.get(`users[${index}][password]`),
            role: formData.get(`users[${index}][role]`)
        });
        index++;
    }
    
    showLoading();
    
    try {
        const response = await fetch(`${API_BASE_URL}/users`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ users })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showToast('Success', `Bulk creation initiated for ${users.length} users!`, 'success');
            document.getElementById('bulkCreateForm').reset();
            showUsers();
        } else {
            showToast('Error', data.message || 'Failed to create users', 'error');
        }
    } catch (error) {
        console.error('Bulk create error:', error);
        showToast('Error', 'Network error. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

function addBulkUser() {
    const container = document.getElementById('bulkUsersContainer');
    const userCount = container.children.length;
    
    const newRow = document.createElement('div');
    newRow.className = 'row mb-2 bulk-user-row';
    newRow.innerHTML = `
        <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Name" name="users[${userCount}][name]" required>
        </div>
        <div class="col-md-3">
            <input type="email" class="form-control" placeholder="Email" name="users[${userCount}][email]" required>
        </div>
        <div class="col-md-2">
            <input type="password" class="form-control" placeholder="Password" name="users[${userCount}][password]" required>
        </div>
        <div class="col-md-2">
            <select class="form-select" name="users[${userCount}][role]" required>
                <option value="">Role</option>
                <option value="User">User</option>
                <option value="Admin">Admin</option>
                <option value="SuperAdmin" class="super-admin-option" style="display: none;">SuperAdmin</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeBulkUser(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newRow);
}

function removeBulkUser(button) {
    const container = document.getElementById('bulkUsersContainer');
    if (container.children.length > 1) {
        button.closest('.bulk-user-row').remove();
    }
}

// Edit User Functions
async function editUser(userId) {
    try {
        const users = await fetchUsers();
        const user = users.find(u => u.id === userId);
        
        if (!user) {
            showToast('Error', 'User not found', 'error');
            return;
        }
        
        // Populate modal
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editUserName').value = user.name;
        document.getElementById('editUserEmail').value = user.email;
        document.getElementById('editUserPassword').value = '';
        document.getElementById('editUserRole').value = user.role;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
        
    } catch (error) {
        console.error('Error loading user for edit:', error);
        showToast('Error', 'Failed to load user data', 'error');
    }
}

async function updateUser() {
    const userId = document.getElementById('editUserId').value;
    const userData = {
        name: document.getElementById('editUserName').value,
        email: document.getElementById('editUserEmail').value,
        role: document.getElementById('editUserRole').value
    };
    
    const password = document.getElementById('editUserPassword').value;
    if (password) {
        userData.password = password;
    }
    
    showLoading();
    
    try {
        const response = await fetch(`${API_BASE_URL}/users/${userId}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(userData)
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showToast('Success', 'User updated successfully!', 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            modal.hide();
            
            // Refresh users list
            loadUsers();
        } else {
            showToast('Error', data.message || 'Failed to update user', 'error');
        }
    } catch (error) {
        console.error('Update user error:', error);
        showToast('Error', 'Network error. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

// Delete User Functions
async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    showLoading();
    
    try {
        const response = await fetch(`${API_BASE_URL}/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showToast('Success', 'User deleted successfully!', 'success');
            loadUsers();
        } else {
            showToast('Error', data.message || 'Failed to delete user', 'error');
        }
    } catch (error) {
        console.error('Delete user error:', error);
        showToast('Error', 'Network error. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

// Utility Functions
function showLoading() {
    document.getElementById('loadingSpinner').classList.remove('d-none');
}

function hideLoading() {
    document.getElementById('loadingSpinner').classList.add('d-none');
}

function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    const toastTitle = document.getElementById('toastTitle');
    const toastBody = document.getElementById('toastBody');
    
    // Update toast content
    toastTitle.textContent = title;
    toastBody.textContent = message;
    
    // Update toast styling based on type
    const toastHeader = toast.querySelector('.toast-header');
    const icon = toast.querySelector('.toast-header i');
    
    toastHeader.className = 'toast-header';
    icon.className = 'fas me-2';
    
    switch (type) {
        case 'success':
            toastHeader.classList.add('bg-success', 'text-white');
            icon.classList.add('fa-check-circle');
            break;
        case 'error':
            toastHeader.classList.add('bg-danger', 'text-white');
            icon.classList.add('fa-exclamation-circle');
            break;
        case 'warning':
            toastHeader.classList.add('bg-warning', 'text-dark');
            icon.classList.add('fa-exclamation-triangle');
            break;
        default:
            toastHeader.classList.add('bg-primary', 'text-white');
            icon.classList.add('fa-info-circle');
    }
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

// Global functions for onclick handlers
window.logout = logout;
window.showDashboard = showDashboard;
window.showUsers = showUsers;
window.showCreateUser = showCreateUser;
window.showBulkCreate = showBulkCreate;
window.addBulkUser = addBulkUser;
window.removeBulkUser = removeBulkUser;
window.editUser = editUser;
window.updateUser = updateUser;
window.deleteUser = deleteUser;
window.showProfile = function() {
    showToast('Info', 'Profile feature coming soon!', 'info');
}; 