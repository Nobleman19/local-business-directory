<?php
require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Service.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Validation.php';
require_once __DIR__ . '/../classes/Helper.php';

class BookingController {
    private $bookingModel;
    private $serviceModel;

    public function __construct() {
        $this->bookingModel = new Booking();
        $this->serviceModel = new Service();
    }

    public function getById() {
        Auth::requireLogin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            return ['error' => 'Booking not found'];
        }

        $booking = $this->bookingModel->getById($id);
        return ['booking' => $booking ?? null];
    }

    public function getUserBookings() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $bookings = $this->bookingModel->getByUserId($user['id'], $limit, $offset);
        return ['bookings' => $bookings, 'page' => $page];
    }

    public function getBusinessBookings() {
        Auth::requireRole('business_owner');
        $user = Auth::getCurrentUser();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Need to get businesses owned by user first, then their bookings
        // This is simplified - in production, might need different approach
        $bookings = [];
        return ['bookings' => $bookings, 'page' => $page];
    }

    public function create() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize input
            $data = Validation::sanitize($_POST);

            // Validate required fields
            $required_fields = ['business_id', 'booking_date', 'booking_time', 'duration', 'customer_name', 'customer_email', 'customer_phone'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    return ['error' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
                }
            }

            // Validate email format
            if (!filter_var($data['customer_email'], FILTER_VALIDATE_EMAIL)) {
                return ['error' => 'Please enter a valid email address'];
            }

            // Validate phone number (at least 7 characters)
            $phone = preg_replace('/[^0-9\-\+\s\(\)]/', '', $data['customer_phone']);
            if (strlen($phone) < 7) {
                return ['error' => 'Please enter a valid phone number'];
            }

            // Validate service if provided (optional)
            if (!empty($data['service_id'])) {
                $service = $this->serviceModel->getById($data['service_id']);
                if (!$service) {
                    return ['error' => 'Service not found'];
                }
            } else {
                $service = null;
            }

            // Validate booking date is at least 2 days in the future
            try {
                $booking_datetime = new DateTime($data['booking_date'] . ' ' . $data['booking_time']);
                $today = new DateTime();
                $today->add(new DateInterval('P2D')); // Add 2 days
                
                if ($booking_datetime < $today) {
                    return ['error' => 'Booking date must be at least 2 days from now'];
                }
            } catch (Exception $e) {
                return ['error' => 'Invalid date or time format'];
            }

            // Prepare booking data
            $booking_data = [
                'user_id' => $user['id'],
                'service_id' => !empty($data['service_id']) ? (int)$data['service_id'] : null,
                'business_id' => (int)$data['business_id'],
                'scheduled_date' => $data['booking_date'] . ' ' . $data['booking_time'],
                'duration' => (int)$data['duration'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'notes' => $data['notes'] ?? '',
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_amount' => $service ? $service['price'] : 0,
                'discount_code' => $data['discount_code'] ?? null
            ];

            // Create the booking
            $result = $this->bookingModel->create($booking_data);

            if ($result['success']) {
                return [
                    'success' => true, 
                    'message' => 'Booking created successfully! Check your email for confirmation.',
                    'id' => $result['id'],
                    'redirect' => '/FindItLocal/php-app/bookings'
                ];
            } else {
                return ['error' => $result['error'] ?? 'Failed to create booking'];
            }
        }

        return [];
    }

    public function updateStatus() {
        Auth::requireLogin();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';

        if (!$id || !in_array($status, ['pending', 'confirmed', 'canceled', 'completed'])) {
            return ['error' => 'Invalid booking or status'];
        }

        $result = $this->bookingModel->update($id, ['status' => $status]);

        if ($result['success']) {
            return ['success' => true, 'message' => 'Booking status updated'];
        } else {
            return ['error' => $result['error']];
        }
    }

    public function cancel() {
        Auth::requireLogin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$id) {
            return ['error' => 'Booking not found'];
        }

        if ($this->bookingModel->cancel($id)) {
            return ['success' => true, 'message' => 'Booking canceled successfully'];
        }

        return ['error' => 'Cannot cancel booking'];
    }
}
?>
