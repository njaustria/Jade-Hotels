<?php 
include('db_connect.php');
if(isset($_GET['id'])){
$user = $conn->query("SELECT * FROM users where id =".$_GET['id']);
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
}
?>
<div class="container-fluid">
	
	<form action="" id="manage-user">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" class="form-control" value="<?php echo isset($meta['name']) ? $meta['name']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<div class="input-group">
				<input type="password" name="password" id="password" class="form-control" value="<?php echo isset($meta['password']) ? $meta['password']: '' ?>" required>
				<div class="input-group-append">
					<button type="button" class="btn btn-outline-secondary" id="togglePassword" onclick="togglePasswordVisibility()">
						<i class="fa fa-eye" id="toggleIcon"></i>
					</button>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="type">User Type</label>
			<select name="type" id="type" class="custom-select">
				<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected': '' ?>>Admin</option>
				<option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected': '' ?>>User</option>
			</select>
		</div>
	</form>
</div>

<style>
	.input-group {
		position: relative;
	}
	
	.input-group-append .btn {
		border-left: 0;
		border-radius: 0 0.25rem 0.25rem 0;
	}
	
	.input-group .form-control {
		border-right: 0;
		border-radius: 0.25rem 0 0 0.25rem;
	}
	
	#togglePassword {
		background-color: #f8f9fa;
		border-color: #ced4da;
		color: #6c757d;
		cursor: pointer;
		transition: all 0.2s ease-in-out;
	}
	
	#togglePassword:hover {
		background-color: #e9ecef;
		color: #495057;
	}
	
	#togglePassword:focus {
		box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
		outline: 0;
	}
</style>

<script>
	function togglePasswordVisibility() {
		const passwordField = document.getElementById('password');
		const toggleIcon = document.getElementById('toggleIcon');
		const toggleButton = document.getElementById('togglePassword');
		
		if (passwordField.type === 'password') {
			// Show password
			passwordField.type = 'text';
			toggleIcon.classList.remove('fa-eye');
			toggleIcon.classList.add('fa-eye-slash');
			toggleButton.setAttribute('title', 'Hide password');
		} else {
			// Hide password
			passwordField.type = 'password';
			toggleIcon.classList.remove('fa-eye-slash');
			toggleIcon.classList.add('fa-eye');
			toggleButton.setAttribute('title', 'Show password');
		}
	}

	$('#manage-user').submit(function(e){
		e.preventDefault();
		start_load()
		$.ajax({
			url:'ajax.php?action=save_user',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp ==1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	})
</script>