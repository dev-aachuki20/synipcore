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
        $(document).on('click', '.toggle-password', function() {
            var passwordInput = $(this).prev('input');
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                $(this).removeClass('show-password');
            } else {
                passwordInput.attr('type', 'password');
                $(this).addClass('show-password');
            }
        });

        $(document).on("submit", "#userForm, #userEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');

            var verified = $("#mobile_verified").prop('checked');
            var enabled = $("#is_enabled").prop('checked');
            var formData = new FormData(this);
            formData.append('mobile_verified', verified ? 1 : 0);
            formData.append('status', enabled ? 1 : 0);
            storeUpdateUser(formData, url)
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
                    toasterAlert('success', "{{trans('messages.otp_sent_number')}}");
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
                    $('.enter_otp').addClass('d-none');
                    toasterAlert('success', "{{trans('messages.verified_otp')}}");
                    $('#password').attr('readonly', false);
                    $('#password').prop('disabled', false);
                    $('.verify-otp').prop('disabled', true);
                    $('.otp-error-message').addClass('d-none');
                }).catch((error) => {
                    console.log(error);
                    $('.otp-error-message').removeClass('d-none');
                    $('.otp-error-message').text(enteredOtp.length == 0 ? 'Please enter Otp.' : 'Invalid OTP.');
                });
        });

    });

    // update and store user
    function storeUpdateUser(formData, url) {
        $('.loader-div').show();
        $(".submitBtn").attr('disabled', true);
        $('.validation-error-block').remove();
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
                        window.location.href = "{{ route('admin.users.index') }}";
                    }, 1000);
                } else {
                    toasterAlert('error', response.message);
                }
            },
            error: function(response) {
                $(".loader-div").css('display', 'none');
                $(".submitBtn").attr('disabled', false);
                if (response.responseJSON.error_type == 'something_error') {
                    toasterAlert('error', response.responseJSON.error);
                } else {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function(key, item) {
                        var errorLabelTitle = '<span class="validation-error-block">' +
                            item[0] + '</span>';
                        if (key === 'roles') {
                            var selectElement = $("select[name='roles[]']");
                            if (selectElement.length) {
                                selectElement.after(errorLabelTitle);
                            }
                        } else if (key === 'password') {
                            var inputElement = $("input[name='" + key + "']");
                            if (inputElement.length) {
                                inputElement.parent().after(errorLabelTitle);
                            }
                        } else if (key === 'mobile_number') {
                            var inputElement = $("input[name='" + key + "']");
                            if (inputElement.length) {
                                inputElement.closest('.number_prefix').after(errorLabelTitle);
                            }
                        } else if (key === 'society_ids') {
                            var selectElement = $("select[name='society_ids[]']");
                            if (selectElement.length) {
                                selectElement.closest('.form-group').append(errorLabelTitle);
                            }
                        } else {
                            var inputElement = $("input[name='" + key +
                                "'], select[name='" + key + "']");
                            if (inputElement.length) {
                                inputElement.after(errorLabelTitle);
                            }
                        }
                    });
                }
            },
            complete: function() {
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
            }
        });
    }
</script>