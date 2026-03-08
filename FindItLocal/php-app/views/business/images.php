<div class="container">
    <div class="page-header">
        <h1>Business Gallery</h1>
        <p>Manage your business photos</p>
    </div>

    <div class="images-container">
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Upload Section -->
        <div class="upload-section">
            <h2>Upload New Image</h2>
            <form action="/FindItLocal/php-app/business/images/<?php echo $business_id; ?>" method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="form-group">
                    <label for="business_image">Select Image</label>
                    <input type="file" id="business_image" name="business_image" accept="image/*" required>
                    <small>Max file size: 5MB. Supported formats: JPG, PNG, GIF</small>
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="is_primary" id="is_primary">
                        <span>Set as primary image</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Upload Image</button>
            </form>
        </div>

        <!-- Gallery Section -->
        <div class="gallery-section">
            <h2>Your Gallery</h2>

            <?php if (!empty($images)): ?>
                <div class="images-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="image-card">
                            <div class="image-wrapper">
                                <img src="/FindItLocal/php-app/uploads/business_images/<?php echo htmlspecialchars($image['image_url']); ?>" 
                                     alt="Business Image">
                                
                                <?php if ($image['is_primary']): ?>
                                    <div class="primary-badge">Primary</div>
                                <?php endif; ?>
                            </div>

                            <div class="image-actions">
                                <?php if (!$image['is_primary']): ?>
                                    <form action="/FindItLocal/php-app/business/images/set-primary" method="POST" class="inline-form">
                                        <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                        <input type="hidden" name="business_id" value="<?php echo $business_id; ?>">
                                        <button type="submit" class="btn btn-sm btn-secondary">Set as Primary</button>
                                    </form>
                                <?php endif; ?>

                                <form action="/FindItLocal/php-app/business/images/delete" method="POST" class="inline-form">
                                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this image?')">Delete</button>
                                </form>
                            </div>

                            <p class="image-date">
                                Uploaded: <?php echo date('M d, Y', strtotime($image['uploaded_at'])); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-images">
                    <i class="fas fa-image"></i>
                    <p>No images yet. Upload your first business photo!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.page-header {
    margin-bottom: 40px;
}

.page-header h1 {
    font-size: 32px;
    margin: 0 0 10px 0;
}

.page-header p {
    color: #666;
    margin: 0;
}

.images-container {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.upload-section {
    margin-bottom: 40px;
    padding-bottom: 40px;
    border-bottom: 1px solid #eee;
}

.upload-section h2 {
    font-size: 20px;
    margin: 0 0 20px 0;
}

.upload-form {
    max-width: 500px;
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

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #999;
    font-size: 12px;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    font-weight: normal;
    cursor: pointer;
    margin: 0;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
    margin-right: 10px;
    margin-bottom: 0;
}

.gallery-section h2 {
    font-size: 20px;
    margin: 0 0 20px 0;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.image-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.image-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.image-wrapper {
    position: relative;
    width: 100%;
    padding-bottom: 100%;
    overflow: hidden;
    background: #f8f9fa;
}

.image-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.primary-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.image-actions {
    padding: 15px;
    background: white;
}

.image-actions .inline-form {
    display: inline-block;
    margin-right: 10px;
}

.image-actions .inline-form button {
    margin: 0;
}

.image-date {
    margin: 10px 0 0 0;
    color: #999;
    font-size: 12px;
    padding: 0 15px 15px 15px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.no-images {
    text-align: center;
    padding: 60px 20px;
}

.no-images i {
    font-size: 64px;
    color: #ddd;
    display: block;
    margin-bottom: 20px;
}

.no-images p {
    color: #666;
}

.alert {
    padding: 15px 20px;
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
    .images-grid {
        grid-template-columns: 1fr;
    }

    .image-actions {
        display: flex;
        gap: 10px;
    }

    .image-actions .inline-form {
        flex: 1;
        margin: 0;
    }

    .image-actions .inline-form button {
        width: 100%;
    }
}
</style>
