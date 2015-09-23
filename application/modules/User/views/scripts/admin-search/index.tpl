<h2><?php echo $this->translate("Search Settings") ?></h2>

<div class='clear'>
    <div class='settings'>
    <?php echo $this->form->render($this); ?>
    </div>
</div>
<script type="text/javascript">
    $('level_id').addEvent('change', function(){
        window.location.href = en4.core.baseUrl + 'admin/user/search/index/level_id/'+this.get('value');
    });
</script>