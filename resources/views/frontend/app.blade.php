@extends('frontend.layouts.app')

@section('title', 'User Management System')

@section('content')
    <!-- Login Form -->
    <div id="loginSection" class="container-fluid">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-users-cog fa-3x text-primary mb-3"></i>
                            <h2 class="card-title">User Management System</h2>
                            <p class="text-muted">Sign in to access your account</p>
                        </div>
                        
                        <form id="loginForm">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <strong>Test Accounts:</strong><br>
                                SuperAdmin: superadmin@example.com / password123<br>
                                Admin: admin@example.com / password123<br>
                                User: user@example.com / password123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard -->
    <div id="dashboardSection" class="container-fluid d-none">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-users-cog me-2"></i>User Management System
                </a>
                
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <span id="currentUserName">User</span>
                            <span class="badge bg-light text-dark ms-1" id="currentUserRole">Role</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="showProfile()">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container-fluid mt-4">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active" onclick="showDashboard()">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="showUsers()" id="usersLink">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="showCreateUser()" id="createUserLink">
                            <i class="fas fa-user-plus me-2"></i>Create User
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="showBulkCreate()" id="bulkCreateLink">
                            <i class="fas fa-users-cog me-2"></i>Bulk Create
                        </a>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-md-9 col-lg-10">
                    <!-- Dashboard -->
                    <div id="dashboardContent">
                        <div class="row">
                            <div class="col-12">
                                <h2 class="mb-4">Dashboard</h2>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">Total Users</h5>
                                                <h3 id="totalUsers">0</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-users fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">Active Users</h5>
                                                <h3 id="activeUsers">0</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-user-check fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">Your Role</h5>
                                                <h3 id="userRoleDisplay">User</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-user-shield fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Recent Activity</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="recentActivity">
                                            <p class="text-muted">No recent activity to display.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users List -->
                    <div id="usersContent" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Users Management</h2>
                            <button class="btn btn-primary" id="addUserButton" onclick="showCreateUser()">
                                <i class="fas fa-plus me-2"></i>Add User
                            </button>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="usersTableBody">
                                            <!-- Users will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Create User Form -->
                    <div id="createUserContent" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Create New User</h2>
                            <button class="btn btn-secondary" onclick="showUsers()">
                                <i class="fas fa-arrow-left me-2"></i>Back to Users
                            </button>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <form id="createUserForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="newUserName" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="newUserName" name="name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="newUserEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="newUserEmail" name="email" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="newUserPassword" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="newUserPassword" name="password" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="newUserRole" class="form-label">Role</label>
                                            <select class="form-select" id="newUserRole" name="role" required>
                                                <option value="">Select Role</option>
                                                <option value="User">User</option>
                                                <option value="Admin">Admin</option>
                                                <option value="SuperAdmin" id="superAdminOption" style="display: none;">SuperAdmin</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="button" class="btn btn-secondary me-2" onclick="showUsers()">Cancel</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Create User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Create Users -->
                    <div id="bulkCreateContent" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Bulk Create Users</h2>
                            <button class="btn btn-secondary" onclick="showUsers()">
                                <i class="fas fa-arrow-left me-2"></i>Back to Users
                            </button>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Bulk Creation:</strong> Add multiple users at once. The system will process them in the background.
                                </div>
                                
                                <form id="bulkCreateForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Users to Create</label>
                                        <div id="bulkUsersContainer">
                                            <div class="row mb-2 bulk-user-row">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" placeholder="Name" name="users[0][name]" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="email" class="form-control" placeholder="Email" name="users[0][email]" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="password" class="form-control" placeholder="Password" name="users[0][password]" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <select class="form-select" name="users[0][role]" required>
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
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary" onclick="addBulkUser()">
                                            <i class="fas fa-plus me-2"></i>Add Another User
                                        </button>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="button" class="btn btn-secondary me-2" onclick="showUsers()">Cancel</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-users-cog me-2"></i>Bulk Create Users
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        @csrf
                        <input type="hidden" id="editUserId">
                        <div class="mb-3">
                            <label for="editUserName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editUserName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editUserEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserPassword" class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="editUserPassword" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="editUserRole" class="form-label">Role</label>
                            <select class="form-select" id="editUserRole" name="role" required>
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                                <option value="SuperAdmin" id="editSuperAdminOption" style="display: none;">SuperAdmin</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateUser()">Update User</button>
                </div>
            </div>
        </div>
    </div>
@endsection 