<?php 
include('db_connect.php');
$rid = $_GET['rid'];
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $qry = $conn->query("SELECT * FROM checked where id =".$id);
    if($qry->num_rows > 0){
        foreach($qry->fetch_array() as $k => $v){
            $meta[$k]=$v;
        }
    }
    $calc_days = abs(strtotime($meta['date_out']) - strtotime($meta['date_in'])) ; 
    $calc_days = floor($calc_days / (60*60*24));
    $cat = $conn->query("SELECT * FROM room_categories");
    $cat_arr = array();
    while($row = $cat->fetch_assoc()){
        $cat_arr[$row['id']] = $row;
    }
}
?>

<style>
.container { max-width: 500px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
.required { color: #dc3545; }
.form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
.form-control:focus { border-color: #007bff; outline: none; }
.readonly-field { background: #e9ecef !important; cursor: not-allowed; }
.help-text { font-size: 12px; color: #6c757d; margin-top: 3px; }
.alert { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 10px; border-radius: 4px; margin: 15px 0; }
.btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
.btn-primary { background: #dc3545; color: white; }
.btn-secondary { background: #6c757d; color: white; }
.form-row { display: flex; gap: 15px; }
.form-row .form-group { flex: 1; }
.contact-row { display: flex; gap: 10px; } /* Added for side-by-side country code and contact input */
.contact-row select { flex: 0.3; } /* Adjust width of country code select */
.contact-row input { flex: 0.7; } /* Adjust width of contact input */
</style>

<div class="container">
    <form action="" id="manage-check">
        <input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
        
        <?php if(isset($_GET['id'])):
            $rooms = $conn->query("SELECT * FROM rooms where status =0 or id = $rid order by id asc");
        ?>
        <div class="form-group">
            <label>Room <span class="required">*</span></label>
            <select name="rid" class="form-control">
                <?php while($row=$rooms->fetch_assoc()): ?>
                <option value="<?php echo $row['id'] ?>" <?php echo $row['id'] == $rid ? "selected": '' ?>>
                    <?php echo $row['room'] . " | ". ($cat_arr[$row['category_id']]['name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php else: ?>
        <input type="hidden" name="rid" value="<?php echo isset($_GET['rid']) ? $_GET['rid']: '' ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Name <span class="required">*</span></label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="<?php echo isset($meta['name']) ? $meta['name']: '' ?>" required>
        </div>

        <div class="form-group">
            <label>Contact <span class="required">*</span></label>
            <div class="contact-row">
                <select name="country_code" id="country_code" class="form-control">
                    <option value="+63" selected>+63 (PH)</option>
                    <option value="+1">+1 (USA/CA)</option>
                    <option value="+44">+44 (UK)</option>
                    <option value="+61">+61 (AU)</option>
                    <option value="+81">+81 (JP)</option>
                    <option value="+82">+82 (KR)</option>
                </select>
                <input type="text" name="contact" id="contact" class="form-control" 
                       value="<?php echo isset($meta['contact_no']) ? preg_replace('/^\+\d+\s/', '', $meta['contact_no']) : '' ?>" 
                       placeholder="Enter contact number" required pattern="[0-9]{10}" maxlength="10">
            </div>
        </div>

        <div class="form-group">
            <label>Email <span class="required">*</span></label>
            <input type="email" name="email" id="email" class="form-control" 
                   value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>ID Type <span class="required">*</span></label>
                <select name="id_type" id="id_type" class="form-control" required>
                    <option value="">Select ID Type</option>
                    <option value="passport" <?php echo isset($meta['id_type']) && $meta['id_type'] == 'passport' ? 'selected' : '' ?>>Passport</option>
                    <option value="drivers_license" <?php echo isset($meta['id_type']) && $meta['id_type'] == "drivers_license" ? 'selected' : '' ?>>Driver's License</option>
                    <option value="national_id" <?php echo isset($meta['id_type']) && $meta['id_type'] == 'national_id' ? 'selected' : '' ?>>National ID</option>
                    <option value="voters_id" <?php echo isset($meta['id_type']) && $meta['id_type'] == "voters_id" ? 'selected' : '' ?>>Voter's ID</option>
                    <option value="postal_id" <?php echo isset($meta['id_type']) && $meta['id_type'] == 'postal_id' ? 'selected' : '' ?>>Postal ID</option>
                </select>
            </div>

            <div class="form-group">
                <label>ID Number <span class="required">*</span></label>
                <input type="text" name="id_number" id="id_number" class="form-control" 
                       value="<?php echo isset($meta['id_number']) ? $meta['id_number']: '' ?>" 
                       placeholder="Enter ID number" required>
            </div>
        </div>

        <div class="form-group">
            <label>Check-in Date <span class="required">*</span></label>
            <input type="date" name="date_in" id="date_in" class="form-control" 
                   value="<?php echo isset($meta['date_in']) ? date("Y-m-d",strtotime($meta['date_in'])): date("Y-m-d") ?>" 
                   min="<?php echo date('Y-m-d') ?>" required>
        </div>

        <div class="form-group">
            <label>Check-in Time</label>
            <input type="time" name="date_in_time" class="form-control readonly-field" 
                   value="<?php echo isset($meta['date_in']) ? date("H:i",strtotime($meta['date_in'])): '12:00' ?>" readonly>
        </div>

        <div class="form-group">
            <label>Check-out Date <span class="required">*</span></label>
            <input type="date" name="date_out" id="date_out" class="form-control" 
                   value="<?php echo isset($meta['date_out']) ? date("Y-m-d",strtotime($meta['date_out'])): date("Y-m-d", strtotime('+3 days')) ?>" required>
        </div>

        <div class="form-group">
            <label>Check-out Time</label>
            <input type="time" name="date_out_time" class="form-control readonly-field" 
                   value="<?php echo isset($meta['date_out']) ? date("H:i",strtotime($meta['date_out'])): '12:00' ?>" readonly>
        </div>

        <div class="form-group">
            <label>Days</label>
            <input type="number" name="days" id="days" class="form-control readonly-field" 
                   value="<?php echo isset($meta['date_in']) ? $calc_days: 3 ?>" readonly>
            <div class="help-text">Auto-calculated</div>
        </div>

        <div class="alert">All required fields must be filled.</div>
    </form>
</div>

<script>
$(document).ready(function() {
    // --- NEW: Country code selection and input attribute updates ---
    $('#country_code').on('change', function() {
        const countryCode = $(this).val();
        const contactInput = $('#contact');
        
        // Reset attributes before setting new ones
        contactInput.val(''); 
        contactInput.removeAttr('pattern');
        contactInput.removeAttr('maxlength');
        
        // Set placeholder, pattern (for digit validation only), and maxlength based on country
        switch(countryCode) {
            case '+63': // Philippines
                contactInput.attr('placeholder', '9123456789'); // 10 digits
                contactInput.attr('pattern', '[0-9]{10}'); // Exactly 10 digits
                contactInput.attr('maxlength', '10'); 
                break;
            case '+1': // USA/Canada
                contactInput.attr('placeholder', '5551234567'); // 10 digits
                contactInput.attr('pattern', '[0-9]{10}'); // Exactly 10 digits
                contactInput.attr('maxlength', '10');
                break;
            case '+44': // UK
                contactInput.attr('placeholder', '79111234567'); // Example 11 digits
                contactInput.attr('pattern', '[0-9]{10,11}'); // 10 to 11 digits
                contactInput.attr('maxlength', '11');
                break;
             case '+61': // Australia
                contactInput.attr('placeholder', '412345678'); // 9 digits (mobile numbers)
                contactInput.attr('pattern', '[0-9]{9}'); // Exactly 9 digits
                contactInput.attr('maxlength', '9');
                break;
             case '+81': // Japan
                contactInput.attr('placeholder', '90123456789'); // Example 11 digits
                contactInput.attr('pattern', '[0-9]{10,11}'); // 10 to 11 digits
                contactInput.attr('maxlength', '11');
                break;
             case '+82': // South Korea
                contactInput.attr('placeholder', '10123456789'); // Example 11 digits
                contactInput.attr('pattern', '[0-9]{10,11}'); // 10 to 11 digits
                contactInput.attr('maxlength', '11');
                break;
            default:
                contactInput.attr('placeholder', 'Enter contact number');
                // No specific pattern or maxlength for default to allow flexibility
        }
    });

    // Initialize with the correct attributes for the default selected country (+63 PH)
    $('#country_code').trigger('change'); 

    $('#date_in, #date_out').on('change', function() {
        calcDays();
        updateMinDate();
    });
    
    calcDays();
    updateMinDate();
    
    function calcDays() {
        var checkIn = new Date($('#date_in').val());
        var checkOut = new Date($('#date_out').val());
        if (checkIn && checkOut && checkOut > checkIn) {
            var days = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            $('#days').val(days);
        } else {
            $('#days').val(1);
        }
    }
    
    function updateMinDate() {
        var checkIn = $('#date_in').val();
        if (checkIn) {
            var minOut = new Date(checkIn);
            minOut.setDate(minOut.getDate() + 1);
            $('#date_out').attr('min', minOut.toISOString().split('T')[0]);
        }
    }
    
    $('#date_in').on('change', function() {
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var checkIn = new Date($(this).val());
        
        if (checkIn < today) {
            alert('Check-in date cannot be in the past');
            $(this).val('<?php echo date("Y-m-d") ?>');
            return;
        }
        
        var checkOut = new Date($('#date_out').val());
        if (checkOut <= checkIn) {
            var nextDay = new Date(checkIn);
            nextDay.setDate(nextDay.getDate() + 1);
            $('#date_out').val(nextDay.toISOString().split('T')[0]);
        }
    });
    
    $('#date_out').on('change', function() {
        var checkIn = new Date($('#date_in').val());
        var checkOut = new Date($(this).val());
        
        if (checkOut <= checkIn) {
            alert('Check-out must be after check-in');
            var nextDay = new Date(checkIn);
            nextDay.setDate(nextDay.getDate() + 1);
            $(this).val(nextDay.toISOString().split('T')[0]);
        }
    });
});

$('#manage-check').submit(function(e){
    e.preventDefault();
    start_load();
    
    // Combine country code and contact number for submission
    var fullContact = $('#country_code').val() + ' ' + $('#contact').val().trim();

    var name = $('#name').val().trim();
    var contact = $('#contact').val().trim();
    var email = $('#email').val().trim();
    var idType = $('#id_type').val();
    var idNumber = $('#id_number').val().trim();
    var checkIn = $('#date_in').val();
    var checkOut = $('#date_out').val();

    // Get the pattern from the contact input for validation
    var contactPattern = $('#contact').attr('pattern') ? new RegExp($('#contact').attr('pattern')) : null;
    
    if (!name || !contact || !email || !idType || !idNumber || !checkIn || !checkOut) {
        alert('Please fill all required fields');
        end_load();
        return;
    }

    // Validate contact number against the dynamically set pattern
    if (contactPattern && !contactPattern.test(contact)) {
        alert('Please enter a valid contact number format for the selected country.');
        end_load();
        return;
    }
    
    if (new Date(checkOut) <= new Date(checkIn)) {
        alert('Check-out must be after check-in');
        end_load();
        return;
    }
    
    $.ajax({
        url:'ajax.php?action=save_check-in',
        method:'POST',
        data: $(this).serialize().replace(/contact=[^&]*/, 'contact=' + encodeURIComponent(fullContact)),
        success:function(resp){
            if(resp > 0){
                alert_toast("Data saved",'success');
                uni_modal("Check-in Details","manage_check_out.php?id="+resp);
                setTimeout(end_load, 1500);
            } else {
                alert('Error saving. Try again.');
                end_load();
            }
        },
        error: function() {
            alert('Error saving. Try again.');
            end_load();
        }
    });
});
</script>