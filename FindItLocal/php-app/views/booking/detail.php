<!-- Booking Detail Page -->
<?php
// View file - routed through index.php
?>

<div class="booking-detail-container">
    <div class="detail-header">
        <a href="/FindItLocal/php-app/bookings" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to My Bookings
        </a>
        <h1>Booking Details</h1>
    </div>
    
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (isset($message) && $message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if (isset($booking) && !empty($booking)): ?>
    <div class="booking-detail">
        <!-- Status Banner -->
        <div class="status-banner status-<?php echo strtolower($booking['status']); ?>">
            <i class="fas fa-<?php echo $booking['status'] === 'confirmed' ? 'check-circle' : ($booking['status'] === 'completed' ? 'check-double' : 'clock'); ?>"></i>
            <span>Status: <strong><?php echo ucfirst($booking['status']); ?></strong></span>
        </div>
        
        <!-- Main Details -->
        <div class="detail-grid">
            <div class="detail-section">
                <h3>Service Information</h3>
                
                <div class="detail-row">
                    <label>Business:</label>
                    <p>
                        <a href="/FindItLocal/php-app/business/<?php echo $booking['business_id']; ?>">
                            <?php echo htmlspecialchars($booking['business_name']); ?>
                        </a>
                    </p>
                </div>
                
                <div class="detail-row">
                    <label>Service:</label>
                    <p><?php echo htmlspecialchars($booking['service_name']); ?></p>
                </div>
                
                <div class="detail-row">
                    <label>Scheduled Date & Time:</label>
                    <p><?php 
                        $dt = new DateTime($booking['scheduled_date'] ?? 'now');
                        echo $dt->format('D, M d, Y \a\t g:i A'); 
                    ?></p>
                </div>
                
                <div class="detail-row">
                    <label>Duration:</label>
                    <p><?php echo htmlspecialchars($booking['duration'] ?? 'N/A'); ?> minutes</p>
                </div>
            </div>
            
            <div class="detail-section">
                <h3>Your Information</h3>
                
                <div class="detail-row">
                    <label>Name:</label>
                    <p><?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="detail-row">
                    <label>Email:</label>
                    <p><?php echo htmlspecialchars($booking['customer_email'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="detail-row">
                    <label>Phone:</label>
                    <p><?php echo htmlspecialchars($booking['customer_phone'] ?? 'N/A'); ?></p>
                </div>
            </div>
            
            <div class="detail-section">
                <h3>Payment Information</h3>
                
                <div class="detail-row">
                    <label>Service Price:</label>
                    <p><?php echo Helper::formatCurrency($booking['price'] ?? 0); ?></p>
                </div>
                
                <div class="detail-row">
                    <label>Total Amount:</label>
                    <p class="total-amount"><?php echo Helper::formatCurrency($booking['total_amount'] ?? 0); ?></p>
                </div>
                
                <div class="detail-row">
                    <label>Payment Status:</label>
                    <p>
                        <span class="badge badge-<?php echo strtolower($booking['payment_status']); ?>">
                            <?php echo ucfirst($booking['payment_status']); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Notes Section -->
        <?php if (!empty($booking['notes'])): ?>
        <div class="notes-section">
            <h3>Special Requests</h3>
            <p><?php echo htmlspecialchars($booking['notes']); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Booking Reference -->
        <div class="reference-section">
            <p>
                <strong>Booking Reference:</strong> <span class="reference-id">#BK<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></span>
            </p>
            <p>
                <strong>Booked on:</strong> <?php echo date('M d, Y \a\t g:i A', strtotime($booking['created_at'] ?? 'now')); ?>
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <?php if ($booking['status'] === 'pending'): ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                    <i class="fas fa-times-circle"></i> Cancel Booking
                </button>
            </form>
            <?php endif; ?>
            
            <a href="/FindItLocal/php-app/business/<?php echo $booking['business_id']; ?>" class="btn btn-secondary">
                <i class="fas fa-store"></i> View Business
            </a>
            
            <a href="/FindItLocal/php-app/bookings" class="btn btn-primary">
                <i class="fas fa-list"></i> Back to Bookings
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">Booking not found</div>
    <?php endif; ?>
</div>

<style>
.booking-detail-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}

.detail-header {
    margin-bottom: 30px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #3498db;
    text-decoration: none;
    margin-bottom: 15px;
    font-weight: 500;
    transition: color 0.3s ease;
}

.back-link:hover {
    color: #2980b9;
}

.detail-header h1 {
    color: #2c3e50;
    font-size: 28px;
    margin-bottom: 20px;
}

/* Status Banner */
.status-banner {
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    font-size: 16px;
}

.status-banner i {
    font-size: 20px;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-completed {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-canceled {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Detail Grid */
.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.detail-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    border: 1px solid #ecf0f1;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.detail-section h3 {
    color: #2c3e50;
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
}

.detail-row {
    margin-bottom: 18px;
}

.detail-row:last-child {
    margin-bottom: 0;
}

.detail-row label {
    display: block;
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 6px;
}

.detail-row p {
    margin: 0;
    color: #2c3e50;
    font-size: 15px;
    word-break: break-word;
}

.detail-row a {
    color: #3498db;
    text-decoration: none;
}

.detail-row a:hover {
    text-decoration: underline;
}

.total-amount {
    font-size: 18px;
    font-weight: 700;
    color: #2ecc71;
}

.badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-pending {
    background: #fff3cd;
    color: #856404;
}

.badge-paid {
    background: #d4edda;
    color: #155724;
}

.badge-failed {
    background: #f8d7da;
    color: #721c24;
}

/* Notes Section */
.notes-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3498db;
    margin-bottom: 30px;
}

.notes-section h3 {
    color: #2c3e50;
    margin-bottom: 12px;
}

.notes-section p {
    color: #555;
    line-height: 1.6;
    margin: 0;
}

/* Reference Section */
.reference-section {
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 25px;
    border: 1px solid #ecf0f1;
}

.reference-section p {
    margin: 8px 0;
    color: #2c3e50;
}

.reference-id {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    color: #3498db;
    font-weight: 700;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 30px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(149, 165, 166, 0.3);
}

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background: #c0392b;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
    
    .booking-detail-container {
        padding: 0 15px;
    }
    
    .detail-header h1 {
        font-size: 22px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
