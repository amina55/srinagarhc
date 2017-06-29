$(document).ready(function(){

    //Pusher.logToConsole = true;

    var pusher = new Pusher(getTranslation('pusher_key'), {
        cluster: getTranslation('pusher_cluster')
    });

    var userId = $('body').data('user-id');
    var channel = pusher.subscribe('channel-' + userId);
    channel.bind('notify', function(data) {

        if(data.title == 'email_job_pusher_notification_title' && typeof data.project_id !== 'undefined') {
            var element = $('.project-spinner-'+data.project_id);
            $(element).data('status', 'cron');
            $(element).find('.job-queued, .job-inprogress').addClass('hidden');
            $(element).find('.job-idle').removeClass('hidden');
        } 
        var response = '<ul>';
        $.each(data.message, function (key, value) {
            response += '<li>' + getTranslation(key) + ' : ' + value + '</li>';
        });
        response += '</ul>';
        var modal = $('.pusher-response-modal:first').clone().appendTo('body');
        modal.find('.modal-title:first').html(getTranslation(data.title));
        modal.find('.modal-body:first').html(response);
        modal.modal();
        $('.alert-success').remove();
    })
});