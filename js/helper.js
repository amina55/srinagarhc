Alert = {
    errorClass: 'alert alert-danger',
    successClass: 'alert alert-success',
    getAlert: function () {
        return $('.alert');
    },
    showError: function (errors) {
        var alertBlock = this.getAlert().attr('class', 'alert ' + this.errorClass);

        var html = '<ul>';
        $.each(errors, function (key, val) {
            if ($.type(val) === "string") {
                html += '<li>' + val + '</li>';
            } else {
                $.each(val, function (key2, val2) {
                    html += '<li>' + val2 + '</li>';
                });
            }
        });
        html += '</ul>';

        alertBlock.html(html);

        this.showAlert();
    },
    showSingleError: function (error) {
        this.hideErrors();
        var alertBlock = this.createOrGetAlertBox().attr('class', 'alert ' + this.errorClass);
        alertBlock.html(error);

        this.showAlert();
    },
    showSuccess: function (success) {
        this.hideErrors();
        var alertBlock = this.getAlert().attr('class', 'alert ' + this.successClass);
        alertBlock.html(success);

        this.showAlert();
    },
    showAlert: function () {
        this.createOrGetAlertBox().show();
        setTimeout(function() {
            Alert.hideErrors();
        }, 10000 );
    },
    hideErrors: function () {
        //this.getAlert().hide();
        $('.alert-danger').hide();
    },
    createOrGetAlertBox : function () {        
        if($('.box-body').find('.alert').length === 0) {
            $('.box-body').prepend('<div class="alert"></div>');
        } 
        return $('.alert');
    },
    appendHtmlInAlertBox : function(html, classToAdd, classToRemove) {
        var alertBox = this.createOrGetAlertBox();
        alertBox.addClass(classToAdd);
        alertBox.removeClass(classToRemove);
        alertBox.append(html);
    },
    prependHtmlInAlertBox : function(html, classToAdd, classToRemove) {
        var alertBox = this.createOrGetAlertBox();
        alertBox.addClass(classToAdd);
        alertBox.removeClass(classToRemove);
        alertBox.prepend(html);
    },
    findElementWithClassAndSetHtml : function(className, html, classToAdd, classToRemove) {
        var alertBox = this.createOrGetAlertBox();
        var elemWithClass = alertBox.find('.'+className);
        if(elemWithClass.length > 0) {
            elemWithClass.html(html);
            elemWithClass.addClass(classToAdd);
            elemWithClass.removeClass(classToRemove);
        } else {
            this.appendHtmlInAlertBox('<div class="'+className+'">'+html+'</div>',classToAdd,classToRemove);
        }
        setTimeout(function() { $('.'+className).alert('close'); }, 10000);
    } 
};

function ajax_call(url, type, dataType , data , async){

    data = data || null;
    dataType = dataType || 'json';
    async = async || false;
    type = type || 'get';

    response = {};
    response.status = false;
    response.result = '';

    $.ajax({
        url: url,
        type: type,
        dataType: dataType,
        data : data,
        async: async,
        success: function(result,status,xhr) {
            response.status = status;
            response.result = result;
        },
        error: function(xhr,status,error){
            response.status = status;
            console.log("An error occured while ajax call " + xhr.status + " " + xhr.statusText);
        }
    });
    return response;
}
function goToUrl(url) {
    location.href = url;
}