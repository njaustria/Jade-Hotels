<?php 
// Start the session, which is good practice for web applications.
session_start();

// Correct the path to include the database connection file from the 'admin' folder.
include('admin/db_connect.php');

// Get and validate booking ID from URL
$booking_id = intval($_GET['id'] ?? 0);
if($booking_id <= 0) {
    // If no ID is provided, redirect to the home page
    header('Location: index.php');
    exit();
}

// Get booking details from the 'booked' table (for new bookings)
$stmt = $conn->prepare("SELECT * FROM booked WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

// If no booking is found with that ID, redirect to the home page
if(!$booking) {
    echo "Error: Booking not found. The record may have been deleted or the ID is incorrect.";
    exit();
}

// Initialize default values
$category = null;
$rate = 0;
$room_category_name = 'Standard Room'; // Default fallback

// Debug: Check what booking data we have
error_log("Booking data: " . print_r($booking, true));

// Get category information using the 'category' field from booked table
$category_id = $booking['category'] ?? null;

if($category_id) {
    // Fetch the category details from room_categories table
    $stmt_cat = $conn->prepare("SELECT * FROM room_categories WHERE id = ?");
    $stmt_cat->bind_param("i", $category_id);
    $stmt_cat->execute();
    $result_cat = $stmt_cat->get_result();
    $category = $result_cat->fetch_assoc();
    
    if($category) {
        // Get the room category name
        $room_category_name = $category['name'] ?? 'Standard Room';
        
        // Get the rate/price from room_categories table
        $rate = floatval($category['price'] ?? 0);
        
        error_log("Category found: " . print_r($category, true));
        error_log("Rate set to: " . $rate);
    } else {
        error_log("No category found for category_id: " . $category_id);
    }
} else {
    error_log("No category field found in booking data");
}

// If still no rate found, try to get from session or set default
if($rate <= 0) {
    // Check if rate was passed via session
    if(isset($_SESSION['booking_rate'])) {
        $rate = floatval($_SESSION['booking_rate']);
        unset($_SESSION['booking_rate']); // Clear it after use
        error_log("Rate retrieved from session: " . $rate);
    }
    
    // If still no rate, set a default
    if($rate <= 0) {
        $rate = 2500.00; // Default room rate
        error_log("Using default rate: " . $rate);
    }
}

// Calculate the duration of the stay in days
$days = 1; // Default to 1 day
if (isset($booking['check_in']) && isset($booking['check_out'])) {
    $check_in = new DateTime($booking['check_in']);
    $check_out = new DateTime($booking['check_out']);
    $diff = $check_out->diff($check_in);
    $days = max(1, $diff->days);
}

// Calculate total amount based on the rate and duration
$total = $rate * $days;

// Generate a unique receipt number
$receipt_no = 'RCP-' . date('Ymd') . '-' . str_pad($booking['id'], 6, '0', STR_PAD_LEFT);

// Helper function to safely get booking field value
function getBookingField($booking, $field, $default = 'N/A') {
    return htmlspecialchars($booking[$field] ?? $default);
}

// Helper function to format ID type
function formatIdType($idType) {
    if(empty($idType) || $idType === 'N/A') return 'N/A';
    return htmlspecialchars(ucwords(str_replace('_', ' ', $idType)));
}

// Helper function to format payment method
function formatPaymentMethod($paymentMethod) {
    if(empty($paymentMethod) || $paymentMethod === 'N/A') return 'N/A';
    
    switch(strtolower($paymentMethod)) {
        case 'card':
            return 'Credit/Debit Card';
        case 'ewallet':
            return 'E-Wallet';
        case 'cash':
            return 'Cash';
        case 'bank_transfer':
            return 'Bank Transfer';
        default:
            return htmlspecialchars(ucwords(str_replace('_', ' ', $paymentMethod)));
    }
}

// Helper function to get payment status
function getPaymentStatus($booking) {
    // Check if there's a payment status field
    if(isset($booking['payment_status'])) {
        return $booking['payment_status'];
    }
    
    // Check if there's a status field that might indicate payment
    if(isset($booking['status'])) {
        $status = strtolower($booking['status']);
        if(in_array($status, ['paid', 'confirmed', 'completed'])) {
            return 'paid';
        } elseif(in_array($status, ['pending', 'reserved'])) {
            return 'pending';
        }
    }
    
    // Default to pending if payment method exists, otherwise unpaid
    return isset($booking['payment_method']) && !empty($booking['payment_method']) ? 'pending' : 'unpaid';
}

// Get payment information
$payment_method = formatPaymentMethod($booking['payment_method'] ?? '');
$payment_status = getPaymentStatus($booking);

// Format payment status for display
function formatPaymentStatus($status) {
    switch(strtolower($status)) {
        case 'paid':
        case 'completed':
            return '<span style="color: #28a745; font-weight: bold;">✓ Paid</span>';
        case 'pending':
            return '<span style="color: #ffc107; font-weight: bold;">⏳ Pending</span>';
        case 'failed':
            return '<span style="color: #dc3545; font-weight: bold;">✗ Failed</span>';
        default:
            return '<span style="color: #6c757d; font-weight: bold;">○ Unpaid</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?= htmlspecialchars($receipt_no) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #f5f5f5; color: #333; line-height: 1.6; }
        
        .container { max-width: 800px; margin: 20px auto; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; }
        
        .header { background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 26px; margin-bottom: 10px; font-weight: 600; }
        .receipt-no { font-size: 16px; opacity: 0.9; }
        .status { background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 15px; margin-top: 15px; display: inline-block; font-size: 14px; font-weight: 500;}
        
        .hotel-info { padding: 20px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; text-align: center; }
        .hotel-info h2 { font-size: 22px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .hotel-info p { margin-bottom: 5px; color: #555; font-size: 14px; }
        .hotel-info a { color: #dc3545; text-decoration: none; }
        .hotel-info a:hover { text-decoration: underline; }

        .content { padding: 30px; }
        .section { margin-bottom: 25px; }
        .section h3 { color: #dc3545; font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; font-weight: 600; }
        
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 15px; }
        .label { color: #666; font-weight: 500; }
        .value { font-weight: 600; text-align: right; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px; }
        
        .payment-summary { background: #f8f9fa; padding: 20px; border-radius: 6px; border: 1px solid #dee2e6; }
        .total { border-top: 2px solid #dc3545; padding-top: 12px; margin-top: 12px; font-size: 20px; font-weight: bold; }
        
        .payment-info { background: #e3f2fd; border-left: 4px solid #2196f3; border-radius: 4px; padding: 20px; margin-bottom: 20px; }
        .payment-info h4 { color: #1976d2; margin-bottom: 10px; font-weight: 600; }
        .payment-status { font-size: 16px; margin-bottom: 8px; }
        
        .notes { margin-top: 30px; background: #fff3cd; border-left: 4px solid #ffeeba; border-radius: 4px; padding: 20px; }
        .notes h4 { color: #856404; margin-bottom: 10px; font-weight: 600; }
        .notes ul { color: #856404; padding-left: 20px; font-size: 14px; }
        .notes li { margin-bottom: 5px; }
        
        .buttons { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; padding: 20px; border-top: 1px solid #eee; margin-top: 10px;}
        .btn { padding: 12px 25px; border: none; border-radius: 5px; font-size: 15px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s ease; }
        .btn-primary { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-outline { background: transparent; border: 2px solid #dc3545; color: #dc3545; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
        
        .debug-info { background: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; padding: 15px; margin: 15px 0; font-size: 12px; }
        .debug-info h5 { color: #856404; margin-bottom: 10px; }
        
        @media print {
            body { 
                background: white; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
                font-size: 10px; 
                line-height: 1.2; 
            }
            .container { box-shadow: none; margin: 0; width: 100%; border-radius: 0; }
            .buttons, .footer, .debug-info { display: none; }
            .header { background: #c82333 !important; padding: 8px; }
            .header h1 { font-size: 16px; margin-bottom: 3px; }
            .receipt-no { font-size: 11px; }
            .status { padding: 2px 6px; margin-top: 4px; font-size: 10px; }
            .hotel-info { padding: 6px; }
            .hotel-info h2 { font-size: 14px; margin-bottom: 3px; }
            .hotel-info p { margin-bottom: 1px; font-size: 10px; }
            .content { padding: 8px; }
            .section { margin-bottom: 8px; }
            .section h3 { font-size: 12px; margin-bottom: 4px; padding-bottom: 2px; border-bottom: 1px solid #ddd; }
            .grid { gap: 10px; margin-bottom: 8px; }
            .info-row { margin-bottom: 3px; font-size: 11px; }
            .label { font-size: 10px; }
            .value { font-size: 11px; }
            .payment-summary { padding: 6px; }
            .payment-info { padding: 6px; margin-bottom: 8px; }
            .payment-info h4 { margin-bottom: 3px; font-size: 11px; }
            .payment-status { font-size: 11px; margin-bottom: 3px; }
            .total { padding-top: 4px; margin-top: 4px; font-size: 14px; }
            .notes { margin-top: 8px; padding: 6px; }
            .notes h4 { margin-bottom: 3px; font-size: 11px; }
            .notes ul { font-size: 9px; padding-left: 12px; }
            .notes li { margin-bottom: 1px; }
        }
        
        @media (max-width: 768px) {
            .container { margin: 10px; }
            .content { padding: 20px; }
            .grid { grid-template-columns: 1fr; gap: 0; }
            .section { margin-bottom: 30px; }
            .buttons { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🏨 Booking Receipt</h1>
        <div class="receipt-no">Receipt No: <?= htmlspecialchars($receipt_no) ?></div>
        <div class="status">✓ Booking Confirmed</div>
    </div>
    
    <div class="hotel-info">
        <h2>Jade Hotels</h2>
        <p>Evozone Avenue, Nuvali Boulevard, Santa Rosa City, Laguna</p>
        <p>Contact: +6391 345 6789 | Email: <a href="mailto:jadehotels@hotel.com">jadehotels@hotel.com</a></p>
        <p>Website: <a href="http://www.jadehotels.com" target="_blank">www.jadehotels.com</a></p>
    </div>

    <div class="content">
        <!-- Debug information (remove in production) -->
        <?php if($rate <= 2500 || !$category): ?>
        <div class="debug-info">
            <h5>Debug Information (Remove in production):</h5>
            <p><strong>Booking Category Field:</strong> <?= $booking['category'] ?? 'Not found' ?></p>
            <p><strong>Category ID Used:</strong> <?= $category_id ?? 'Not found' ?></p>
            <p><strong>Rate found:</strong> ₱<?= number_format($rate, 2) ?></p>
            <p><strong>Room Category Name:</strong> <?= $room_category_name ?></p>
            <p><strong>Category Data:</strong> <?= $category ? 'Found' : 'Not found' ?></p>
            <p><strong>Payment Method:</strong> <?= $booking['payment_method'] ?? 'Not found' ?></p>
            <p><strong>Payment Status:</strong> <?= $payment_status ?></p>
            <p><strong>Available booking fields:</strong> <?= implode(', ', array_keys($booking)) ?></p>
            <?php if($category): ?>
            <p><strong>Category fields:</strong> <?= implode(', ', array_keys($category)) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Guest & Stay Info -->
        <div class="grid">
            <div class="section">
                <h3>Guest Information</h3>
                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value"><?= getBookingField($booking, 'name') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value"><?= getBookingField($booking, 'email') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Contact:</span>
                    <span class="value"><?= getBookingField($booking, 'contact_number') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">ID Type:</span>
                    <span class="value"><?= formatIdType($booking['id_type'] ?? '') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">ID Number:</span>
                    <span class="value"><?= getBookingField($booking, 'id_number') ?></span>
                </div>
            </div>

            <div class="section">
                <h3>Stay Information</h3>
                <div class="info-row">
                    <span class="label">Check-in:</span>
                    <span class="value"><?= date('M j, Y, g:i A', strtotime($booking['check_in'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Check-out:</span>
                    <span class="value"><?= date('M j, Y, g:i A', strtotime($booking['check_out'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Duration:</span>
                    <span class="value"><?= $days ?> night<?= $days != 1 ? 's' : '' ?></span>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        <?php if($payment_method !== 'N/A' || $payment_status !== 'unpaid'): ?>
        <div class="payment-info">
            <h4>💳 Payment Information</h4>
            <div class="payment-status">
                <strong>Status:</strong> <?= formatPaymentStatus($payment_status) ?>
            </div>
            <?php if($payment_method !== 'N/A'): ?>
            <div class="info-row" style="margin-bottom: 5px;">
                <span class="label">Payment Method:</span>
                <span class="value"><?= $payment_method ?></span>
            </div>
            <?php endif; ?>
            
            <?php if(isset($booking['ewallet_provider']) && !empty($booking['ewallet_provider'])): ?>
            <div class="info-row" style="margin-bottom: 5px;">
                <span class="label">E-Wallet Provider:</span>
                <span class="value"><?= htmlspecialchars(ucwords($booking['ewallet_provider'])) ?></span>
            </div>
            <?php endif; ?>
            
            <?php if(strtolower($payment_status) === 'pending'): ?>
            <div style="font-size: 14px; color: #856404; margin-top: 8px;">
                <strong>Note:</strong> Payment will be processed upon check-in. Please bring your payment method and valid ID.
            </div>
            <?php elseif(strtolower($payment_status) === 'paid'): ?>
            <div style="font-size: 14px; color: #155724; margin-top: 8px;">
                <strong>Note:</strong> Payment has been successfully processed. Thank you!
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Room & Payment Summary -->
        <div class="section payment-summary">
            <h3>Payment Summary</h3>
            <div class="info-row">
                <span class="label">Room Category:</span>
                <span class="value"><?= htmlspecialchars($room_category_name) ?></span>
            </div>
            <div class="info-row">
                <span class="label">Rate per Night:</span>
                <span class="value">₱<?= number_format($rate, 2) ?></span>
            </div>
            <div class="info-row">
                <span>Room Subtotal (<?= $days ?> night<?= $days != 1 ? 's' : '' ?>):</span>
                <span>₱<?= number_format($total, 2) ?></span>
            </div>
            <div class="info-row total">
                <span>Total Amount <?= strtolower($payment_status) === 'paid' ? 'Paid' : 'Due' ?>:</span>
                <span>₱<?= number_format($total, 2) ?></span>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="notes">
            <h4>Important Information</h4>
            <ul>
                <li>Standard Check-in time is 12:00 PM onwards. Standard Check-out time is 12:00 PM.</li>
                <li>Please present the ID used for this booking (<?= formatIdType($booking['id_type'] ?? '') ?>) upon arrival.</li>
                <?php if(strtolower($payment_status) === 'paid'): ?>
                <li>Payment has been successfully processed. This receipt serves as proof of payment.</li>
                <?php else: ?>
                <li>This receipt confirms your booking. You will pay the total amount at the hotel.</li>
                <?php endif; ?>
                <li>Keep this receipt for your records.</li>
                <li>For any changes or cancellations, please contact the hotel at least 24 hours in advance.</li>
                <?php if($payment_method !== 'N/A' && strtolower($payment_status) !== 'paid'): ?>
                <li>Please bring your <?= strtolower($payment_method) ?> for payment during check-in.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="buttons">
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print Receipt</button>
        <a href="index.php" class="btn btn-outline">Back to Home</a>
    </div>

    <div class="footer">
        <p>Thank you for choosing Jade Hotels! We look forward to your stay.</p>
    </div>
</div>

</body>
</html>