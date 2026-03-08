<div class="container">
    <div class="page-header">
        <h1>My Bookings</h1>
    </div>

    <?php if (!empty($bookings)): ?>
        <div class="bookings-table">
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Business</th>
                        <th>Scheduled Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['business_name']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($booking['scheduled_date'])); ?></td>
                            <td><?php echo Helper::formatCurrency($booking['total_amount']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="/FindItLocal/php-app/booking/<?php echo $booking['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <a href="/FindItLocal/php-app/booking/cancel/<?php echo $booking['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-results">
            <p>No bookings found.</p>
            <a href="/FindItLocal/php-app/businesses" class="btn btn-primary">Browse Businesses</a>
        </div>
    <?php endif; ?>
</div>
