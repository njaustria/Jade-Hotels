<?php
class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php';
        $this->db = $conn;
    }

    function __destruct() {
        $this->db->close();
        ob_end_flush();
    }

    function login() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_POST['username'], $_POST['password'])) return 3;
        
        $username = $this->db->real_escape_string($_POST['username']);
        $password = $this->db->real_escape_string($_POST['password']);
        
        $qry = $this->db->query("SELECT * FROM users WHERE username = '$username' AND password = '$password'");
        if ($qry && $qry->num_rows > 0) {
            $user = $qry->fetch_array();
            foreach ($user as $key => $value) {
                if ($key != 'password' && !is_numeric($key))
                    $_SESSION['login_'.$key] = $value;
            }
            return $_SESSION['login_type'] == 1 ? 1 : 2;
        }
        return 3;
    }

    function logout() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        session_destroy();
        foreach ($_SESSION as $key => $value) unset($_SESSION[$key]);
        header("location:login.php");
    }

    private function getWeeklyStats($table, $date_field, $status_condition = '') {
        $total = $current = $last = 0;
        
        // Total
        $result = $this->db->query("SELECT COUNT(*) as total FROM $table WHERE $status_condition");
        if ($result && $result->num_rows > 0) $total = $result->fetch_array()['total'];
        
        // Current week
        $result = $this->db->query("SELECT COUNT(*) as total FROM $table WHERE $status_condition AND WEEK($date_field) = WEEK(NOW()) AND YEAR($date_field) = YEAR(NOW())");
        if ($result && $result->num_rows > 0) $current = $result->fetch_array()['total'];
        
        // Last week
        $result = $this->db->query("SELECT COUNT(*) as total FROM $table WHERE $status_condition AND WEEK($date_field) = WEEK(NOW()) - 1 AND YEAR($date_field) = YEAR(NOW())");
        if ($result && $result->num_rows > 0) $last = $result->fetch_array()['total'];
        
        $percentage = $last > 0 ? (($current - $last) / $last) * 100 : ($current > 0 ? 100 : 0);
        return ['total' => $total, 'percentage_change' => round($percentage, 1)];
    }

    function getTotalRevenue() {
        $total = $current = $last = 0;
        
        // Total revenue
        $query = "SELECT SUM(rc.price * DATEDIFF(c.date_out, c.date_in)) as total_revenue FROM checked c JOIN rooms r ON c.room_id = r.id JOIN room_categories rc ON r.category_id = rc.id WHERE c.status = 2";
        $result = $this->db->query($query);
        if ($result && $result->num_rows > 0) $total = $result->fetch_array()['total_revenue'] ?: 0;
        
        // Current week
        $result = $this->db->query($query . " AND WEEK(c.date_out) = WEEK(NOW()) AND YEAR(c.date_out) = YEAR(NOW())");
        if ($result && $result->num_rows > 0) $current = $result->fetch_array()['total_revenue'] ?: 0;
        
        // Last week
        $result = $this->db->query($query . " AND WEEK(c.date_out) = WEEK(NOW()) - 1 AND YEAR(c.date_out) = YEAR(NOW())");
        if ($result && $result->num_rows > 0) $last = $result->fetch_array()['total_revenue'] ?: 0;
        
        $percentage = $last > 0 ? (($current - $last) / $last) * 100 : ($current > 0 ? 100 : 0);
        return ['total' => $total, 'percentage_change' => round($percentage, 1)];
    }

    function getCheckIns() {
        return $this->getWeeklyStats('checked', 'date_in', 'status = 1');
    }

    function getCheckOuts() {
        return $this->getWeeklyStats('checked', 'date_out', 'status = 2');
    }

    // New function to get Total Booked rooms
    function getTotalBooked() {
        // Assuming 'booked' table has a 'status' field where 0 means 'Booked'
        // and 'check_in' field is the relevant date for weekly stats.
        return $this->getWeeklyStats('booked', 'check_in', 'status = 0');
    }

    function save_user() {
        extract($_POST);
        $data = "name = '".$this->db->real_escape_string($name)."', username = '".$this->db->real_escape_string($username)."', password = '".$this->db->real_escape_string($password)."', type = '".intval($type)."'";
        
        $query = empty($id) ? "INSERT INTO users SET $data" : "UPDATE users SET $data WHERE id = ".intval($id);
        return $this->db->query($query) ? 1 : 0;
    }

    function save_settings() {
        extract($_POST);
        $data = "hotel_name = '".$this->db->real_escape_string($name)."', email = '".$this->db->real_escape_string($email)."', contact = '".$this->db->real_escape_string($contact)."', about_content = '".htmlentities(str_replace("'","&#x2019;",$this->db->real_escape_string($about)))."'";
        
        if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] && !$_FILES['img']['error']) {
            $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
            if (move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'.$fname)) {
                $data .= ", cover_img = '$fname'";
            }
        }
        
        $chk = $this->db->query("SELECT * FROM system_settings");
        $query = $chk->num_rows > 0 ? "UPDATE system_settings SET $data WHERE id = ".$chk->fetch_array()['id'] : "INSERT INTO system_settings SET $data";
        
        if ($this->db->query($query)) {
            $settings = $this->db->query("SELECT * FROM system_settings LIMIT 1")->fetch_array();
            foreach ($settings as $key => $value) {
                if (!is_numeric($key)) $_SESSION['setting_'.$key] = $value;
            }
            return 1;
        }
        return 0;
    }

    function save_category() {
        extract($_POST);
        $data = "name = '".$this->db->real_escape_string($name)."', price = '".floatval($price)."', description = '".$this->db->real_escape_string($description ?? '')."', amenities = '".$this->db->real_escape_string($amenities ?? '')."'";
        
        if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] && !$_FILES['img']['error']) {
            $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
            if (move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'.$fname)) {
                $data .= ", cover_img = '$fname'";
            }
        }
        
        $query = empty($id) ? "INSERT INTO room_categories SET $data" : "UPDATE room_categories SET $data WHERE id = ".intval($id);
        return $this->db->query($query) ? 1 : 0;
    }

    function delete_category() {
        return isset($_POST['id']) && $this->db->query("DELETE FROM room_categories WHERE id = ".intval($_POST['id'])) ? 1 : 0;
    }

    function save_facility() {
        extract($_POST);
        $data = "facility_name = '".$this->db->real_escape_string($facility_name)."', description = '".$this->db->real_escape_string($description)."', operating_hours = '".$this->db->real_escape_string($operating_hours)."', location = '".$this->db->real_escape_string($location)."', date_updated = NOW()";
        
        $upload_dir = '../assets/img/facilities/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        if (isset($_FILES['image']) && $_FILES['image']['tmp_name'] && !$_FILES['image']['error']) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $fname = 'facility_'.time().'_'.uniqid().'.'.$ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir.$fname)) {
                    if (!empty($id)) {
                        $old = $this->db->query("SELECT image FROM hotel_facilities WHERE id = ".intval($id));
                        if ($old && $old->num_rows > 0) {
                            $old_img = $old->fetch_array()['image'];
                            if ($old_img && file_exists($upload_dir.$old_img)) unlink($upload_dir.$old_img);
                        }
                    }
                    $data .= ", image = '$fname'";
                }
            }
        }
        
        $query = empty($id) ? "INSERT INTO hotel_facilities SET $data" : "UPDATE hotel_facilities SET $data WHERE id = ".intval($id);
        return $this->db->query($query) ? 1 : 0;
    }

    function delete_facility() {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) return 0;
        
        $get_image = $this->db->query("SELECT image FROM hotel_facilities WHERE id = $id");
        $image_file = $get_image && $get_image->num_rows > 0 ? $get_image->fetch_array()['image'] : '';
        
        if ($this->db->query("DELETE FROM hotel_facilities WHERE id = $id")) {
            if ($image_file && file_exists('../assets/img/facilities/'.$image_file)) {
                unlink('../assets/img/facilities/'.$image_file);
            }
            return 1;
        }
        return 0;
    }

    function save_room() {
        extract($_POST);
        // Added 'status' to the data string
        $data = "room = '".$this->db->real_escape_string($room)."', category_id = '".intval($category_id)."', status = '".intval($status)."'";
        $query = empty($id) ? "INSERT INTO rooms SET $data" : "UPDATE rooms SET $data WHERE id = ".intval($id);
        return $this->db->query($query) ? 1 : 0;
    }

    function delete_room() {
        return isset($_POST['id']) && $this->db->query("DELETE FROM rooms WHERE id = ".intval($_POST['id'])) ? 1 : 0;
    }

    function save_check_in() {
    extract($_POST);
    
    // Calculate check-out datetime
    $check_in_datetime = $date_in . ' ' . ($date_in_time ?? '12:00');
    $check_out_datetime = $date_out . ' ' . ($date_out_time ?? '12:00');
    
    // Prepare data with all required fields
    $data = "room_id = '" . intval($rid) . "', " .
            "name = '" . $this->db->real_escape_string($name) . "', " .
            "email = '" . $this->db->real_escape_string($email ?? '') . "', " .
            "contact_no = '" . $this->db->real_escape_string($contact) . "', " .
            "id_type = '" . $this->db->real_escape_string($id_type ?? '') . "', " .
            "id_number = '" . $this->db->real_escape_string($id_number ?? '') . "', " .
            "status = 1, " .
            "date_in = '" . $check_in_datetime . "', " .
            "date_out = '" . $check_out_datetime . "'";
    
    if (empty($id)) {
        // Insert new check-in record
        if ($this->db->query("INSERT INTO checked SET $data")) {
            $id = $this->db->insert_id;
            // Update room status to occupied
            $this->db->query("UPDATE rooms SET status = 1 WHERE id = " . intval($rid));
            return $id;
        }
    } else {
        // Update existing check-in record
        if ($this->db->query("UPDATE checked SET $data WHERE id = " . intval($id))) {
            // Update room status to occupied
            $this->db->query("UPDATE rooms SET status = 1 WHERE id = " . intval($rid));
            return intval($id);
        }
    }
    return 0;
}

