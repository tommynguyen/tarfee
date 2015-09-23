<h2><?php echo $this->translate("YouNet Feedback Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
    <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        var text = $('ynfeedback_button_type-1');
        var icon = $('ynfeedback_button_type-2');
        
        if (text.checked) {
            $('icon-wrapper').hide();
            $('ynfeedback_button_hovertext-wrapper').hide();
        }
        else {
            $('ynfeedback_button_text-wrapper').hide();
            $('ynfeedback_button_textcolor-wrapper').hide();
        }
        
        text.addEvent('click', function(){
            $('icon-wrapper').hide();
            $('ynfeedback_button_hovertext-wrapper').hide();
            $('ynfeedback_button_text-wrapper').show();
            $('ynfeedback_button_textcolor-wrapper').show();
        });
        
        icon.addEvent('click', function(){
            $('icon-wrapper').show();
            $('ynfeedback_button_hovertext-wrapper').show();
            $('ynfeedback_button_text-wrapper').hide();
            $('ynfeedback_button_textcolor-wrapper').hide();
        });
        
        var left = $('ynfeedback_button_left-1');
        var right = $('ynfeedback_button_left-2');
        
        if (left.checked) {
            $('button').setStyle('right', null);
            $('button').setStyle('left', 0);
        }
        else {
            $('button').setStyle('left', null);
            $('button').setStyle('right', 0);
        }
        
        left.addEvent('click', function(){
            $('button').setStyle('right', null);
            $('button').setStyle('left', 0);
        });
        
        right.addEvent('click', function(){
            $('button').setStyle('left', null);
            $('button').setStyle('right', 0);
        });
        
        <?php if (!is_null($this->position)) :?>
            $('range').set('value', <?php echo $this->position;?>);
            $('position').set('value', <?php echo $this->position;?>);
            $('button').setStyle('top', '<?php echo $this->position;?>%');
        <?php endif;?>
    });
    
    function changePos(obj) {
        var value = obj.get('value');
        $('button').setStyle('top', value+'%');
        $('position').set('value', value);
    }
</script>
     