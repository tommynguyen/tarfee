 <?php
 $session = new Zend_Session_Namespace('mobile');
 if($session -> mobile)
 {
	 $this -> headScript()
	 	-> appendFile($this->baseUrl().'/application/modules/Ynevent/externals/scripts/iscroll.js');
 }	
?>
<div id = 'add_location_form'>
	<?php echo $this->form->render($this);?> 
</div>
<?php $session = new Zend_Session_Namespace('mobile');
if($session -> mobile):?>
<script type="text/javascript">
	 var height = screen.height;
	 height = (height*90)/100;
	 $('add_location_form').setAttribute("style","height:" + height + "px");
	 var myScroll;
	jQuery(document).ready(function() 
	{
        setTimeout(function () {
			myScroll = new iScroll('add_location_form',{
				onBeforeScrollStart: function (e) {
				 var target = e.target;
			        while (target.nodeType != 1) target = target.parentNode;
			        if (target.tagName != 'SELECT' && target.tagName!= 'INPUT' && target.tagName != 'TEXTAREA'){			        	
			            e.preventDefault();
					}
			}
			}
			);
		}, 200);
    });
</script>
<?php endif; ?>
