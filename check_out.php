<?php include('db_connect.php'); 
$cat = $conn->query("SELECT * FROM room_categories");
$cat_arr = array();
while($row = $cat->fetch_assoc()){
	$cat_arr[$row['id']] = $row;
}
$room = $conn->query("SELECT * FROM rooms");
$room_arr = array();
while($row = $room->fetch_assoc()){
	$room_arr[$row['id']] = $row;
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row mt-3">
			<div class="col-md-12">
				<div class="card-header" style="background-color: white;">
					<h4>Check-out Rooms</h4>
					<p class="text-muted mb-0">Manage check-out rooms bookings</p>
				</div>
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<th>#</th>
								<th>Name</th>
								<th>Email</th>
								<th>Contact</th>
								<th>ID Type</th>
								<th>ID Number</th>
								<th>Category</th>
								<th>Check-in</th>
								<th>Check-out</th>
								<th>Action</th>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$checked = $conn->query("SELECT * FROM checked where status != 0 order by status desc, id asc ");
								while($row=$checked->fetch_assoc()):
									// Get room information safely
									$room_info = isset($room_arr[$row['room_id']]) ? $room_arr[$row['room_id']] : null;
									$category_info = null;
									
									if($room_info && isset($cat_arr[$room_info['category_id']])) {
										$category_info = $cat_arr[$room_info['category_id']];
									}
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class=""><?php echo isset($row['name']) ? $row['name'] : 'N/A' ?></td>
									<td class=""><?php echo isset($row['email']) ? $row['email'] : 'N/A' ?></td>
									<td class=""><?php echo isset($row['contact_no']) ? $row['contact_no'] : 'N/A' ?></td>
									<td class=""><?php echo isset($row['id_type']) ? $row['id_type'] : 'N/A' ?></td>
									<td class=""><?php echo isset($row['id_number']) ? $row['id_number'] : 'N/A' ?></td>
									<td class="text-center">
										<?php 
										if($category_info) {
											echo $category_info['name'];
										} else {
											echo 'Unknown Category';
										}
										?>
									</td>
									<td class="text-center">
										<?php 
										if(isset($row['date_in']) && $row['date_in'] != '') {
											echo date('M d, Y', strtotime($row['date_in']));
										} else {
											echo 'N/A';
										}
										?>
									</td>
									<td class="text-center">
										<?php 
										if(isset($row['date_out']) && $row['date_out'] != '' && $row['status'] == 2) {
											echo date('M d, Y', strtotime($row['date_out']));
										} else {
											echo 'N/A';
										}
										?>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary check_out" type="button" data-id="<?php echo $row['id'] ?>">View</button>
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
	$('.check_out').click(function(){
		uni_modal("Check Out","manage_check_out.php?checkout=1&id="+$(this).attr("data-id"))
	})
	$('#filter').submit(function(e){
		e.preventDefault()
		location.replace('index.php?page=check_in&category_id='+$(this).find('[name="category_id"]').val()+'&status='+$(this).find('[name="status"]').val())
	})
</script>