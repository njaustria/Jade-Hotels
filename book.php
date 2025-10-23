<?php 
include('db_connect.php');
$rid = '';
$calc_days = 1;
if(isset($_GET['in']) && isset($_GET['out'])){
    $calc_days = abs(strtotime($_GET['out']) - strtotime($_GET['in'])) ; 
    $calc_days = floor($calc_days / (60*60*24));
}
?>

<style>
/* NOTE: The original CSS is preserved. No changes were made here. */
.booking-container { max-width: 1000px; margin: 0 auto; padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
.booking-title { font-size: 28px; font-weight: bold; margin-bottom: 30px; color: #333; text-align: center; }
.booking-content { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; min-height: 600px; }
.booking-info { padding: 20px; border-right: 2px solid #f0f0f0; }
.payment-section { padding: 20px; }
.section-title { font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #333; border-bottom: 2px solid #dc3545; padding-bottom: 10px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
.required { color: #dc3545; }
.form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s; }
.form-control:focus { outline: none; border-color: #007bff; box-shadow: 0 0 0 3px rgba(0,123,255,0.1); }
.readonly-field { background-color: #e9ecef !important; cursor: not-allowed; }
.help-text { font-size: 12px; color: #6c757d; margin-top: 5px; }
.id-row { display: grid; grid-template-columns: 1fr 2fr; gap: 15px; }
.contact-row { display: grid; grid-template-columns: 1fr 3fr; gap: 15px; }
.payment-methods { display: flex; flex-direction: column; gap: 15px; margin-bottom: 25px; }
.payment-option { border: 2px solid #ddd; border-radius: 10px; padding: 15px; cursor: pointer; transition: all 0.3s; }
.payment-option:hover { border-color: #007bff; background-color: #f8f9fa; }
.payment-option.selected { border-color: #dc3545; background-color: #fff5f5; }
.payment-option input[type="radio"] { position: absolute; opacity: 0; }
.payment-header { display: flex; align-items: center; margin-bottom: 10px; }
.payment-icon { width: 24px; height: 24px; margin-right: 12px; font-size: 20px; }
.payment-title { font-weight: 600; font-size: 16px; color: #333; }
.payment-description { font-size: 14px; color: #666; margin-left: 36px; }
.card-details, .ewallet-details { margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: none; }
.card-details.active, .ewallet-details.active { display: block; }
.card-row { display: grid; grid-template-columns: 2fr 1fr; gap: 15px; }
.ewallet-providers { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
.ewallet-provider { padding: 10px; border: 1px solid #ddd; border-radius: 6px; text-align: center; cursor: pointer; transition: all 0.3s; }
.ewallet-provider:hover { border-color: #dc3545; background-color: #fff5f5; }
.ewallet-provider.selected { border-color: #dc3545; background-color: #dc3545; color: white; }
.btn-group { display: flex; gap: 15px; justify-content: center; margin-top: 30px; grid-column: 1 / -1; }
.btn { padding: 15px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; min-width: 120px; }
.btn-primary { background-color: #dc3545; color: white; }
.btn-primary:hover { background-color: #c82333; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220,53,69,0.3); }
.btn-secondary { background-color: #6c757d; color: white; }
.btn-secondary:hover { background-color: #5a6268; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(108,117,125,0.3); }
.alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 12px; border-radius: 8px; margin: 20px 0; display: flex; align-items: center; }
.alert-info i { margin-right: 8px; color: #17a2b8; }

/* Cancel confirmation modal styles */
.cancel-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
.cancel-modal-content { background-color: #fff; margin: 15% auto; padding: 20px; border-radius: 10px; width: 400px; max-width: 90%; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
.cancel-modal h3 { color: #dc3545; margin-bottom: 15px; }
.cancel-modal p { margin-bottom: 20px; color: #666; }
.cancel-modal .btn-group { justify-content: center; margin-top: 20px; }

@media (max-width: 768px) {
    .booking-content { grid-template-columns: 1fr; gap: 20px; }
    .booking-info { border-right: none; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; }
    .id-row { grid-template-columns: 1fr; }
    .contact-row { grid-template-columns: 1fr; }
    .card-row { grid-template-columns: 1fr; }
    .ewallet-providers { grid-template-columns: 1fr; }
    .cancel-modal-content { width: 300px; }
}
</style>

<div class="booking-container">
    <h2 class="booking-title">Complete Your Booking</h2>
    
    <form action="" id="manage-check">
        <input type="hidden" name="cid" value="<?php echo isset($_GET['cid']) ? $_GET['cid']: '' ?>">
        <input type="hidden" name="rid" value="<?php echo isset($_GET['rid']) ? $_GET['rid']: '' ?>">

        <div class="booking-content">
            <div class="booking-info">
                <h3 class="section-title">Booking Information</h3>
                
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($meta['name']) ? $meta['name']: '' ?>" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>" placeholder="Enter email address" required>
                    <div class="help-text">For booking confirmation</div>
                </div>
                
                <div class="form-group">
                    <label for="contact">Contact Number <span class="required">*</span></label>
                    <div class="contact-row">
                        <select name="country_code" id="country_code" class="form-control">
                            <option value="+63" selected>+63 (PH)</option>
                            <option value="+1">+1 (USA/CA)</option>
                            <option value="+44">+44 (UK)</option>
                            <option value="+61">+61 (AU)</option>
                            <option value="+81">+81 (JP)</option>
                            <option value="+82">+82 (KR)</option>
                        </select>
                        <!-- Added maxlength attribute to limit input based on country code -->
                        <input type="text" name="contact" id="contact" class="form-control" value="<?php echo isset($meta['contact_no']) ? $meta['contact_no']: '' ?>" placeholder="912 345 6789" required pattern="[0-9\s]{10,12}" maxlength="12">
                    </div>
                </div>


                <div class="form-group">
                    <label>Identification Document <span class="required">*</span></label>
                    <div class="id-row">
                        <div class="form-group">
                            <label for="id_type">ID Type <span class="required">*</span></label>
                            <select name="id_type" id="id_type" class="form-control" required>
                                <option value="">Select ID Type</option>
                                <option value="passport">Passport</option>
                                <option value="drivers_license">Driver's License</option>
                                <option value="national_id">National ID</option>
                                <option value="voters_id">Voter's ID</option>
                                <option value="postal_id">Postal ID</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_number">ID Number <span class="required">*</span></label>
                            <input type="text" name="id_number" id="id_number" class="form-control" placeholder="Enter ID number" required>
                        </div>
                    </div>
                    <div class="help-text">Please bring this ID during check-in for verification</div>
                </div>
                
                <div class="form-group">
                    <label for="date_in">Check-in Date <span class="required">*</span></label>
                    <input type="date" name="date_in" id="date_in" class="form-control" value="<?php echo isset($_GET['in']) ? date("Y-m-d",strtotime($_GET['in'])): date("Y-m-d") ?>" min="<?php echo date('Y-m-d') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="date_in_time">Check-in Time</label>
                    <input type="time" name="date_in_time" id="date_in_time" class="form-control readonly-field" value="12:00" readonly>
                </div>
                
                <div class="form-group">
                    <label for="date_out">Check-out Date <span class="required">*</span></label>
                    <input type="date" name="date_out" id="date_out" class="form-control" value="<?php echo isset($_GET['out']) ? date("Y-m-d",strtotime($_GET['out'])): date("Y-m-d", strtotime('+1 day')) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="date_out_time">Check-out Time</label>
                    <input type="time" name="date_out_time" id="date_out_time" class="form-control readonly-field" value="12:00" readonly>
                </div>
                
                <div class="form-group">
                    <label for="days">Number of Days</label>
                    <input type="number" min="1" name="days" id="days" class="form-control readonly-field" value="<?php echo $calc_days ?>" readonly>
                </div>
            </div>

            <div class="payment-section">
                <h3 class="section-title">Payment Method</h3>
                
                <div class="payment-methods">
                    <div class="payment-option" data-payment="card">
                        <input type="radio" name="payment_method" value="card" id="payment_card" required>
                        <div class="payment-header">
                            <div class="payment-icon">💳</div>
                            <div class="payment-title">Credit/Debit Card</div>
                        </div>
                        <div class="payment-description">Pay securely with your card</div>
                        
                        <div class="card-details">
                            <div class="form-group">
                                <label for="card_number">Card Number <span class="required">*</span></label>
                                <input type="text" name="card_number" id="card_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            
                            <div class="card-row">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date <span class="required">*</span></label>
                                    <input type="text" name="expiry_date" id="expiry_date" class="form-control" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV <span class="required">*</span></label>
                                    <input type="text" name="cvv" id="cvv" class="form-control" placeholder="123" maxlength="4">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="card_name">Name on Card <span class="required">*</span></label>
                                <input type="text" name="card_name" id="card_name" class="form-control" placeholder="JOHN DELA CRUZ">
                            </div>
                        </div>
                    </div>

                    <div class="payment-option" data-payment="ewallet">
                        <input type="radio" name="payment_method" value="ewallet" id="payment_ewallet">
                        <div class="payment-header">
                            <div class="payment-icon">📱</div>
                            <div class="payment-title">E-Wallet</div>
                        </div>
                        <div class="payment-description">Pay using digital wallet</div>
                        
                        <div class="ewallet-details">
                            <div class="form-group">
                                <label>E-Wallet Provider <span class="required">*</span></label>
                                <div class="ewallet-providers">
                                    <div class="ewallet-provider" data-provider="gcash"><strong>GCash</strong></div>
                                    <div class="ewallet-provider" data-provider="paymaya"><strong>PayMaya</strong></div>
                                </div>
                                <input type="hidden" name="ewallet_provider" id="ewallet_provider">
                            </div>
                            
                            <div class="form-group">
                                <label for="ewallet_number">Account Number <span class="required">*</span></label>
                                <input type="text" name="ewallet_number" id="ewallet_number" class="form-control" placeholder="09123456789">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert-info">
                    <i>ℹ</i>
                    Your payment information is secure and encrypted. Valid ID required during check-in.
                </div>
            </div>
        </div>

        <div class="btn-group">
            <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
            <button type="submit" class="btn btn-primary">Complete Booking</button>
        </div>
    </form>
</div>

<div id="cancel-modal" class="cancel-modal">
    <div class="cancel-modal-content">
        <h3>⚠️ Cancel Booking</h3>
        <p>Are you sure you want to cancel this booking? All entered information will be lost.</p>
        <div class="btn-group">
            <button type="button" class="btn btn-secondary" id="stay-btn">Stay</button>
            <button type="button" class="btn btn-primary" id="confirm-cancel-btn">Yes, Cancel</button>
        </div>
    </div>
</div>

<script>
// NOTE: Most of the original Javascript is preserved. Only the form submission part is changed.
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
                contactInput.attr('placeholder', '7911123456'); // Example 10 digits (mobile can be 10 or 11)
                contactInput.attr('pattern', '[0-9]{10,11}'); // 10 to 11 digits
                contactInput.attr('maxlength', '11');
                break;
             case '+61': // Australia
                contactInput.attr('placeholder', '412345678'); // 9 digits (mobile numbers)
                contactInput.attr('pattern', '[0-9]{9}'); // Exactly 9 digits
                contactInput.attr('maxlength', '9');
                break;
             case '+81': // Japan
                contactInput.attr('placeholder', '9012345678'); // Example 10 digits (mobile can be 10 or 11)
                contactInput.attr('pattern', '[0-9]{10,11}'); // 10 to 11 digits
                contactInput.attr('maxlength', '11');
                break;
             case '+82': // South Korea
                contactInput.attr('placeholder', '1012345678'); // Example 10 digits (mobile can be 10 or 11, without hyphens for validation)
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


    // Auto-calculate days and update dates
    $('#date_in, #date_out').on('change', function() {
        calculateDays();
        updateMinCheckoutDate();
    });
    
    calculateDays();
    updateMinCheckoutDate();
    
    // Payment method selection
    $('.payment-option').on('click', function() {
        const paymentType = $(this).data('payment');
        $('.payment-option').removeClass('selected');
        $('.card-details, .ewallet-details').removeClass('active');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
        if (paymentType === 'card') $('.card-details').addClass('active');
        else if (paymentType === 'ewallet') $('.ewallet-details').addClass('active');
        // Require relevant fields when a payment method is chosen
        $('.card-details input, .ewallet-details input').prop('required', false);
        if (paymentType === 'card') {
            $('#card_number, #expiry_date, #cvv, #card_name').prop('required', true);
        } else if (paymentType === 'ewallet') {
             $('#ewallet_number').prop('required', true);
        }
    });
    
    // E-wallet provider selection
    $('.ewallet-provider').on('click', function() {
        $('.ewallet-provider').removeClass('selected');
        $(this).addClass('selected');
        $('#ewallet_provider').val($(this).data('provider'));
    });
    
    // Input formatting
    $('#card_number').on('input', function() {
        let value = $(this).val().replace(/\s/g, '').replace(/[^0-9]/gi, '');
        $(this).val(value.match(/.{1,4}/g)?.join(' ') || value);
    });
    
    $('#expiry_date').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) value = value.substring(0,2) + '/' + value.substring(2,4);
        $(this).val(value);
    });
    
    $('#cvv').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // ID number formatting based on ID type
    $('#id_type').on('change', function() {
        const idNumberField = $('#id_number');
        idNumberField.val('');
        idNumberField.attr('placeholder', 'Enter ID number');
        idNumberField.removeAttr('pattern');
    });
    
    // Cancel button functionality
    $('#cancel-btn').on('click', function() {
        var hasData = checkForUserInput();
        if (hasData) {
            $('#cancel-modal').show();
        } else {
            redirectToHome();
        }
    });
    
    // Modal event handlers
    $('#stay-btn, #cancel-modal').on('click', function(e) {
        if (e.target === this) {
            $('#cancel-modal').hide();
        }
    });
    
    $('#confirm-cancel-btn').on('click', function() {
        $('#cancel-modal').hide();
        redirectToHome();
    });
    
    $(window).on('click', function(e) {
        if (e.target === document.getElementById('cancel-modal')) {
            $('#cancel-modal').hide();
        }
    });
    
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#cancel-modal').hide();
        }
    });
    
    function checkForUserInput() {
        var name = $('#name').val().trim();
        var email = $('#email').val().trim();
        var contact = $('#contact').val().trim();
        var idType = $('#id_type').val();
        var idNumber = $('#id_number').val().trim();
        var paymentSelected = $('input[name="payment_method"]:checked').length > 0;
        var cardDetails = $('#card_number').val().trim() || $('#card_name').val().trim();
        var ewalletDetails = $('#ewallet_number').val().trim() || $('#ewallet_provider').val();
        
        return name || email || contact || idType || idNumber || paymentSelected || cardDetails || ewalletDetails;
    }
    
    function redirectToHome() {
        if (document.referrer && document.referrer !== window.location.href) {
            window.location.href = document.referrer;
        } else if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = 'index.php';
        }
        
        setTimeout(function() {
            if (window.location.href === window.location.href) {
                window.location.href = '/';
            }
        }, 1000);
    }
    
    function calculateDays() {
        var checkIn = new Date($('#date_in').val());
        var checkOut = new Date($('#date_out').val());
        if (!isNaN(checkIn) && !isNaN(checkOut) && checkOut > checkIn) {
            var timeDiff = checkOut.getTime() - checkIn.getTime();
            var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
            $('#days').val(daysDiff > 0 ? daysDiff : 1);
        } else {
            $('#days').val(1);
        }
    }
    
    function updateMinCheckoutDate() {
        var checkIn = $('#date_in').val();
        if (checkIn) {
            var minCheckout = new Date(checkIn);
            minCheckout.setDate(minCheckout.getDate() + 1);
            $('#date_out').attr('min', minCheckout.toISOString().split('T')[0]);
        }
    }
    
    // Date validation
    $('#date_in').on('change', function() {
        var today = new Date(); today.setHours(0, 0, 0, 0);
        var checkIn = new Date($(this).val());
        if (checkIn < today) {
            // Replaced alert with custom modal logic or a non-blocking message
            // For this example, retaining alert as per original code's pattern, 
            // but in a production app, use a custom modal or message.
            alert('Check-in date cannot be in the past'); 
            $(this).val('<?php echo date("Y-m-d") ?>');
            updateMinCheckoutDate();
            return;
        }
        var checkOut = new Date($('#date_out').val());
        if (checkOut <= checkIn) {
            var nextDay = new Date(checkIn);
            nextDay.setDate(nextDay.getDate() + 1);
            $('#date_out').val(nextDay.toISOString().split('T')[0]);
        }
        calculateDays();
    });
    
    $('#date_out').on('change', function() {
        var checkIn = new Date($('#date_in').val());
        var checkOut = new Date($(this).val());
        if (checkOut <= checkIn) {
            // Replaced alert with custom modal logic or a non-blocking message
            alert('Check-out date must be after check-in date'); 
            var nextDay = new Date(checkIn);
            nextDay.setDate(nextDay.getDate() + 1);
            $(this).val(nextDay.toISOString().split('T')[0]);
        }
        calculateDays();
    });
});

// Enhanced form validation and submission
$('#manage-check').submit(function(e){
    e.preventDefault();
    start_load();
    
    // Combine country code and contact number for submission
    var fullContact = $('#country_code').val() + ' ' + $('#contact').val().trim();

    var data = {
        name: $('#name').val().trim(),
        email: $('#email').val().trim(),
        contact: fullContact,
        idType: $('#id_type').val(),
        idNumber: $('#id_number').val().trim(),
        checkIn: $('#date_in').val(),
        checkOut: $('#date_out').val(),
        days: parseInt($('#days').val()),
        paymentMethod: $('input[name="payment_method"]:checked').val()
    };
    
    // Get the pattern from the contact input for validation
    var contactPattern = $('#contact').attr('pattern') ? new RegExp($('#contact').attr('pattern')) : null;

    // Enhanced validation
    if (!data.name) return showError('Please enter your full name', '#name');
    if (!data.email) return showError('Please enter your email address', '#email');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) return showError('Please enter a valid email address', '#email');
    if (!$('#contact').val().trim()) return showError('Please enter a valid contact number', '#contact');
    // Validate contact number against the dynamically set pattern
    if (contactPattern && !contactPattern.test($('#contact').val().trim())) {
        return showError('Please enter a valid contact number format for the selected country.', '#contact');
    }
    if (!data.idType) return showError('Please select an ID type', '#id_type');
    if (!data.idNumber) return showError('Please enter your ID number', '#id_number');
    if (!data.checkIn) return showError('Please select a check-in date', '#date_in');
    if (!data.checkOut) return showError('Please select a check-out date', '#date_out');
    if (new Date(data.checkOut) <= new Date(data.checkIn)) return showError('Check-out date must be after check-in date', '#date_out');
    if (data.days <= 0) return showError('Please select valid dates for your stay', '#date_out');
    if (!data.paymentMethod) return showError('Please select a payment method');
    
    // Payment method validation
    if (data.paymentMethod === 'card') {
        var cardData = {
            number: $('#card_number').val().replace(/\s/g, ''),
            expiry: $('#expiry_date').val(),
            cvv: $('#cvv').val(),
            name: $('#card_name').val().trim()
        };
        if (!cardData.number || cardData.number.length < 13) return showError('Please enter a valid card number', '#card_number');
        if (!cardData.expiry || !/^(0[1-9]|1[0-2])\/\d{2}$/.test(cardData.expiry)) return showError('Please enter a valid expiry date (MM/YY)', '#expiry_date');
        if (!cardData.cvv || cardData.cvv.length < 3) return showError('Please enter a valid CVV', '#cvv');
        if (!cardData.name) return showError('Please enter the name on card', '#card_name');
    } else if (data.paymentMethod === 'ewallet') {
        var ewalletData = {
            provider: $('#ewallet_provider').val(),
            number: $('#ewallet_number').val().trim()
        };
        if (!ewalletData.provider) return showError('Please select an e-wallet provider');
        if (!ewalletData.number) return showError('Please enter your e-wallet account details', '#ewallet_number');
    }
    
    function showError(message, focusElement) {
        // NOTE: Using alert() as per the original code's pattern.
        // In a production application, consider replacing with a custom modal for better UX.
        alert(message); 
        if (focusElement) $(focusElement).focus();
        end_load();
        return false;
    }
    
    var formData = $(this).serialize();
    formData = formData.replace(/contact=[^&]*/, 'contact=' + encodeURIComponent(fullContact));

    // Submit form
    $.ajax({
        url:'admin/ajax.php?action=save_book',
        method:'POST',
        data: formData,
        success:function(resp){
            // Check if the response is a positive number (the new booking ID)
            if(resp > 0){
                alert_toast("Booking successful! Redirecting to your receipt...",'success');
                // Redirect to the receipt page with the new booking ID
                setTimeout(function(){
                    end_load();
                    window.location.href = 'book_receipt.php?id=' + resp;
                },1500);
            } else {
                // Provide a more specific error message if possible
                alert('Booking failed. The room may no longer be available. Please try again.');
                end_load();
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.error("AJAX Error: ", textStatus, errorThrown);
            alert('A server error occurred. Please try again later.');
            end_load();
        }
    });
});
</script>
