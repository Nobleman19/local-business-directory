<div class="container">
    <div class="page-header">
        <h1><?php echo isset($business) && $business ? 'Edit Business' : 'Create New Business'; ?></h1>
        <p><?php echo isset($business) && $business ? 'Update your business information' : 'Add your business to Find It Local and start reaching customers'; ?></p>
    </div>

    <div class="create-business-container">
        <?php if (isset($message) && $message): ?>
            <div class="alert alert-success">
                <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger">
                <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($error) || !$error): ?>
        <form action="<?php echo isset($business) && $business ? '/FindItLocal/php-app/business/edit/' . $business['id'] : '/FindItLocal/php-app/business/create'; ?>" method="POST" enctype="multipart/form-data" class="business-form" id="businessForm" novalidate>
            
            <div class="form-section">
                <h2>Business Information</h2>

                <div class="form-group">
                    <label for="business_name">Business Name <span class="required">*</span></label>
                    <input type="text" id="business_name" name="business_name" 
                           value="<?php echo isset($business) && $business ? htmlspecialchars($business['business_name']) : ''; ?>" 
                           required minlength="3" maxlength="255" 
                           placeholder="Enter your business name"
                           title="Business name must be between 3 and 255 characters">
                    <small class="form-text">3-255 characters required</small>
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" rows="6" required minlength="20" maxlength="5000"
                              placeholder="Describe your business, services, and what makes you unique..."
                              title="Description must be between 20 and 5000 characters"><?php echo isset($business) && $business ? htmlspecialchars($business['description']) : ''; ?></textarea>
                    <small class="form-text">Minimum 20 characters required</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="location">Location/Address <span class="required">*</span></label>
                        <input type="text" id="location" name="location" 
                               value="<?php echo isset($business) && $business ? htmlspecialchars($business['location']) : ''; ?>" 
                               required minlength="5" maxlength="255"
                               placeholder="e.g., 123 Main Street, Lusaka"
                               title="Complete address required">
                        <small class="form-text">Complete address (min 5 characters)</small>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number <span class="required">*</span></label>
                        <input type="tel" id="contact_number" name="contact_number" 
                               value="<?php echo isset($business) && $business ? htmlspecialchars($business['contact_number']) : ''; ?>" 
                               required placeholder="+260123456789"
                               pattern="^\+\d{1,15}$"
                               title="Format: +country code followed by phone number (1-15 digits)">
                        <small class="form-text">Format: +260 followed by number</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo isset($business) && $business ? htmlspecialchars($business['email']) : ''; ?>" 
                               required maxlength="255"
                               placeholder="business@example.com">
                        <small class="form-text">Valid email address required</small>
                    </div>
                    <div class="form-group">
                        <label for="website">Website URL</label>
                        <input type="url" id="website" name="website" 
                               value="<?php echo isset($business) && $business ? htmlspecialchars($business['website'] ?? '') : ''; ?>"
                               placeholder="https://www.example.com"
                               title="Enter a complete URL including http:// or https://">
                        <small class="form-text">Optional - Must be a valid URL</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="working_hours">Working Hours</label>
                    <input type="text" id="working_hours" name="working_hours" 
                           value="<?php echo isset($business) && $business ? htmlspecialchars($business['working_hours'] ?? '') : ''; ?>" 
                           placeholder="e.g., Mon-Fri: 9AM-5PM, Sat: 10AM-2PM, Sun: Closed"
                           maxlength="500">
                    <small class="form-text">Optional - Help customers know your hours</small>
                </div>

                <div class="form-group">
                    <label for="business_logo">Business Logo</label>
                    <?php if (isset($business) && $business && !empty($business['business_logo'])): ?>
                        <div class="logo-preview">
                            <img src="/FindItLocal/php-app/uploads/business_logos/<?php echo htmlspecialchars($business['business_logo']); ?>" 
                                 alt="<?php echo htmlspecialchars($business['business_name']); ?> Logo"
                                 loading="lazy">
                            <p>Current Logo</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="business_logo" name="business_logo" 
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           title="Upload a business logo (JPG, PNG, GIF, WebP)">
                    <small class="form-text">Optional - Recommended size: 300x300px. Max file size: 5MB. Formats: JPG, PNG, GIF, WebP</small>
                </div>
            </div>

            <div class="form-section">
                <h2>Business Category <span class="required">*</span></h2>
                <p class="section-description">Select your primary business category</p>
                
                <div class="form-group">
                    <label for="primary_category">Primary Category *</label>
                    <select id="primary_category" name="primary_category" class="form-control" required>
                        <option value="">-- Select a Category --</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>"
                                    <?php if (isset($business) && $business && isset($business['categories']) && count($business['categories']) > 0) {
                                        if ($business['categories'][0]['id'] == $category['id']) {
                                            echo ' selected';
                                        }
                                    } ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-text">This is the main category for your business</small>
                </div>

                <div class="form-group">
                    <label>Additional Categories (Optional)</label>
                    <p class="section-description">Select additional categories that describe your business</p>
                    <div class="categories-grid">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <div class="form-check">
                                    <input type="checkbox" id="category_<?php echo htmlspecialchars($category['id']); ?>" 
                                           name="categories[]" value="<?php echo htmlspecialchars($category['id']); ?>"
                                           class="category-checkbox"
                                           <?php if (isset($business) && $business && isset($business['categories'])) {
                                               foreach ($business['categories'] as $bc) {
                                                   if ($bc['id'] == $category['id']) {
                                                       echo ' checked';
                                                   }
                                               }
                                           } ?>>
                                    <label for="category_<?php echo htmlspecialchars($category['id']); ?>">
                                        <?php if (!empty($category['icon'])): ?>
                                            <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($category['category_name']); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <p>No categories available. Please contact support.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="categoryError" class="error-text" style="display: none; margin-top: 10px;">
                    Please select a primary category
                </div>
            </div>

            <div class="form-section">
                <h2>Services (Optional)</h2>
                <p class="section-description">Add services and prices that your business offers</p>
                
                <div id="services-container">
                    <?php if (isset($business) && $business && isset($business['services'])): ?>
                        <?php foreach ($business['services'] as $index => $service): ?>
                            <div class="service-item" data-service-index="<?php echo $index; ?>">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="service_name_<?php echo $index; ?>">Service Name</label>
                                        <input type="text" id="service_name_<?php echo $index; ?>" 
                                               name="services[<?php echo $index; ?>][name]" 
                                               placeholder="e.g., Haircut, Web Design"
                                               maxlength="255"
                                               value="<?php echo htmlspecialchars($service['service_name'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="service_price_<?php echo $index; ?>">Price (ZMW)</label>
                                        <input type="number" id="service_price_<?php echo $index; ?>" 
                                               name="services[<?php echo $index; ?>][price]" 
                                               placeholder="0.00"
                                               min="0" step="0.01" required
                                               value="<?php echo htmlspecialchars($service['price'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="service_description_<?php echo $index; ?>">Description</label>
                                    <textarea id="service_description_<?php echo $index; ?>" 
                                              name="services[<?php echo $index; ?>][description]" 
                                              rows="3"
                                              placeholder="Describe this service..."
                                              maxlength="1000"><?php echo htmlspecialchars($service['description'] ?? ''); ?></textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-service" onclick="removeService(this)">
                                    <i class="fas fa-trash"></i> Remove Service
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <button type="button" class="btn btn-secondary btn-sm" id="addServiceBtn" style="margin-top: 15px;">
                    <i class="fas fa-plus"></i> Add Another Service
                </button>
            </div>

            <div class="form-actions">
                <a href="/FindItLocal/php-app/dashboard" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <?php echo isset($business) && $business ? 'Update Business' : 'Create Business'; ?>
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<style>
* {
    box-sizing: border-box;
}

