<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<!-- dropify Js -->
<script src="{{ asset('backend/vendor/dropify/dropify.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('.dropify').dropify();
        $('.dropify-errors-container').remove();
        CKEDITOR.replace('content', {
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

        $(document).on("submit", "#postAdd", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var fileVals = dropzone.getAcceptedFiles();
            var formData = new FormData(this);
            for (var i = 0; i < fileVals.length; i++) {
                formData.append('post_image[]', fileVals[i]);
            }
            storeUpdatePost(formData, url)
        });


        $(document).on("submit", "#postEditForm", function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var formData = new FormData(this);
            if ($('#post_image').length) {
                var fileVals = dropzone.getAcceptedFiles();
                for (var i = 0; i < fileVals.length; i++) {
                    formData.append('post_image[]', fileVals[i]);
                }
            }
            storeUpdatePost(formData, url)
        });
    });

    $(document).on('change', '#post_type', function(e){
        let postType = $(this).val();

        $('.post-content-fields').addClass('d-none');
        if(postType == 'text'){
            $('#post_text_main').removeClass('d-none');
        } else if(postType == 'image'){
            $('#post_image_main').removeClass('d-none');
        } else if(postType == 'video'){
            $('#post_video_main').removeClass('d-none');
        }
    });

    // update and store post
    function storeUpdatePost(formData, url) {
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
                        window.location.href = "{{ route('admin.posts.index') }}";
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
                        } else if (key === 'content') {
                            var selectElement = $("textarea[name='content']");
                            if (selectElement.length) {
                                selectElement.parent().append(errorLabelTitle);
                            }
                        } else if(key == 'post_image'){
                            $(`#${key}`).after(errorLabelTitle);
                        } else if(key == 'post_video'){
                            $(`#${key}`).parent('.dropify-wrapper').after(errorLabelTitle);
                        } else {
                            var inputElement = $("input[name='" + key +
                                "'], textarea[name='" + key + "']");
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
    let maxFileLimit = {{config('constant.post_image_max_file_count')}}

    let filesCount = 0;

    let preloaded = [];
    @if (isset($post->uploads) && $post->uploads)
        filesCount = {{$post->postImages()->count()}}
        @foreach ($post->postImages as $record)
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
    var dropzone = new Dropzone('#post_image', {
        url: "{% url 'dropzone/images' %}",
        addRemoveLinks: true,
        maxFiles: parseInt(maxFileLimit) - filesCount,
        init: function() {
        this.on('maxfilesexceeded', function(file) {
            $('#max-upload-image').html('<p>Sorry, Only up to '+maxFileLimit+' images are allowed!</p>');
            
        });
        
        // Optionally, clear the message when files are removed
        this.on('removedfile', function(file) {
            if (this.getAcceptedFiles().length <= maxFileLimit) {
                $('#max-upload-image').empty();
            }
        });
    }
    });

    dropzone.on('addedfile', function(file) {
        $('.dz-message').css('display', 'none');
        var countImage = $("#post_image").find(".dz-complete").length;
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
        var countImage = $("#post_image").find(".dz-complete").length;
        if (countImage > 0) {
            $('.dz-message').css('display', 'none');
        }
        var removeDocIds = $('#postDocIds').val();
        if (removeDocIds && file.id) {
            var imageIds = removeDocIds + ',' + file.id;
            $("#postDocIds").val(imageIds);
        } else if (file.id) {
            $("#postDocIds").val(file.id)
        }
    });
    /* dropzone end */

    // dropfiy clear
    $(document).on('click', '.dropify-clear', function(e){
        e.preventDefault();

        $("#postVideoRemoved").val(1);
    });
</script>
