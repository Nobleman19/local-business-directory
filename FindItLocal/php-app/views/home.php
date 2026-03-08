<section class="hero">
    <div class="hero-content">
        <h1>Welcome to NobleLink</h1>
        <p>Discover and connect with local businesses near you</p>
        <div class="search-container">
            <form action="/FindItLocal/php-app/search" method="GET" class="search-form">
                <input type="text" name="q" placeholder="Search businesses..." class="search-input">
                <input type="text" name="location" placeholder="Enter location..." class="search-input">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
</section>

<section class="featured-section">
    <div class="container">
        <div class="featured-header">
            <h2>Featured Categories</h2>
            <p>Browse services by category</p>
        </div>
        <div class="categories-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <a href="/FindItLocal/php-app/category/<?php echo $category['id']; ?>" class="category-card">
                        <div class="category-icon">
                            <?php if (!empty($category['icon']) && strpos($category['icon'], 'fa') === 0): ?>
                                <i class="fas <?php echo htmlspecialchars($category['icon']); ?>"></i>
                            <?php elseif (!empty($category['icon'])): ?>
                                <img src="/FindItLocal/php-app/assets/images/<?php echo htmlspecialchars($category['icon']); ?>" alt="<?php echo htmlspecialchars($category['category_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-store"></i>
                            <?php endif; ?>
                        </div>
                        <div class="category-info">
                            <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                            <?php if (!empty($category['description'])): ?>
                                <p><?php echo htmlspecialchars(substr($category['description'], 0, 60)); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-categories">No categories available</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <h2>Why Choose NobleLink?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-check-circle"></i>
                <h3>Verified Businesses</h3>
                <p>All businesses on our platform are verified and rated by real customers.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-calendar"></i>
                <h3>Easy Booking</h3>
                <p>Book services quickly and easily with just a few clicks.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-star"></i>
                <h3>Reviews & Ratings</h3>
                <p>Read honest reviews from customers and make informed decisions.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-lock"></i>
                <h3>Secure Payments</h3>
                <p>Safe and secure payment options to protect your transactions.</p>
            </div>
        </div>
    </div>
</section>

<style>
/* Featured Section Styling */
.featured-section {
    padding: 60px 20px;
    background: #f8f9fa;
}

.featured-header {
    text-align: center;
    margin-bottom: 40px;
}

.featured-header h2 {
    font-size: 32px;
    color: #2c3e50;
    margin-bottom: 10px;
    font-weight: 700;
}

.featured-header p {
    color: #7f8c8d;
    font-size: 16px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
}

.category-card {
    background: white;
    border-radius: 12px;
    padding: 30px 20px;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.category-icon {
    font-size: 48px;
    color: #3498db;
    margin-bottom: 15px;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #e3f2fd;
}

.category-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.category-icon i {
    font-size: 40px;
}

.category-info h3 {
    font-size: 18px;
    color: #2c3e50;
    margin: 15px 0 10px 0;
    font-weight: 600;
}

.category-info p {
    color: #7f8c8d;
    font-size: 13px;
    line-height: 1.5;
    margin: 0;
}

.no-categories {
    text-align: center;
    color: #95a5a6;
    font-size: 16px;
    padding: 40px;
    grid-column: 1 / -1;
}

@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
    }
    
    .category-card {
        padding: 20px 15px;
    }
    
    .category-icon {
        width: 60px;
        height: 60px;
        font-size: 36px;
    }
    
    .category-icon i {
        font-size: 28px;
    }
    
    .category-info h3 {
        font-size: 16px;
    }
}
</style>
