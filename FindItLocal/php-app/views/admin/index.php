<!-- Admin Dashboard -->
<?php
// Admin dashboard view
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')); ?></p>
    </div>
    
    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon businesses">
                <i class="fas fa-store"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($data['total_businesses']); ?></h3>
                <p>Total Businesses</p>
            </div>
            <a href="/admin/businesses" class="stat-link">View All</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($data['total_users']); ?></h3>
                <p>Total Users</p>
            </div>
            <a href="/admin/users" class="stat-link">View All</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon reviews">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($data['total_reviews']); ?></h3>
                <p>Total Reviews</p>
            </div>
            <a href="/admin/reviews" class="stat-link">View All</a>
        </div>
        
        <div class="stat-card alert">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($data['pending_bookings']); ?></h3>
                <p>Pending Bookings</p>
            </div>
            <a href="/admin/bookings" class="stat-link">View All</a>
        </div>
        
        <div class="stat-card alert">
            <div class="stat-icon unverified">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($data['unverified_businesses']); ?></h3>
                <p>Unverified Businesses</p>
            </div>
            <a href="/admin/businesses?status=unverified" class="stat-link">Verify</a>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="activity-section">
        <div class="activity-card">
            <div class="card-header">
                <h3>Recent Reviews</h3>
                <a href="/admin/reviews" class="view-all">View All →</a>
            </div>
            <div class="activity-list">
                <?php if (!empty($data['recent_reviews'])): ?>
                    <?php foreach ($data['recent_reviews'] as $review): ?>
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="activity-details">
                                <p class="activity-title">
                                    <strong><?php echo htmlspecialchars($review['user_name'] ?? 'Anonymous'); ?></strong>
                                    reviewed 
                                    <strong><?php echo htmlspecialchars($review['business_name'] ?? 'Unknown'); ?></strong>
                                </p>
                                <p class="activity-text"><?php echo htmlspecialchars(substr($review['review_text'], 0, 100)); ?>...</p>
                                <div class="activity-meta">
                                    <span class="rating">
                                        <i class="fas fa-star"></i> <?php echo htmlspecialchars($review['rating']); ?>/5
                                    </span>
                                    <span class="timestamp">
                                        <i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">No reviews yet</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="activity-card">
            <div class="card-header">
                <h3>Recent Bookings</h3>
                <a href="/admin/bookings" class="view-all">View All →</a>
            </div>
            <div class="activity-list">
                <?php if (!empty($data['recent_bookings'])): ?>
                    <?php foreach ($data['recent_bookings'] as $booking): ?>
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="activity-details">
                                <p class="activity-title">
                                    <strong><?php echo htmlspecialchars($booking['user_name'] ?? 'Anonymous'); ?></strong>
                                    booked 
                                    <strong><?php echo htmlspecialchars($booking['service_name'] ?? 'Unknown'); ?></strong>
                                </p>
                                <p class="activity-text">
                                    At <strong><?php echo htmlspecialchars($booking['business_name'] ?? 'Unknown Business'); ?></strong>
                                </p>
                                <div class="activity-meta">
                                    <span class="status <?php echo htmlspecialchars($booking['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                    </span>
                                    <span class="timestamp">
                                        <i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">No bookings yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="action-buttons">
            <a href="/admin/businesses?status=unverified" class="action-btn">
                <i class="fas fa-check-circle"></i> Verify Businesses
            </a>
            <a href="/admin/bookings?status=pending" class="action-btn">
                <i class="fas fa-handshake"></i> Confirm Bookings
            </a>
            <a href="/admin/reviews?status=pending" class="action-btn">
                <i class="fas fa-eye"></i> Review Pending Reviews
            </a>
            <a href="/admin/users" class="action-btn">
                <i class="fas fa-user-plus"></i> Manage Users
            </a>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    padding: 30px 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    margin-bottom: 40px;
}

.dashboard-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin-bottom: 10px;
    font-weight: 700;
}

.dashboard-header p {
    color: #7f8c8d;
    font-size: 16px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    gap: 15px;
    align-items: flex-start;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.stat-card.alert {
    border-left: 4px solid #e74c3c;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    flex-shrink: 0;
}

.stat-icon.businesses {
    background: #3498db;
}

.stat-icon.users {
    background: #2ecc71;
}

.stat-icon.reviews {
    background: #9b59b6;
}

.stat-icon.pending {
    background: #e67e22;
}

.stat-icon.unverified {
    background: #e74c3c;
}

.stat-content {
    flex: 1;
}

.stat-content h3 {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.stat-content p {
    color: #7f8c8d;
    font-size: 13px;
    margin: 0;
}

.stat-link {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #3498db;
    font-size: 12px;
    text-decoration: none;
    font-weight: 600;
    opacity: 0;
    transition: all 0.3s ease;
}

.stat-card:hover .stat-link {
    opacity: 1;
}

/* Activity Section */
.activity-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.activity-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
}

.view-all {
    color: #3498db;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.view-all:hover {
    color: #2980b9;
}

.activity-list {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f5f5f5;
    display: flex;
    gap: 12px;
    transition: background 0.2s ease;
}

.activity-item:hover {
    background: #f9f9f9;
}

.activity-avatar {
    font-size: 32px;
    color: #3498db;
    flex-shrink: 0;
}

.activity-details {
    flex: 1;
    min-width: 0;
}

.activity-title {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #2c3e50;
}

.activity-text {
    margin: 0 0 8px 0;
    font-size: 12px;
    color: #7f8c8d;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    overflow: hidden;
}

.activity-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 11px;
    color: #95a5a6;
}

.rating {
    color: #3498db;
    font-weight: 600;
}

.timestamp {
    display: flex;
    align-items: center;
    gap: 4px;
}

.status {
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 11px;
}

.status.pending {
    background: #e3f2fd;
    color: #1976d2;
}

.status.confirmed {
    background: #d4edda;
    color: #155724;
}

.status.completed {
    background: #d1ecf1;
    color: #0c5460;
}

.status.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.empty-state {
    padding: 40px 20px;
    text-align: center;
    color: #95a5a6;
}

/* Quick Actions */
.quick-actions {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.quick-actions h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #2c3e50;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-btn {
    padding: 15px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: center;
    justify-content: center;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

@media (max-width: 768px) {
    .admin-dashboard {
        padding: 15px 10px;
    }
    
    .dashboard-header h1 {
        font-size: 24px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .activity-section {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        grid-template-columns: 1fr;
    }
}
</style>
