<?php 
include('db_connect.php');
$cat = $conn->query("SELECT * FROM room_categories");
$cat_arr = array();
while($row = $cat->fetch_assoc()) $cat_arr[$row['id']] = $row;

$status_arr = array(0 => 'Booked', 1 => 'Checked-in', 2 => 'Checked-out', 3 => 'Cancelled');
$payment_status_arr = array('paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded');
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card mt-3">
			<div class="card-header">
				<h4>Booked Rooms</h4>
				<p class="text-muted mb-0">Manage room bookings and booking status</p>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped" id="bookings-table">
						<thead class="thead-dark">
							<tr>
								<th width="3%">#</th>
								<th width="12%">Guest Info</th>
								<th width="8%">Contact</th>
								<th width="10%">ID Details</th>
								<th width="10%">Category</th>
								<th width="8%">Payment</th>
								<th width="12%">Check-in</th>
								<th width="12%">Check-out</th>
								<th width="10%">Status</th>
								<th width="15%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$i = 1;
							$booked = $conn->query("SELECT * FROM booked ORDER BY check_in ASC, id ASC");
							while($row = $booked->fetch_assoc()): ?>
							<tr>
								<td><?= $i++ ?></td>
								<td>
									<div class="guest-info">
										<strong><?= htmlspecialchars($row['name']) ?></strong><br>
										<small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
									</div>
								</td>
								<td>
									<small><?= htmlspecialchars($row['contact_number']) ?></small>
								</td>
								<td>
									<div class="id-details">
										<small>
											<strong><?= htmlspecialchars($row['id_type'] ?? 'N/A') ?></strong><br>
											<?= htmlspecialchars($row['id_number'] ?? 'Not provided') ?>
										</small>
									</div>
								</td>
								<td>
									<span class="badge badge-info"><?= htmlspecialchars($cat_arr[$row['category']]['name']) ?></span>
								</td>
								<td>
									<?php 
									$payment_status = $row['payment_status'] ?? 'paid';
									$badge_class = '';
									switch($payment_status) {
										case 'paid': $badge_class = 'badge-success'; break;
										case 'pending': $badge_class = 'badge-warning'; break;
										case 'failed': $badge_class = 'badge-danger'; break;
										case 'refunded': $badge_class = 'badge-secondary'; break;
										default: $badge_class = 'badge-light';
									}
									?>
									<span class="badge <?= $badge_class ?>">
										<?= isset($payment_status_arr[$payment_status]) ? $payment_status_arr[$payment_status] : 'Unknown' ?>
									</span>
								</td>
								<td>
									<small><?= date('M d, Y', strtotime($row['check_in'])) ?><br>
									<?= date('h:i A', strtotime($row['check_in'])) ?></small>
								</td>
								<td>
									<small><?= date('M d, Y', strtotime($row['check_out'])) ?><br>
									<?= date('h:i A', strtotime($row['check_out'])) ?></small>
								</td>
								<td>
									<select class="form-control form-control-sm status-select" 
											data-booking-id="<?= $row['id'] ?>" data-original-status="<?= $row['status'] ?>">
										<?php foreach($status_arr as $status_id => $status_name): ?>
											<option value="<?= $status_id ?>" <?= ($row['status'] == $status_id) ? 'selected' : '' ?>>
												<?= $status_name ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<button class="btn btn-sm btn-danger" onclick="deleteBooking(<?= $row['id'] ?>)" title="Delete Booking">
										<i class="fa fa-trash"></i> Delete
									</button>
								</td>
							</tr>
							<?php endwhile; ?>
							
							<?php if($booked->num_rows == 0): ?>
							<tr><td colspan="10" class="text-center text-muted py-4"><i class="fa fa-info-circle"></i> No bookings found</td></tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title">
					<i class="fa fa-eye mr-2"></i>Booking Details
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" id="booking-details">
				</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					<i class="fa fa-times mr-1"></i>Close
				</button>
			</div>
		</div>
	</div>