.page-header {
    margin-bottom: 40px;
    text-align: center;
}

.page-header h1 {
    font-size: 32px;
    margin: 0 0 10px 0;
    color: #333;
}

.page-header p {
    color: #666;
    margin: 0;
    font-size: 16px;
}

.create-business-container {
    max-width: 700px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.business-form {
    margin: 0;
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #eee;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h2 {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 20px 0;
    color: #333;
}

.section-description {
    margin: -15px 0 20px 0;
    color: #666;
    font-size: 14px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.required {
    color: #dc3545;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: white;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    background-color: #f8f9ff;
}

.form-group input:invalid,
.form-group textarea:invalid {
    border-color: #dc3545;
}

.form-group input:valid:not(:placeholder-shown),
.form-group textarea:valid:not(:placeholder-shown) {
    border-color: #28a745;
}

.form-group input[type="file"] {
    padding: 8px;
}

.form-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    padding-right: 30px;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3c%3fxml version='1.0' encoding='UTF-8'%3f%3e%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px;
    padding-right: 35px;
}

.form-control option {
    padding: 5px;
}

.form-text {
    display: block;
    margin-top: 5px;
    color: #999;
    font-size: 12px;
}

.error-text {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.logo-preview {
    margin-bottom: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
}

.logo-preview img {
    max-width: 150px;
    max-height: 150px;
    border-radius: 4px;
    display: block;
    margin-bottom: 8px;
    border: 1px solid #ddd;
}

.logo-preview p {
    margin: 0;
    color: #666;
    font-size: 12px;
    font-weight: 500;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 15px;
}

.form-check {
    display: flex;
    align-items: flex-start;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.form-check:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

.form-check input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-top: 2px;
    margin-right: 10px;
    cursor: pointer;
    flex-shrink: 0;
}

.form-check input[type="checkbox"]:checked + label {
    color: #007bff;
}

.form-check label {
    margin: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    flex-wrap: wrap;
}

.form-check label i {
    font-size: 18px;
    color: #007bff;
}

.form-check label span {
    font-size: 14px;
    font-weight: 500;
}

.service-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 15px;
    border: 1px solid #dee2e6;
}

.service-item .form-row {
    margin-bottom: 15px;
}

.service-item:last-child {
    margin-bottom: 0;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
}

.btn {
    padding: 10px 24px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.3);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-primary:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
    transform: translateY(-2px);
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

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border-color: #ffc107;
}

