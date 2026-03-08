<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Business.php';
require_once __DIR__ . '/../classes/Booking.php';

class OwnerController {
    private $authModel;
    private $businessModel;
    private $bookingModel;

    public function __construct() {
        $this->authModel = new Auth();
        $this->businessModel = new Business();
        $this->bookingModel = new Booking();
    }

    public function viewBusinesses() {
        Auth::requireRole('business_owner');
        $user = Auth::getCurrentUser();

        $businesses = $this->businessModel->getByOwner($user['id']);

        // Get service count and review count for each business
        foreach ($businesses as &$business) {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            // Get service count
            $serviceSQL = "SELECT COUNT(*) as count FROM services WHERE business_id = ?";
            $stmt = $conn->prepare($serviceSQL);
            $stmt->bind_param("i", $business['id']);
            $stmt->execute();
            $serviceResult = $stmt->get_result()->fetch_assoc();
            $business['service_count'] = $serviceResult['count'] ?? 0;

            // Get review count
            $reviewSQL = "SELECT COUNT(*) as count FROM reviews WHERE business_id = ?";
            $stmt = $conn->prepare($reviewSQL);
            $stmt->bind_param("i", $business['id']);
            $stmt->execute();
            $reviewResult = $stmt->get_result()->fetch_assoc();
            $business['review_count'] = $reviewResult['count'] ?? 0;
        }

        return ['businesses' => $businesses];
    }

    public function viewBookings() {
        Auth::requireRole('business_owner');
        $user = Auth::getCurrentUser();
        
        $businessId = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;

        if (!$businessId) {
            return ['error' => 'Business not found'];
        }

        // Verify owner owns this business
        $business = $this->businessModel->getById($businessId);
        if (!$business || $business['owner_id'] !== $user['id']) {
            return ['error' => 'Unauthorized'];
        }

        // Get bookings for this business
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        
        $sql = "SELECT b.*, s.service_name, u.first_name, u.last_name, u.email as customer_email
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                JOIN users u ON b.user_id = u.id
                WHERE s.business_id = ?";

        $params = [$businessId];
        $types = "i";

        if (!empty($status)) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $sql .= " ORDER BY b.scheduled_date DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $bookings = $result->fetch_all(MYSQLI_ASSOC);

        // Format booking data
        foreach ($bookings as &$booking) {
            $booking['customer_name'] = htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']);
        }

        return [
            'business' => $business,
            'bookings' => $bookings
        ];
    }

    public function confirmBooking() {
        Auth::requireRole('business_owner');
        $user = Auth::getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['error' => 'Invalid request'];
        }

        $bookingId = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';

        if (!$bookingId || !in_array($action, ['confirm', 'cancel'])) {
            return ['error' => 'Invalid parameters'];
        }

        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Get booking and verify ownership
        $bookingSQL = "SELECT b.*, s.business_id FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.id = ?";
        $stmt = $conn->prepare($bookingSQL);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();

        if (!$booking) {
            return ['error' => 'Booking not found'];
        }

        // Verify owner owns this business
        $business = $this->businessModel->getById($booking['business_id']);
        if ($business['owner_id'] !== $user['id']) {
            return ['error' => 'Unauthorized'];
        }

        // Update booking status
        $newStatus = ($action === 'confirm') ? 'confirmed' : 'cancelled';
        $updateSQL = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSQL);
        $stmt->bind_param("si", $newStatus, $bookingId);

        if ($stmt->execute()) {
            $message = ($action === 'confirm') ? 'Booking confirmed successfully!' : 'Booking cancelled!';
            return ['success' => true, 'message' => $message];
        }

        return ['error' => 'Failed to update booking'];
    }
}
?>
