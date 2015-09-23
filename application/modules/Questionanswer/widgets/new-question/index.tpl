<?php

?>
<ul>
  <?php foreach( $this->topQuestions as $question ): ?>  	
    <li style="clear:both;">
      <?php echo $question['user_photo'] ?>
      <div class='topquestions_info'>
        <div class='topquestions_name'>
          <?php echo $question['user_link'] ?>
        </div>
      </div>  
	  <div class='topquestions_content'>
          <?php $urlBase = $this->url(array('controller' => 'qa'), 'default', true); ?>       
          <?php echo $this->htmlLink($urlBase . '/' . $question['question_id'], $question['content'], array('style'=>'color:#999999')) ?>
      </div>
    </li>
    <div class="entry_line"></div>
  <?php endforeach; ?>
</ul>
<style type="text/css">
.layout_questionanswer_new_question >ul
{
	-moz-border-radius: 3px 3px 3px 3px;
    background-color: #E9F4FA;
    background-image: url("/application/modules/Core/externals/images/foreground_bg.png?c=203");
    background-repeat: repeat-x;
    border: 1px solid #DDDDDD;
    margin-bottom: 15px;
    padding: 5px;
}
.layout_questionanswer_new_question > ul > li {

	clear:both;

	overflow:hidden;

	padding:3px 5px;

}
.layout_questionanswer_new_question .topquestions_name {
    font-weight: 700;
}
.layout_questionanswer_new_question .topquestions_content {
    color: #999999;
    font-size: 0.8em;
}
.layout_questionanswer_new_question .topquestions_info ,.layout_questionanswer_new_question .topquestions_content{
    overflow: hidden;
    padding: 0 0 0 6px;
}
</style>
