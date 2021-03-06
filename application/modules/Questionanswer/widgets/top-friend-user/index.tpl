<?php

?>
<?php
    $this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Questionanswer/externals/styles/main-widgets.css');
 
?>
<ul>
  <?php foreach( $this->topUsers as $user ): ?>  	
    <li>
      <?php echo $user['user_photo'] ?>
      <div class='topusers_info'>
        <div class='topusesrs_name'>
          <?php echo $user['user_link'] ?>
        </div>
        <div class='topusers_questions'>
          <?php echo $this->translate(array('%s question', '%s questions', $user['TopUser']),$this->locale()->toNumber($user['TopUser'])) ?>
        </div>
      </div>
     
    </li>
     <div  class="entry_line"></div>
  <?php endforeach; ?>
</ul>
<style type="text/css">
.layout_questionanswer_top_friend_user >ul
{
	-moz-border-radius: 3px 3px 3px 3px;
    background-color: #E9F4FA;
    background-image: url("/application/modules/Core/externals/images/foreground_bg.png?c=203");
    background-repeat: repeat-x;
    border: 1px solid #DDDDDD;
    margin-bottom: 15px;
    padding: 5px;
}
.layout_questionanswer_top_friend_user > ul > li {


	clear:both;

	overflow:hidden;

	padding:3px 5px;

}
.layout_questionanswer_top_friend_user .topusesrs_name {
    font-weight: 700;
}
.layout_questionanswer_top_friend_user .topusers_questions {
    color: #999999;
    font-size: 0.8em;
}
.layout_questionanswer_top_friend_user .topusers_info {
    overflow: hidden;
    padding: 0 0 0 6px;
}
</style>