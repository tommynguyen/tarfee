<?php
 $session = new Zend_Session_Namespace('mobile');
 $onclick = 'parent.Smoothbox.close();';
 if($session -> mobile)
 {
	$onclick = 'history.go(-1); return false;';
 }	
?>

<?php if ($this->count > 0): ?>
   <script type="text/javascript">
      en4.core.runonce.add(function(){
          $('selectall').addEvent('click', function(){
                 
            var ids = document.getElementById('users-element').getElementsByTagName('li');        
                    
            for (var i=0; i<ids.length; i++)
            {
               var temp =ids[i].firstChild ;
                                 
               if(temp.type == 'checkbox')
               {
                   temp.checked = this.checked;
               }
            }
      })});
   </script>
<div id="ymb_scroller">
   <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  </div>
<?php else: ?>
	<div class="global_form_popup">
	   <div class="ymb_no_friend_invite">
	      <?php echo $this->translate('You have no friends you can invite.'); ?>
	   </div>
	   <div style="padding-top: 5px">
	   	<button onclick = '<?php echo $onclick?>'>
	     	 <?php echo $this->translate('Close'); ?>
	    </button>
	   </div>
   </div>
<?php endif; ?>