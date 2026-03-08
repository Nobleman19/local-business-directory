<?php
/**
 * Admin Controller
 * Handles all admin-related operations for business and user management
 */
require_once __DIR__ . '/../classes/Business.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Review.php';
require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../config/Database.php';

class AdminController {
    private $business;
    private $user;
    private $review;
    private $booking;
    private $db;
    
    public function __construct() {
        $this->business = new Business();
        $this->user = new User();
        $this->review = new Review();
        $this->booking = new Booking();
        $this->db = Database::getInstance();
    }
    
    /**
     * Display admin dashboard
     */
    public function dashboard() {
        if (!Auth::isAdmin()) {
            return ['error' => 'Unauthorized'];
        }
        
        $conn = $this->db->getConnection();
        
        return [
            'total_businesses' => $this->getTotalBusinesses($conn),
            'total_users' => $this->getTotalUsers($conn),
            'total_reviews' => $this->getTotalReviews($conn),
            'pending_bookings' => $this->getPendingBookings($conn),
            'unverified_businesses' => $this->getUnverifiedBusinesses($conn),
            'recent_reviews' => $this->getRecentReviews($conn, 5),
            'recent_bookings' => $this->getRecentBookings($conn, 5),
            'page_title' => 'Admin Dashboard'
        ];
    }
    
    /**
     * Get all businesses with filters
     */
    public function getBusinesses($page = 1, $search = '', $status = 'all') {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $conn = $this->db->getConnection();
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT b.*, u.first_name, u.last_name, u.email 
                  FROM businesses b 
                  LEFT JOIN users u ON b.owner_id = u.id
                  WHERE 1=1";
        
        if (!empty($search)) {
            $search = $conn->real_escape_string($search);
            $query .= " AND (b.business_name LIKE '%$search%' OR b.location LIKE '%$search%' OR u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%')";
        }
        
        if ($status === 'verified') {
            $query .= " AND b.is_verified = 1";
        } elseif ($status === 'unverified') {
            $query .= " AND b.is_verified = 0";
        } elseif ($status === 'inactive') {
            $query .= " AND b.is_active = 0";
        }
        
        $query .= " ORDER BY b.created_at DESC LIMIT $offset, $limit";
        
        $result = $conn->query($query);
        $businesses = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        return [
            'businesses' => $businesses,
            'total' => $this->countBusinesses($conn, $search, $status),
            'page' => $page,
            'limit' => $limit
        ];
    }
    
    /**
     * Update business status
     */
    public function updateBusinessStatus($business_id, $is_verified, $is_active) {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $conn = $this->db->getConnection();
        $business_id = (int)$business_id;
        $is_verified = $is_verified ? 1 : 0;
        $is_active = $is_active ? 1 : 0;
        
        $query = "UPDATE businesses SET is_verified = $is_verified, is_active = $is_active WHERE id = $business_id";
        
        return $conn->query($query);
    }
    
    /**
     * Delete business
     */
    public function deleteBusiness($business_id) {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $business_id = (int)$business_id;
        $conn = $this->db->getConnection();
        
        $conn->query("DELETE FROM reviews WHERE business_id = $business_id");
        $conn->query("DELETE FROM bookings WHERE business_id = $business_id");
        $conn->query("DELETE FROM services WHERE business_id = $business_id");
        $conn->query("DELETE FROM business_categories WHERE business_id = $business_id");
        $conn->query("DELETE FROM business_images WHERE business_id = $business_id");
        
        return $conn->query("DELETE FROM businesses WHERE id = $business_id");
    }
    
    /**
     * Get all users with filters
     */
    public function getUsers($page = 1, $search = '', $role = 'all') {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $conn = $this->db->getConnection();
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT u.* FROM users u WHERE u.role != 'admin'";
        
        if (!empty($search)) {
            $search = $conn->real_escape_string($search);
            $query .= " AND (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR u.email LIKE '%$search%' OR u.phone_number LIKE '%$search%')";
        }
        
        if ($role !== 'all') {
            $role = $conn->real_escape_string($role);
            $query .= " AND u.role = '$role'";
        }
        
        $query .= " ORDER BY u.created_at DESC LIMIT $offset, $limit";
        
        $result = $conn->query($query);
        $users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        return [
            'users' => $users,
            'total' => $this->countUsers($conn, $search, $role),
            'page' => $page,
            'limit' => $limit
        ];
    }
    
    /**
     * Get all reviews with moderation options
     */
    public function getReviews($page = 1, $status = 'all') {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $conn = $this->db->getConnection();
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT r.*, b.business_name, u.first_name, u.last_name, s.service_name
                  FROM reviews r
                  LEFT JOIN businesses b ON r.business_id = b.id
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN services s ON r.service_id = s.id
                  WHERE 1=1";
        
        if ($status === 'pending') {
            $query .= " AND r.is_approved = 0";
        } elseif ($status === 'approved') {
            $query .= " AND r.is_approved = 1";
        }
        
        $query .= " ORDER BY r.created_at DESC LIMIT $offset, $limit";
        
        $result = $conn->query($query);
        $reviews = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        return [
            'reviews' => $reviews,
            'total' => $this->countReviews($conn, $status),
            'page' => $page,
            'limit' => $limit
        ];
    }
    
