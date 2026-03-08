<!-- Owner Bookings Management -->
<div class="container">
    <div class="page-header">
        <h1>Business Bookings</h1>
        <p>View and manage all bookings for your business</p>
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

    <div class="bookings-manager">
        <!-- Business Info -->
        <?php if (isset($business)): ?>
            <div class="business-info-banner">
                <div class="banner-logo">
                    <?php if (!empty($business['business_logo'])): ?>
                        <img src="/FindItLocal/php-app/uploads/business_logos/<?php echo htmlspecialchars($business['business_logo']); ?>" 
                             alt="<?php echo htmlspecialchars($business['business_name']); ?>">
                    <?php else: ?>
                        <i class="fas fa-store"></i>
                    <?php endif; ?>
                </div>
                <div class="banner-info">
                    <h2><?php echo htmlspecialchars($business['business_name']); ?></h2>
                    <p><?php echo htmlspecialchars($business['location']); ?></p>
                </div>
                <a href="/FindItLocal/php-app/my-businesses" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Businesses
                </a>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filter-bar">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="status-filter">Filter by Status:</label>
                    <select id="status-filter" name="status" class="form-control">
                        <option value="">All Bookings</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo isset($_GET['status']) && $_GET['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo isset($_GET['status']) && $_GET['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo isset($_GET['status']) && $_GET['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="fas fa-search"></i> Filter
                </button>
            </form>
        </div>

        <!-- Bookings Table -->
        <?php if (!empty($bookings)): ?>
            <div class="table-container">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <p class="customer-name"><?php echo htmlspecialchars($booking['customer_name']); ?></p>
                                        <p class="customer-email"><?php echo htmlspecialchars($booking['customer_email']); ?></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-time">
                                        <p class="date"><?php echo date('M d, Y', strtotime($booking['scheduled_date'])); ?></p>
                                        <p class="time"><?php echo date('H:i', strtotime($booking['scheduled_time'])); ?></p>
                                    </div>
                                </td>
                                <td>
                                    <?php echo $booking['duration']; ?> mins
                                </td>
                                <td>
                                    <span class="amount">ZMW <?php echo number_format($booking['total_amount'], 2); ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/FindItLocal/php-app/owner/booking-detail/<?php echo $booking['id']; ?>" class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <input type="hidden" name="action" value="confirm">
                                                <button type="submit" class="btn btn-sm btn-success" title="Accept Booking" onclick="return confirm('Accept this booking?');">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Reject Booking" onclick="return confirm('Reject this booking?');">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="booking-status-info">
                                                <?php if ($booking['status'] === 'confirmed'): ?>
                                                    <span class="status-badge status-confirmed"><i class="fas fa-check-circle"></i> Confirmed</span>
                                                <?php elseif ($booking['status'] === 'completed'): ?>
                                                    <span class="status-badge status-completed"><i class="fas fa-flag-checkered"></i> Completed</span>
                                                <?php elseif ($booking['status'] === 'cancelled'): ?>
                                                    <span class="status-badge status-cancelled"><i class="fas fa-ban"></i> Cancelled</span>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-bookings">
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <h3>No Bookings Yet</h3>
                    <p>You don't have any bookings for this business yet. When customers book your services, they'll appear here.</p>
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

.bookings-manager {
    max-width: 1200px;
    margin: 0 auto;
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

/* Business Info Banner */
.business-info-banner {
    background: white;
    padding: 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.banner-logo {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.banner-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.banner-logo i {
    font-size: 40px;
    color: #bdc3c7;
}

.banner-info {
    flex: 1;
}

.banner-info h2 {
    font-size: 24px;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.banner-info p {
    color: #7f8c8d;
    margin: 0;
}

/* Filter Bar */
.filter-bar {
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.filter-group label {
    font-size: 12px;
    font-weight: 600;
    color: #2c3e50;
    text-transform: uppercase;
}

.form-control {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    min-width: 200px;
}

.btn-search {
    background-color: #3498db;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
}

.btn-search:hover {
    background-color: #2980b9;
}

.btn-secondary {
    background-color: #95a5a6;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-secondary:hover {
    background-color: #7f8c8d;
}

/* Bookings Table */
.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.bookings-table {
    width: 100%;
    border-collapse: collapse;
}

.bookings-table thead {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.bookings-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bookings-table td {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
}

.bookings-table tbody tr:hover {
    background: #f8f9fa;
}

.customer-info {
    margin: 0;
}

.customer-name {
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    font-size: 14px;
}

.customer-email {
    color: #7f8c8d;
    margin: 3px 0 0 0;
    font-size: 12px;
}

.date-time {
    margin: 0;
}

.date {
    font-weight: 500;
    color: #2c3e50;
    margin: 0;
    font-size: 14px;
}

.time {
    color: #7f8c8d;
    margin: 3px 0 0 0;
    font-size: 12px;
}

.amount {
    font-weight: 600;
    color: #27ae60;
    font-size: 15px;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-pending {
    background: #fff3cd;
    color: #856404;
}

.badge-confirmed {
    background: #d1ecf1;
    color: #0c5460;
}

.badge-completed {
    background: #d4edda;
    color: #155724;
}

.badge-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 11px;
}

.btn-info {
    background-color: #16a085;
    color: white;
}

.btn-info:hover {
    background-color: #138d75;
}

.btn-success {
    background-color: #27ae60;
    color: white;
}

.btn-success:hover {
    background-color: #229954;
}

/* No Bookings */
.no-bookings {
    display: flex;
    justify-content: center;
    padding: 80px 20px;
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
    margin: 0;
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .business-info-banner {
        flex-direction: column;
        text-align: center;
    }

    .filter-form {
        flex-direction: column;
    }

    .filter-group {
        width: 100%;
    }

    .form-control {
        width: 100%;
    }

    .btn-search {
        width: 100%;
    }

    .bookings-table {
        font-size: 12px;
    }

    .bookings-table th,
    .bookings-table td {
        padding: 10px;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>
