<?php

?>
<?php
    $this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Questionanswer/externals/styles/main-widgets.css');
 
?>
<ul>
  <?php foreach( $this->topFriendQuestions as $question ): ?>  	
    <li style="clear:both;">
      <?php echo $question['user_photo'] ?>
      <div class='topquestions_info'>
        <div class='topquestions_name'>
          <?php echo $question['user_link'] ?>
        </div>
      </div>  
	  <div class='topquestions_content'>
          <?php $urlBase = $this->url(array('controller' => 'qa'), 'default', true); ?>       
          <?php echo $this->htmlLink($urlBase . '/' . $question['question_id'], substr($question['content'],0,100), array('style'=>'color:#999999')) ?>
      </div>
    
    </li>
      <div  class="entry_line"></div>
  <?php endforeach; ?>
</ul>
