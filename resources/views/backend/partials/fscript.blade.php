<!-- Vendor js -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="{{ asset('backend/js/vendor.min.js') }}"></script>

<!-- App js -->
<script src="{{ asset('backend/vendor/select2/js/select2.min.js') }}"></script>

<script src="{{ asset('backend/js/app.min.js') }}"></script>


<script src="{{ asset('backend/js/custom.js') }}"></script>

<script>
  $(document).ready(function() {
    $('.featureSelect').select2({
      templateResult: function(data) {
        if (!data.id) {
          return data.text;
        }
        // Customize the option display
        return $('<span class="checkBoxFlex"><div class="form-check"><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></div><div class="custom-option">' + data.text + '</div></span>');
      },
      templateSelection: function(data) {
        return data.text;
      }
    });

    // Check if the dropdown has data-refresh attribute set
    $('#notificationDropdown').on('click', function() {
      let url = "{{ route('admin.notifications') }}";
      let notificationDropdown = $(this);

      // If the dropdown has data-refresh attribute, initiate an AJAX call
      if (notificationDropdown.data('refresh')) {
        $.ajax({
          type: 'GET',
          url: url,
          dataType: 'json',
          data: {
            'type': 'header_notification'
          },
          success: function(response) {
            $('#notification-items').html(response.html);
            $('#notification-count').text(response.totalUnread);
          },
          error: function(xhr) {
            console.error('Failed to fetch notifications:', xhr);
          }
        });
      }
    });

    // mark-all-read
    $('#mark-all-read').on('click', function() {
      let url = "{{ route('admin.read.allNotifications') }}";

      $.ajax({
        type: 'POST',
        url: url,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          toasterAlert('success', response.message);
          window.location.reload();

          // $('#notification-items').html('<p class="dropdown-item notify-item">{{trans("global.no_new_notifications")}}</p>');
          // $('#notification-count').text(0);
        },
        error: function(xhr) {
          console.error('Failed to mark notifications as read:', xhr);
        }
      });
    });

    $(document).on('click', '.mark_single_read_notification', function() {
      // e.preventDefault();
      var notificationElement = $(this);
      var notificationId = notificationElement.data('notification-id');
      var notificationCount = $('#notification-count');
      var currentCount = parseInt(notificationCount.text());

      let url = "{{ route('admin.read.notifications') }}";
      $.ajax({
        type: 'POST',
        url: url,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
          _token: "{{ csrf_token() }}",
          id: notificationId
        },
        success: function() {
          notificationCount.text(currentCount - 1);
          // notificationElement.find('.notify-details').removeClass('fw-bold');
          // notificationElement.closest('a').remove();
          notificationElement.remove();
          // window.location.reload();
        },
        error: function(xhr) {
          console.error('Failed to mark notifications as read:', xhr);
        }
      });
    });

  });
</script>

@include('partials.alert')

<script>
  function getSocietyBuildingUnits(model_id, type) {
    var model_elmt = $("." + type + "_id");
    if (type == 'building') {
      $(".unit_id").html('');
    }
    model_elmt.html('');
    $.ajax({
      type: "GET",
      url: "{{ route('admin.get-society-building-unit-options') }}",
      data: {
        type: type,
        id: model_id
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          model_elmt.append(response.data);
        }
      },
      error: function(response) {
        toasterAlert('error', response.responseJSON.error);
      }
    });
  }
</script>