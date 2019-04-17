
/********MANUAL REFRESH PAGE*************/
jQuery(document).ready(function(){

    jQuery("#loader").hide();

    //TODO: Finish this functionality to resume polling of already running processes
    //check for running process
    // var check_run = function() {
    //     var data;
    //     data = { action: 'check_run'};
    //
    //
    //     jQuery.post(ajaxurl, data, function (response) {
    //         if (response === true) {
    //             jQuery("#loader").show();
    //             jQuery("#refresh").prop("disabled", true);
    //             poll_log();
    //         }
    //     })
    // };

    jQuery("#refresh").click( function() {
        // Data to send to the AJAX call
        var data;
        data = {
            action: 'import',
            beforeSend: function () {
                jQuery("#loader").show();
                jQuery("#refresh").prop("disabled", true);
            }
        };

        // ajaxurl is defined by WordPress
        jQuery.post(ajaxurl, data, function(response){
            console.log("Data refreshed - "+response);
            jQuery("#loader").hide();
            jQuery("#refresh").prop("disabled",false);
            clearInterval(poll_log());
        });
        //Poll the logs
        poll_log();
    });

    poll_log = function () {
        var data;
        data = {
            action: 'poll'
        };
        window.setInterval(function(){
            jQuery.post(ajaxurl, data, function(response){
                var logfeed = jQuery('#log_feed');
                logfeed.empty();
                logfeed.append(response);
                if(logfeed.length)
                    logfeed.scrollTop(logfeed[0].scrollHeight - logfeed.height());
            });
        }, 1000);
    }
});
