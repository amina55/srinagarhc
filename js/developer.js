$(document).ready(function(){

    $("[class^='project-spinner-']").on('click', function () {
        var status = $(this).data('status');
        if(status == 'cron') {
            var shopId = $(this).data('shopId');
            var projectId = $(this).data('projectId');
            goToUrlWithConfirmation(baseUrl+'/send-emails/'+shopId+'/'+projectId+'/1', getTranslation('project_cron_msg'), getTranslation('project_cron_confirmation'));
        }
    });

    //############ Admin JS ##################//
    $('.data-tables').DataTable({
        'aoColumnDefs': [{
            //'bSortable': false,
            //'aTargets': [-1] /* 1st one, start by the right */
        }]
    });

    $('.campaign-data-tables').dataTable({
        "aaSorting": [],
        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false,
        }]
    });

    $('.projects-with-ab-testing-modal').modal('hide');

    $('.select-email-service').on('change', function () {
        if ($(this).val() == 'smtp') {
            $('#edit-shop-form #smtp-details').slideDown();
        } else {

            if ($(this).val() == 'mailjet' && $('.projects-with-ab-testing-modal').length) {
                $('.projects-with-ab-testing-modal').modal('show');
            }

            if ($('#edit-shop-form #smtp-details').is(':visible')) {
                $('#edit-shop-form #smtp-details').slideUp();
            }
        }
    });

    $('.recipient-tracking-category').on('change', function () {
        $('.recipient-tracking-request-type').hide();
        $('.recipient-tracking-rejection-type').hide();

        if ($(this).val() == 'rejected_emails') {
            $('.recipient-tracking-rejection-type').show();
        } else if($(this).val() != 'unsubscribe') {
            $('.recipient-tracking-request-type').show();
        }
    });

    $('.test-smtp-connection').on('click', function () {
        var form = $(this).parents('form:first');
        var port = form.find('#smtp_port').val();
        var host = form.find('#smtp_host').val();
        var username = form.find('#smtp_username').val();
        var password = form.find('#smtp_password').val();
        var encryption = form.find('#smtp_encryption').val();

        var loaderImg = $(this).data('loader-img');
        var overlay = '<div id="loading-overlay">' +
            '<img id="loading-overlay-img" src="'+loaderImg+'">' +
            '</div>';
        $('.alert').remove();
        $(overlay).appendTo('body');

        $.get($(this).data('url'), {
            port : port,
            host : host,
            username : username,
            password : password,
            encryption : encryption
        }, function (response) {            
            $('#loading-overlay').remove();            
            var alertBox = Alert.createOrGetAlertBox();
            var classToAdd = (response.status == 'success') ? 'alert-success' : 'alert-danger';
            var classToRemove = (response.status == 'success') ? 'alert-danger' : 'alert-success';
            alertBox.html(response.message);
            alertBox.addClass(classToAdd);
            alertBox.removeClass(classToRemove);
        }, 'json');
    });

    //############ End of Admin JS ##################//

    $('.toggle-advanced-placeholders').on('click', function () {
        if($(this).parent().find('.advanced-placeholders:first').is(':visible')) {
            $(this).parent().find('.transaction-times-placeholders').hide();
        }
        $(this).parent().find('.advanced-placeholders').slideToggle();
    });

    $('.toggle-transaction-times-placeholders').on('click', function () {
        $(this).parent().find('.transaction-times-placeholders').slideToggle();
    });

    var ListofDays = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    $('.send-emails-for-project-link').click(function () {
        var projectId = $(this).data('project-id');
        $('#confirmation_project_cron').val(projectId);
        $('#project_cron_confirmation_modal').modal();
    });

    var ckeditor_content = ['feedback_email_content', 'thanku_email_content', 'reminder1_content', 'reminder2_content', 'reminder3_content'];

    for(var i = 0 ; i < ckeditor_content.length; i++) {
        if($('#'+ckeditor_content[i]).length > 0) {
            CKEDITOR.replace( ckeditor_content[i], {
                filebrowserImageUploadUrl: baseUrl+'/upload-ckeditor-image',
                height:'500px'
            });
        }
    }

    if($('.view-default-template-modal').length > 0) {
        $('.view-default-template-modal').modal('toggle');
    }

    $('.campaign-steps .next').click(function(){
        if($('#campaign_type').length > 0) {
            var campaignType = $('#campaign_type').val();
            if(campaignType == 'sms') {
                setSMSContent();
            } else {
                changeContentOfAllEmails();
            }
        }
        var nextId = $(this).parents('.tab-pane').next().attr("id");
        $('[href=#'+nextId+']').tab('show');
        $('.campaign-steps .tab-pane').addClass('hidden');
        $('.campaign-steps #'+nextId).removeClass('hidden');
        window.scrollTo(0, 0);
        return false;
    });

    $('.campaign-steps .previous').click(function(){
        if($('#campaign_type').length > 0) {
            var campaignType = $('#campaign_type').val();
            if(campaignType == 'sms') {
                setSMSContent();
            } else {
                changeContentOfAllEmails();
            }
        }
        var prevId = $(this).parents('.tab-pane').prev().attr("id");
        $('[href=#'+prevId+']').tab('show');
        $('.campaign-steps .tab-pane').addClass('hidden');
        $('.campaign-steps #'+prevId).removeClass('hidden');
        window.scrollTo(0, 0);
        return false;
    });

    $('.campaign-steps a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if($('#campaign_type').length > 0) {
            var campaignType = $('#campaign_type').val();
            if(campaignType == 'sms') {
                setSMSContent();
            } else {
                changeContentOfAllEmails();
            }
        }
        //update progress
        var totalSteps = $('.campaign-steps').attr('data-campaign-steps');
        var currentStep = $(e.target).data('step');
        var targetTab = $(this).attr('href');
        updateCampaignStepsProgressBar(targetTab,totalSteps, currentStep)
    });

    function updateCampaignStepsProgressBar(targetTab,totalSteps,currentStep)
    {
        var percent = (parseInt(currentStep) / parseInt(totalSteps)) * 100;
        $('.progress-bar').css({width: percent + '%'});
        $('.progress-bar').text("Step " + currentStep + " of "+totalSteps);

        //hide steps
        $('.campaign-steps .tab-pane').addClass('hidden');
        $('.campaign-steps '+targetTab).removeClass('hidden');
    }

    function changeContentOfAllEmails()
    {
        var oldTemplateId = $('#old_template').val();
        var currentTemplateId = $("input[name=selected_template]:checked").val();
        var errors_count = $('#errors_count').val();

        if(errors_count == 0) {
            if(oldTemplateId != currentTemplateId && currentTemplateId != 0) {
                var result = getEmailContentAgainstTemplate(currentTemplateId);
                if(result) {
                    if(result.email_content) {
                        CKEDITOR.instances.feedback_email_content.setData(result.email_content);
                        CKEDITOR.instances.thanku_email_content.setData(result.email_content);
                        CKEDITOR.instances.reminder1_content.setData(result.email_content);
                        CKEDITOR.instances.reminder2_content.setData(result.email_content);
                        CKEDITOR.instances.reminder3_content.setData(result.email_content);
                        $('#feedback_email_plain_text,#reminder1_email_plain_text,#reminder2_email_plain_text,#reminder3_email_plain_text,#thankyou_email_plain_text').val('');
                    } else if(result.feedback_email_content && result.reminder_email_content && result.thanku_email_content) {
                        CKEDITOR.instances.feedback_email_content.setData(result.feedback_email_content);
                        CKEDITOR.instances.thanku_email_content.setData(result.thanku_email_content);
                        CKEDITOR.instances.reminder1_content.setData(result.reminder_email_content);
                        CKEDITOR.instances.reminder2_content.setData(result.reminder_email_content);
                        CKEDITOR.instances.reminder3_content.setData(result.reminder_email_content);
                        if(result.feedback_subject && $('#feedback_email_subject').length > 0 && $('#feedback_email_subject').val() == '') {
                            $('#feedback_email_subject').val(result.feedback_subject);
                        }
                    }
                }
            } else if(currentTemplateId == 0 && oldTemplateId != 0) {
                CKEDITOR.instances.feedback_email_content.setData('');
                CKEDITOR.instances.thanku_email_content.setData('');
                CKEDITOR.instances.reminder1_content.setData('');
                CKEDITOR.instances.reminder2_content.setData('');
                CKEDITOR.instances.reminder3_content.setData('');
                $('#feedback_email_plain_text,#reminder1_email_plain_text,#reminder2_email_plain_text,#reminder3_email_plain_text,#thankyou_email_plain_text').val('');
                $('#old_template').val(0);
            }
        }
        return false;
    }

    function getEmailContentAgainstTemplate(templateId)
    {
        var url = baseUrl+'/get-email-content-against-template/'+templateId;
        var emailContents = '';
        jQuery.ajax({
            url: url,
            dataType: 'json',
            type: 'GET',
            async :false,
            data: {},
            beforeSend : function(xhr, opts){
                $('.loader').removeClass('hidden');
            },
            success: function (result) {
                $('#old_template').val(templateId);
                emailContents = result;
            },
            complete: function(){
                $('.loader').addClass('hidden');
            }
        });
        return emailContents;
    }

    $('body').on('change','#enable_feedback_email_only',function(){
        if(this.checked) {
            enableFeedbackEmailOnly();
        } else {
            disableFeedbackEmailOnly();
        }
    });

    function enableFeedbackEmailOnly()
    {
        $('.feedback-section-btns .save_feedback_only_campaign').removeClass('hidden');
        $('.feedback-section-btns .next,.reminder-emails-section,.thanku-email-section').addClass('hidden');
        $('.reminder_emails_info .error-message,.thanku_email_info .error-message').html('');
        $('.reminder_emails_info .form-group,.thanku_email_info .form-group').removeClass('error-group');
        $('#enable_reminder2').prop('checked',false);
        $('.reminder-emails-section .required,.thanku-email-section .required').addClass('hidden');
        $('.campaign-steps').attr('data-campaign-steps',4);
        updateCampaignStepsProgressBar('#step4',4,4);
        disableReminder2();
        $('#reminder1_interval,#reminder1_subject,#thanku_email_subject,#reminder1_email_plain_text,#reminder2_email_plain_text,#reminder3_email_plain_text,#thankyou_email_plain_text').val('');
        CKEDITOR.instances.reminder1_content.setData('');
        CKEDITOR.instances.thanku_email_content.setData('');
    }

    function disableFeedbackEmailOnly()
    {
        $('.feedback-section-btns .save_feedback_only_campaign').addClass('hidden');
        $('.feedback-section-btns .next,.reminder-emails-section,.thanku-email-section').removeClass('hidden');
        $('.reminder_emails_info .error-message,.thanku_email_info .error-message').html('');
        $('.reminder_emails_info .form-group,.thanku_email_info .form-group').removeClass('error-group');
        $('#enable_reminder2').prop('checked',false);
        $('.reminder-emails-section .required,.thanku-email-section .required').addClass('hidden');
        $('.campaign-steps').attr('data-campaign-steps',6);
        updateCampaignStepsProgressBar('#step4',6,4);
        disableReminder2();
        $('#thanku_email_subject').val('');
        $('#reminder1_interval,#reminder1_subject,#thanku_email_subject').val('');
        var reminder1EmailContent = '';
        var thankuEmailContent = '';
        var reminder1EmailPlainText = '';
        var thankuEmailPlainText = '';
        if($('input:radio[name=selected_template]')) {
            var templateId = $('input:radio[name=selected_template]:checked').val();
            var result = getEmailContentAgainstTemplate(templateId);
            if(result) {
                if(result.email_content) {
                    reminder1EmailContent = result.email_content;
                    thankuEmailContent = result.email_content;
                } else if(result.reminder_email_content && result.thanku_email_content) {
                    reminder1EmailContent = result.reminder_email_content;
                    thankuEmailContent = result.thanku_email_content;
                }
            }
        }
        CKEDITOR.instances.reminder1_content.setData(reminder1EmailContent);
        CKEDITOR.instances.thanku_email_content.setData(thankuEmailContent);
        $('#reminder1_email_plain_text').val(reminder1EmailPlainText);
        $('#thankyou_email_plain_text').val(thankuEmailPlainText);
    }

    $('body').on('change', '#enable_reminder2', function () {
        if (this.checked) {
            enableReminder2();
        } else {
            disableReminder2();
        }
    });

    function enableReminder2()
    {
        $('.reminder3-section,.reminder2-detailed-info').addClass("hidden");
        $('#reminder3_interval,#reminder3_subject').val('');
        CKEDITOR.instances.reminder3_content.setData('');
        $('#use_reminder1_info_for_reminder3,#enable_reminder3').prop('checked',false);
        $('.reminder3-section .error-message,.reminder2-section .error-message').html('');
        $('.reminder3-section .form-group,.reminder2-section .form-group').removeClass('error-group');
        var reminder1_subject = $('#reminder1_subject').val();
        var reminder1_content = CKEDITOR.instances.reminder1_content.getData();
        $('#reminder2_subject').val(reminder1_subject);
        CKEDITOR.instances.reminder2_content.setData(reminder1_content);
        $('#reminder2_email_plain_text').val($('#reminder1_email_plain_text').val());
        $('#use_reminder1_info_for_reminder2').prop('checked',true);
        $('.reminder2-section').removeClass("hidden");
    }

    function disableReminder2()
    {
        $('.reminder3-section,.reminder2-section').addClass("hidden");
        $('#reminder3_interval,#reminder3_subject,#reminder2_interval,#reminder2_subject,#reminder2_email_plain_text,#reminder3_email_plain_text').val('');
        CKEDITOR.instances.reminder3_content.setData('');
        $('#use_reminder1_info_for_reminder3,#enable_reminder3,#use_reminder1_info_for_reminder2').prop('checked',false);
        $('.reminder3-section .error-message,.reminder2-section .error-message').html('');
        $('.reminder3-section .form-group,.reminder2-section .form-group').removeClass('error-group');
        CKEDITOR.instances.reminder2_content.setData('');
    }

    $('body').on('change', '#use_reminder1_info_for_reminder2', function () {
        if (this.checked) {
            $('.reminder2-detailed-info').addClass("hidden");
            var reminder1_subject = $('#reminder1_subject').val();
            var reminder1_content = CKEDITOR.instances.reminder1_content.getData();
            $('#reminder2_subject').val(reminder1_subject);
            CKEDITOR.instances.reminder2_content.setData(reminder1_content);
            $('#reminder2_email_plain_text').val($('#reminder1_email_plain_text').val());
        } else {
            $('#reminder2_subject').val('');
            var reminderContent = '';
            if($('input:radio[name=selected_template]')) {
                var templateId = $('input:radio[name=selected_template]:checked').val();
                var result = getEmailContentAgainstTemplate(templateId);
                var reminderEmailPlainText = '';
                if(result) {
                    if(result.email_content) {
                        reminderContent = result.email_content;
                    } else if(result.reminder_email_content) {
                        reminderContent = result.reminder_email_content;
                    }
                }
            }
            CKEDITOR.instances.reminder2_content.setData(reminderContent);
            $('#reminder2_email_plain_text').val(reminderEmailPlainText);
            $('.reminder2-detailed-info').removeClass("hidden");
        }
    });

    $('body').on('change', '#enable_reminder3', function () {
        if (this.checked) {
            enableReminder3();
        } else {
            disableReminder3();
        }
    });

    function enableReminder3()
    {
        $('.reminder3-section .error-message').html('');
        $('.reminder3-section .form-group').removeClass('error-group');
        var reminder1_subject = $('#reminder1_subject').val();
        var reminder1_content = CKEDITOR.instances.reminder1_content.getData();
        $('#reminder3_subject').val(reminder1_subject);
        CKEDITOR.instances.reminder3_content.setData(reminder1_content);
        $('#reminder3_email_plain_text').val($('#reminder1_email_plain_text').val());
        $('#use_reminder1_info_for_reminder3').prop('checked',true);
        $('.reminder3-detailed-info').addClass('hidden');
        $('.reminder3-section').removeClass("hidden");
    }

    function disableReminder3()
    {
        $('.reminder3-section .error-message').html('');
        $('.reminder3-section .form-group').removeClass('error-group');
        $('.reminder3-section').addClass("hidden");
        $('#reminder3_interval,#reminder3_subject,#reminder3_email_plain_text').val('');
        CKEDITOR.instances.reminder3_content.setData('');
        $('#use_reminder1_info_for_reminder3').prop('checked',false);
    }

    $('body').on('change', '#use_reminder1_info_for_reminder3', function () {
        if (this.checked) {
            $('.reminder3-detailed-info').addClass("hidden");
            var reminder1_subject = $('#reminder1_subject').val();
            var reminder1_content = CKEDITOR.instances.reminder1_content.getData();
            $('#reminder3_subject').val(reminder1_subject);
            CKEDITOR.instances.reminder3_content.setData(reminder1_content);
            $('#reminder3_email_plain_text').val($('#reminder1_email_plain_text').val());
        } else {
            $('#reminder3_subject').val('');
            var reminderContent = '';
            if($('input:radio[name=selected_template]')) {
                var templateId = $('input:radio[name=selected_template]:checked').val();
                var result = getEmailContentAgainstTemplate(templateId);
                var reminderEmailPlainText = '';
                if(result) {
                    if(result.email_content) {
                        reminderContent = result.email_content;
                    } else if(result.reminder_email_content) {
                        reminderContent = result.reminder_email_content;
                    }
                }
            }
            CKEDITOR.instances.reminder3_content.setData(reminderContent);
            $('#reminder3_email_plain_text').val(reminderEmailPlainText);
            $('.reminder3-detailed-info').removeClass("hidden");
        }
    });

    $('body').on('click', '.close-modal', function () {
        $(this).closest('.modal').find('#campaign_1').val('');
        $(this).closest('.modal').find('#campaign_2').val('');
        $(this).closest('.modal').modal('toggle');
        $(this).closest('.modal').find('.error-message').css('display','none');
        return false;
    });

    // A/B testing js
    $('body').on('click', '.open-ab-settings-modal', function () {
        $('#ab-testing-error-span').css('display','none');
        $('#ab-testing-error-span').html('');
        $('#campaign_1,#campaign_2,#winning_campaign_duration').val('');
        $('.ab-testing-modal').modal('toggle');
    });

    $('#abtesting_end_date').datetimepicker({
        dateFormat : 'mm/dd/yy',
        showTimepicker : false,
        minDate : addDaysInCurrentDate(1),
        showButtonPanel : false
    });

    function addDaysInCurrentDate(days)
    {
        return new Date(new Date().getTime() + (days * 86400000));
    }

    $('body').on('click', '#save-AB-testing-settings', function () {
        var campaign1 = $("#campaign_1").val();
        var campaign2 = $("#campaign_2").val();
        var abtestingEndDate = $('#abtesting_end_date').val();
        var errorMessage = validateCampaignsForABTesting(campaign1,campaign2);
        var regex = /^\d{2}\/\d{2}\/\d{4}$/

        if(!errorMessage) {
            if(!abtestingEndDate) {
                errorMessage = getTranslation('required_end_date');
            } else if(!regex.test(abtestingEndDate)) {
                errorMessage = getTranslation('invalid_date_format');
            } else if(!validateDate(abtestingEndDate)) {
                errorMessage = getTranslation('invalid_date');
            } else {
                var tomorrow = addDaysInCurrentDate(1);
                var tomorrowMonth = ((tomorrow.getMonth()+1 < 10) ? '0' : '') + (tomorrow.getMonth()+1);
                var tomorrowDate  = ((tomorrow.getDate() < 10) ? '0' : '') + tomorrow.getDate();
                var tomorrowDateString = Date.parse(tomorrow.getFullYear()+'-'+tomorrowMonth+'-'+tomorrowDate).toString();
                var endDateInParts = abtestingEndDate.split('/');
                var abtestingEndDateString = Date.parse(endDateInParts[2]+'-'+endDateInParts[0]+'-'+endDateInParts[1]).toString();

                if(abtestingEndDateString < tomorrowDateString) {
                    errorMessage = getTranslation('wrong_abtesting_duration');
                } else {
                    $('#winning_campaign_duration').val(endDateInParts[2]+'-'+endDateInParts[0]+'-'+endDateInParts[1]);
                }
            }
        }
        if(errorMessage) {
            $('#ab-testing-error-span').html(errorMessage);
            $('#ab-testing-error-span').css('display','block');
            return false;
        } else {
            $(this).closest('.modal').modal('toggle');
            $(this).closest('form').submit();
        }
    });

    function validateCampaignsForABTesting(campaign1, campaign2) {
        var error_message = '';
        if(campaign1 != '' && campaign2 != '' && campaign1 != campaign2) {
            error_message = '';
        } else if(campaign1 == '' || campaign2 == '') {
            error_message = getTranslation('required_two_campaigns');
        } else if(campaign1 != '' && campaign2 != '' && campaign1 == campaign2) {
            error_message = getTranslation('select_different_campaigns');
        }
        return error_message;
    }

    $('body').on('click', '.open-disable-testing-mode-popup', function () {
        $('.disable-ab-testing-mode').modal('toggle');
    });

    function getTranslation(key) {
        return LOCALE[key] ? LOCALE[key] : key;
    }

    $('body').on('click', '.open-email-preview', function () {
        var email_type = $(this).attr('data-email-type');
        var content_field_id = $(this).attr('data-related-field-id');
        //var content_field_id = $(this).closest('.email-content-section').find('.form-control').attr('id');
        var content = CKEDITOR.instances[content_field_id].getData();
//        document.getElementById('desktop-preview').src = "data:text/html;charset=utf-8," + escape(content);
//        document.getElementById('mobile-preview').src = "data:text/html;charset=utf-8," + escape(content);
        var $desktop_frame = $('<iframe id="desktop-preview">');
        $('.desktop-frame').html( $desktop_frame );
        var $mobile_frame = $('<iframe id="mobile-preview" class="mobile-iframe">');
        $('.mobile-frame').html( $mobile_frame );
        setTimeout( function() {
            var doc = $desktop_frame[0].contentWindow.document;
            var $body = $('body',doc);
            $body.html(content);
            var doc = $mobile_frame[0].contentWindow.document;
            var $body = $('body',doc);
            $body.html(content);
        }, 1 );
        $('.preview-container-header .email_type').html(email_type+' - ');
        $('.preview-container-header h3').addClass('h3-style');
        $('.preview-container').removeClass('hidden');
    });

    $('body').on('click', '.exit-preview-mode', function () {
        $('.preview-container-body #desktop-preview').attr('src','');
        $('.preview-container-body #mobile-preview').attr('src','');
        $('.preview-container-header .email_type').html('');
        $('body').addClass('bg');
        $('.box-header h3').addClass('h3-style');
        $('.preview-container').addClass('hidden');
    });

    $('body').on('click','.save_project_btn',function() {
        $(this).closest('form').submit();
    });

    $('body').on('click', '.open-test-campaign-emails-popup', function () {
        var isSenderActive = $(this).data('is-sender-active');
        if(isSenderActive === 'yes') {
            var campaign_id = $(this).data('campaign-id');
            var enable_feedback_email_only = $(this).data('feedback-email-enabled-only');
            var reminder2_enabled = $(this).data('reminder2-enabled');
            var reminder3_enabled = $(this).data('reminder3-enabled');
            var emailTypeDropdownHtml = makeEmailTypeDropDown(reminder2_enabled,reminder3_enabled,enable_feedback_email_only);
            $('.input-field').val('');
            $('#product_id').val('12345678');
            $('#product_name').val('Test product');
            var min = 10000000;
            var max = 99999999;
            var randomNumber = Math.floor(Math.random()*(max-min+1)+min);
            $('#transaction_id').val(randomNumber);
            $('#email_type').html(emailTypeDropdownHtml);
            $("#campaign_id").val(campaign_id);
            $('.test-campaign-emails-modal').modal('toggle');
        } else {
            $('.inactive_sender_notification').modal('toggle');
        }        
    });

    function makeEmailTypeDropDown(reminder2_enabled,reminder3_enabled,enable_feedback_email_only)
    {
        var html = '';
        if(enable_feedback_email_only === 'yes') {
            html += "<option value='feedback'>"+getTranslation('feedback_email')+"</option>";
        } else {
            html += "<option value='all'>"+getTranslation('all')+"</option>";
            html += "<option value='feedback'>"+getTranslation('feedback_email')+"</option>";
            html += "<option value='reminder1'>"+getTranslation('reminder1_email')+"</option>";
            if(reminder2_enabled === 'yes') {
                html += "<option value='reminder2'>"+getTranslation('reminder2_email')+"</option>";
            }
            if(reminder3_enabled === 'yes') {
                html += "<option value='reminder3'>"+getTranslation('reminder3_email')+"</option>";
            }
            html += "<option value='thanku'>"+getTranslation('thanku_email')+"</option>";
        }
        return html;
    }

    jQuery('.form-cls').validate({
        errorElement: "span",
        errorClass: "form-error-message",
        errorPlacement: function (error, element) {
            if (element[0]) {
                error.attr('style', 'display: block;');
                jQuery('#' + element[0].id + '-error').replaceWith(error);
            }
        },
        rules: {
            email_type : {
                required : true
            },
            email: {
                required:true,
                email:true
            },
            product_id: {
                required:true
            },
            transaction_id: {
                required:true,
                digits: true
            },
            transaction_time: {
                required:true,
                dateTimeFormat:true,
                validateDateTime:true
            },
            campaign_id: {
                required:true
            },
            project_id: {
                required:true
            },
            product_name: {
                required:true
            }
        },
        messages: {
            email_type: {
                required: getTranslation('required_email_type')
            },            
            email: {
                required:getTranslation('required_email'),
                email:getTranslation('invalid_email')
            },
            product_id: {
                required: getTranslation('required_product_id')
            },
            transaction_id: {
                required: getTranslation('required_transaction_id'),
                digits: getTranslation('digits_only'),
            },
            transaction_time: {
                required:getTranslation('required_transaction_time')
            },
            product_name: {
                required:getTranslation('required_product_name')
            }
        },
        submitHandler: function (form) {
            $('#'+form.id).closest('.modal').modal('toggle');
            form.submit();
        }
    });

    jQuery.validator.addMethod("dateTimeFormat", function (value, element) {
        var regex = /^\d{2}\/\d{2}\/\d{4} \d{2}\:\d{2}\:\d{2}$/
        return this.optional(element) || regex.test(value);
    }, getTranslation('invalid_date_time_format'));

    jQuery.validator.addMethod("validateDateTime", function (value, element) {
        var dateTime = value.split(" ");
        var dateValidation = validateDate(dateTime[0]);
        var timeValidation = validateTime(dateTime[1]);
        return this.optional(element) || (dateValidation && timeValidation);
    }, getTranslation("invalid_date_time"));

    function validateDate(date)
    {
        date = date.split("/");

        var month = date[0];
        var day = date[1];
        var year = date[2];
        var dateValidationStatus = false;
        var zeroDateCheck = checkIfDateHasZeros(day, month, year);
        if (zeroDateCheck == true) {
            var dateLimitCheck = checkIfDateExceedsDateLimit(day, month);
            if (dateLimitCheck == true) {
                var leapYearCheck = checkIfYearIsLeap(day, month, year);
                if (leapYearCheck == true) {
                    dateValidationStatus = true;
                }
            }
        }
        return dateValidationStatus;
    }

    function validateTime(time)
    {
        time = time.split(":");
        var hour = time[0];
        var minute = time[1];
        var second = time[2];

        var timeValidationStatus = false;
        if(hour >= 0 && hour <= 23) {
            if(minute >= 0 && minute <= 59) {
                if(second >= 0 && second <= 59) {
                    timeValidationStatus = true;
                }
            }
        }
        return timeValidationStatus;
    }

    function checkIfDateHasZeros(date, month, year) {
        if (date != "" && month != "" && year != "") {
            if (date != "00" && month != "00" && year != "0000") {
                return true;
            }
        }
        return false;
    }

    function checkIfDateExceedsDateLimit(date, month) {
        if (date <= ListofDays[month - 1]) {
            return true;
        }
        return false;
    }

    function checkIfYearIsLeap(date, month, year) {
        var leapYear = ((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0);
        if (date == 29 && month == 2 && leapYear == false) {
            return false;
        }
        return true;
    }

    $('#campaign_type').on('change',function(){
        var selectedCampaignType = $(this).val();
        commonOperationsOnCampaignTypeChange();
        $('input[name=selected_campaign_type]').val(selectedCampaignType);
        if(selectedCampaignType === 'telephone') {
            campaignTypeChangeToTelephone();                       
        } else if(selectedCampaignType === 'sms') {  
            campaignTypeChangeToSMS();                                
        } else {
            campaignTypeChangeToEmail();            
        } 
    });
  
    function campaignTypeChangeToTelephone()
    {
        $('.campaign-steps-progressbar,.basic-campaign-info-btns .next,.campaign_detail_info_tabs').addClass('hidden');            
        $('.call_script_section,.save_telephone_campaign').removeClass('hidden');  
        $('.campaign-steps').attr('data-campaign-steps',1);
    }
    
    function campaignTypeChangeToSMS()
    {
        var totalSteps = 4;                                
        $('.call_script_section,.save_telephone_campaign,.sender_info_for_email_campaign,.template_selection_for_email_campaign,.inbound_sms_settings,.feedback_email,.campaign_step5_tab_header,.campaign_step6_tab_header').addClass('hidden');
        $('.campaign_step2_tab_header,.campaign_step3_tab_header,.campaign_step4_tab_header,.campaign-steps-progressbar,.basic-campaign-info-btns .next,.template_selection_for_sms_campaign,.sms_sender_info,.outbound_sms_settings,.delayed-inbound-sms,.delayed-outbound-sms').removeClass('hidden');                
        $('.step2_tab_heading').html(getTranslation('template_selection'));
        $('.step3_tab_heading').html(getTranslation('sender_info'));
        $('.step4_tab_heading').html(getTranslation('sms_settings'));
        $('#sms_sender_name').val('eKomi');
        $('.campaign-steps').attr('data-campaign-steps',totalSteps);
        updateCampaignStepsProgressBar('#step1',totalSteps,1);
        $('input[name="sms_method"][value="outbound"]').prop('checked',true);
        $('.outbound_sms_templates').removeClass('hidden');
        $('.inbound_sms_templates').addClass('hidden');            
        $('.first_outbound_template input:radio').prop('checked',true);
        $('#enable_inbound_sms_instant_reviews,#enable_outbound_sms_instant_reviews').prop('checked',false);
    }
    
    function campaignTypeChangeToEmail()
    {
        var totalSteps = 6;
        $('.campaign-steps-progressbar,.basic-campaign-info-btns .next,.campaign_step2_tab_header,.campaign_step3_tab_header,.campaign_step4_tab_header,.campaign_step5_tab_header,.campaign_step6_tab_header,.template_selection_for_email_campaign,.feedback_email,.sender_info_for_email_campaign').removeClass('hidden');
        $('.call_script_section,.save_telephone_campaign,.template_selection_for_sms_campaign,.sms_sender_info,.inbound_sms_settings,.outbound_sms_settings').addClass('hidden');        
        $('.step2_tab_heading').html(getTranslation('sender_info'));
        $('.step3_tab_heading').html(getTranslation('template_selection'));
        $('.step4_tab_heading').html(getTranslation('feedback_email'));
        $('.campaign-steps').attr('data-campaign-steps',totalSteps);
        updateCampaignStepsProgressBar('#step1',totalSteps,1);
    }
    
    function commonOperationsOnCampaignTypeChange()
    {                                        
        $('input[name="selected_template"][value="0"]').prop('checked', true);
        $('#enable_feedback_email_only,#enable_reminder2,#enable_outbound_sms_instant_reviews,#enable_inbound_sms_instant_reviews,#enable_email_instant_reviews').prop('checked',false);
        $('#feedback_email_delay,#outbound_sms_sending_delay,#inbound_sms_sending_delay').removeAttr('readonly');        
        $('#feedback_email_delay').val(5);        
        $('#outbound_sms_sending_delay,#inbound_sms_sending_delay,#old_sms_template').val(0);
        $('.campaign_sender_info input,#feedback_email_subject,#reminder1_interval,#reminder1_subject,#thanku_email_subject,#inbound_sms_content,#outbound_sms_content,#call_script,#sms_sender_name').val(''); 
        $('.campaign_sender_info .error-message,.feedback_email_info .error-message,.reminder_emails_info .error-message,.thanku_email_info .error-message,.sms_settings_section .error-message,.call_script_section .error-message,.campaign_sms_sender_info .error-message').html('');
        $('.campaign_sender_info .form-group,.feedback_email_info .form-group,.reminder_emails_info .form-group,.thanku_email_info .form-group,.sms_settings_section .form-group,.call_script_section.form-group,.campaign_sms_sender_info .form-group').removeClass('error-group');
        $('.campaign_detail_info_tabs .required').addClass('hidden'); 
        $('.delayed-feedback-email').removeClass('hidden');
        CKEDITOR.instances.feedback_email_content.setData('');
        CKEDITOR.instances.reminder1_content.setData('');   
        CKEDITOR.instances.thanku_email_content.setData('');          
        disableReminder2();                         
    }

    $('input[name=sms_method]').on('change',function(){
        var smsMethod = $(this).val();
        var totalSteps = 0;
        if(smsMethod === 'inbound') {
            totalSteps = 3;
            smsMethodChangeToInbound();           
        } else {
            totalSteps = 4;
            smsMethodChangeToOutbound();
        }
        $('.campaign-steps').attr('data-campaign-steps',totalSteps);
        updateCampaignStepsProgressBar('#step2',totalSteps,2);
        $('#old_sms_template,#errors_count').val('0');
    });
    
    function smsMethodChangeToInbound()
    {
        $('.inbound_sms_templates,.inbound_sms_settings,.delayed-outbound-sms').removeClass('hidden');
        $('.outbound_sms_templates,.campaign_step4_tab_header,.sms_sender_info,.step3_required,.outbound_sms_settings').addClass('hidden');            
        $('.first_inbound_template input:radio').prop('checked',true);  
        $('.step3_tab_heading').html(getTranslation('sms_settings'));  
        $('#outbound_sms_sending_delay').val(0);
        $('#outbound_sms_sending_delay').removeAttr('readonly');
        $('#enable_outbound_sms_instant_reviews').prop('checked',false);         
        $('#sms_sender_name').val('');
        $('.outbound_sms_settings_section .form-group,.campaign_sms_sender_info .form-group').removeClass('error-group');
        $('.outbound_sms_settings_section .error-message,.campaign_sms_sender_info .error-message').html('');
    }
    
    function smsMethodChangeToOutbound()
    {
        $('.outbound_sms_templates,.campaign_step4_tab_header,.sms_sender_info,.outbound_sms_settings,.delayed-inbound-sms').removeClass('hidden');
        $('.inbound_sms_templates,.inbound_sms_settings,.step3_required,.step4_required').addClass('hidden');
        $('.first_outbound_template input:radio').prop('checked',true); 
        $('.step3_tab_heading').html(getTranslation('sender_info'));
        $('.step4_tab_heading').html(getTranslation('sms_settings')); 
        $('#inbound_sms_sending_delay').val(0);
        $('#inbound_sms_sending_delay').removeAttr('readonly');
        $('#enable_inbound_sms_instant_reviews').prop('checked',false);
        $('#sms_sender_name').val('eKomi');
        $('.inbound_sms_settings_section .form-group').removeClass('error-group');
        $('.inbound_sms_settings_section .error-message').html(''); 
    }

    $("#rating_stars").raty({
        path : baseUrl+'/img/',
        click: function(score, evt) {
            $('#recipient_rating').val(score);
        },
        mouseover : function(score, evt) {
//            $('#recipient_rating').val(score);
        }
    });

    $('body').on('click', '.add-recipients-csv-btn', function () {
        $(this).closest('form').submit();
    });

    $('body').on('click', '.skip_tel_recipient_btn', function () {
        var campaignId = $('#call_campaign_id').val();
        callTelRecipient(campaignId,'skip_current_and_get_next');
    });

    $('body').on('click', '.save_tel_recipient_btn', function () {
        var campaignId = $('#call_campaign_id').val();
        callTelRecipient(campaignId,'save_current');
    });

    $('body').on('click', '.save_and_fetch_next_tel_recipient_btn', function () {
        var campaignId = $('#call_campaign_id').val();
        callTelRecipient(campaignId,'save_current_and_get_next');
    });

    $('body').on('click','.close_start_call_modal_btn',function() {
        $('.start-calls-modal').modal('toggle');
    });

    $('#transaction_time').datetimepicker({
       // dateFormat : "dd/mm/yy",
        dateFormat : "mm/dd/yy",
        timeFormat: "HH:mm:ss",
        timezone : timezone
    });
    
    $('#start_date,#end_date').datetimepicker({
        dateFormat : 'mm/dd/yy',
        maxDate : new Date(),
        showTimepicker : false
    });       
    
    $('.available_templates').on('change',function(){
        $('#errors_count').val('0');
    });

    function setSMSContent()
    {
        var oldSMSTemplateId = $('#old_sms_template').val();
        var currentSMSTemplateId = $('input[name=selected_sms_template]:checked').val();     
        var selectedSMSMethod = $('input[name=sms_method]:checked').val();
        var errors_count = $('#errors_count').val();                
        if(errors_count == 0) {
            if(oldSMSTemplateId != currentSMSTemplateId && currentSMSTemplateId != 0) {
                var result = getSMSContentAgainstTemplate(currentSMSTemplateId);
                if(result) {
                    if(result.sms_content) {
                        $('#'+selectedSMSMethod+'_sms_content').val(result.sms_content);
                    }
                }
            } else if(currentSMSTemplateId == 0 && oldSMSTemplateId != 0) {
                $('#'+selectedSMSMethod+'_sms_content').val('');
                $('#old_template').val(0); 
            }
        }
        return false;
    }

    function getSMSContentAgainstTemplate(templateId)
    {
        var url = baseUrl+'/get-sms-content-against-template/'+templateId;
        var smsContent = '';
        jQuery.ajax({
            url: url,
            dataType: 'json',
            type: 'GET',
            async :false,
            data: {},
            beforeSend : function(xhr, opts){
                $('.loader').removeClass('hidden');
            },
            success: function (result) {
                $('#old_sms_template').val(templateId);
                smsContent = result;
            },
            complete: function(){
                $('.loader').addClass('hidden');
            }
        });
        return smsContent;
    }

    $('body').on('change', '#enable_email_instant_reviews', function () {        
        $('#feedback_email_delay').parents('.form-group').removeClass('error-group');
        $('#feedback_email_delay').parents('.form-group').find('.error-message').html('');
        if (this.checked) {
            enableEmailInstantReviews();           
        } else {
            disableEmailInstantReviews();            
        }
    });
    
    function enableEmailInstantReviews()
    {               
        $('#feedback_email_delay').val('');
        $('#feedback_email_delay').attr('readonly','readonly');  
        $('.delayed-feedback-email').addClass('hidden');
        $('.instant-review-option').removeClass('mb6px');
    }
    
    function disableEmailInstantReviews()
    {                
        $('#feedback_email_delay').val('5');
        $('#feedback_email_delay').removeAttr('readonly');  
        $('.delayed-feedback-email').removeClass('hidden');
        $('.instant-review-option').addClass('mb6px');
    } 
    
    $('body').on('change','#enable_outbound_sms_instant_reviews,#enable_inbound_sms_instant_reviews',function() {                        
        var smsMethod = $('input[name=sms_method]:checked').val();          
        $('#'+smsMethod+'_sms_sending_delay').parents('.form-group').removeClass('error-group');
        $('#'+smsMethod+'_sms_sending_delay').parents('.form-group').find('.error-message').html('');                        
        if(this.checked) {
            enableSMSInstantReviews(smsMethod);
        } else {            
            disableSMSInstantReviews(smsMethod);
        }        
    });
    
    function enableSMSInstantReviews(smsMethod)
    {        
        $('#'+smsMethod+'_sms_sending_delay').val('');
        $('#'+smsMethod+'_sms_sending_delay').attr('readonly','readonly');  
        $('.delayed-'+smsMethod+'-sms').addClass('hidden');
        $('.delayed-'+smsMethod+'-sms').removeClass('mb6px');
    }
    
    function disableSMSInstantReviews(smsMethod)
    {        
        $('#'+smsMethod+'_sms_sending_delay').val(0);
        $('#'+smsMethod+'_sms_sending_delay').removeAttr('readonly');      
        $('.delayed-'+smsMethod+'-sms').removeClass('hidden');
        $('.delayed-'+smsMethod+'sms').addClass('mb6px');
    }
});

