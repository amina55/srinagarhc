<div id="alert">
    <script type="application/javascript">

        @if ($errors->count() > 0)
            Alert.showError(JSON.parse('<?= json_encode($errors->getMessages(), JSON_FORCE_OBJECT); ?>'));
        @endif

        @if (Session::has('message'))
            Alert.showSingleError('<?= Session::get('message');?>');
        @endif
        @if (Session::has('successMsg'))
            Alert.showSuccess('<?= Session::get('successMsg');?>');
        @endif
    </script>
</div>