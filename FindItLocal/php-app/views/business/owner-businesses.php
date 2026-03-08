<!-- Owner Businesses Management -->
<div class="container">
    <div class="page-header">
        <h1>My Businesses</h1>
        <p>Manage and view all your businesses</p>
    </div>

    <?php if (isset($message) && $message): ?>
        <div class="alert alert-success">
            <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger">
            <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="businesses-manager">
        <!-- Add Business Button -->
        <div class="manager-header">
            <a href="/FindItLocal/php-app/business/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Business
            </a>
        </div>

        <!-- Businesses Grid -->
        <?php if (!empty($businesses)): ?>
            <div class="owner-businesses-grid">
                <?php foreach ($businesses as $business): ?>
                    <div class="owner-business-card">
                        <!-- Business Logo -->
                        <div class="business-logo-section">
                            <?php if (!empty($business['business_logo'])): ?>
                                <img src="/FindItLocal/php-app/uploads/business_logos/<?php echo htmlspecialchars($business['business_logo']); ?>" 
                                     alt="<?php echo htmlspecialchars($business['business_name']); ?>">
                            <?php else: ?>
                                <div class="logo-placeholder">
                                    <i class="fas fa-store"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Business Info -->
                        <div class="business-info">
                            <h3><?php echo htmlspecialchars($business['business_name']); ?></h3>
                            <p class="business-location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($business['location']); ?>
                            </p>
                            <p class="business-description">
                                <?php echo htmlspecialchars(substr($business['description'], 0, 100)); ?>...
                            </p>

                            <!-- Stats -->
                            <div class="business-stats">
                                <div class="stat">
                                    <span class="stat-label">Services</span>
                                    <span class="stat-value"><?php echo isset($business['service_count']) ? $business['service_count'] : 0; ?></span>
                                </div>
                                <div class="stat">
                                    <span class="stat-label">Rating</span>
                                    <span class="stat-value">
                                        <?php if ($business['rating']): ?>
                                            <i class="fas fa-star"></i> <?php echo number_format($business['rating'], 1); ?>
                                        <?php else: ?>
                                            No ratings
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="stat">
                                    <span class="stat-label">Reviews</span>
                                    <span class="stat-value"><?php echo $business['review_count'] ?? 0; ?></span>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="status-section">
                                <?php if ($business['is_verified']): ?>
                                    <span class="badge badge-verified">
                                        <i class="fas fa-check-circle"></i> Verified
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-unverified">
                                        <i class="fas fa-clock"></i> Pending Verification
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!$business['is_active']): ?>
                                    <span class="badge badge-inactive">
                                        <i class="fas fa-ban"></i> Inactive
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="business-actions">
                            <a href="/FindItLocal/php-app/business/detail/<?php echo $business['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="/FindItLocal/php-app/business/edit/<?php echo $business['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="/FindItLocal/php-app/owner/bookings/<?php echo $business['id']; ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-calendar-check"></i> Bookings
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-businesses">
                <div class="empty-state">
                    <i class="fas fa-store"></i>
                    <h3>No Businesses Yet</h3>
                    <p>You haven't created any businesses yet. Start by creating your first business!</p>
                    <a href="/FindItLocal/php-app/business/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Business
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.page-header {
    margin-bottom: 40px;
    text-align: center;
}

.page-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin-bottom: 10px;
    font-weight: 700;
}

.page-header p {
    color: #7f8c8d;
    font-size: 16px;
}

.businesses-manager {
    max-width: 1200px;
    margin: 0 auto;
}

.manager-header {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 30px;
}

.alert {
    padding: 15px 20px;
    border-radius: 4px;
    margin-bottom: 20px;
    border-left: 4px solid;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-color: #28a745;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-color: #dc3545;
}

.alert-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    line-height: 1;
}

/* Owner Businesses Grid */
.owner-businesses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
}

.owner-business-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.owner-business-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

/* Business Logo Section */
.business-logo-section {
    width: 100%;
    height: 200px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.business-logo-section img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.logo-placeholder {
    font-size: 60px;
    color: #bdc3c7;
}

/* Business Info */
.business-info {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.business-info h3 {
    font-size: 18px;
    color: #2c3e50;
    margin: 0 0 10px 0;
    font-weight: 600;
}

.business-location {
    color: #7f8c8d;
    font-size: 13px;
    margin: 0 0 10px 0;
}

.business-location i {
    margin-right: 5px;
}

.business-description {
    color: #555;
    font-size: 14px;
    line-height: 1.4;
    margin: 0 0 15px 0;
    flex: 1;
}

/* Business Stats */
.business-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    padding: 12px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
}

.stat {
    text-align: center;
    padding: 8px 0;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.stat-value {
    display: block;
    font-size: 16px;
    color: #2c3e50;
    font-weight: 600;
    margin-top: 4px;
}

.stat-value i {
    color: #f39c12;
}

/* Status Section */
.status-section {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 500;
}

.badge-verified {
    background: #d4edda;
    color: #155724;
}

.badge-unverified {
    background: #fff3cd;
    color: #856404;
}

.badge-inactive {
    background: #f8d7da;
    color: #721c24;
}

/* Business Actions */
.business-actions {
    display: flex;
    gap: 8px;
    padding: 15px 20px;
    border-top: 1px solid #eee;
}

.btn {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    justify-content: center;
    flex: 1;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 11px;
}

.btn-primary {
    background-color: #3498db;
    color: white;
}

.btn-primary:hover {
    background-color: #2980b9;
}

.btn-success {
    background-color: #27ae60;
    color: white;
}

.btn-success:hover {
    background-color: #229954;
}

.btn-info {
    background-color: #16a085;
    color: white;
}

.btn-info:hover {
    background-color: #138d75;
}

/* No Businesses */
.no-businesses {
    display: flex;
    justify-content: center;
    padding: 60px 20px;
}

.empty-state {
    text-align: center;
    max-width: 500px;
}

.empty-state i {
    font-size: 80px;
    color: #bdc3c7;
    margin-bottom: 20px;
    display: block;
}

.empty-state h3 {
    font-size: 24px;
    color: #2c3e50;
    margin: 0 0 10px 0;
}

.empty-state p {
    color: #7f8c8d;
    margin: 0 0 20px 0;
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .owner-businesses-grid {
        grid-template-columns: 1fr;
    }

    .business-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }

    .business-stats {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>
