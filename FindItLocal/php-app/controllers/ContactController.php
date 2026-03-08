<?php
require_once __DIR__ . '/../classes/Contact.php';
require_once __DIR__ . '/../classes/Validation.php';

class ContactController {
    private $contactModel;

    public function __construct() {
        $this->contactModel = new Contact();
    }

    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = Validation::sanitize($_POST);

            if (empty($data['name']) || empty($data['email']) || 
                empty($data['subject']) || empty($data['message'])) {
                return ['error' => 'All required fields must be filled'];
            }

            if (!Validation::validateEmail($data['email'])) {
                return ['error' => 'Invalid email format'];
            }

            $result = $this->contactModel->sendMessage($data);

            if ($result['success']) {
                return ['success' => true, 'message' => 'Message sent successfully'];
            } else {
                return ['error' => $result['error']];
            }
        }

        return [];
    }

    public function getAll() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $messages = $this->contactModel->getAllMessages($limit, $offset);
        return ['messages' => $messages, 'page' => $page];
    }

    public function getUnread() {
        $messages = $this->contactModel->getUnreadMessages();
        return ['messages' => $messages];
    }
}
?>
