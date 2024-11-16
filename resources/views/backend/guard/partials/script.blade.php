<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js";
    import {
        getAuth,
        signInWithPhoneNumber,
        RecaptchaVerifier
    } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js";

    $(document).ready(function() {

        // Secured security pin by *.
        // let realPassword = '';

        // function maskPasswordField() {
        //     // Update the password input to show asterisks
        //     $('#passwordField').val('*'.repeat(realPassword.length));
        //     $('#realPassword').val(realPassword); // Keep the real password in the hidden field
        // }

        // // Initialize the realPassword variable with the current password value (if any)
        // if ($('#passwordField').val()) {
        //     realPassword = $('#passwordField').val(); // Set the real password
        //     maskPasswordField(); // Mask the input on load
        // }

        // $('#passwordField').on('input', function(e) {
        //     let currentInput = $(this).val();
        //     if (currentInput.length < realPassword.length) {
        //         // If the length decreases, slice the realPassword
        //         realPassword = realPassword.substring(0, currentInput.length);
        //     } else {
        //         // If the length increases, add new characters to realPassword
        //         let newCharacters = currentInput.slice(realPassword.length);
        //         realPassword += newCharacters;
        //     }

        //     maskPasswordField(); // Mask the input
        // });

        // $('#passwordField').on('keydown', function(e) {
        //     // Prevent text selection with Ctrl+A
        //     if (e.ctrlKey && e.key === 'a') {
        //         e.preventDefault();
        //         $(this).select();
        //     } else if (e.key === 'Backspace' || e.key === 'Delete') {
        //         // Clear realPassword if the entire input is selected
        //         if (this.selectionStart === 0 && this.selectionEnd === realPassword.length) {
        //             realPassword = '';
        //             $(this).val('');
        //             maskPasswordField(); // Update the mask
        //         }
        //     }
        // });

        // $('#passwordField').on('paste', function(e) {
        //     e.preventDefault(); // Prevent pasting
        // });

        $(document).on("submit", "#guardAddForm, #guardEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var enabled = $("#is_enabled").prop('checked');
            var formData = new FormData(this);
            formData.append('status', enabled ? 1 : 0);

            storeUpdateGuard(formData, url)
        });

        // OTP
        let userId = "{{ $user->id ?? '' }}";
        let countryCode = "{{env('COUNTRY_CODE')}}";
        let number = "{{ $user->mobile_number ?? '' }}";
        let phoneNumber = countryCode + number;

        // Show OTP Field and Send OTP to User's Phone
        $(document).on('click', '.resetPasswordOtp', function(e) {
            e.preventDefault();

            const firebaseConfig = {
                apiKey: "AIzaSyAq1sFcaLk9-ZGIn9s-4QSUlWltx_Ip500",
                authDomain: "synipcore-15ffe.firebaseapp.com",
                projectId: "synipcore-15ffe",
                storageBucket: "synipcore-15ffe.appspot.com",
                messagingSenderId: "346930485687",
                appId: "1:346930485687:web:317f568e56c8ff6eb97032",
                measurementId: "G-WLCNT6JFPL"
            };

            const app = initializeApp(firebaseConfig);
            const auth = getAuth(app);

            // Initialize ReCAPTCHA
            const recaptchaVerifier = new RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible',
                'callback': (response) => {
                    console.log('Recaptcha verified automatically');
                },
                'expired-callback': () => {
                    console.log('Recaptcha expired');
                }
            }, auth);

            signInWithPhoneNumber(auth, phoneNumber, recaptchaVerifier)
                .then((confirmationResult) => {
                    window.confirmationResult = confirmationResult;
                    $('.enter_otp').removeClass('d-none');
                    toasterAlert('success', response.message);
                }).catch((error) => {
                    console.error("SMS not sent", error);
                });
        })

        // Verify OTP and Enable Password Field
        $(document).on('click', '.verify-otp', function(e) {
            e.preventDefault();
            let enteredOtp = $('#otp-input').val();
            window.confirmationResult.confirm(enteredOtp)
                .then((result) => {
                    const user = result.user;
                    toasterAlert('success', 'Otp Verifed');
                    $('.enter_otp').addClass('d-none');
                    $('#security_pin').attr('readonly', false);
                    $('#security_pin').prop('disabled', false);
                    $('.verify-otp').prop('disabled', true);
                    $('.otp-error-message').addClass('d-none');
                }).catch((error) => {
                    console.log(error);
                    $('.otp-error-message').removeClass('d-none');
                    $('.otp-error-message').text(enteredOtp.length == 0 ? 'Please enter Otp.' : 'Invalid OTP.');
                });
        });
    });

    // update and store post
    function storeUpdateGuard(formData, url) {
        $('.validation-error-block').remove();
        $('.loader-div').show();
        $(".submitBtn").attr('disabled', true);
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function(response) {
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
                if (response.success) {
                    toasterAlert('success', response.message);
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.guards.index') }}";
                    }, 1000);
                } else {
                    toasterAlert('error', response.message);
                }
            },
            error: function(response) {
                $('.loader-div').hide();
                $(".submitBtn").attr('disabled', false);
                if (response.responseJSON.error_type == 'something_error') {
                    toasterAlert('error', response.responseJSON.error);
                } else {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function(key, item) {
                        var errorLabelTitle =
                            '<span class="validation-error-block">' + item[0] +
                            '</span>';
                        if (key === 'status') {
                            var selectElement = $("select[name='status']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'resident_id') {
                            var selectElement = $("select[name='resident_id']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'society_id') {
                            var selectElement = $("select[name='society_id']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'building_id') {
                            var selectElement = $("select[name='building_id']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'unit_id') {
                            var selectElement = $("select[name='unit_id']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else {
                            var inputElement = $("input[name='" + key + "']");
                            if (inputElement.length) {
                                inputElement.after(errorLabelTitle);
                            }
                        }
                    });
                }
            },
            complete: function() {
                $('.loader-div').hide();
                $(".submitBtn").attr('disabled', false);
            }
        });
    }

    $(document).on('change', '.society_id', function() {
        var selectedValue = $(this).val();
        var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
        var societyId = selectedOption.data('society_id');

        if (!societyId) {
            $(".unit_id").html('');
            $(".building_id").html('');
            return;
        }

        $(".unit_id").html('');
        $(".building_id").html('');
        $.ajax({
            type: "GET",
            url: "{{ route('admin.filterLocation') }}",
            data: {
                type: 'get_building_by_society',
                id: societyId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(".building_id").html('');
                    $(".building_id").append(response.data);
                } else {}
            },
            error: function(response) {
                toasterAlert('error', response.responseJSON.error);
            }
        });
    });

    $(document).on('change', '.building_id', function() {
        var selectedValue = $(this).val();
        var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
        var buildingId = selectedOption.data('building_id');

        if (!buildingId) {
            $(".unit_id").html('');
            return;
        }

        $(".unit_id").html('');
        $.ajax({
            type: "GET",
            url: "{{ route('admin.filterLocation') }}",
            data: {
                type: 'get_unit_by_building',
                id: buildingId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(".unit_id").html('');
                    $(".unit_id").append(response.data);
                } else {

                }
            },
            error: function(response) {
                toasterAlert('error', response.responseJSON.error);
            }
        });
    });
</script>