function save_checkout() {
    extract($_POST);
    
    // If checkout_date is provided, use it; otherwise use current timestamp
    $checkout_date = isset($checkout_date) ? $checkout_date : date('Y-m-d H:i:s');
    
    // Update the checked record with checkout status and actual checkout date
    $update_query = "UPDATE checked SET 
                     status = 2, 
                     date_out = '" . $this->db->real_escape_string($checkout_date) . "' 
                     WHERE id = " . intval($id);
    
    if ($this->db->query($update_query)) {
        // Update room status to available
        $this->db->query("UPDATE rooms SET status = 0 WHERE id = " . intval($rid));
        return 1;
    }
    return 0;
}

    function save_book() {
        // Extract all POST data into variables
        extract($_POST);
    
        // Construct check-in and check-out datetime from form inputs
        $check_in_datetime = $date_in . ' ' . ($date_in_time ?? '12:00:00');
        $check_out_datetime = $date_out . ' ' . ($date_out_time ?? '12:00:00');
    
        // Basic booking data
        $data = "name = '".$this->db->real_escape_string($name)."', " .
                "email = '".$this->db->real_escape_string($email)."', " .
                "contact_number = '".$this->db->real_escape_string($contact)."', " .
                "category = '".intval($cid)."', " .
                "check_in = '$check_in_datetime', " .
                "check_out = '$check_out_datetime', " .
                "id_type = '".$this->db->real_escape_string($id_type ?? '')."', " .
                "id_number = '".$this->db->real_escape_string($id_number ?? '')."', " .
                "status = 0"; // Default status is Booked
    
        // Add payment information if available
        if (!empty($payment_method)) {
            $data .= ", payment_method = '".$this->db->real_escape_string($payment_method)."'";
    
            // If payment method is 'card', save card number
            if ($payment_method == 'card' && isset($card_number)) {
                $sanitized_card_number = str_replace(' ', '', $card_number);
                $data .= ", card_number = '".$this->db->real_escape_string($sanitized_card_number)."'";
            }
    
            // If payment method is 'ewallet', save provider and account number
            if ($payment_method == 'ewallet') {
                if (isset($ewallet_provider)) {
                     $data .= ", ewallet_provider = '".$this->db->real_escape_string($ewallet_provider)."'";
                }
                if (isset($ewallet_number)) { // 'ewallet_number' from the form corresponds to 'account_number' in DB
                     $data .= ", account_number = '".$this->db->real_escape_string($ewallet_number)."'";
                }
            }
        }
    
        // Default payment status to 'paid' as per the original schema design
        $data .= ", payment_status = 'paid'";
    
        // Build and execute the final query
        $query = "INSERT INTO booked SET $data";
        $save = $this->db->query($query);
    
        if ($save) {
            return $this->db->insert_id; // Return the new booking ID on success
        } else {
            // For debugging purposes, you can log the database error
            error_log("SQL Error in save_book: " . $this->db->error);
            return 0; // Return 0 on failure
        }
    }
    
    // Add this method to your admin_class.php file inside the Action class

