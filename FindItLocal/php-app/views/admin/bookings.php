<!-- Admin - Manage Bookings -->
<?php
// Admin manage bookings view
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Manage Bookings</h1>
        <p>View and manage all service bookings</p>
    </div>
    
    <!-- Filter -->
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="filter-select">
                <label>Status:</label>
                <select name="status">
                    <option value="all" <?php echo (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'selected' : ''; ?>>
                        All Bookings
                    </option>
                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : ''; ?>>
                        Pending
                    </option>
                    <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] === 'confirmed') ? 'selected' : ''; ?>>
                        Confirmed
                    </option>
                    <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] === 'completed') ? 'selected' : ''; ?>>
                        Completed
                    </option>
                    <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] === 'cancelled') ? 'selected' : ''; ?>>
                        Cancelled
                    </option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-search">Filter</button>
        </form>
    </div>
    
    <!-- Bookings Table -->
    <div class="table-container">
        <?php if (!empty($bookings)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Business</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($booking['user_name'] ?? 'N/A'); ?></strong>
                                <div class="small-text"><?php echo htmlspecialchars($booking['email'] ?? ''); ?></div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($booking['business_name'] ?? 'N/A'); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($booking['service_name'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <strong><?php echo date('M d, Y', strtotime($booking['booking_date'] ?? 'now')); ?></strong>
                                <div class="small-text"><?php echo date('g:i A', strtotime($booking['booking_time'] ?? '00:00')); ?></div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo htmlspecialchars($booking['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button class="btn-action view" onclick="viewBooking(<?php echo htmlspecialchars($booking['id']); ?>)" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action edit" onclick="editBooking(<?php echo htmlspecialchars($booking['id']); ?>, '<?php echo htmlspecialchars($booking['status']); ?>')" title="Change Status">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No bookings found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Update Booking Status</h2>
        
        <form method="POST" class="status-form">
            <input type="hidden" name="action" value="update_booking_status">
            <input type="hidden" name="booking_id" id="bookingId">
            
            <div class="form-group">
                <label for="newStatus">New Status:</label>
                <select id="newStatus" name="status" required>
                    <option value="">-- Select Status --</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Status</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Booking Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content wide">
        <span class="close" onclick="closeDetailsModal()">&times;</span>
        <h2>Booking Details</h2>
        <div id="bookingDetails"></div>
    </div>
</div>

<style>
.admin-container {
    padding: 30px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 10px;
    font-weight: 700;
}

.page-header p {
    color: #7f8c8d;
}

.filter-bar {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}

.filter-select {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-select label {
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.filter-select select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
}

.btn-search {
    padding: 8px 16px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
}

.btn-search:hover {
    background: #2980b9;
}

.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead {
    background: #f8f9fa;
    border-bottom: 2px solid #ddd;
}

.admin-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.admin-table tr:hover {
    background: #f9f9f9;
}

.small-text {
    font-size: 12px;
    color: #7f8c8d;
    margin-top: 3px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 12px;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge.confirmed {
    background: #d1ecf1;
    color: #0c5460;
}

.status-badge.completed {
    background: #d4edda;
    color: #155724;
}

.status-badge.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.actions {
    display: flex;
    gap: 8px;
}

.btn-action {
    width: 32px;
    height: 32px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7f8c8d;
}

.btn-action:hover {
    border-color: #3498db;
    color: #3498db;
    background: #ecf0f1;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #95a5a6;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    overflow-y: auto;
}

.modal-content {
    background: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.modal-content.wide {
    max-width: 600px;
}

.close {
    color: #7f8c8d;
    float: right;
    font-size: 28px;
    cursor: pointer;
    font-weight: bold;
}

.close:hover {
    color: #2c3e50;
}

.status-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 10px;
        font-size: 12px;
    }
}
</style>

<script>
const statusModal = document.getElementById('statusModal');
const detailsModal = document.getElementById('detailsModal');

function editBooking(bookingId, currentStatus) {
    document.getElementById('bookingId').value = bookingId;
    document.getElementById('newStatus').value = currentStatus;
    statusModal.style.display = 'block';
}

function closeModal() {
    statusModal.style.display = 'none';
}

function closeDetailsModal() {
    detailsModal.style.display = 'none';
}

function viewBooking(bookingId) {
    // In a real application, this would fetch booking details via AJAX
    detailsModal.style.display = 'block';
}

// Close modal when X is clicked
document.querySelectorAll('.close').forEach(closeBtn => {
    closeBtn.onclick = function() {
        this.closest('.modal').style.display = 'none';
    };
});

window.onclick = function(event) {
    if (event.target === statusModal) {
        statusModal.style.display = 'none';
    }
    if (event.target === detailsModal) {
        detailsModal.style.display = 'none';
    }
};
</script>
