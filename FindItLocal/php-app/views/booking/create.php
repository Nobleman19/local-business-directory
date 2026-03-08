<!-- Service Booking Page -->
<?php
// View file - routed through index.php
?>

<div class="booking-container">
    <div class="booking-header">
        <h2>Book a Service</h2>
        <p class="subtitle">Schedule your service with us</p>
    </div>
    
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (isset($message) && $message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="/FindItLocal/php-app/booking/create/<?php echo isset($business_id) ? htmlspecialchars($business_id) : ''; ?>" class="booking-form" id="bookingForm">
        <input type="hidden" name="business_id" value="<?php echo isset($business_id) ? htmlspecialchars($business_id) : ''; ?>">
        
        <div class="form-row">
            <!-- Service Selection Column -->
            <div class="form-column">
                <h4>Service Details</h4>
                
                <div class="form-group">
                    <label for="service_id">Service <span class="optional">(Optional)</span></label>
                    <select id="service_id" name="service_id" class="form-control">
                        <option value="">-- Select a Service --</option>
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo htmlspecialchars($service['id']); ?>" 
                                        data-price="<?php echo htmlspecialchars($service['price']); ?>"
                                        data-description="<?php echo htmlspecialchars($service['description']); ?>">
                                    <?php echo htmlspecialchars($service['service_name']); ?> 
                                    - ZMW <?php echo number_format($service['price'], 2); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled>No services available</option>
                        <?php endif; ?>
                    </select>
                    <small class="form-text">Choose the service you want to book</small>
                </div>

                <!-- Service Description -->
                <div class="service-description-box" id="serviceDescriptionBox" style="display: none;">
                    <div class="description-header">Service Details</div>
                    <p id="serviceDescription" class="description-text"></p>
                </div>
                
                <!-- Date Selection -->
                <div class="form-group">
                    <label for="booking_date">Preferred Date <span class="required">*</span></label>
                    <input type="date" id="booking_date" name="booking_date" class="form-control" required>
                    <small class="form-text">Select a date for your booking (minimum 2 days from now)</small>
                </div>
                
                <!-- Time Selection -->
                <div class="form-group">
                    <label for="booking_time">Preferred Time <span class="required">*</span></label>
                    <input type="time" id="booking_time" name="booking_time" class="form-control" required>
                    <small class="form-text">Choose your preferred time slot</small>
                </div>
                
                <!-- Duration -->
                <div class="form-group">
                    <label for="duration">Duration (minutes) <span class="required">*</span></label>
                    <select id="duration" name="duration" class="form-control" required>
                        <option value="">-- Select Duration --</option>
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="90">1.5 hours</option>
                        <option value="120">2 hours</option>
                        <option value="180">3 hours</option>
                    </select>
                    <small class="form-text">How long do you need?</small>
                </div>
            </div>
            
            <!-- Customer Details Column -->
            <div class="form-column">
                <h4>Your Details</h4>
                
                <div class="form-group">
                    <label for="customer_name">Full Name <span class="required">*</span></label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" 
                           value="<?php echo isset($_SESSION['first_name'], $_SESSION['last_name']) ? htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) : ''; ?>"
                           required>
                    <small class="form-text">Your full name</small>
                </div>
                
                <div class="form-group">
                    <label for="customer_email">Email <span class="required">*</span></label>
                    <input type="email" id="customer_email" name="customer_email" class="form-control"
                           value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>"
                           required>
                    <small class="form-text">We'll send confirmation to this email</small>
                </div>
                
                <div class="form-group">
                    <label for="customer_phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="customer_phone" name="customer_phone" class="form-control" 
                           pattern="[0-9\-\+\s\(\)]{7,}" required>
                    <small class="form-text">Contact number for confirmation</small>
                </div>
                
                <div class="form-group">
                    <label for="notes">Additional Notes (Optional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="4" 
                              placeholder="Any special requests or additional information?"></textarea>
                    <small class="form-text">Maximum 500 characters</small>
                </div>
            </div>
        </div>
        
        <!-- Price Summary -->
        <div class="price-summary">
            <h4>Price Summary</h4>
            <div class="summary-row">
                <span>Service Price:</span>
                <span id="servicePrice">ZMW 0.00</span>
            </div>
            <div class="summary-row">
                <span>Duration:</span>
                <span id="durationText">-</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span id="totalPrice">ZMW 0.00</span>
            </div>
        </div>
        
        <!-- Terms & Conditions -->
        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" name="terms_agreed" id="termsAgreed" required>
                I agree to the terms and conditions and confirm that I want to book this service
            </label>
            <small class="form-text">By checking this, you confirm your booking request</small>
        </div>
        
        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Confirm Booking</button>
            <a href="/business/detail/<?php echo isset($business_id) ? htmlspecialchars($business_id) : '#'; ?>" class="btn btn-secondary btn-lg">
                Back to Business
            </a>
        </div>
    </form>
</div>

<style>
.booking-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
}

.booking-header {
    text-align: center;
    margin-bottom: 40px;
}

.booking-header h2 {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.subtitle {
    color: #7f8c8d;
    font-size: 16px;
}

.booking-form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.form-column h4 {
    color: #2c3e50;
    font-size: 16px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
}

.form-group {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.required {
    color: #e74c3c;
}

.form-control {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-text {
    font-size: 12px;
    color: #7f8c8d;
    margin-top: 5px;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.checkbox-group input[type="checkbox"] {
    cursor: pointer;
}

/* Price Summary */
.price-summary {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 30px;
    border-left: 4px solid #3498db;
}

/* Service Description Box */
.service-description-box {
    background: #e8f4f8;
    padding: 15px;
    border-radius: 6px;
    border-left: 4px solid #3498db;
    margin-bottom: 20px;
    margin-top: 10px;
}

.description-header {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.description-text {
    color: #34495e;
    font-size: 14px;
    line-height: 1.5;
    margin: 0;
}

.price-summary h4 {
    margin-bottom: 15px;
    color: #2c3e50;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    color: #555;
}

.summary-row.total {
    border-top: 2px solid #ddd;
    padding-top: 12px;
    font-weight: 700;
    font-size: 18px;
    color: #2c3e50;
}

/* Alerts */
.alert {
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary {
    background: #3498db;
    color: white;
    flex: 1;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
}

.btn-secondary {
    background: #95a5a6;
    color: white;
    flex: 1;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.btn-lg {
    padding: 14px 28px;
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .booking-container {
        padding: 0 15px;
    }
    
    .booking-form {
        padding: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .booking-header h2 {
        font-size: 24px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const durationSelect = document.getElementById('duration');
    const bookingDate = document.getElementById('booking_date');
    const bookingForm = document.getElementById('bookingForm');
    
    // Update price and description when service changes
    function updatePrice() {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const description = selectedOption.dataset.description || '';
        
        document.getElementById('servicePrice').textContent = 'ZMW ' + price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        
        // Show/hide service description
        const descBox = document.getElementById('serviceDescriptionBox');
        if (description && serviceSelect.value) {
            document.getElementById('serviceDescription').textContent = description;
            descBox.style.display = 'block';
        } else {
            descBox.style.display = 'none';
        }
        
        calculateTotal();
    }
    
    // Calculate total price
    function calculateTotal() {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const duration = durationSelect.value || 0;
        
        const total = price * (duration / 60);
        document.getElementById('totalPrice').textContent = 'ZMW ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    // Update duration display
    function updateDurationText() {
        const duration = durationSelect.value;
        if (!duration) {
            document.getElementById('durationText').textContent = '-';
            return;
        }
        
        const durationText = durationSelect.options[durationSelect.selectedIndex].text;
        document.getElementById('durationText').textContent = durationText;
        calculateTotal();
    }
    
    // Set minimum date to 2 days from now
    function setMinimumDate() {
        const today = new Date();
        today.setDate(today.getDate() + 2);
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        bookingDate.min = `${year}-${month}-${day}`;
    }
    
    // Form validation
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        let errors = [];
        
        if (!serviceSelect.value) {
            errors.push('Please select a service');
        }
        
        if (!bookingDate.value) {
            errors.push('Please select a date');
        }
        
        if (!document.getElementById('booking_time').value) {
            errors.push('Please select a time');
        }
        
        if (!durationSelect.value) {
            errors.push('Please select a duration');
        }
        
        if (!document.getElementById('customer_name').value.trim()) {
            errors.push('Please enter your name');
        }
        
        if (!document.getElementById('customer_email').value.trim()) {
            errors.push('Please enter your email');
        }
        
        if (!document.getElementById('customer_phone').value.trim()) {
            errors.push('Please enter your phone number');
        }
        
        if (!document.getElementById('termsAgreed').checked) {
            errors.push('Please agree to the terms and conditions');
        }
        
        if (errors.length > 0) {
            alert(errors.join('\n'));
            return;
        }
        
        // Submit form
        this.submit();
    });
    
    // Event listeners
    serviceSelect.addEventListener('change', updatePrice);
    durationSelect.addEventListener('change', updateDurationText);
    
    // Initialize
    setMinimumDate();
    updatePrice();
});
</script>
