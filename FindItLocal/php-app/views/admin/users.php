<!-- Admin - Manage Users -->
<?php
// Admin manage users view
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Manage Users</h1>
        <p>View and manage all user accounts</p>
    </div>
    
    <!-- Filters and Search -->
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search users..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <div class="filter-select">
                <label>Role:</label>
                <select name="role">
                    <option value="all" <?php echo (!isset($_GET['role']) || $_GET['role'] === 'all') ? 'selected' : ''; ?>>
                        All Users
                    </option>
                    <option value="customer" <?php echo (isset($_GET['role']) && $_GET['role'] === 'customer') ? 'selected' : ''; ?>>
                        Customers
                    </option>
                    <option value="business_owner" <?php echo (isset($_GET['role']) && $_GET['role'] === 'business_owner') ? 'selected' : ''; ?>>
                        Business Owners
                    </option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-search">Search</button>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="table-container">
        <?php if (!empty($users)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Businesses</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($user['user_name']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <span class="role-badge <?php echo htmlspecialchars($user['role']); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($user['role']))); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['role'] === 'business_owner'): ?>
                                    <span class="business-count">
                                        <i class="fas fa-store"></i> <?php echo htmlspecialchars($user['business_count']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="date">
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button class="btn-action view" onclick="viewUser(<?php echo htmlspecialchars($user['id']); ?>)" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action message" title="Send Message">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No users found</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['role']) ? '&role=' . urlencode($_GET['role']) : ''; ?>"
                   class="page-link <?php echo $i == (isset($_GET['page']) ? $_GET['page'] : 1) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- User Details Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>User Details</h2>
        <div id="userDetails"></div>
    </div>
</div>

<style>
.admin-container {
    padding: 30px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 10px;
    font-weight: 700;
}

.page-header p {
    color: #7f8c8d;
}

.filter-bar {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filter-form {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.search-box {
    flex: 1;
    min-width: 250px;
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 12px;
    color: #95a5a6;
}

.search-box input {
    width: 100%;
    padding: 10px 12px 10px 36px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.search-box input:focus {
    outline: none;
    border-color: #3498db;
}

.filter-select {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-select label {
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.filter-select select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.btn-search {
    padding: 10px 20px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-search:hover {
    background: #2980b9;
}

.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead {
    background: #f8f9fa;
    border-bottom: 2px solid #ddd;
}

.admin-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.admin-table tr:hover {
    background: #f9f9f9;
}

.role-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.role-badge.customer {
    background: #e3f2fd;
    color: #1976d2;
}

.role-badge.business_owner {
    background: #f3e5f5;
    color: #7b1fa2;
}

.role-badge.admin {
    background: #ffebee;
    color: #d32f2f;
}

.business-count {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #2c3e50;
    font-weight: 500;
}

.text-muted {
    color: #95a5a6;
}

.date {
    color: #7f8c8d;
    font-size: 13px;
}

.actions {
    display: flex;
    gap: 8px;
}

.btn-action {
    width: 32px;
    height: 32px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7f8c8d;
}

.btn-action:hover {
    border-color: #3498db;
    color: #3498db;
    background: #ecf0f1;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #95a5a6;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.pagination {
    display: flex;
    gap: 5px;
    justify-content: center;
    margin-top: 30px;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    color: #3498db;
    text-decoration: none;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #3498db;
    color: white;
}

.page-link.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.close {
    color: #7f8c8d;
    float: right;
    font-size: 28px;
    cursor: pointer;
}

.close:hover {
    color: #2c3e50;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }
    
    .search-box {
        min-width: auto;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 10px;
        font-size: 12px;
    }
}
</style>

<script>
const userModal = document.getElementById('userModal');
const closeBtn = document.querySelector('.close');

function viewUser(userId) {
    // In a real application, this would fetch user details via AJAX
    userModal.style.display = 'block';
    // For now, show a simple message
    document.getElementById('userDetails').innerHTML = '<p>User #' + userId + ' details would be displayed here.</p>';
}

closeBtn.onclick = function() {
    userModal.style.display = 'none';
};

window.onclick = function(event) {
    if (event.target === userModal) {
        userModal.style.display = 'none';
    }
};
</script>
