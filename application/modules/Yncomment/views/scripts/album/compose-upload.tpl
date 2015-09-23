<script type="text/javascript">
    $try(function() {
        parent.en4.album.getComposer().processResponse(<?php echo $this->jsonInline($this->getVars())?> );
    });
    $try(function() {
        parent._composePhotoResponse = <?php echo $this->jsonInline($this->getVars()) ?>;
    });
</script>