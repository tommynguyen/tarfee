<?php

?>
<?php
	  $this->headLink()
    	  ->appendStylesheet($this->baseUrl() . '/application/modules/Questionanswer/externals/styles/question-answer.css');  
?>
<div class="global_form_popup">
  <?php 
	if($this->form != null)
		echo $this->form->setAttrib('class', '')->render($this); 
  ?>
</div>
