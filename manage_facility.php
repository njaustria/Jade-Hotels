<?php
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM hotel_facilities where id= ".$_GET['id']);
	foreach($qry->fetch_array() as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form action="" id="manage-facility">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="form-group">
			<label for="" class="control-label">Facility Name</label>
			<input type="text" class="form-control" name="facility_name" value="<?php echo isset($facility_name) ? $facility_name : '' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Description</label>
			<textarea name="description" id="" cols="30" rows="4" class="form-control" required><?php echo isset($description) ? $description : '' ?></textarea>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Operating Hours</label>
			<input type="text" class="form-control" name="operating_hours" value="<?php echo isset($operating_hours) ? $operating_hours : '' ?>" placeholder="e.g., 6:00 AM - 10:00 PM" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Location</label>
			<input type="text" class="form-control" name="location" value="<?php echo isset($location) ? $location : '' ?>" placeholder="e.g., Ground Floor, Main Building" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Facility Image</label>
			<div class="custom-file">
              <input type="file" class="custom-file-input" id="customFile" name="image" onchange="displayImg(this,$(this))">
              <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
		</div>
		<div class="form-group d-flex justify-content-center">
			<img src="<?php echo isset($image) && !empty($image) ? '../assets/img/facilities/'.$image :'' ?>" alt="" id="cimg" class="img-fluid img-thumbnail <?php echo !isset($image) || empty($image) ? 'd-none' : '' ?>">
		</div>
	</form>
</div>

<style>
	img#cimg{
		height: 15vh;
		width: 25vw;
		object-fit: cover;
		border-radius: 5px;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        	$('#cimg').removeClass('d-none');
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#manage-facility').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_facility',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	})
</script>