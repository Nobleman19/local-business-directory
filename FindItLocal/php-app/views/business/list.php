<div class="container">
    <div class="page-header">
        <h1>Businesses</h1>
        <p>Browse all registered businesses</p>
    </div>

    <div class="businesses-grid">
        <?php if (!empty($businesses)): ?>
            <?php foreach ($businesses as $business): ?>
                <div class="business-card">
                    <?php if (!empty($business['business_logo'])): ?>
                        <img src="/FindItLocal/php-app/uploads/business_logos/<?php echo htmlspecialchars($business['business_logo']); ?>" alt="<?php echo htmlspecialchars($business['business_name']); ?>" class="business-logo">
                    <?php else: ?>
                        <div class="business-logo-placeholder">
                            <i class="fas fa-store"></i>
                        </div>
                    <?php endif; ?>

                    <div class="business-info">
                        <h3><?php echo htmlspecialchars($business['business_name']); ?></h3>
                        <p class="category">
                            <?php 
                            if (!empty($business['categories'])) {
                                echo implode(', ', array_map(fn($c) => htmlspecialchars($c['category_name']), $business['categories']));
                            }
                            ?>
                        </p>
                        <p class="location"><i class="fas fa-map-pin"></i> <?php echo htmlspecialchars($business['location']); ?></p>

                        <?php if (!empty($business['rating'])): ?>
                            <div class="rating">
                                <i class="fas fa-star-solid"></i> <?php echo round($business['rating'], 1); ?>/5 (<?php echo $business['review_count'] ?? 0; ?> reviews)
                            </div>
                        <?php endif; ?>

                        <p class="description"><?php echo htmlspecialchars(substr($business['description'], 0, 100)); ?>...</p>

                        <div class="business-contact">
                            <small><i class="fas fa-phone"></i> <?php echo htmlspecialchars($business['contact_number']); ?></small>
                            <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($business['email']); ?></small>
                        </div>

                        <div class="business-actions">
                            <a href="/FindItLocal/php-app/business/detail/<?php echo $business['id']; ?>" class="btn btn-primary">
                                View Details
                            </a>
                            <a href="/FindItLocal/php-app/booking/create/<?php echo $business['id']; ?>" class="btn btn-success">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No businesses found.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($page > 1 || count($businesses) >= 20): ?>
        <div class="pagination-container">
            <?php if ($page > 1): ?>
                <a href="/FindItLocal/php-app/businesses?page=1" class="btn">First</a>
                <a href="/FindItLocal/php-app/businesses?page=<?php echo $page - 1; ?>" class="btn">Previous</a>
            <?php endif; ?>

            <span class="page-info">Page <?php echo $page; ?></span>

            <?php if (count($businesses) >= 20): ?>
                <a href="/FindItLocal/php-app/businesses?page=<?php echo $page + 1; ?>" class="btn">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>


