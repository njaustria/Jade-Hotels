<?php include('db_connect.php'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Payment List (from Bookings)</h3>
                    <div class="card-tools">
                        </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Guest Name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Payment Method</th>
                                <th>Card Number</th>
                                <th>E-Wallet Provider</th>
                                <th>Account Number</th>
                                <th>Payment Status</th>
                                <th>Booking Check-in</th>
                                <th>Booking Check-out</th>
                                <th>Date Updated (Booking)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            // Fetch data directly from the 'booked' table
                            $payments = $conn->query("SELECT * FROM booked ORDER BY date_updated DESC");
                            while($row = $payments->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i++ ?></td>
                                <td><?php echo ucwords($row['name']) ?></td>
                                <td><?php echo $row['email'] ?></td>
                                <td><?php echo $row['contact_number'] ?></td>
                                <td><?php echo ucwords(str_replace('_', ' ', $row['payment_method'] ?? 'N/A')) ?></td>
                                <td><?php echo $row['card_number'] ?? 'N/A' ?></td>
                                <td><?php echo ucwords(str_replace('_', ' ', $row['ewallet_provider'] ?? 'N/A')) ?></td>
                                <td><?php echo $row['account_number'] ?? 'N/A' ?></td>
                                <td>
                                    <?php
                                    $payment_status = $row['payment_status'] ?? 'N/A';
                                    $badge_class = '';
                                    switch(strtolower($payment_status)) {
                                        case 'paid': $badge_class = 'badge-success'; break;
                                        case 'pending': $badge_class = 'badge-warning'; break;
                                        case 'failed': $badge_class = 'badge-danger'; break;
                                        case 'refunded': $badge_class = 'badge-secondary'; break;
                                        default: $badge_class = 'badge-light';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo ucwords($payment_status); ?>
                                    </span>
                                </td>
                                <td><?php echo date("M d, Y h:i A", strtotime($row['check_in'])) ?></td>
                                <td><?php echo date("M d, Y h:i A", strtotime($row['check_out'])) ?></td>
                                <td><?php echo date("M d, Y h:i A", strtotime($row['date_updated'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if($payments->num_rows == 0): ?>
                            <tr><td colspan="12" class="text-center text-muted py-4"><i class="fa fa-info-circle"></i> No payment records found in bookings.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.table').dataTable({
            "responsive": true,
            "scrollX": true,
            "order": [[ 10, "desc" ]], // Order by Date Updated (Booking) column
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "language": {
                "search": "Search payments:",
                "lengthMenu": "Show _MENU_ payments per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ payments",
                "emptyTable": "No payment records found in bookings.",
                "zeroRecords": "No matching payment records found"
            }
        });
    })
</script>

<style>
    /* Custom styles for better table appearance */
    table td, table th {
        vertical-align: middle !important;
    }
    .card-tools {
        float: right;
    }
    .card-title {
        float: left;
    }
    /* Badge Styling (copied from booked.php for consistency) */
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
</style>