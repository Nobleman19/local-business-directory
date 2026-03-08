<?php
require_once __DIR__ . '/../classes/Payment.php';
require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Helper.php';

class PaymentController {
    private $paymentModel;
    private $bookingModel;

    public function __construct() {
        $this->paymentModel = new Payment();
        $this->bookingModel = new Booking();
    }

    public function getById() {
        Auth::requireLogin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            return ['error' => 'Payment not found'];
        }

        $payment = $this->paymentModel->getById($id);
        return ['payment' => $payment ?? null];
    }

    public function getUserPayments() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $payments = $this->paymentModel->getByUserId($user['id'], $limit, $offset);
        return ['payments' => $payments, 'page' => $page];
    }

    public function process() {
        Auth::requireLogin();
        $user = Auth::getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;

            $booking = $this->bookingModel->getById($booking_id);
            if (!$booking || $booking['user_id'] !== $user['id']) {
                return ['error' => 'Booking not found'];
            }

            // In a real application, this would integrate with a payment gateway
            // For now, we'll just create the payment record

            $data = [
                'booking_id' => $booking_id,
                'user_id' => $user['id'],
                'amount' => $booking['total_amount'],
                'payment_method' => $_POST['payment_method'] ?? 'Credit Card',
                'transaction_id' => Helper::generateTransactionId(),
                'payment_status' => 'Completed'
            ];

            $result = $this->paymentModel->create($data);

            if ($result['success']) {
                // Update booking payment status
                $this->bookingModel->update($booking_id, ['payment_status' => 'paid']);
                
                return ['success' => true, 'message' => 'Payment processed successfully', 'id' => $result['id']];
            } else {
                return ['error' => $result['error']];
            }
        }

        return [];
    }

    public function verify() {
        $transaction_id = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : '';

        if (empty($transaction_id)) {
            return ['error' => 'Transaction ID required'];
        }

        $payment = $this->paymentModel->verifyTransaction($transaction_id);

        if ($payment) {
            return ['success' => true, 'payment' => $payment];
        }

        return ['error' => 'Transaction not found'];
    }
}
?>