.alert-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    line-height: 1;
}

@media (max-width: 768px) {
    .create-business-container {
        padding: 20px;
    }

    .page-header h1 {
        font-size: 24px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .form-actions .btn {
        width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    }
}
</style>

<script>
let serviceIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    const businessForm = document.getElementById('businessForm');
    const primaryCategory = document.getElementById('primary_category');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const categoryError = document.getElementById('categoryError');
    const addServiceBtn = document.getElementById('addServiceBtn');
    const servicesContainer = document.getElementById('services-container');
    
    // Get initial service index from existing services
    const existingServices = document.querySelectorAll('.service-item');
    if (existingServices.length > 0) {
        serviceIndex = existingServices.length;
    }
    
    // Add service button
    if (addServiceBtn) {
        addServiceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addService();
        });
    }
    
    // Validate primary category is selected
    function validateCategories() {
        // Check if primary category is selected
        if (!primaryCategory || primaryCategory.value === '') {
            categoryError.style.display = 'block';
            return false;
        } else {
            categoryError.style.display = 'none';
            return true;
        }
    }
    
    // Add change listener to primary category select
    if (primaryCategory) {
        primaryCategory.addEventListener('change', validateCategories);
    }
    
    // Add change listeners to category checkboxes for visual feedback
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Just visual feedback, primary_category is required
        });
    });
    
    // Form submission validation
    if (businessForm) {
        businessForm.addEventListener('submit', function(e) {
            // Check if form is valid
            if (!businessForm.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                businessForm.classList.add('was-validated');
            } else if (!validateCategories()) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
    
    // Validate categories on load for edit page
    validateCategories();
});

function addService() {
    const servicesContainer = document.getElementById('services-container');
    
    const serviceHTML = `
        <div class="service-item" data-service-index="${serviceIndex}">
            <div class="form-row">
                <div class="form-group">
                    <label for="service_name_${serviceIndex}">Service Name</label>
                    <input type="text" id="service_name_${serviceIndex}" 
                           name="services[${serviceIndex}][name]" 
                           placeholder="e.g., Haircut, Web Design"
                           maxlength="255">
                </div>
                <div class="form-group">
                    <label for="service_price_${serviceIndex}">Price (ZMW)</label>
                    <input type="number" id="service_price_${serviceIndex}" 
                           name="services[${serviceIndex}][price]" 
                           placeholder="0.00"
                           min="0" step="0.01">
                </div>
            </div>
            <div class="form-group">
                <label for="service_description_${serviceIndex}">Description</label>
                <textarea id="service_description_${serviceIndex}" 
                          name="services[${serviceIndex}][description]" 
                          rows="3"
                          placeholder="Describe this service..."
                          maxlength="1000"></textarea>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-service" onclick="removeService(this)">
                <i class="fas fa-trash"></i> Remove Service
            </button>
        </div>
    `;
    
    servicesContainer.insertAdjacentHTML('beforeend', serviceHTML);
    serviceIndex++;
}

function removeService(btn) {
    const serviceItem = btn.closest('.service-item');
    serviceItem.remove();
}
</script>
