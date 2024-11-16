<script src="{{asset('backend/vendor/flatpickr/flatpickr.min.js')}}"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>

<script>
    $(document).ready(function() {
        CKEDITOR.replace('message', {
            toolbar: [{
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink']
                },
                {
                    name: 'styles',
                    items: ['Font', 'FontSize']
                }
            ],
            removePlugins: 'elementspath',
            resize_enabled: false,
        });

        $("#expire_date").flatpickr({
            altInput: true,
            minDate: 'today',
            enableTime: true,
            altFormat: "{{ config('constant.flatpicker_date_format.date_time') }}", // Display format
            dateFormat: "Y-m-d h:i K", // 12-hour format with AM/PM
            time_24hr: false, // Ensure 12-hour format with AM/PM
        });

        // Announcement type change listener
        $(document).on('change', '.announcementType', function(e) {
            e.preventDefault();
            let type = $(this).find(":selected").val();
            if (type == 2) {
                $(".pollType").removeClass('d-none');
            } else {
                $(".pollType").addClass('d-none');
            }
        });

        // Preload the poll fields if editing an existing poll
        let selectedType = $('.announcementType').val();
        if (selectedType == 2) {
            $(".pollType").removeClass('d-none');
        }

        // Appending option for poll
        $(document).on('click', '.add-option', function() {
            let optionCount = $('.pollType .options').length;
            console.log(optionCount);
            $('.pollType .defaultOptRow').append(`
            <div class="option-row col-lg-6">
                <div class="form-group">
                    <label for="options" class="form-label">{{ trans('cruds.announcement.fields.option') }} ${optionCount + 1} <span
                    class="required">*</span></label>
                    <input type="text" name="options[]" class="form-control options" id="options_${optionCount + 1}">
                    <button type="button" class="btn btn-danger remove-option">-</button>
                </div>
            </div>
        `);
        });

        $(document).on('click', '.remove-option', function() {
            $(this).closest('.option-row').remove();
        });

        $(document).on("submit", "#announcementAddForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var fileVals = dropzone.getAcceptedFiles();
            var formData = new FormData(this);
            for (var i = 0; i < fileVals.length; i++) {
                formData.append('announcement_image[]', fileVals[i]);
            }
            storeUpdateAnnouncment(formData, url)
        });

        $(document).on("submit", "#announcementEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            if ($('#announcement_image').length) {
                var fileVals = dropzone.getAcceptedFiles();
                for (var i = 0; i < fileVals.length; i++) {
                    formData.append('announcement_image[]', fileVals[i]);
                }
            }
            storeUpdateAnnouncment(formData, url)
        });
    });

    // update and store announcements
    function storeUpdateAnnouncment(formData, url) {
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
                        window.location.href = "{{ route('admin.announcements.index') }}";
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
                        if (key === 'message') {
                            var selectElement = $("textarea[name='message']");
                            if (selectElement.length) {
                                selectElement.parent().append(errorLabelTitle);
                            }
                        } else if (key == 'expire_date') {
                            $('#expire_date').next('input[type="text"]').after(errorLabelTitle);

                        } else if (key.startsWith('options')) {
                            let optionIndex = key.split('.')[1]; // Extract the index (e.g., 'options.0' -> 0)

                            // Find the specific option input using the index
                            let optionField = $('.pollType .options').eq(optionIndex);

                            // Custom clean error message (without 'options.0')
                            var cleanErrorMessage = '<span class="validation-error-block text-danger">Option is required.</span>';

                            // Append the clean error message only to the related option field
                            if (optionField.length) {
                                optionField.after(cleanErrorMessage);
                            }
                        } else {
                            var inputElement = $("input[name='" + key +
                                "'], select[name='" + key + "'],textarea[name='" + key + "']");
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


    /* dropzone start */
    let excelFileExtensions = ['xls', 'xlsx', 'xml', 'csv', 'xlsm', 'xlw', 'xlr'];
    let wordFileExtensions = ['doc', 'docm', 'docx', 'dot'];
    let pdfFileExtensions = ['pdf'];
    let zipFileExtensions = ['zip', 'rar'];
    let filesExtensions = ['css', 'html', 'txt', 'php', 'sql', 'js'];
    // let maxFileLimit = {{ config('constant.post_image_max_file_count') }}


    let preloaded = [];
    @if (isset($announcement->uploads) && $announcement->uploads)
        @foreach ($announcement->uploads as $record)
            @php
                $fileNameArray = explode('/', $record->file_path);
                $fileName = end($fileNameArray);
                $fileSize = \File::size(public_path('storage/' . $record->file_path));
                if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                    $MediaImage = asset('storage/' . $record->file_path);
                } else {
                    $MediaImage = '';
                }
            @endphp
            preloaded.push(
                <?= json_encode(['id' => $record->id, 'src' => $MediaImage, 'documentType' => $record->type, 'fileName' => $fileName, 'size' => $fileSize]) ?>
            );
        @endforeach
    @endif

    Dropzone.autoDiscover = false;
    var dropzone = new Dropzone('#announcement_image', {
        url: "{% url 'dropzone/images' %}",
        addRemoveLinks: true,
    });

    dropzone.on('addedfile', function(file) {
        $('.dz-message').css('display', 'none');
        var countImage = $("#announcement_image").find(".dz-complete").length;
        if (countImage == -1) {
            $('.dz-message').css('display', 'block');
        }
        var documentType = file.name.split('.').pop();
        if ($.inArray(documentType, excelFileExtensions) != -1) {
            $(file.previewElement).find(".dz-image img").attr("src", "{{ asset('default_images/excel.png') }}");
        } else if ($.inArray(documentType, wordFileExtensions) != -1) {
            $(file.previewElement).find(".dz-image img").attr("src",
                "{{ asset('default_images/word-icon.png') }}");
        } else if ($.inArray(documentType, pdfFileExtensions) != -1) {
            $(file.previewElement).find(".dz-image img").attr("src",
                "{{ asset('default_images/pdf-icon.png') }}");
        } else if ($.inArray(documentType, zipFileExtensions) != -1) {
            $(file.previewElement).find(".dz-image img").attr("src",
                "{{ asset('default_images/zip-icon.png') }}");
        } else if ($.inArray(documentType, filesExtensions) != -1) {
            $(file.previewElement).find(".dz-image img").attr("src",
                "{{ asset('default_images/file-icon.png') }}");
        }
    });

    $.each(preloaded, function(key, value) {
        var mockFile = {
            name: value.fileName,
            size: value.size,
            path: value.src,
            id: value.id,
            type: value.documentType
        };
        dropzone.emit("addedfile", mockFile);
        if ($.inArray(value.documentType, excelFileExtensions) != -1) {
            dropzone.emit("thumbnail", mockFile, "{{ asset('default_images/excel.png') }}");
        } else if ($.inArray(value.documentType, wordFileExtensions) != -1) {
            dropzone.emit("thumbnail", mockFile, "{{ asset('default_images/word-icon.png') }}");
        } else if ($.inArray(value.documentType, pdfFileExtensions) != -1) {
            dropzone.emit("thumbnail", mockFile, "{{ asset('default_images/pdf-icon.png') }}");
        } else if ($.inArray(value.documentType, zipFileExtensions) != -1) {
            dropzone.emit("thumbnail", mockFile, "{{ asset('default_images/zip-icon.png') }}");
        } else if ($.inArray(value.documentType, filesExtensions) != -1) {
            dropzone.emit("thumbnail", mockFile, "{{ asset('default_images/file-icon.png') }}");
        } else {
            dropzone.emit("thumbnail", mockFile, value.src);
        }
        dropzone.emit("complete", mockFile);
    });

    dropzone.on("removedfile", function(file) {
        $('.dz-message').css('display', 'block');
        var countImage = $("#announcement_image").find(".dz-complete").length;
        if (countImage > 0) {
            $('.dz-message').css('display', 'none');
        }
        var removeDocIds = $('#announcement_managementIds').val();
        if (removeDocIds && file.id) {
            var imageIds = removeDocIds + ',' + file.id;
            $("#announcement_managementIds").val(imageIds);
        } else if (file.id) {
            $("#announcement_managementIds").val(file.id)
        }
    });
    /* dropzone end */
</script>