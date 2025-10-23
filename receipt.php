<?php
// Hotel Receipt Generator
// receipt.php

include('db_connect.php');

// Get booking data from database using ID parameter
if(isset($_GET['id']) && $_GET['id']){
    $id = $_GET['id'];
    $qry = $conn->query("SELECT * FROM checked where id =".$id);
    if($qry->num_rows > 0){
        foreach($qry->fetch_array() as $k => $v){
            $$k=$v;
        }
        
        // Get room and category information
        if($room_id > 0){
            $room = $conn->query("SELECT * FROM rooms where id =".$room_id)->fetch_array();
            $cat = $conn->query("SELECT * FROM room_categories where id =".$room['category_id'])->fetch_array();
        }else{
            $cat = $conn->query("SELECT * FROM room_categories where id =".$booked_cid)->fetch_array();
            $room = array('room' => 'N/A');
        }
        
        // Calculate days
        $calc_days = abs(strtotime($date_out) - strtotime($date_in)) ; 
        $calc_days = floor($calc_days / (60*60*24));
        
        // Populate booking data array
        $booking_data = [
            'room_number' => isset($room['room']) ? $room['room'] : 'N/A',
            'category' => $cat['name'],
            'price_per_night' => $cat['price'],
            'guest_name' => $name,
            'contact' => $contact_no,
            'email' => isset($email) ? $email : 'N/A',
            'id_type' => isset($id_type) ? $id_type : 'N/A',
            'id_number' => isset($id_number) ? $id_number : 'N/A',
            'check_in' => $date_in,
            'check_out' => $date_out,
            'days' => $calc_days,
            'total_amount' => $cat['price'] * $calc_days
        ];
    } else {
        // If no data found, redirect or show error
        echo "<script>alert('No booking data found!'); window.close();</script>";
        exit;
    }
} else {
    // If no ID provided, redirect or show error
    echo "<script>alert('No booking ID provided!'); window.close();</script>";
    exit;
}

// Hotel information - You can customize this or get from database
$hotel_info = [
    'name' => 'Jade Hotels',
    'address' => 'Evozone Avenue, Nuvali Boulevard, Santa Rosa City, Laguna',
    'phone' => '+6391 345 6789',
    'email' => 'jadehotels@hotel.com',
    'website' => 'www.jadehotels.com'
];

// Receipt number generation
$receipt_number = 'HPH-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
$receipt_date = date('F j, Y g:i A');

// No tax calculation - direct amount
$subtotal = $booking_data['total_amount'];

// Format currency function
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

// Format date function
function formatDate($datetime) {
    return date('M j, Y g:i A', strtotime($datetime));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Receipt - <?php echo $booking_data['guest_name']; ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .hotel-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .hotel-details {
            color: #7f8c8d;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin: 20px 0;
            text-align: center;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 40px;
            margin-bottom: 30px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #bdc3c7;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .info-value {
            color: #34495e;
        }
        
        .amount-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        
        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #dee2e6;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 2px solid #2c3e50;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .thank-you {
            font-size: 18px;
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .print-btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 20px 0;
            font-size: 16px;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
        
        @media print {
            body { background-color: white; }
            .receipt-container { box-shadow: none; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="hotel-name"><?php echo $hotel_info['name']; ?></div>
            <div class="hotel-details">
                <?php echo $hotel_info['address']; ?><br>
                Tel: <?php echo $hotel_info['phone']; ?> | Email: <?php echo $hotel_info['email']; ?><br>
                Website: <?php echo $hotel_info['website']; ?>
            </div>
        </div>
        
        <!-- Receipt Title -->
        <div class="receipt-title">HOTEL RECEIPT</div>
        
        <!-- Receipt Information -->
        <div class="receipt-info">
            <div>
                <strong>Receipt No:</strong> <?php echo $receipt_number; ?>
            </div>
            <div>
                <strong>Date:</strong> <?php echo $receipt_date; ?>
            </div>
        </div>
        
        <!-- Guest Information -->
        <div class="info-section">
            <div class="info-item">
                <span class="info-label">Guest Name:</span>
                <span class="info-value"><?php echo $booking_data['guest_name']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Contact:</span>
                <span class="info-value"><?php echo $booking_data['contact']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo $booking_data['email']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">ID Type:</span>
                <span class="info-value"><?php echo $booking_data['id_type']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">ID Number:</span>
                <span class="info-value"><?php echo $booking_data['id_number']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Room Number:</span>
                <span class="info-value"><?php echo $booking_data['room_number']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Room Category:</span>
                <span class="info-value"><?php echo $booking_data['category']; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Check-in:</span>
                <span class="info-value"><?php echo formatDate($booking_data['check_in']); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Check-out:</span>
                <span class="info-value"><?php echo formatDate($booking_data['check_out']); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Number of Days:</span>
                <span class="info-value"><?php echo $booking_data['days']; ?> day(s)</span>
            </div>
        </div>
        
        <!-- Amount Breakdown -->
        <div class="amount-section">
            <div class="amount-row">
                <span>Room Rate (<?php echo $booking_data['days']; ?> night × <?php echo formatCurrency($booking_data['price_per_night']); ?>)</span>
                <span><?php echo formatCurrency($subtotal); ?></span>
            </div>
            
            <div class="total-row">
                <span>TOTAL AMOUNT PAID</span>
                <span><?php echo formatCurrency($booking_data['total_amount']); ?></span>
            </div>
        </div>
        
        <!-- Print Button -->
        <div style="text-align: center;">
            <button class="print-btn" onclick="window.print()">Print Receipt</button>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Thank you for staying with us!</div>
            <p>We hope you enjoyed your stay at <?php echo $hotel_info['name']; ?>.</p>
            <p>For any inquiries regarding this receipt, please contact us at <?php echo $hotel_info['phone']; ?></p>
        </div>
    </div>
</body>
</html>