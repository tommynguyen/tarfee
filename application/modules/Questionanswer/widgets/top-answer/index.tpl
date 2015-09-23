<?php

?>
<?php
    $this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Questionanswer/externals/styles/main-widgets.css');
 
?>
<ul>
  <?php foreach( $this->topAnswers as $answer ): ?>  	
    <li style="clear:both;">
      <?php echo $answer['user_photo'] ?>
      <div class='topanswers_info'>
        <div class='topanswers_name'>
          <?php echo $answer['user_link'] ?>
        </div>
        <div class='topanswers_answers'>
          <?php echo $this->translate(array('%s answer', '%s answers', $answer['TopUser']),$this->locale()->toNumber($answer['TopUser'])) ?>
        </div>
      </div>
     
    </li>
     <div class="entry_line"></div>
  <?php endforeach; ?>
</ul>
