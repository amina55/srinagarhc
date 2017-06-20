<script>
    function refreshCaptcha() {
        console.log('yes clicked captcha');
    $("#captcha_code").attr('src', 'captcha_code.php');
}
</script>
<div class="form-group">
    <div class="col-sm-12 mt10">
        <div class="col-sm-4">
            <img id="captcha_code" src="captcha_code.php" />
        </div>
        <div class="col-sm-4">
            <a onClick="refreshCaptcha();"><img class="btnRefresh" width="50px" height="40px" src="../images/refresh.png"/></a>
        </div>
    </div>
</div>