<!-- Admin - Manage Reviews -->
<?php
// Admin manage reviews view
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Manage Reviews</h1>
        <p>Moderate and manage customer reviews</p>
    </div>
    
    <!-- Filter -->
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="filter-select">
                <label>Status:</label>
                <select name="status">
                    <option value="all" <?php echo (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'selected' : ''; ?>>
                        All Reviews
                    </option>
                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : ''; ?>>
                        Pending Approval
                    </option>
                    <option value="approved" <?php echo (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'selected' : ''; ?>>
                        Approved
                    </option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-search">Filter</button>
        </form>
    </div>
    
    <!-- Reviews List -->
    <div class="reviews-container">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <h3><?php echo htmlspecialchars($review['user_name'] ?? 'Anonymous'); ?></h3>
                            <p class="business-info">
                                Reviewed: <strong><?php echo htmlspecialchars($review['business_name'] ?? 'Unknown'); ?></strong>
                                <?php if ($review['service_name']): ?>
                                    for <strong><?php echo htmlspecialchars($review['service_name']); ?></strong>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="review-meta">
                            <span class="rating">
                                <i class="fas fa-star"></i>
                                <?php echo htmlspecialchars($review['rating']); ?>/5
                            </span>
                            <span class="date">
                                <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                            </span>
                            <?php if (!$review['is_approved']): ?>
                                <span class="badge pending">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            <?php else: ?>
                                <span class="badge approved">
                                    <i class="fas fa-check"></i> Approved
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="review-text">
                        <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                    </div>
                    
                    <div class="review-actions">
                        <?php if (!$review['is_approved']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="approve_review">
                                <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['id']); ?>">
                                <button type="submit" class="btn btn-approve">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this review?');">
                            <input type="hidden" name="action" value="delete_review">
                            <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['id']); ?>">
                            <button type="submit" class="btn btn-delete">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No reviews found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.admin-container {
    padding: 30px 20px;
    max-width: 900px;
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
    align-items: flex-end;
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
}

.btn-search {
    padding: 8px 16px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
}

.btn-search:hover {
    background: #2980b9;
}

.reviews-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.review-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #3498db;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    gap: 15px;
}

.reviewer-info h3 {
    margin: 0 0 5px 0;
    color: #2c3e50;
    font-size: 16px;
}

.business-info {
    margin: 0;
    color: #7f8c8d;
    font-size: 13px;
}

.review-meta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: flex-end;
    align-items: center;
}

.rating {
    color: #f39c12;
    font-weight: 600;
    font-size: 14px;
}

.date {
    color: #7f8c8d;
    font-size: 12px;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge.pending {
    background: #fff3cd;
    color: #856404;
}

.badge.approved {
    background: #d4edda;
    color: #155724;
}

.review-text {
    margin-bottom: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
}

.review-text p {
    margin: 0;
    color: #2c3e50;
    line-height: 1.6;
}

.review-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 8px 14px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-approve {
    background: #2ecc71;
    color: white;
}

.btn-approve:hover {
    background: #27ae60;
}

.btn-delete {
    background: #e74c3c;
    color: white;
}

.btn-delete:hover {
    background: #c0392b;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 8px;
    color: #95a5a6;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .review-header {
        flex-direction: column;
    }
    
    .review-meta {
        justify-content: flex-start;
    }
}
</style>
