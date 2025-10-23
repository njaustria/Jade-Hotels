<?php
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM hotel_facilities where id= ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<div id="msg"></div>
	<div class="row">
		<div class="col-md-6">
			<dl>
				<dt><b class="border-bottom border-primary">Facility Name</b></dt>
				<dd><?php echo ucwords($facility_name) ?></dd>
				<dt><b class="border-bottom border-primary">Description</b></dt>
				<dd><?php echo $description ?></dd>
				<dt><b class="border-bottom border-primary">Operating Hours</b></dt>
				<dd><?php echo $operating_hours ?></dd>
				<dt><b class="border-bottom border-primary">Location</b></dt>
				<dd><?php echo $location ?></dd>
				<dt><b class="border-bottom border-primary">Date Updated</b></dt>
				<dd><?php echo date("M d, Y", strtotime($date_updated)) ?></dd>
			</dl>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<b>Facility Image</b>
				</div>
				<div class="card-body d-flex justify-content-center align-items-center">
					<?php if(!empty($image)): ?>
						<img src="../assets/img/facilities/<?php echo $image ?>" alt="" class="img-fluid img-thumbnail" style="max-height: 300px;">
					<?php else: ?>
						<div class="text-center text-muted">
							<i class="fa fa-image fa-5x"></i>
							<p>No Image Available</p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	#uni_modal .modal-footer{
		display: none
	}
</style>