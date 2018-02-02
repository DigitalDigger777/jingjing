/**
 * Created by korman on 02.02.18.
 */
(function($){
    $('document').ready(function () {

        setInterval(function () {
            $.ajax({
                url: 'http://jingjing.fenglinfl.com/check-interval?mac=EC:FA:BC:85:9C:6F',
                success: function (data, textStatus, xhr) {
                    console.log(xhr.status);
                    if (xhr.status == 204) {
                        $('#status').text('Disabled');
                        $('.weui-btn').removeClass('weui-btn_disabled');
                        $('.weui-badge').css('background-color', '#f43530');
                        $('.weui-badge').css('color', '#fff');
                    }

                    if (xhr.status == 200) {
                        $('#status').text('Enabled');
                        $('.weui-badge').css('background-color', 'yellow');
                        $('.weui-badge').css('color', '#000');
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }, 1000);

        $('.weui-btn').click(function (e) {
            e.preventDefault();
            const interval  = $(this).attr('data-interval');
            var self        = $(this);

            $.ajax({
                url: 'http://jingjing.fenglinfl.com/add-schedule',
                data: {
                    mac: 'EC:FA:BC:85:9C:6F',
                    interval: interval
                },
                success: function () {
                    self.addClass('weui-btn_disabled');

                }
            });
        });
    });
})(jQuery);