function update_booking_status() {
    // Extract data from POST request
    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
    
    // Validate inputs
    if ($booking_id <= 0) {
        return json_encode(['status' => 'error', 'message' => 'Invalid booking ID']);
    }
    
    if (!in_array($status, [0, 1, 2, 3])) {
        return json_encode(['status' => 'error', 'message' => 'Invalid status']);
    }
    
    try {
        // Prepare and execute the update query
        $stmt = $this->db->prepare("UPDATE booked SET status = ? WHERE id = ?");
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return 0;
        }
        
        $stmt->bind_param("ii", $status, $booking_id);
        $result = $stmt->execute();
        
        if ($result) {
            // Check if any rows were actually updated
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return 1; // Success
            } else {
                error_log("No rows affected for booking ID: " . $booking_id);
                $stmt->close();
                return 0; // No rows updated (booking might not exist)
            }
        } else {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return 0;
        }
        
    } catch (Exception $e) {
        error_log("Exception in update_booking_status: " . $e->getMessage());
        return 0;
    }
}

function get_booking_details() {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        return '<div class="alert alert-danger">Invalid booking ID</div>';
    }
    
    try {
        $stmt = $this->db->prepare("SELECT b.*, rc.name as category_name FROM booked b LEFT JOIN room_categories rc ON b.category = rc.id WHERE b.id = ?");
        
        if (!$stmt) {
            return '<div class="alert alert-danger">Database error</div>';
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $status_arr = array(0 => 'Booked', 1 => 'Checked-in', 2 => 'Checked-out', 3 => 'Cancelled');
            
            $html = '<div class="booking-details">';
            $html .= '<p><strong>Name:</strong> ' . htmlspecialchars($row['name']) . '</p>';
            $html .= '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';
            $html .= '<p><strong>Contact:</strong> ' . htmlspecialchars($row['contact_number']) . '</p>';
            $html .= '<p><strong>Category:</strong> ' . htmlspecialchars($row['category_name']) . '</p>';
            $html .= '<p><strong>Check-in:</strong> ' . date('M d, Y h:i A', strtotime($row['check_in'])) . '</p>';
            $html .= '<p><strong>Check-out:</strong> ' . date('M d, Y h:i A', strtotime($row['check_out'])) . '</p>';
            $html .= '<p><strong>Status:</strong> ' . $status_arr[$row['status']] . '</p>';
            
            if (!empty($row['additional_request'])) {
                $html .= '<p><strong>Additional Requests:</strong> ' . htmlspecialchars($row['additional_request']) . '</p>';
            }
            
            $html .= '</div>';
            
            $stmt->close();
            return $html;
        } else {
            $stmt->close();
            return '<div class="alert alert-danger">Booking not found</div>';
        }
        
    } catch (Exception $e) {
        error_log("Exception in get_booking_details: " . $e->getMessage());
        return '<div class="alert alert-danger">An error occurred</div>';
    }
}

