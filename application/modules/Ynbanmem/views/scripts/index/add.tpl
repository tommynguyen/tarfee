
<div class="headline">
  <h2>
    <?php echo $this->translate('Ban Members');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php echo $this->form->render($this);?>
<script type="text/javascript">
    
   var updateTextFields = window.updateTextFields = function() {
        
            var type_element = document.getElementById("type");
            var email_element = document.getElementById("email-wrapper");
            var username_element = document.getElementById("username-wrapper");
            var ip_element = document.getElementById("ip-wrapper");
            var submit_element = document.getElementById("add-wrapper");			
			var email_message_element = document.getElementById("email_message-wrapper");

            // clear url if input field on change
            //$('code').value = "";
           // $('add-wrapper').style.display = "none";

            // If video source is empty
            if( type_element.value == 0 ) {
                //$('email').value = "";
                email_element.style.display = "block";
                username_element.style.display = "none";
                ip_element.style.display = "none";				
				email_message_element.style.display = "block";
                return;
            } else  if( type_element.value == 1 ) {
                //$('username').value = "";
                email_element.style.display = "none";
                username_element.style.display = "block";
                ip_element.style.display = "none";				
				email_message_element.style.display = "block";
                return;
            }  else {
               // $('ip').value = "";
                username_element.style.display = "none";
                email_element.style.display = "none";
                ip_element.style.display = "block";				
				email_message_element.style.display = "none";
                return;
            } 
            
    }
    updateTextFields();
</script>


