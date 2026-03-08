<div class="container">
    <div class="page-header">
        <h1>Contact Us</h1>
        <p>Have questions? We'd love to hear from you!</p>
    </div>

    <div class="contact-container">
        <div class="contact-form-section">
            <h2>Send us a Message</h2>

            <?php if (isset($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="/FindItLocal/php-app/contact" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>

        <div class="contact-info-section">
            <h2>Get in Touch</h2>
            
            <div class="contact-info">
                <h3><i class="fas fa-phone"></i> Call Us</h3>
                <p>+26 0779827519</p>
            </div>

            <div class="contact-info">
                <h3><i class="fas fa-envelope"></i> Email Us</h3>
                <p>info@noblelink.com</p>
            </div>

            <div class="contact-info">
                <h3><i class="fas fa-map-pin"></i> Visit Us</h3>
                <p>KS 2018<br>Kawama, Kitwe</p>
            </div>

            <div class="contact-info">
                <h3><i class="fas fa-clock"></i> Business Hours</h3>
                <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
            </div>
        </div>
    </div>
</div>
