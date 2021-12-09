
    jQuery(document).ready(function($) {
        $( document ).on( 'click', 'my-button', function(event) {
            event.preventDefault();

            // Use ajax to do something
            var postData = {
                action: 'test',
                my_var: 'my_data',
            }

            // Ajax load more posts
            $.ajax({
                type: 'POST',
                data: postData,
                dataType: 'json',
                url: test_js_vars.ajaxurl,

                success: function (response) {
                    alert( response.test );
                }
            }).fail(function (data) {
                console.log(data);
            });
        });
    )};

