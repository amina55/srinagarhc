<script>
    var Url = '';
    function goToUrlWithConfirmation(url, confirmation, title)
    {
        if(title){
            $('#confirmation_title').html(title);
        }
        confirmation += '?';
        Url = url;
        $('#confirm_msg').html(confirmation);
        $('#confirmation_modal').modal();
    }
    $(document).on('click', '#confirm_yes',function() {
        return goToUrl(Url);
    });
</script>

<!-- Confirmation Modal   -->
<div id='confirmation_modal' class="modal fade" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-sm">
        <div class="modal-content clearfix">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h3 id="confirmation_title" class="modal-title" >{{ trans('content.confirmation_popup_heading') }}</h3>
            </div>
            <div class="modal-body">
                <label id="confirm_msg"></label>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-global-thick" >{{ trans('content.no') }}</button>
                <button id="confirm_yes" type="button" class="btn btn-global btn-global-thick">{{ trans('content.yes') }}</button>
            </div>
        </div>
    </div>
</div>