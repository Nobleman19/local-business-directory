<!-- Enhanced Review Writing Form -->
<div class="review-form-container">
    <h3>Write a Review</h3>
    
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (isset($message) && $message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <form method="POST" class="review-form" id="reviewForm">
        <input type="hidden" name="business_id" value="<?php echo isset($business['id']) ? htmlspecialchars($business['id']) : ''; ?>">
        
        <!-- Service Selection -->
        <div class="form-group">
            <label for="service_id">Service <span class="required">*</span></label>
            <select id="service_id" name="service_id" class="form-control" required>
                <option value="">-- Select a Service --</option>
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo htmlspecialchars($service['id']); ?>">
                            <?php echo htmlspecialchars($service['service_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option disabled>No services available</option>
                <?php endif; ?>
            </select>
            <small class="form-text">Select the service you want to review</small>
        </div>
        
        <!-- Rating Selection with Stars -->
        <div class="form-group">
            <label>Rating <span class="required">*</span></label>
            <div class="star-rating" id="starRating">
                <input type="hidden" name="rating" id="ratingInput" value="0">
                <div class="stars">
                    <i class="star far fa-star" data-value="1"></i>
                    <i class="star far fa-star" data-value="2"></i>
                    <i class="star far fa-star" data-value="3"></i>
                    <i class="star far fa-star" data-value="4"></i>
                    <i class="star far fa-star" data-value="5"></i>
                </div>
                <span class="rating-text">Select a rating</span>
            </div>
            <small class="form-text">How would you rate this service?</small>
        </div>
        
        <!-- Review Text -->
        <div class="form-group">
            <label for="review_text">Your Review <span class="required">*</span></label>
            <textarea id="review_text" name="review_text" class="form-control" rows="5" 
                      placeholder="Share your experience with this service... (minimum 20 characters)"
                      minlength="20" maxlength="2000" required></textarea>
            <small class="form-text">Minimum 20 characters, maximum 2000 characters</small>
        </div>
        
        <!-- Category Tags -->
        <div class="form-group">
            <label>Highlight Categories (Optional)</label>
            <div class="category-tags">
                <?php if (!empty($business['categories'])): ?>
                    <?php foreach ($business['categories'] as $category): ?>
                        <div class="category-tag">
                            <?php if (!empty($category['icon'])): ?>
                                <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($category['category_name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No categories for this business</p>
                <?php endif; ?>
            </div>
            <small class="form-text">These are the service categories for this business</small>
        </div>
        
        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="submitBtn">Submit Review</button>
            <button type="reset" class="btn btn-secondary">Clear</button>
        </div>
    </form>
</div>

<style>
.review-form-container {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 8px;
    margin-top: 40px;
    margin-bottom: 30px;
}

.review-form-container h3 {
    margin-bottom: 25px;
    color: #333;
    font-size: 20px;
}

.review-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
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
    color: #666;
    margin-top: 5px;
}

/* Star Rating */
.star-rating {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.stars {
    display: flex;
    gap: 10px;
    font-size: 28px;
}

.star {
    cursor: pointer;
    color: #ddd;
    transition: all 0.2s ease;
}

.star:hover,
.star.active {
    color: #f39c12;
    transform: scale(1.1);
}

.star.hover {
    color: #f39c12;
}

.rating-text {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

/* Category Tags */
.category-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 15px;
    background: white;
    border-radius: 4px;
    border: 1px solid #eee;
}

.category-tag {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: #e8f4f8;
    border: 1px solid #3498db;
    border-radius: 20px;
    color: #2c3e50;
    font-size: 13px;
    font-weight: 500;
}

.category-tag i {
    font-size: 14px;
    color: #3498db;
}

.text-muted {
    color: #888;
    font-size: 13px;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.alert {
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .review-form-container {
        padding: 20px;
    }
    
    .stars {
        font-size: 24px;
        gap: 8px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .category-tags {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('ratingInput');
    const ratingText = document.querySelector('.rating-text');
    const reviewForm = document.getElementById('reviewForm');
    
    const ratings = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
    
    // Star rating interaction
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            ratingInput.value = value;
            ratingText.textContent = ratings[value - 1];
            
            // Update star display
            stars.forEach(s => {
                s.classList.remove('active');
                if (s.getAttribute('data-value') <= value) {
                    s.classList.add('active');
                    s.classList.replace('far', 'fas');
                } else {
                    s.classList.replace('fas', 'far');
                }
            });
        });
        
        // Hover effect
        star.addEventListener('mouseover', function() {
            const value = this.getAttribute('data-value');
            stars.forEach(s => {
                if (s.getAttribute('data-value') <= value) {
                    s.classList.add('hover');
                } else {
                    s.classList.remove('hover');
                }
            });
        });
    });
    
    document.addEventListener('mouseout', function() {
        stars.forEach(s => s.classList.remove('hover'));
    });
    
    // Form validation
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            if (ratingInput.value === '0' || ratingInput.value === '') {
                e.preventDefault();
                alert('Please select a rating');
                return;
            }
            
            const serviceId = document.getElementById('service_id').value;
            if (!serviceId) {
                e.preventDefault();
                alert('Please select a service');
                return;
            }
            
            const reviewText = document.getElementById('review_text').value.trim();
            if (reviewText.length < 20) {
                e.preventDefault();
                alert('Review must be at least 20 characters long');
                return;
            }
        });
    }
});
</script>
