<!-- Booking Confirmation Page -->
<?php
// View file - routed through index.php
?>

<div class="confirmation-container">
    <div class="confirmation-card">
        <!-- Success Icon -->
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <!-- Success Message -->
        <h1>Booking Confirmed!</h1>
        <p class="confirmation-message">
            Your booking has been successfully created and is pending confirmation from the business.
        </p>
        
        <!-- Booking Details -->
        <?php if (isset($booking) && !empty($booking)): ?>
        <div class="booking-details">
            <h3>Booking Details</h3>
            
            <div class="details-grid">
                <div class="detail-item">
                    <label>Booking Reference:</label>
                    <p class="reference-id">#BK<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></p>
                </div>
                
                <div class="detail-item">
                    <label>Business:</label>
                    <p><?php echo htmlspecialchars($booking['business_name'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="detail-item">
                    <label>Service:</label>
                    <p><?php echo htmlspecialchars($booking['service_name'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="detail-item">
                    <label>Scheduled Date:</label>
                    <p><?php 
                        $dt = new DateTime($booking['scheduled_date'] ?? 'now');
                        echo $dt->format('D, M d, Y \a\t g:i A'); 
                    ?></p>
                </div>
                
                <div class="detail-item">
                    <label>Duration:</label>
                    <p><?php echo htmlspecialchars($booking['duration'] ?? 'N/A'); ?> minutes</p>
                </div>
                
                <div class="detail-item">
                    <label>Total Amount:</label>
                    <p class="amount"><?php echo Helper::formatCurrency($booking['total_amount'] ?? 0); ?></p>
                </div>
                
                <div class="detail-item">
                    <label>Your Name:</label>
                    <p><?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="detail-item">
                    <label>Email:</label>
                    <p><?php echo htmlspecialchars($booking['customer_email'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="detail-item">
                    <label>Phone:</label>
                    <p><?php echo htmlspecialchars($booking['customer_phone'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="detail-item">
                    <label>Status:</label>
                    <p>
                        <span class="badge badge-pending">
                            <i class="fas fa-clock"></i> <?php echo ucfirst($booking['status'] ?? 'pending'); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <?php if (!empty($booking['notes'])): ?>
            <div class="notes-section">
                <h4>Special Requests:</h4>
                <p><?php echo htmlspecialchars($booking['notes']); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Info Message -->
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>
                A confirmation email has been sent to <strong><?php echo htmlspecialchars($booking['customer_email'] ?? 'your email'); ?></strong>. 
                The business will review your booking and contact you with confirmation details.
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="/FindItLocal/php-app/bookings" class="btn btn-primary btn-lg">
                <i class="fas fa-list"></i> View My Bookings
            </a>
            <a href="/FindItLocal/php-app/businesses" class="btn btn-secondary btn-lg">
                <i class="fas fa-search"></i> Browse More Services
            </a>
        </div>
    </div>
</div>

<style>
.confirmation-container {
    max-width: 700px;
    margin: 60px auto;
    padding: 0 20px;
}

.confirmation-card {
    background: white;
    border-radius: 12px;
    padding: 60px 40px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.success-icon {
    font-size: 80px;
    color: #2ecc71;
    margin-bottom: 20px;
    animation: scaleIn 0.5s ease-in-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.confirmation-card h1 {
    color: #2c3e50;
    font-size: 32px;
    margin: 20px 0 10px;
}

.confirmation-message {
    color: #7f8c8d;
    font-size: 16px;
    margin-bottom: 40px;
}

/* Booking Details */
.booking-details {
    text-align: left;
    margin: 40px 0;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.booking-details h3 {
    color: #2c3e50;
    margin-bottom: 25px;
    text-align: center;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.detail-item {
    padding: 15px;
    background: white;
    border-radius: 6px;
    border: 1px solid #ecf0f1;
}

.detail-item label {
    display: block;
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
}

.detail-item p {
    margin: 0;
    color: #2c3e50;
    font-size: 15px;
    font-weight: 500;
}

.reference-id {
    font-family: 'Courier New', monospace;
    font-size: 18px;
    color: #3498db;
    font-weight: 700;
}

.amount {
    font-size: 18px;
    color: #2ecc71;
    font-weight: 700;
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

.badge-confirmed {
    background: #d4edda;
    color: #155724;
}

.badge-completed {
    background: #d1ecf1;
    color: #0c5460;
}

/* Notes Section */
.notes-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #ecf0f1;
    text-align: left;
}

.notes-section h4 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.notes-section p {
    background: white;
    padding: 12px;
    border-radius: 4px;
    color: #555;
    border-left: 3px solid #3498db;
}

/* Info Box */
.info-box {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    border-radius: 6px;
    padding: 15px;
    margin: 30px 0;
    color: #0c5460;
    text-align: left;
    display: flex;
    gap: 15px;
}

.info-box i {
    font-size: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}

.info-box p {
    margin: 0;
    font-size: 14px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 40px;
    flex-direction: column;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 28px;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 15px;
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

.btn-lg {
    width: 100%;
}

/* Responsive */
@media (max-width: 768px) {
    .confirmation-card {
        padding: 40px 20px;
    }
    
    .success-icon {
        font-size: 60px;
    }
    
    .confirmation-card h1 {
        font-size: 24px;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .booking-details {
        padding: 20px;
    }
    
    .detail-item {
        padding: 12px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
