<div class="generic_layout_container layout_top">
    <div class="generic_layout_container layout_middle">
        <?php echo $this->content()->renderWidget('user.settings-menu') ?>
    </div>
</div>
<div class="generic_layout_container layout_main">
    <div class="generic_layout_container layout_middle">
        <div class="global_form">
            <?php echo $this->form->render($this) ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.addEvent("domready", function() {
        $('exp-step').inject($$('#cus_step_verify .form-elements')[0], 'before');

        var myDes = $('document-element');
        var myRequest = new Request({
            url: '<?php echo $this->url(array("module" => "slprofileverify", "controller" => "index", "action" => "ajax"), "default", true) ?>',
            method: 'get',
            onRequest: function() {
                myDes.set('text', 'loading...');
            },
            onSuccess: function(responseHTML){
                myDes.set('html', responseHTML);
            },
            onFailure: function() {
                myDes.set('text', 'Sorry, your request failed!');
            }
        });

        var option_id = $('fields-0_0_1').value;
        if (!option_id) {
            $('document-wrapper').setStyle('display', 'none');
        } else {
            $('document-wrapper').setStyle('display', 'inherit');
            myRequest.send('option_id=' + option_id);
        }

        $('fields-0_0_1').addEvent('change', function() {
            var option_id = this.value;
            if (!option_id) {
                $('document-wrapper').setStyle('display', 'none');
            } else {
                $('document-wrapper').setStyle('display', 'inherit');
                myRequest.send('option_id=' + option_id);
            }
        });
    });
</script>

<?php echo $this->partial('_jsSwitch.tpl', 'fields', array('topLevelId' => 0, 'topLevelValue' => 0)); ?>
