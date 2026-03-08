<?php
class Helper {
    public static function redirect($url) {
        header('Location: ' . $url);
        exit();
    }

    public static function json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    public static function formatDate($date, $format = 'Y-m-d H:i:s') {
        return date($format, strtotime($date));
    }

    public static function getTimeAgo($date) {
        $timestamp = strtotime($date);
        $difference = time() - $timestamp;

        if ($difference < 60) {
            return "Just now";
        } elseif ($difference < 3600) {
            return floor($difference / 60) . " minutes ago";
        } elseif ($difference < 86400) {
            return floor($difference / 3600) . " hours ago";
        } elseif ($difference < 604800) {
            return floor($difference / 86400) . " days ago";
        } else {
            return date('M d, Y', $timestamp);
        }
    }

    public static function calculateDiscount($original, $discount_percentage) {
        return $original - ($original * $discount_percentage / 100);
    }

    public static function formatCurrency($amount) {
        return 'ZMW ' . number_format($amount, 2);
    }

    public static function generateTransactionId() {
        return 'TXN' . time() . rand(100, 999);
    }

    public static function uploadFile($file, $directory) {
        $validation = Validation::validateFileUpload($file);
        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $directory . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename, 'path' => $filepath];
        }

        return ['success' => false, 'errors' => ['Cannot upload file']];
    }

    public static function getPaginationLinks($currentPage, $totalPages, $baseUrl) {
        $html = '<nav><ul class="pagination">';

        if ($currentPage > 1) {
            $html .= '<li><a href="' . $baseUrl . '?page=1">First</a></li>';
            $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '">Previous</a></li>';
        }

        for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="active"><span>' . $i . '</span></li>';
            } else {
                $html .= '<li><a href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
            }
        }

        if ($currentPage < $totalPages) {
            $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '">Next</a></li>';
            $html .= '<li><a href="' . $baseUrl . '?page=' . $totalPages . '">Last</a></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }

    public static function generateSlug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }
}
?>
