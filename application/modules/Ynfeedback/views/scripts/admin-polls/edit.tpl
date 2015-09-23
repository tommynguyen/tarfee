  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  <a href="javascript: void(0);" onclick="return addAnotherOption();" id="addOptionLink"><?php echo $this->translate("Add another option") ?></a>
  <script type="text/javascript">
    //<!--
    en4.core.runonce.add(function() {
      var options = <?php echo Zend_Json::encode($this->options) ?>;
      var optionParent = $('options').getParent();

      var addAnotherOption = window.addAnotherOption = function (dontFocus, label, isPopulate) {
		if (!isPopulate)
		{
			name = 'optionsArray[]';
		}
		else
		{
			name = 'option_' + label.poll_option_id;
		}
		var divElement = new Element('div');
        var optionElement = new Element('input', {
          'type': 'text',
          'name': name,
          'style': 'display: inline-block;',
          'class': 'pollOptionInput',
          'value': (label) ? label.poll_option : "",
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
        
        if(isPopulate)
        {
	         var remove = new Element('a', {
	            href: 'javascript:void(0)',
	            html: '',
	            'class': 'fa fa-minus-circle remove-poll',
	            events : {
	                click: function(event) {
	                    event.preventDefault();
	                    this.getPrevious().destroy();
	                    this.destroy();
	                }
	            }
	        });
        }
        if( dontFocus ) {
          divElement.inject(optionParent);
          optionElement.inject(divElement);
           if(isPopulate)
           {
         	 remove.inject(divElement);
           }
        } else {
        	divElement.inject(optionParent);
          optionElement.inject(divElement).focus();
        }

        $('addOptionLink').inject(optionParent);

      }
      
      // Do stuff
      if( $type(options) == 'array' && options.length > 0 ) {
        options.each(function(label) {
          addAnotherOption(true, label, true);
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
