$( document ).ready(function() {
    $('#ajax_btn').click(
        function() {
            var date_range = $('#date_range_input').val();

            $.ajax
            ({
                url: 'formhandler.php',
                data: {'daterange_txt': date_range, 'requesttype': 'async'},
                type: 'post',
                success: function(result) {
                    var respObject = JSON.parse(result);
                    $('#user_notifications_block p').text(respObject.subject);
                    $('#user_notifications_block').removeClass().addClass(respObject.type);
                }
            });
        }
    );
});