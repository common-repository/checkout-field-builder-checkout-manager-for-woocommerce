jQuery(document).ready(function($) {

    $('form.checkout').on('checkout_place_order', function(e) {

        var isValid = true;
        var errorClass = "has-error";
        // Email validation
        $('input[type="email"]').each(function() {
            var wrapper = $(this).closest('.dcfem-field-wrapper');
            var emailField = $(this).val();
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            // Real-time validation
            $(this).on('input', function() {
                var currentValue = $(this).val();

                if (emailPattern.test(currentValue)) {
                    $(this).removeClass(errorClass);
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                    $('.woocommerce-error').remove(); // Optionally remove the WooCommerce error message
                } else {
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                }
            });

            // If the wrapper has the validate-required class, perform validation
            if (wrapper.hasClass('validate-required')) {
                if (!emailPattern.test(emailField)) {
                    isValid = false;
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                    $('form.checkout').prepend('<div class="woocommerce-error">Please enter a valid email.</div>'); // This triggers WooCommerce to show its error notice
                    return false; // Stop further validation
                } else {
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                }
            } else {
                if (emailField !== '' && !emailPattern.test(emailField)) {
                    isValid = false;
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                    $('form.checkout').prepend('<div class="woocommerce-error">Please enter a valid email.</div>');
                    return false; // Stop further validation
                } else {
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                }
            }
        });

        // Number validation
        $('input[type="number"]').each(function() {
            var wrapper = $(this).closest('.dcfem-field-wrapper');
            var numberPattern = /^[0-9]+$/;

            // Real-time validation
            $(this).on('input', function() {
                var currentValue = $(this).val();

                if (numberPattern.test(currentValue)) {
                    $(this).removeClass(errorClass);
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                    $('.woocommerce-error').remove(); // Optionally remove WooCommerce error message
                } else {
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                }
            });

            // Initial validation check
            var numberField = $(this).val();
            if (wrapper.hasClass('validate-required')) {
                if (!numberPattern.test(numberField)) {
                    isValid = false;
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                    $('form.checkout').prepend('<div class="woocommerce-error">Please enter a valid number.</div>');
                    return false;
                } else {
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                }
            } else {
                if (numberField !== '' && !numberPattern.test(numberField)) {
                    isValid = false;
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                    $('form.checkout').prepend('<div class="woocommerce-error">Please enter a valid number.</div>');
                    return false;
                } else {
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                }
            }
        });

        // URL validation
        $('input[type="url"]').each(function() {
            var wrapper = $(this).closest('.dcfem-field-wrapper');
            var urlPattern = /^(https?:\/\/)?([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,})([\/\w .-]*)*\/?$/;

            // Real-time validation
            $(this).on('input', function() {
                var currentValue = $(this).val();

                if (urlPattern.test(currentValue)) {
                    $(this).removeClass(errorClass);
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                    $('.woocommerce-error').remove(); // Optionally remove WooCommerce error message
                } else {
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                }
            });

            // Initial validation check
            var urlField = $(this).val();
            if (wrapper.hasClass('validate-required')) {
                if (!urlPattern.test(urlField)) {
                    isValid = false;
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                    $('form.checkout').prepend('<div class="woocommerce-error">Please enter a valid URL.</div>');
                    return false;
                } else {
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                }
            } else {
                if (urlField !== '' && !urlPattern.test(urlField)) {
                    isValid = false;
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                    $('form.checkout').prepend('<div class="woocommerce-error">Please enter a valid URL.</div>');
                    return false;
                } else {
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                }
            }
        });

        // Phone number validation
        $('input[type="tel"]').each(function() {
            var wrapper = $(this).closest('.dcfem-field-wrapper');
            var phonePattern = /^\+?[\d\s\-().]{7,20}$/;

            // Real-time validation
            $(this).on('input', function() {
                var currentValue = $(this).val();

                if (phonePattern.test(currentValue)) {
                    $(this).removeClass(errorClass);
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                    $('.woocommerce-error').remove(); // Optionally remove WooCommerce error message
                } else {
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                }
            });

            // Initial validation check
            var phoneField = $(this).val();
            if (wrapper.hasClass('validate-required')) {
                if (!phonePattern.test(phoneField)) {
                    isValid = false;
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                    $('form.checkout').prepend('<div class="woocommerce-error">Please enter a valid phone number.</div>');
                    return false;
                } else {
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                }
            } else {
                if (phoneField !== '' && !phonePattern.test(phoneField)) {
                    isValid = false;
                    $(this).addClass(errorClass);
                    $(this).addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                    $('form.checkout').prepend('<div class="woocommerce-error">Please enter a valid phone number.</div>');
                    return false;
                } else {
                    $(this).removeClass('woocommerce-invalid').addClass('woocommerce-validated');
                }
            }
        });

        // Prevent form submission if any validation fails
        if (!isValid) {
            return false;
        }
    });
}(jQuery));