<?php include('db_connect.php');?>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
				<form action="" id="manage-category">
					<div class="card">
						<div class="card-header">Room Category Form</div>
						<div class="card-body">
							<input type="hidden" name="id">
							<div class="form-group">
								<label>Category</label>
								<input type="text" class="form-control" name="name" required>
							</div>
							<div class="form-group">
								<label>Price (₱)</label>
								<input type="number" class="form-control" name="price" step="any" required>
							</div>
							<div class="form-group">
								<label>Description</label>
								<textarea class="form-control" name="description" rows="3"></textarea>
							</div>
							
							<!-- Amenities Section -->
							<div class="form-group">
								<label>Room Amenities</label>
								<div class="input-group mb-2">
									<input type="text" class="form-control" id="new-amenity" placeholder="Add custom amenity..." maxlength="50">
									<div class="input-group-append">
										<button class="btn btn-outline-primary" type="button" id="add-amenity">+</button>
									</div>
								</div>
								
								<div id="selected-amenities" class="mb-2 p-2 border rounded bg-light" style="min-height: 50px;">
									<small class="text-muted">No amenities selected</small>
								</div>
								
								<div class="row">
									<?php 
									$common_amenities = [
										'Free Wi-Fi', 'Flat Screen TV', 'Air Conditioning', 'Private Bathroom',
										'Room Service', 'Mini Bar', 'Balcony', 'Safe', 'Telephone', 'Hair Dryer',
										'Iron', 'Desk', 'Sofa', 'Kitchenette', 'Coffee Maker', 'Ocean View'
									];
									$half = ceil(count($common_amenities) / 2);
									for($i = 0; $i < 2; $i++): ?>
										<div class="col-6">
											<?php 
											$start = $i * $half;
											$end = min(($i + 1) * $half, count($common_amenities));
											for($j = $start; $j < $end; $j++): ?>
												<div class="form-check form-check-sm">
													<input class="form-check-input amenity-check" type="checkbox" value="<?php echo $common_amenities[$j]; ?>">
													<label class="form-check-label small"><?php echo $common_amenities[$j]; ?></label>
												</div>
											<?php endfor; ?>
										</div>
									<?php endfor; ?>
								</div>
								
								<small class="text-muted">
									<span id="amenity-count">0</span>/10 amenities
									<span id="max-warning" class="text-danger" style="display:none;">Maximum reached</span>
								</small>
								<input type="hidden" name="amenities" id="amenities-input">
							</div>
							
							<div class="form-group">
								<label>Image</label>
								<input type="file" class="form-control" name="img" onchange="displayImg(this)">
								<img src="" alt="" id="cimg" style="max-height: 100px; margin-top: 10px; display: none;">
							</div>
						</div>
						
						<div class="card-footer">
							<button class="btn btn-primary btn-sm">Save</button>
							<button class="btn btn-secondary btn-sm" type="button" onclick="resetForm()">Cancel</button>
						</div>
					</div>
				</form>
			</div>

			<!-- Table Panel -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Image</th>
									<th>Details</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$cats = $conn->query("SELECT * FROM room_categories ORDER BY id ASC");
								while($row = $cats->fetch_assoc()): ?>
								<tr>
									<td><?php echo $i++; ?></td>
									<td><img src="<?php echo '../assets/img/'.$row['cover_img']; ?>" class="img-thumbnail" style="max-width: 60px;"></td>
									<td>
										<strong><?php echo $row['name']; ?></strong><br>
										<small>₱<?php echo number_format($row['price'], 2); ?></small><br>
										<small class="text-muted"><?php echo substr($row['description'], 0, 50).'...'; ?></small><br>
										<?php if($row['amenities']): 
											$amenities = explode(',', $row['amenities']);
											echo '<span class="badge badge-info">'.count($amenities).' amenities</span>';
										endif; ?>
									</td>
									<td>
										<button class="btn btn-sm btn-primary edit-cat" 
											data-id="<?php echo $row['id']; ?>"
											data-name="<?php echo $row['name']; ?>"
											data-price="<?php echo $row['price']; ?>"
											data-description="<?php echo htmlspecialchars($row['description']); ?>"
											data-amenities="<?php echo htmlspecialchars($row['amenities']); ?>"
											data-img="<?php echo $row['cover_img']; ?>">Edit</button>
										<button class="btn btn-sm btn-danger delete-cat" data-id="<?php echo $row['id']; ?>">Delete</button>
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

