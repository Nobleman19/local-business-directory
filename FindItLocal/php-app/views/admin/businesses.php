<!-- Admin - Manage Businesses -->
<?php
// Admin manage businesses view
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Manage Businesses</h1>
        <p>View, verify, and manage all businesses on the platform</p>
    </div>
    
    <!-- Filters and Search -->
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search businesses, owners..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <div class="filter-select">
                <label>Status:</label>
                <select name="status">
                    <option value="all" <?php echo (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'selected' : ''; ?>>
                        All Businesses
                    </option>
                    <option value="verified" <?php echo (isset($_GET['status']) && $_GET['status'] === 'verified') ? 'selected' : ''; ?>>
                        Verified
                    </option>
                    <option value="unverified" <?php echo (isset($_GET['status']) && $_GET['status'] === 'unverified') ? 'selected' : ''; ?>>
                        Unverified
                    </option>
                    <option value="inactive" <?php echo (isset($_GET['status']) && $_GET['status'] === 'inactive') ? 'selected' : ''; ?>>
                        Inactive
                    </option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-search">Search</button>
        </form>
    </div>
    
    <!-- Businesses Table -->
    <div class="table-container">
        <?php if (!empty($businesses)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Business Name</th>
                        <th>Owner</th>
                        <th>Location</th>
                        <th>Rating</th>
                        <th>Reviews</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($businesses as $business): ?>
                        <tr>
                            <td class="business-name">
                                <strong><?php echo htmlspecialchars($business['business_name']); ?></strong>
                            </td>
                            <td class="owner-name">
                                <?php echo htmlspecialchars($business['user_name']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($business['location']); ?>
                            </td>
                            <td>
                                <?php if ($business['average_rating']): ?>
                                    <span class="rating">
                                        <i class="fas fa-star"></i>
                                        <?php echo number_format($business['average_rating'], 1); ?>/5
                                    </span>
                                <?php else: ?>
                                    <span class="no-rating">No ratings</span>
                                <?php endif; ?>
                            </td>
                            <td class="review-count">
                                <?php echo number_format($business['review_count'] ?? 0); ?>
                            </td>
                            <td>
                                <div class="status-badges">
                                    <?php if ($business['is_verified']): ?>
                                        <span class="badge verified">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    <?php else: ?>
                                        <span class="badge unverified">
                                            <i class="fas fa-exclamation-circle"></i> Unverified
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!$business['is_active']): ?>
                                        <span class="badge inactive">
                                            <i class="fas fa-ban"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="actions">
                                <a href="/business/detail/<?php echo htmlspecialchars($business['id']); ?>" 
                                   class="btn-action view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-action edit" onclick="editBusiness(<?php echo htmlspecialchars($business['id']); ?>)" 
                                        title="Edit Status">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action delete" onclick="deleteBusiness(<?php echo htmlspecialchars($business['id']); ?>)" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No businesses found</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?>"
                   class="page-link <?php echo $i == (isset($_GET['page']) ? $_GET['page'] : 1) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Edit Status Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Update Business Status</h2>
        
        <form method="POST" class="status-form">
            <input type="hidden" name="business_id" id="businessId">
            <input type="hidden" name="action" value="update_status">
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_verified" id="isVerified">
                    Verify This Business
                </label>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" id="isActive" checked>
                    Keep Business Active
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Status</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
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
    font-size: 14px;
}

/* Filter Bar */
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
    background: white;
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

/* Table */
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
    font-size: 14px;
}

.admin-table tr:hover {
    background: #f9f9f9;
}

.business-name {
    font-weight: 600;
    color: #2c3e50;
}

.owner-name {
    color: #7f8c8d;
}

.rating {
    color: #f39c12;
    font-weight: 600;
}

.no-rating {
    color: #95a5a6;
    font-style: italic;
}

.status-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge.verified {
    background: #d4edda;
    color: #155724;
}

.badge.unverified {
    background: #fff3cd;
    color: #856404;
}

.badge.inactive {
    background: #f8d7da;
    color: #721c24;
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

.btn-action.delete:hover {
    border-color: #e74c3c;
    color: #e74c3c;
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

.empty-state p {
    font-size: 16px;
    margin: 0;
}

/* Pagination */
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
    max-width: 400px;
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

.status-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: 500;
    color: #2c3e50;
}

.form-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }
    
    .search-box {
        min-width: auto;
    }
    
    .admin-table {
        font-size: 12px;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 10px;
    }
}
</style>

<script>
const modal = document.getElementById('editModal');
const closeBtn = document.querySelector('.close');

function editBusiness(businessId) {
    document.getElementById('businessId').value = businessId;
    modal.style.display = 'block';
}

function closeModal() {
    modal.style.display = 'none';
}

function deleteBusiness(businessId) {
    if (confirm('Are you sure you want to delete this business? This action cannot be undone.')) {
        // Submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="business_id" value="${businessId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

closeBtn.onclick = closeModal;
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};
</script>
