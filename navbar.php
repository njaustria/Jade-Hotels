<style>
	nav#sidebar {
    background:rgb(255, 255, 255);
    background-repeat: no-repeat;
    background-size: cover;
	}
</style>
<nav id="sidebar" class='mx-lt-5' >
		
		<div class="sidebar-list">

				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span> Home</a>
				<a href="index.php?page=booked" class="nav-item nav-booked"><span class='icon-field'><i class="fa fa-book"></i></span> Booked </a>
				<a href="index.php?page=check_in" class="nav-item nav-check_in"><span class='icon-field'><i class="fa fa-sign-in-alt"></i></span> Check In </a>
				<a href="index.php?page=check_out" class="nav-item nav-check_out"><span class='icon-field'><i class="fa fa-sign-out-alt"></i></span> Check Out </a>
				<a href="index.php?page=payment_list" class="nav-item nav-payment_list"><span class='icon-field'><i class="fa fa-credit-card"></i></span> Payment List</a>
				<a href="index.php?page=rooms" class="nav-item nav-rooms"><span class='icon-field'><i class="fa fa-bed"></i></span> Rooms </a>
				<a href="index.php?page=categories" class="nav-item nav-categories"><span class='icon-field'><i class="fa fa-list"></i></span> Room Category</a>
				<a href="index.php?page=facilities" class="nav-item nav-facilities"><span class='icon-field'><i class="fa fa-concierge-bell"></i></span> Hotel Facilities</a>
				<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Users</a>
				<a href="index.php?page=team_members" class="nav-item nav-team_members"><span class='icon-field'><i class="fa fa-user-friends"></i></span> Team Members</a>
				<?php if($_SESSION['login_type'] == 1): ?>
				<a href="index.php?page=site_settings" class="nav-item nav-site_settings"><span class='icon-field'><i class="fa fa-cogs"></i></span> Site Settings</a>
			<?php endif; ?>
		</div>

</nav>
<script>
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>