<div class="container">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($userData['first_name']); ?>!</h1>
        <p>This is your personal dashboard</p>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <div class="sidebar-menu">
                <ul>
                    <li><a href="/FindItLocal/php-app/dashboard" class="active">Dashboard</a></li>
                    <li><a href="/FindItLocal/php-app/profile">Profile</a></li>
                    <li><a href="/FindItLocal/php-app/bookings">My Bookings</a></li>
                    <?php if ($userRole === 'business_owner'): ?>
                        <li><a href="/FindItLocal/php-app/my-businesses">My Businesses</a></li>
                        <li><a href="/FindItLocal/php-app/business/create">Create Business</a></li>
                    <?php endif; ?>
                    <li><a href="/FindItLocal/php-app/logout" class="logout-link">Logout</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <!-- User Info Cards -->
            <section class="dashboard-section">
                <h2>Account Summary</h2>
                <div class="info-cards">
                    <div class="info-card">
                        <div class="card-icon">👤</div>
                        <div class="card-content">
                            <h3>Email</h3>
                            <p><?php echo htmlspecialchars($userData['email']); ?></p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="card-icon">📞</div>
                        <div class="card-content">
                            <h3>Phone</h3>
                            <p><?php echo htmlspecialchars($userData['phone_number']); ?></p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="card-icon">📍</div>
                        <div class="card-content">
                            <h3>Location</h3>
                            <p><?php echo htmlspecialchars($userData['city'] . ', ' . $userData['state']); ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Bookings -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2>Recent Bookings</h2>
                    <a href="/FindItLocal/php-app/bookings" class="btn btn-sm btn-primary">View All</a>
                </div>

                <?php if (!empty($bookings)): ?>
                    <div class="bookings-grid">
                        <?php foreach (array_slice($bookings, 0, 5) as $booking): ?>
                            <div class="booking-card">
                                <div class="booking-header">
                                    <h3><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                                    <span class="badge badge-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </div>
                                <p class="booking-business"><strong><?php echo htmlspecialchars($booking['business_name']); ?></strong></p>
                                <p class="booking-date">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo date('M d, Y', strtotime($booking['scheduled_date'])); ?>
                                </p>
                                <p class="booking-amount">
                                    <strong><?php echo Helper::formatCurrency($booking['total_amount']); ?></strong>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <p>No bookings yet. <a href="/FindItLocal/php-app/businesses">Browse businesses</a> to make your first booking!</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Business Owner Section -->
            <?php if ($userRole === 'business_owner'): ?>
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2>Your Businesses</h2>
                        <a href="/FindItLocal/php-app/business/create" class="btn btn-sm btn-success">Add New Business</a>
                    </div>

                    <?php if (!empty($ownedBusinesses)): ?>
                        <div class="businesses-grid">
                            <?php foreach ($ownedBusinesses as $business): ?>
                            <div class="business-card">
                                <div class="business-card-image">
                                    <?php if (!empty($business['business_logo'])): ?>
                                        <img src="/FindItLocal/php-app/uploads/business_logos/<?php echo htmlspecialchars($business['business_logo']); ?>" alt="<?php echo htmlspecialchars($business['business_name']); ?>">
                                    <?php else: ?>
                                        <div class="business-card-placeholder">
                                            <i class="fas fa-store"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="business-card-content">
                                    <h3><?php echo htmlspecialchars($business['business_name']); ?></h3>
                                    <p class="business-location">
                                        <i class="fas fa-map-pin"></i> 
                                        <?php echo htmlspecialchars($business['location']); ?>
                                    </p>
                                    <p class="business-rating">
                                        <i class="fas fa-star"></i> 
                                        <?php echo round($business['rating'] ?? 0, 1); ?>/5 
                                        (<?php echo $business['review_count'] ?? 0; ?> reviews)
                                    </p>
                                    <div class="business-actions">
                                        <a href="/FindItLocal/php-app/business/<?php echo $business['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                        <a href="/FindItLocal/php-app/business/edit/<?php echo $business['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <a href="/FindItLocal/php-app/business/images/<?php echo $business['id']; ?>" class="btn btn-sm btn-info">Images</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <p>No businesses available.</p>
                        </div>
                    <?php endif; ?>
                </section>
            <?php elseif ($userRole === 'business_owner'): ?>
                <section class="dashboard-section">
                    <div class="empty-state">
                        <i class="fas fa-store"></i>
                        <h2>No Businesses Yet</h2>
                        <p>Start by creating your first business profile to connect with customers.</p>
                        <a href="/FindItLocal/php-app/business/create" class="btn btn-primary">Create Business</a>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>
</div>

<style>
.dashboard-container {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 30px;
    margin-top: 30px;
}

.dashboard-sidebar {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    height: fit-content;
    position: sticky;
    top: 20px;
}

.sidebar-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li {
    margin: 0;
}

.sidebar-menu a {
    display: block;
    padding: 12px 15px;
    color: #333;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: #e3f2fd;
    border-left-color: #007bff;
    color: #007bff;
}

.sidebar-menu a.logout-link {
    color: #dc3545;
    margin-top: 20px;
    border-top: 1px solid #ddd;
    padding-top: 12px;
}

.dashboard-content {
    flex: 1;
}

.dashboard-header {
    margin-bottom: 40px;
}

.dashboard-header h1 {
    font-size: 32px;
    margin: 0 0 10px 0;
}

.dashboard-header p {
    color: #666;
    margin: 0;
}

.dashboard-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dashboard-section h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    margin: 0;
}

.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.info-card {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
}

.card-icon {
    font-size: 32px;
    flex-shrink: 0;
}

.card-content h3 {
    margin: 0 0 5px 0;
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.card-content p {
    margin: 0;
    color: #333;
    font-weight: 500;
}

.bookings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
}

.booking-card {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 15px;
    transition: all 0.3s ease;
}

.booking-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.booking-header h3 {
    margin: 0;
    font-size: 16px;
}

.booking-business {
    margin: 5px 0;
    color: #666;
    font-size: 14px;
}

.booking-date {
    margin: 5px 0;
    color: #999;
    font-size: 13px;
}

.booking-amount {
    margin: 10px 0 0 0;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.businesses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.business-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.business-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.business-card-image {
    height: 150px;
    background: #f8f9fa;
    overflow: hidden;
}

.business-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.business-card-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #ccc;
}

.business-card-content {
    padding: 15px;
}

.business-card-content h3 {
    margin: 0 0 8px 0;
    font-size: 18px;
}

.business-location {
    margin: 8px 0;
    color: #666;
    font-size: 13px;
}

.business-rating {
    margin: 8px 0;
    color: #f39c12;
    font-size: 13px;
}

.business-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #eee;
}

.business-actions .btn {
    flex: 1;
    text-align: center;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h2 {
    margin: 20px 0 10px 0;
}

.empty-state p {
    color: #666;
    margin-bottom: 20px;
}

.no-data {
    text-align: center;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 6px;
    color: #666;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-pending {
    background: #fff3cd;
    color: #856404;
}

.badge-confirmed {
    background: #d4edda;
    color: #155724;
}

.badge-completed {
    background: #d1ecf1;
    color: #0c5460;
}

.badge-cancelled {
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .dashboard-container {
        grid-template-columns: 1fr;
    }

    .dashboard-sidebar {
        position: relative;
        top: 0;
    }

    .info-cards {
        grid-template-columns: 1fr;
    }

    .bookings-grid,
    .businesses-grid {
        grid-template-columns: 1fr;
    }
}
</style>
