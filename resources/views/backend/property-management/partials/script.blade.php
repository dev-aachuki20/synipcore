<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="{{ asset('backend/vendor/flatpickr/flatpickr.min.js') }}"></script>


<script>
    $(document).ready(function() {
        CKEDITOR.replace('description', {
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
            resize_enabled: false
        });

        $("#purchase_date").flatpickr({
            altInput: !0,
            /* minDate: 'today', */
            altFormat: "{{ config('constant.date_format.date') }}",
            dateFormat: "Y-m-d"
        })

        $(document).on("submit", "#propertyManagementAddForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var fileVals = dropzone.getAcceptedFiles();
            var formData = new FormData(this);
            for (var i = 0; i < fileVals.length; i++) {
                formData.append('property_image[]', fileVals[i]);
            }
            storeUpdatePropertyManagement(formData, url)
        });


        $(document).on("submit", "#propertyManagementEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            if ($('#property_image').length) {
                var fileVals = dropzone.getAcceptedFiles();
                for (var i = 0; i < fileVals.length; i++) {
                    formData.append('property_image[]', fileVals[i]);
                }
            }
            storeUpdatePropertyManagement(formData, url)
        });

    });

    $(document).on('change', '.society_id', function() {
        var societyUuid = $(this).val();
        getSocietyBuildingUnits(societyUuid, 'building');
    });

    // get units by building id
    $(document).on('change', '.building_id', function() {
        var buildingUuid = $(this).val();
        getSocietyBuildingUnits(buildingUuid, 'unit');
    });

    // update and store post
    function storeUpdatePropertyManagement(formData, url) {
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
                        window.location.href = "{{ route('admin.property-managements.index') }}";
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
                        if (key == 'purchase_date') {
                            $('#purchase_date').next('input[type="text"]').after(
                                errorLabelTitle);
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
                $('.loader-div').hide();
                $(".submitBtn").attr('disabled', false);
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
    @if (isset($propertyManagement->uploads) && $propertyManagement->uploads)
        @foreach ($propertyManagement->uploads as $record)
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
    var dropzone = new Dropzone('#property_image', {
        url: "{% url 'dropzone/images' %}",
        addRemoveLinks: true,
    });

    dropzone.on('addedfile', function(file) {
        $('.dz-message').css('display', 'none');
        var countImage = $("#property_image").find(".dz-complete").length;
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
        var countImage = $("#property_image").find(".dz-complete").length;
        if (countImage > 0) {
            $('.dz-message').css('display', 'none');
        }
        var removeDocIds = $('#property_managementIds').val();
        if (removeDocIds && file.id) {
            var imageIds = removeDocIds + ',' + file.id;
            $("#property_managementIds").val(imageIds);
        } else if (file.id) {
            $("#property_managementIds").val(file.id)
        }
    });
    /* dropzone end */
</script>