    /**
     * Update review status
     */
    public function updateReviewStatus($review_id, $is_approved) {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $conn = $this->db->getConnection();
        $review_id = (int)$review_id;
        $is_approved = $is_approved ? 1 : 0;
        
        $query = "UPDATE reviews SET is_approved = $is_approved WHERE id = $review_id";
        
        return $conn->query($query);
    }
    
    /**
     * Delete review
     */
    public function deleteReview($review_id) {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $conn = $this->db->getConnection();
        $review_id = (int)$review_id;
        
        $query = "DELETE FROM reviews WHERE id = $review_id";
        
        return $conn->query($query);
    }
    
    /**
     * Get all bookings with status
     */
    public function getBookings($page = 1, $status = 'all') {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $conn = $this->db->getConnection();
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT b.*, u.first_name, u.last_name, u.email, s.service_name, bus.business_name
                  FROM bookings b
                  LEFT JOIN users u ON b.user_id = u.id
                  LEFT JOIN services s ON b.service_id = s.id
                  LEFT JOIN businesses bus ON b.business_id = bus.id
                  WHERE 1=1";
        
        if ($status !== 'all') {
            $status = $conn->real_escape_string($status);
            $query .= " AND b.status = '$status'";
        }
        
        $query .= " ORDER BY b.created_at DESC LIMIT $offset, $limit";
        
        $result = $conn->query($query);
        $bookings = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        return [
            'bookings' => $bookings,
            'total' => $this->countBookings($conn, $status),
            'page' => $page,
            'limit' => $limit
        ];
    }
    
    /**
     * Update booking status
     */
    public function updateBookingStatus($booking_id, $status) {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            return false;
        }
        
        $conn = $this->db->getConnection();
        $booking_id = (int)$booking_id;
        $status = $conn->real_escape_string($status);
        
        $query = "UPDATE bookings SET status = '$status' WHERE id = $booking_id";
        
        return $conn->query($query);
    }
    
    /**
     * Get dashboard statistics
     */
    private function getTotalBusinesses($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM businesses");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getTotalUsers($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getTotalReviews($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM reviews");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getPendingBookings($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getUnverifiedBusinesses($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM businesses WHERE is_verified = 0");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getRecentReviews($conn, $limit = 5) {
        $limit = (int)$limit;
        $query = "SELECT r.*, b.business_name, u.first_name, u.last_name
                  FROM reviews r
                  LEFT JOIN businesses b ON r.business_id = b.id
                  LEFT JOIN users u ON r.user_id = u.id
                  ORDER BY r.created_at DESC LIMIT $limit";
        
        $result = $conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getRecentBookings($conn, $limit = 5) {
        $limit = (int)$limit;
        $query = "SELECT b.*, u.first_name, u.last_name, s.service_name, bus.business_name
                  FROM bookings b
                  LEFT JOIN users u ON b.user_id = u.id
                  LEFT JOIN services s ON b.service_id = s.id
                  LEFT JOIN businesses bus ON b.business_id = bus.id
                  ORDER BY b.created_at DESC LIMIT $limit";
        
        $result = $conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function countBusinesses($conn, $search = '', $status = 'all') {
        $query = "SELECT COUNT(*) as count FROM businesses WHERE 1=1";
        
        if (!empty($search)) {
            $search = $conn->real_escape_string($search);
            $query .= " AND (business_name LIKE '%$search%' OR location LIKE '%$search%')";
        }
        
        if ($status === 'verified') {
            $query .= " AND is_verified = 1";
        } elseif ($status === 'unverified') {
            $query .= " AND is_verified = 0";
        } elseif ($status === 'inactive') {
            $query .= " AND is_active = 0";
        }
        
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }
    
    private function countUsers($conn, $search = '', $role = 'all') {
        $query = "SELECT COUNT(*) as count FROM users WHERE role != 'admin'";
        
        if (!empty($search)) {
            $search = $conn->real_escape_string($search);
            $query .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%' OR phone_number LIKE '%$search%')";
        }
        
        if ($role !== 'all') {
            $role = $conn->real_escape_string($role);
            $query .= " AND role = '$role'";
        }
        
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }
    
    private function countReviews($conn, $status = 'all') {
        $query = "SELECT COUNT(*) as count FROM reviews";
        
        if ($status === 'pending') {
            $query .= " WHERE is_approved = 0";
        } elseif ($status === 'approved') {
            $query .= " WHERE is_approved = 1";
        }
        
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }
    
    private function countBookings($conn, $status = 'all') {
        $query = "SELECT COUNT(*) as count FROM bookings";
        
        if ($status !== 'all') {
            $status = $conn->real_escape_string($status);
            $query .= " WHERE status = '$status'";
        }
        
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }
}
