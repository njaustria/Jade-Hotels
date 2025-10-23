<?php
ob_start();

if (!isset($_GET['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    exit;
}

include 'admin_class.php';
$crud = new Action();
$action = $_GET['action'];

// Define action mappings - Added team member actions and update_booking_field
$actions = [
    'login', 'logout', 'save_user', 'save_settings', 'save_category', 'delete_category',
    'save_facility', 'delete_facility', 'save_room', 'delete_room', 'save_check_in',
    'save_checkout', 'save_book', 'update_booking_status', 'get_booking_details', 'delete_booking',
    'save_team_member', 'delete_team_member', 'get_team_member', 'toggle_team_member_status',
    'update_booking_field'
];

try {
    // Handle special cases
    if ($action === 'save_check-in') $action = 'save_check_in';
    
    // Handle get_booking_details action directly here
    if ($action === 'get_booking_details') {
        include 'db_connect.php';
        
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'No booking ID provided']);
            exit;
        }
        
        $id = intval($_POST['id']);
        $booking = $conn->query("SELECT * FROM booked WHERE id = $id");
        
        if (!$booking || $booking->num_rows == 0) {
            echo '<div class="alert alert-warning">Booking not found</div>';
            exit;
        }
        
        $row = $booking->fetch_assoc();
        
        // Get category name
        $cat = $conn->query("SELECT * FROM room_categories WHERE id = ".$row['category']);
        $cat_row = $cat->fetch_assoc();
        
        // Payment method and e-wallet provider arrays
        $payment_method_arr = array('card' => 'Credit/Debit Card', 'ewallet' => 'E-wallet', 'cash' => 'Cash', 'bank_transfer' => 'Bank Transfer');
        $ewallet_provider_arr = array('gcash' => 'GCash', 'paymaya' => 'PayMaya', 'grabpay' => 'GrabPay');
        $payment_status_arr = array('pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded');
        
        // Generate the booking details HTML
        echo '<div class="booking-details">';
        echo '<p><strong>Name:</strong> ' . htmlspecialchars($row['name']) . '</p>';
        echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';
        echo '<p><strong>Contact:</strong> ' . htmlspecialchars($row['contact_number']) . '</p>';
        
        // Add ID Type and ID Number fields
        echo '<p><strong>ID Type:</strong> ' . htmlspecialchars($row['id_type'] ?? 'Not provided') . '</p>';
        echo '<p><strong>ID Number:</strong> ' . htmlspecialchars($row['id_number'] ?? 'Not provided') . '</p>';
        
        echo '<p><strong>Category:</strong> ' . htmlspecialchars($cat_row['name']) . '</p>';
        
        // Payment information
        $payment_method = $row['payment_method'] ?? '';
        $payment_method_display = isset($payment_method_arr[$payment_method]) ? $payment_method_arr[$payment_method] : 'Not specified';
        echo '<p><strong>Payment Method:</strong> ' . htmlspecialchars($payment_method_display) . '</p>';
        
        $payment_status = $row['payment_status'] ?? 'pending';
        $payment_status_display = isset($payment_status_arr[$payment_status]) ? $payment_status_arr[$payment_status] : 'Pending';
        echo '<p><strong>Payment Status:</strong> ' . htmlspecialchars($payment_status_display) . '</p>';
        
        if ($payment_method == 'ewallet' && !empty($row['ewallet_provider'])) {
            $ewallet_provider_display = isset($ewallet_provider_arr[$row['ewallet_provider']]) ? $ewallet_provider_arr[$row['ewallet_provider']] : htmlspecialchars($row['ewallet_provider']);
            echo '<p><strong>E-wallet Provider:</strong> ' . $ewallet_provider_display . '</p>';
        }
        
        echo '<p><strong>Check-in:</strong> ' . date('M d, Y h:i A', strtotime($row['check_in'])) . '</p>';
        echo '<p><strong>Check-out:</strong> ' . date('M d, Y h:i A', strtotime($row['check_out'])) . '</p>';
        
        $status_arr = array(0 => 'Booked', 1 => 'Checked-in', 2 => 'Checked-out', 3 => 'Cancelled');
        echo '<p><strong>Status:</strong> ' . $status_arr[$row['status']] . '</p>';
        echo '</div>';
        
        exit;
    }
    
    // Handle update_booking_field action directly here
    if ($action === 'update_booking_field') {
        include 'db_connect.php';
        
        if (!isset($_POST['booking_id']) || !isset($_POST['field_name']) || !isset($_POST['field_value'])) {
            echo 0;
            exit;
        }
        
        $booking_id = $_POST['booking_id'];
        $field_name = $_POST['field_name'];
        $field_value = $_POST['field_value'];
        
        // Sanitize field name to prevent SQL injection - Added payment_method and ewallet_provider
        $allowed_fields = ['payment_method', 'payment_status', 'ewallet_provider', 'status'];
        if (!in_array($field_name, $allowed_fields)) {
            echo 0;
            exit;
        }
        
        $stmt = $conn->prepare("UPDATE booked SET $field_name = ? WHERE id = ?");
        $stmt->bind_param("si", $field_value, $booking_id);
        
        if ($stmt->execute()) {
            echo 1;
        } else {
            echo 0;
        }
        
        $stmt->close();
        exit;
    }
    
    // Execute action if valid
    if (in_array($action, $actions) && method_exists($crud, $action)) {
        echo $crud->$action();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("AJAX Error in action '$action': " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred']);
}

ob_end_flush();
?>