</div>

<style>
/* Table Styling */
.table {
	font-size: 13px;
	margin-bottom: 0;
}

.table th {
	background-color: #343a40;
	color: white;
	font-weight: 600;
	text-align: center;
	vertical-align: middle;
	padding: 10px 8px;
	border: 1px solid #454d55;
}

.table td {
	vertical-align: middle;
	padding: 10px 8px;
	border: 1px solid #dee2e6;
}

.table-responsive {
	border-radius: 0.375rem;
	overflow: hidden;
}

/* Guest Info Styling */
.guest-info strong {
	font-size: 14px;
	color: #2c3e50;
}

.guest-info small {
	font-size: 11px;
}

.id-details {
	line-height: 1.3;
}

/* Card Styling */
.card {
	border: none;
	box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
	background: linear-gradient(135deg,rgb(135, 135, 135) 0%,rgb(135, 135, 135) 100%);
	color: white;
	border-bottom: none;
}

.card-header h4 {
	color: white;
	margin-bottom: 0;
}

/* Status Select Styling */
.status-select {
	min-width: 100px;
	font-size: 12px;
	transition: all 0.3s ease;
}

.status-select:focus {
	border-color: #007bff;
	box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.status-select.changed {
	border-color: #28a745;
	background-color: #f8fff9;
}

/* Button Styling */
.btn-sm {
	font-size: 12px;
	padding: 6px 12px;
}

.btn-danger {
	background-color: #dc3545;
	border-color: #dc3545;
}

/* Badge Styling */
.badge {
	padding: 0.4em 0.6em;
	font-size: 11px;
	font-weight: 600;
	border-radius: 0.375rem;
}

.badge-info {
	background-color: #17a2b8;
	color: white;
}

.badge-success {
	background-color: #28a745;
	color: white;
}

.badge-warning {
	background-color: #ffc107;
	color: #212529;
}

.badge-danger {
	background-color: #dc3545;
	color: white;
}

.badge-secondary {
	background-color: #6c757d;
	color: white;
}

.badge-light {
	background-color: #f8f9fa;
	color: #212529;
	border: 1px solid #dee2e6;
}

/* Alert Styling */
.alert {
	margin-top: 10px;
	padding: 10px;
	border-radius: 0.375rem;
}

.alert-success {
	background-color: #d4edda;
	color: #155724;
	border: 1px solid #c3e6cb;
}

.alert-error {
	background-color: #f8d7da;
	color: #721c24;
	border: 1px solid #f5c6cb;
}

/* Modal Styling */
.modal-header.bg-primary {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.booking-details-container {
	padding: 10px 0;
}

.booking-details-container h6 {
	color: #667eea;
	font-weight: 600;
	margin-bottom: 15px;
	padding-bottom: 5px;
	border-bottom: 2px solid #f8f9fa;
}

.booking-details-container .table-borderless td {
	padding: 8px 10px;
	border: none;
}

.booking-details-container .table-borderless td:first-child {
	width: 120px;
	color: #6c757d;
	font-weight: 500;
}

.booking-details-container .badge {
	font-size: 12px;
}

.text-muted {
	color: #6c757d !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
	.table th, .table td {
		padding: 6px 4px;
		font-size: 11px;
	}
	
	.guest-info strong {
		font-size: 12px;
	}
	
	.guest-info small {
		font-size: 10px;
	}
	
	.btn-group .btn {
		padding: 4px 6px;
	}
}
</style>

<script>
var categoryData = <?= json_encode($cat_arr) ?>;
var statusData = <?= json_encode($status_arr) ?>;

$(document).ready(function() {
	$('#bookings-table').DataTable({
		"responsive": true,
		"scrollX": true,
		"order": [[ 6, "asc" ]], // Order by check-in date
		"columnDefs": [
			{ "orderable": false, "targets": [8, 9] }, // Status and Action columns
			{ "searchable": false, "targets": [0, 9] }  // # and Action columns
		],
		"pageLength": 25,
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		"language": {
			"search": "Search bookings:",
			"lengthMenu": "Show _MENU_ bookings per page",
			"info": "Showing _START_ to _END_ of _TOTAL_ bookings",
			"emptyTable": "No bookings found",
			"zeroRecords": "No matching bookings found"
		}
	});
});

// Handle booking status change
$(document).on('change', '.status-select', function() {
	var $select = $(this);
	var bookingId = $select.data('booking-id');
	var newStatus = parseInt($select.val());
	var originalStatus = parseInt($select.data('original-status'));
	
	if (newStatus !== originalStatus) {
		$select.addClass('changed');
		
		if (confirm('Change booking status to "' + statusData[newStatus] + '"?')) {
			updateBookingStatus(bookingId, newStatus, $select, originalStatus);
		} else {
			$select.val(originalStatus).removeClass('changed');
		}
	}
});

// Update booking status function
function updateBookingStatus(bookingId, newStatus, $select, originalStatus) {
	if (typeof start_load === 'function') {
		start_load();
	}
	
	$select.addClass('changed');
	
	$.ajax({
		url: 'ajax.php?action=update_booking_status',
		method: 'POST',
		data: { 
			booking_id: bookingId, 
			status: newStatus 
		},
		dataType: 'text',
		success: function(resp) {
			console.log('AJAX Response:', resp);
			
			var result;
			try {
				result = JSON.parse(resp);
			} catch(e) {
				result = resp;
			}
			
			if(result == 1 || result == '1' || (result.status && result.status === 'success')) {
				showAlert("Booking status updated successfully", 'success');
				$select.data('original-status', newStatus);
				setTimeout(() => { 
					$select.removeClass('changed'); 
					if (typeof end_load === 'function') end_load(); 
				}, 1000);
			} else {
				showAlert("Failed to update booking status", 'error');
				$select.val(originalStatus).removeClass('changed');
				if (typeof end_load === 'function') end_load();
			}
		},
		error: function(xhr, status, error) {
			console.log('AJAX Error:', {xhr: xhr, status: status, error: error});
			showAlert("An error occurred: " + error, 'error');
			$select.val(originalStatus).removeClass('changed');
			if (typeof end_load === 'function') end_load();
		}
	});
}

function viewBooking(bookingId) {
	if (typeof start_load === 'function') {
		start_load();
	}
	
	$.ajax({
		url: 'ajax.php?action=get_booking_details',
		method: 'POST',
		data: {id: bookingId},
		dataType: 'html',
		success: function(resp) {
			console.log('Raw response:', resp); // Debug log
			
			if (!resp || resp.trim() === '') {
				// If no response from server, create our own booking details
				createBookingDetailsFromTable(bookingId);
			} else {
				// Clean the response to remove Payment Method references
				var cleanedResp = resp;
				
				// Remove Payment Method lines more carefully
				cleanedResp = cleanedResp.replace(/Payment Method:[^<\n]*(<br\s*\/?>|\n)/gi, '');
				cleanedResp = cleanedResp.replace(/<[^>]*Payment Method[^>]*>[\s\S]*?<\/[^>]*>/gi, '');
				
				$('#booking-details').html(cleanedResp);
			}
			
			$('#viewModal').modal('show');
			if (typeof end_load === 'function') end_load();
		},
		error: function(xhr, status, error) {
			console.log('AJAX Error:', error);
			console.log('Status:', status);
			console.log('Response:', xhr.responseText);
			
			// Fallback: create booking details from table data
			createBookingDetailsFromTable(bookingId);
			$('#viewModal').modal('show');
			if (typeof end_load === 'function') end_load();
		}
	});
}

function createBookingDetailsFromTable(bookingId) {
	// Find the booking row in the table
	var $row = $('select[data-booking-id="' + bookingId + '"]').closest('tr');
	
	if ($row.length > 0) {
		var cells = $row.find('td');
		var name = $(cells[1]).find('strong').text();
		var email = $(cells[1]).find('small').text();
		var contact = $(cells[2]).text().trim();
		var idType = $(cells[3]).find('strong').text();
		var idNumber = $(cells[3]).text().split('\n')[1] || $(cells[3]).contents().last().text().trim();
		var category = $(cells[4]).find('.badge').text();
		var paymentStatus = $(cells[5]).find('.badge').text();
		var checkin = $(cells[6]).text().trim();
		var checkout = $(cells[7]).text().trim();
		var status = $(cells[8]).find('option:selected').text();
		
		var detailsHtml = `
			<div class="booking-details-container">
				<div class="row">
					<div class="col-md-6">
						<h6 class="text-primary"><i class="fa fa-user"></i> Guest Information</h6>
						<table class="table table-sm table-borderless">
							<tr><td><strong>Name:</strong></td><td>${name}</td></tr>
							<tr><td><strong>Email:</strong></td><td>${email}</td></tr>
							<tr><td><strong>Contact:</strong></td><td>${contact}</td></tr>
						</table>
					</div>
					<div class="col-md-6">
						<h6 class="text-primary"><i class="fa fa-id-card"></i> Identification</h6>
						<table class="table table-sm table-borderless">
							<tr><td><strong>ID Type:</strong></td><td>${idType}</td></tr>
							<tr><td><strong>ID Number:</strong></td><td>${idNumber}</td></tr>
						</table>
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-md-6">
						<h6 class="text-primary"><i class="fa fa-bed"></i> Room & Payment</h6>
						<table class="table table-sm table-borderless">
							<tr><td><strong>Category:</strong></td><td><span class="badge badge-info">${category}</span></td></tr>
							<tr><td><strong>Payment Status:</strong></td><td><span class="badge badge-success">${paymentStatus}</span></td></tr>
						</table>
					</div>
					<div class="col-md-6">
						<h6 class="text-primary"><i class="fa fa-calendar"></i> Booking Schedule</h6>
						<table class="table table-sm table-borderless">
							<tr><td><strong>Check-in:</strong></td><td>${checkin}</td></tr>
							<tr><td><strong>Check-out:</strong></td><td>${checkout}</td></tr>
							<tr><td><strong>Status:</strong></td><td><span class="badge badge-primary">${status}</span></td></tr>
						</table>
					</div>
				</div>
			</div>
		`;
		
		$('#booking-details').html(detailsHtml);
	} else {
		$('#booking-details').html('<div class="alert alert-warning">Booking details not found.</div>');
	}
}

function deleteBooking(bookingId) {
	if(confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
		if (typeof start_load === 'function') {
			start_load();
		}
		
		$.ajax({
			url: 'ajax.php?action=delete_booking',
			method: 'POST',
			data: {id: bookingId},
			success: function(resp) {
				if(resp == 1) {
					showAlert("Booking deleted successfully", 'success');
					setTimeout(() => { 
						if (typeof end_load === 'function') end_load(); 
						location.reload(); 
					}, 1500);
				} else {
					showAlert("Failed to delete booking", 'error');
					if (typeof end_load === 'function') end_load();
				}
			},
			error: function() {
				showAlert("An error occurred while deleting the booking", 'error');
				if (typeof end_load === 'function') end_load();
			}
		});
	}
}

// Custom alert function
function showAlert(message, type) {
	if (typeof alert_toast === 'function') {
		alert_toast(message, type);
	} else {
		// Fallback alert system
		var alertClass = type === 'success' ? 'alert-success' : 'alert-error';
		var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show">' +
						'<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
						'<span aria-hidden="true">&times;</span></button>' +
						'<i class="fa fa-' + (type === 'success' ? 'check-circle' : 'exclamation-triangle') + ' mr-2"></i>' +
						message + '</div>';
		
		$('.card-body').prepend(alertHtml);
		
		setTimeout(function() {
			$('.alert').fadeOut('slow', function() {
				$(this).remove();
			});
		}, 4000);
	}
}
</script>