function callTelRecipient(campaignId, mode)
{
    var projectId = $('#project_id').val();
    var rating = $('#recipient_rating').val();
    var feedback = $('#feedback').val();
    var currRecipientId = $('#current_tel_recipient_id').val();
    var skippedRecipientIds = $('#skipped_tel_recipient_ids').val();
    $('#rating-error').addClass('hidden');
    $('#feedback-error').addClass('hidden');
 
    if(mode == 'start_call') {
        skippedRecipientIds = "";
        $('#skipped_tel_recipient_ids').val(skippedRecipientIds);
        $('#call_campaign_id').val(campaignId);
    }
    if(mode == 'skip_current_and_get_next') {
        if(currRecipientId !== '') {
            skippedRecipientIds = skippedRecipientIds+','+ currRecipientId;
            $("#skipped_tel_recipient_ids").val(skippedRecipientIds);
        } else {
            $("#skipped_tel_recipient_ids").val(currRecipientId);
        }
    }
    if(mode == 'save_current' || mode == 'save_current_and_get_next') {
        if(rating == '0' || $.trim(rating) == '' || $.trim(feedback) == '') {
            if(rating == '0' || $.trim(rating) == '') {
                $('#rating-error').removeClass('hidden');
            }
            if($.trim(feedback) == '') {
                $('#feedback-error').removeClass('hidden');
            }
            return;
        }
    }
    var url = '/call-tel-recipient';
    var dataToSend = {'mode':mode,'project_id':projectId,'campaign_id':campaignId,'skipped_tel_recipient_ids':skippedRecipientIds,
        'current_recipient_id':currRecipientId,'rating':rating,'feedback':feedback,'_token':$('#form_token').val()};
    jQuery.ajax({
        url: baseUrl+'/'+url,
        dataType: 'json',
        type: "POST",
        data: dataToSend,
        success: function (result) {
            $('#rating_stars').raty('cancel');
            $('#recipient_rating').val('0');
            $('#feedback').val('');
            $('#current_tel_recipient_id').val('');
            if(result.call_script && mode == 'start_call') {
                $('.call-script-text').html(result.call_script);
            }
            if(mode != 'save_current') {
                if(result.recipient) {
                    var recipient_name = result.recipient.first_name;
                    if(result.recipient.first_name != '' && result.recipient.last_name != '') {
                        recipient_name += ' ';
                    }
                    recipient_name += result.recipient.last_name;                                        
                    var telephone = result.recipient.telephone;
                    var products = result.recipient.products;
                    var product_bought = '';
                    if(products.length > 0 ) {
                        product_bought = result.recipient.products[0].product_name;
                        for(var product = 1; product < products.length ; product++) {
                            product_bought += ', '+products[product].product_name;
                        }
                    }
                    $('.tel_recipient_name').html(recipient_name);
                    $('.tel_recipient_phone').html(telephone);
                    $('.tel_recipient_product_bought').html(product_bought);
                    $('#current_tel_recipient_id').val(result.recipient.id);
                    $('.tel-recipient-info-section').removeClass('hidden');
                    $('.start-call-modal-btn').removeClass('hidden');
                    $('.no-recipient-for-call').addClass('hidden');
                    $('.close_start_call_modal').addClass('hidden');
                } else {
                    $('.tel-recipient-info-section').addClass('hidden');
                    $('.start-call-modal-btn').addClass('hidden');
                    $('.no-recipient-for-call').removeClass('hidden');
                    $('.close_start_call_modal').removeClass('hidden');
                }
            }
            if(mode == 'start_call' || mode == 'save_current') {
                $('.start-calls-modal').modal('toggle');
            }
        },
        error : function (xhr) {
            console.log("An error occured: " + xhr.status + " " + xhr.statusText);
            $('.tel-recipient-info-section').addClass('hidden');
            $('.start-call-modal-btn').addClass('hidden');
            $('.no-recipient-for-call').removeClass('hidden');
            $('.close_start_call_modal').removeClass('hidden');
        }
    });
}

function getTranslation(key) {
    return LOCALE[key] ? LOCALE[key] : key;
}
