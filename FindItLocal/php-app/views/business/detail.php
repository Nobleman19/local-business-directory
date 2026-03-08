<div class="container">
    <?php if ($business): ?>
        <div class="business-detail">
            <div class="business-header">
                <?php if (!empty($business['business_logo'])): ?>
                    <img src="/FindItLocal/php-app/uploads/business_logos/<?php echo htmlspecialchars($business['business_logo']); ?>" alt="<?php echo htmlspecialchars($business['business_name']); ?>" class="business-header-image">
                <?php else: ?>
                    <div class="business-header-placeholder">
                        <i class="fas fa-store"></i>
                    </div>
                <?php endif; ?>

                <div class="business-header-info">
                    <h1><?php echo htmlspecialchars($business['business_name']); ?></h1>
                    <p class="location"><i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($business['location']); ?></p>

                    <div class="rating-large">
                        <i class="fas fa-star-solid"></i> <?php echo round($business['rating'] ?? 0, 1); ?>/5
                        <span class="review-count">(<?php echo $business['review_count'] ?? 0; ?> reviews)</span>
                    </div>

                    <div class="business-contact-large">
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($business['contact_number']); ?></p>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($business['email']); ?></p>
                        <?php if (!empty($business['website'])): ?>
                            <p><i class="fas fa-globe"></i> <a href="<?php echo htmlspecialchars($business['website']); ?>" target="_blank"><?php echo htmlspecialchars($business['website']); ?></a></p>
                        <?php endif; ?>
                        <?php if (!empty($business['working_hours'])): ?>
                            <p><i class="fas fa-clock"></i> <?php echo htmlspecialchars($business['working_hours']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="business-content">
                <section class="description-section">
                    <h2>About</h2>
                    <p><?php echo htmlspecialchars($business['description']); ?></p>
                </section>

                <section class="services-section">
                    <h2>Services</h2>
                    <div class="services-list">
                        <?php if (!empty($business['services'])): ?>
                            <?php foreach ($business['services'] as $service): ?>
                                <div class="service-item">
                                    <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($service['description'], 0, 150)); ?>...</p>
                                    <div class="service-details">
                                        <span class="price"><?php echo Helper::formatCurrency($service['price']); ?></span>
                                        <span class="duration"><?php echo $service['duration']; ?> minutes</span>
                                    </div>
                                    <a href="/FindItLocal/php-app/booking/create/<?php echo $business['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-calendar-check"></i> Book Service
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No services available at the moment.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="reviews-section">
                    <h2>Customer Reviews</h2>
                    <?php if (!empty($reviews)): ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <strong><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></strong>
                                        <span class="rating"><i class="fas fa-star-solid"></i> <?php echo $review['rating']; ?>/5</span>
                                    </div>
                                    <p class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></p>
                                    <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No reviews yet. Be the first to review this business!</p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    <?php else: ?>
        <div class="error-container">
            <h2>Business Not Found</h2>
            <p>The business you're looking for doesn't exist.</p>
            <a href="/FindItLocal/php-app/businesses" class="btn btn-primary">Back to Businesses</a>
        </div>
    <?php endif; ?>
</div>