function delete_booking() {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        return 0;
    }
    
    try {
        $stmt = $this->db->prepare("DELETE FROM booked WHERE id = ?");
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return 0;
        }
        
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        
        if ($result && $stmt->affected_rows > 0) {
            $stmt->close();
            return 1;
        } else {
            $stmt->close();
            return 0;
        }
        
    } catch (Exception $e) {
        error_log("Exception in delete_booking: " . $e->getMessage());
        return 0;
    }
  }
// Add these methods to your existing Action class in admin_class.php

// Team member management functions
function save_team_member() {
    extract($_POST);
    $data = "name = '".$this->db->real_escape_string($name)."', position = '".$this->db->real_escape_string($position)."', bio = '".$this->db->real_escape_string($bio)."', display_order = '".intval($display_order ?? 0)."'";
    
    // Handle social media links
    if (isset($linkedin_url)) {
        $data .= ", linkedin_url = '".$this->db->real_escape_string($linkedin_url)."'";
    }
    if (isset($twitter_url)) {
        $data .= ", twitter_url = '".$this->db->real_escape_string($twitter_url)."'";
    }
    
    // Handle image upload
    $upload_dir = '../assets/img/team/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name'] && !$_FILES['image']['error']) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $fname = 'team_'.time().'_'.uniqid().'.'.$ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir.$fname)) {
                // Delete old image if updating
                if (!empty($id)) {
                    $old = $this->db->query("SELECT image FROM team_members WHERE id = ".intval($id));
                    if ($old && $old->num_rows > 0) {
                        $old_img = $old->fetch_array()['image'];
                        if ($old_img && file_exists($upload_dir.$old_img)) {
                            unlink($upload_dir.$old_img);
                        }
                    }
                }
                $data .= ", image = '$fname'";
            }
        }
    }
    
    $query = empty($id) ? "INSERT INTO team_members SET $data" : "UPDATE team_members SET $data WHERE id = ".intval($id);
    return $this->db->query($query) ? 1 : 0;
}

function delete_team_member() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) return 0;
    
    // Get image file to delete
    $get_image = $this->db->query("SELECT image FROM team_members WHERE id = $id");
    $image_file = $get_image && $get_image->num_rows > 0 ? $get_image->fetch_array()['image'] : '';
    
    if ($this->db->query("DELETE FROM team_members WHERE id = $id")) {
        if ($image_file && file_exists('../assets/img/team/'.$image_file)) {
            unlink('../assets/img/team/'.$image_file);
        }
        return 1;
    }
    return 0;
}

function get_team_member() {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) return 0;
    
    $qry = $this->db->query("SELECT * FROM team_members WHERE id = $id");
    if ($qry && $qry->num_rows > 0) {
        return json_encode($qry->fetch_array());
    }
    return 0;
}



function toggle_team_member_status() {
    $id = intval($_POST['id'] ?? 0);
    $status = intval($_POST['status'] ?? 0);
    
    if ($id <= 0) return 0;
    
    return $this->db->query("UPDATE team_members SET status = $status WHERE id = $id") ? 1 : 0;
}
}
?>