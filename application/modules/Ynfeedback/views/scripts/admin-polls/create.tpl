  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  <a href="javascript: void(0);" onclick="return addAnotherOption();" id="addOptionLink"><?php echo $this->translate("Add another option") ?></a>
  <script type="text/javascript">
    //<!--
    en4.core.runonce.add(function() {
      var options = <?php echo Zend_Json::encode($this->options) ?>;
      var optionParent = $('options').getParent();

      var addAnotherOption = window.addAnotherOption = function (dontFocus, label) {

        var optionElement = new Element('input', {
          'type': 'text',
          'name': 'optionsArray[]',
          'style': 'display: block;',
          'class': 'pollOptionInput',
          'value': label,
          'events': {
            'keydown': function(event) {
              if (event.key == 'enter') {
                if (this.get('value').trim().length > 0) {
                  addAnotherOption();
                  return false;
                } else
                  return true;
              } else
                return true;
            } // end keypress event
          } // end events
        });
        
        if( dontFocus ) {
          optionElement.inject(optionParent);
        } else {
          optionElement.inject(optionParent).focus();
        }

        $('addOptionLink').inject(optionParent);

      }
      
      // Do stuff
      if( $type(options) == 'array' && options.length > 0 ) {
        options.each(function(label) {
          addAnotherOption(true, label);
        });
        if( options.length == 1 ) {
          addAnotherOption(true);
        }
      } else {
        // display two boxes to start with
        addAnotherOption(true);
        addAnotherOption(true);
      }
    });
    // -->
  </script>


<script type="text/javascript">
  $$('.core_main_poll').getParent().addClass('active');
</script>
