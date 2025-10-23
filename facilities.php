<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-sm btn-default border-primary" href="javascript:void(0)" id="new_facility">
					<i class="fa fa-plus"></i> Add New Facility
				</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="list">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Description</th>
						<th>Hours</th>
						<th>Location</th>
						<th>Image</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT * FROM hotel_facilities ORDER BY facility_name ASC");
					if($qry):
						while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<td><?php echo $i++ ?></td>
						<td><b><?php echo ucwords($row['facility_name']) ?></b></td>
						<td><small><?php echo $row['description'] ?></small></td>
						<td><?php echo $row['operating_hours'] ?></td>
						<td><?php echo $row['location'] ?></td>
						<td>
							<?php if(!empty($row['image'])): ?>
								<img src="assets/uploads/<?php echo $row['image'] ?>" alt="<?php echo $row['facility_name'] ?>" class="img-thumbnail" width="50" height="50">
							<?php else: ?>
								<span class="text-muted">No Image</span>
							<?php endif; ?>
						</td>
						<td>
							<div class="btn-group">
								<button class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown">Action</button>
								<div class="dropdown-menu">
									<a class="dropdown-item view_facility" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
									<a class="dropdown-item edit_facility" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Edit</a>
									<a class="dropdown-item delete_facility" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
								</div>
							</div>
						</td>
					</tr>
					<?php 
						endwhile; 
					else: 
					?>
					<tr>
						<td colspan="7" class="text-center">No facilities found</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<style>
table p { margin: unset; }
table td { vertical-align: middle !important; }
.img-thumbnail { object-fit: cover; }
</style>

<script>
$(document).ready(function(){
	$('#list').dataTable();
	
	$('#new_facility').click(function(){
		uni_modal("New Facility","manage_facility.php","mid-large");
	});
	
	$('.view_facility').click(function(){
		uni_modal("Facility Details","view_facility.php?id="+$(this).attr('data-id'),"mid-large");
	});
	
	$('.edit_facility').click(function(){
		uni_modal("Manage Facility","manage_facility.php?id="+$(this).attr('data-id'),"mid-large");
	});
	
	$('.delete_facility').click(function(){
		_conf("Are you sure to delete this facility?","delete_facility",[$(this).attr('data-id')]);
	});
});

function delete_facility($id){
	start_load();
	$.ajax({
		url:'ajax.php?action=delete_facility',
		method:'POST',
		data:{id:$id},
		success:function(resp){
			if(resp==1){
				alert_toast("Data deleted",'success');
				setTimeout(function(){
					location.reload();
				},1500);
			}
		}
	});
}
</script>