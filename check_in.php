<?php include('db_connect.php'); 
// Get booking ID if exists
$book_id = isset($_GET['book_id']) ? $_GET['book_id'] : 0;
$booked_room = null;

if($book_id) {
    $booking = $conn->query("SELECT * FROM checked WHERE id = $book_id");
    if($booking->num_rows > 0) {
        $booked_room = $booking->fetch_assoc();
    }
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card-header" style="background-color: white;">
			<h4>Check-in Rooms</h4>
			<p class="text-muted mb-0">Manage check-in room bookings</p>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<div class="container-fluid">
							<div class="col-md-12">
								<form id="filter">
									<div class="row">
										<div class=" col-md-4">
											<label class="control-label">Category</label>
											<select class="custom-select browser-default" name="category_id">
												<option value="all" <?php echo isset($_GET['category_id']) && $_GET['category_id'] == 'all' ? 'selected' : '' ?>>All</option>
												<?php 
												$cat = $conn->query("SELECT * FROM room_categories order by name asc ");
												$cat_name = array(); // Initialize the array
												while($row= $cat->fetch_assoc()) {
													$cat_name[$row['id']] = $row['name'];
													?>
													<option value="<?php echo $row['id'] ?>" <?php echo isset($_GET['category_id']) && $_GET['category_id'] == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
												<?php
												}
												?>
											</select>
										</div> 
										<div class="col-md-2">
											<label for="" class="control-label">&nbsp</label>
											<button class="btn btn-block btn-primary">Filter</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<th>#</th>
								<th>Category</th>
								<th>Room</th>
								<th>Status</th>
								<th>Action</th>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$where = '';
								if(isset($_GET['category_id']) && !empty($_GET['category_id'])  && $_GET['category_id'] != 'all'){
									$where .= " where category_id = '".$_GET['category_id']."' ";
								}
									if(empty($where))
										$where .= " where status = '0' OR status = '1' ";
									else
										$where .= " and (status = '0' OR status = '1') ";
								$rooms = $conn->query("SELECT * FROM rooms ".$where." order by id asc");
								while($row=$rooms->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="text-center"><?php echo isset($cat_name[$row['category_id']]) ? $cat_name[$row['category_id']] : 'Unknown Category' ?></td>
									<td class=""><?php echo $row['room'] ?></td>
									<?php if($row['status'] == 0): ?>
										<td class="text-center"><span class="badge badge-success">Available</span></td>
									<?php elseif($row['status'] == 1): ?>
										<td class="text-center"><span class="badge badge-warning">Booked</span></td>
									<?php elseif($row['status'] == 2): ?>
										<td class="text-center"><span class="badge badge-danger">Occupied</span></td>
									<?php else: ?>
										<td class="text-center"><span class="badge badge-default">Unavailable</span></td>
									<?php endif; ?>
									<td class="text-center">
										<?php if($row['status'] == 0 || ($booked_room && $booked_room['room_id'] == $row['id'])): ?>
											<button class="btn btn-sm btn-primary check_in" type="button" 
												data-id="<?php echo $row['id'] ?>"
												<?php if($booked_room && $booked_room['room_id'] == $row['id']) echo 'style="background-color:#28a745;border-color:#28a745"'; ?>>
												Check-in
											</button>
										<?php endif; ?>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('table').dataTable()
	$('.check_in').click(function(){
		uni_modal("Check In","manage_check_in.php?rid="+$(this).attr("data-id")<?php echo $book_id ? "&book_id=$book_id" : "" ?>)
	})
	$('#filter').submit(function(e){
		e.preventDefault()
		location.replace('index.php?page=check_in&category_id='+$(this).find('[name="category_id"]').val()+'&status='+$(this).find('[name="status"]').val())
	})
	
	<?php if($booked_room): ?>
	// Auto-open modal for booked room
	$(document).ready(function() {
		setTimeout(function() {
			$('.check_in[data-id="<?php echo $booked_room['room_id'] ?>"]').click();
		}, 500);
	});
	<?php endif; ?>
</script>