<style>
.form-check-sm { margin-bottom: 5px; }
.amenity-tag { 
	display: inline-block; 
	background: #007bff; 
	color: white; 
	padding: 3px 8px; 
	margin: 2px; 
	border-radius: 12px; 
	font-size: 12px; 
}
.amenity-tag .remove { 
	margin-left: 5px; 
	cursor: pointer; 
	font-weight: bold; 
}
</style>

<script>
let amenities = [];

function displayImg(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			$('#cimg').attr('src', e.target.result).show();
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function updateAmenities() {
	const container = $('#selected-amenities');
	const count = $('#amenity-count');
	const warning = $('#max-warning');
	
	if (amenities.length === 0) {
		container.html('<small class="text-muted">No amenities selected</small>');
	} else {
		let html = '';
		amenities.forEach((amenity, index) => {
			html += `<span class="amenity-tag">${amenity}<span class="remove" onclick="removeAmenity(${index})">&times;</span></span>`;
		});
		container.html(html);
	}
	
	count.text(amenities.length);
	$('#amenities-input').val(amenities.join(','));
	
	// Handle max limit
	if (amenities.length >= 10) {
		warning.show();
		$('#add-amenity, #new-amenity').prop('disabled', true);
		$('.amenity-check:not(:checked)').prop('disabled', true);
	} else {
		warning.hide();
		$('#add-amenity, #new-amenity').prop('disabled', false);
		$('.amenity-check').prop('disabled', false);
	}
	
	// Update checkboxes
	$('.amenity-check').each(function() {
		$(this).prop('checked', amenities.includes($(this).val()));
	});
}

function addAmenity(name) {
	name = name.trim();
	if (name && !amenities.includes(name) && amenities.length < 10) {
		amenities.push(name);
		updateAmenities();
		return true;
	}
	return false;
}

function removeAmenity(index) {
	amenities.splice(index, 1);
	updateAmenities();
}

function resetForm() {
	$('#manage-category')[0].reset();
	amenities = [];
	$('#cimg').hide();
	updateAmenities();
}

$(document).ready(function() {
	// Add custom amenity
	$('#add-amenity').click(function() {
		if (addAmenity($('#new-amenity').val())) {
			$('#new-amenity').val('');
		}
	});
	
	$('#new-amenity').keypress(function(e) {
		if (e.which === 13) {
			e.preventDefault();
			if (addAmenity($(this).val())) {
				$(this).val('');
			}
		}
	});
	
	// Handle predefined amenities
	$('.amenity-check').change(function() {
		const value = $(this).val();
		if ($(this).is(':checked')) {
			addAmenity(value);
		} else {
			const index = amenities.indexOf(value);
			if (index > -1) removeAmenity(index);
		}
	});
	
	updateAmenities();
});

// Form submission
$('#manage-category').submit(function(e) {
	e.preventDefault();
	start_load();
	$.ajax({
		url: 'ajax.php?action=save_category',
		data: new FormData(this),
		cache: false,
		contentType: false,
		processData: false,
		method: 'POST',
		success: function(resp) {
			if (resp == 1) {
				alert_toast("Data successfully added", 'success');
				setTimeout(() => location.reload(), 1500);
			} else if (resp == 2) {
				alert_toast("Data successfully updated", 'success');
				setTimeout(() => location.reload(), 1500);
			}
		}
	});
});

// Edit category
$('.edit-cat').click(function() {
	start_load();
	const form = $('#manage-category');
	form[0].reset();
	
	form.find("[name='id']").val($(this).data('id'));
	form.find("[name='name']").val($(this).data('name'));
	form.find("[name='price']").val($(this).data('price'));
	form.find("[name='description']").val($(this).data('description'));
	
	if ($(this).data('img')) {
		$('#cimg').attr('src', '../assets/img/' + $(this).data('img')).show();
	}
	
	amenities = $(this).data('amenities') ? $(this).data('amenities').split(',').map(a => a.trim()).filter(a => a) : [];
	updateAmenities();
	end_load();
});

// Delete category
$('.delete-cat').click(function() {
	_conf("Are you sure to delete this category?", "delete_cat", [$(this).data('id')]);
});

function delete_cat(id) {
	start_load();
	$.ajax({
		url: 'ajax.php?action=delete_category',
		method: 'POST',
		data: {id: id},
		success: function(resp) {
			if (resp == 1) {
				alert_toast("Data successfully deleted", 'success');
				setTimeout(() => location.reload(), 1500);
			}
		}
	});
}
</script>