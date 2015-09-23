<?php
	$this->headScript()
          ->appendFile($this->baseUrl() . '/application/modules/Questionanswer/externals/scripts/core.js');
          //->appendFile($this->baseUrl() . '/externals/smoothbox/smoothbox.js');
    $this->headLink()
    	  ->appendStylesheet($this->baseUrl() . '/application/modules/Questionanswer/externals/styles/question-answer.css');  
?>	
<br />
 <table cellpadding='0' cellspacing='0' width="530">
   	<tr>
   		<td valign='bottom'>
			<table cellpadding='0' cellspacing='0'><tr><td class='qa_tab_active' id='qa_tab' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="en4.questionanswer.changetab('qa_tab', 'myqa_tab', 'searchqa_tab')" onMouseUp="this.blur()"><?php echo $this->translate('Q&A');?></a></td></tr></table>
		</td>
    	<td valign='bottom'>
    		<?php
			if($this->user_id){
    		?>
			<table cellpadding='0' cellspacing='0'><tr><td class='qa_tab_inactive' id='myqa_tab' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="en4.questionanswer.changetab('myqa_tab', 'qa_tab', 'searchqa_tab')" onMouseUp="this.blur()"><?php echo $this->translate('My Q&A');?></a></td></tr></table>
			<?php 
			}
			?>
		</td>
		<td valign='bottom'>    		
			<table cellpadding='0' cellspacing='0'><tr><td class='qa_tab_inactive' id='searchqa_tab' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="en4.questionanswer.changetab('searchqa_tab', 'myqa_tab', 'qa_tab')" onMouseUp="this.blur()"><?php echo $this->translate('Search');?></a></td></tr></table>
		</td>
		<td valign='bottom'>    		
			<table cellpadding='0' cellspacing='0'><tr><td class='qa_tab_active' style='display:none' id='answerqa_tab' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseUp="this.blur()"><?php echo $this->translate('Answer');?></a></td></tr></table>
		</td>
   		<td width='100%' class='qa_tab_end'>&nbsp;</td>
  	</tr>
 </table>
<div class='m2b_qa_content'>	
	<br></br>
	  <div id="normal">
	  <form action="m2b_qa_ajax.php" method="post">
		<textarea name="mess" id="q_mess" cols="" rows="" class="text_qa" <?php if(!$this->user_id){echo('disabled="disabled"');}?> > </textarea>
		<button type="button" id="postQuestion" name="btnSubmit" style="float:right;" onClick="en4.questionanswer.postQuestion();" <?php if(!$this->user_id){echo('disabled="disabled"');}?>><?php echo $this->translate('Post question');?></button>
		<button type="button" id="btnSearch" name="btnSearch" style="display:none;float:right;" onClick="en4.questionanswer.searchQuestion();"><?php echo $this->translate('Search');?></button>
		<input type="hidden" name="user_id" id="q_user_id" value="<?php echo($this->user_id);?>">
		<input type="hidden" name="category" id="q_category" value="1">		
	  </form>	  
	  </div>
	  <div id="extra" style="display:none;">
	  <form action="m2b_qa_ajax.php" method="post">
	  	<span style="float:left;color:red;padding-left:5px;"><?php echo $this->translate('Post an Answser to this Question')?></span>
		<textarea name="mess1" id="a_mess1" cols="" rows="" class="text_qa" <?php if(!$this->user_id){echo('disabled="disabled"');}?> > </textarea>
		<button type="button" id="postAnswer" name="btnAnswer" style="float:right;" onClick="en4.questionanswer.postAnswer(1);" <?php if(!$this->user_id){echo('disabled="disabled"');}?>><?php echo $this->translate('Post answer');?></button>
		<input type="hidden" name="question" id="a_question" value="" />
		<input type="hidden" name="user_id" id="q_user_id" value="<?php echo($this->user_id);?>">
		<input type="hidden" name="task" id="a_task" value="post_answer" />				
	  </form>	  
	  </div>
	  <div id='tab_content'><?php echo $this->translate("You don't have any QA");?></div>
	  <div class="answer_box" id="answer_box" style="display:none; position:absolute;">
			&nbsp;Answer Question:
			<form action="m2b_qa_ajax.php" method="post">
				<textarea name="mess" id="a_mess" style="width:92%; margin:5px" <?php if(!$this->user_id){echo('disabled="false"');}?> ></textarea>
				<input type="hidden" name="question" id="a_question" value="" />
				<input type="hidden" name="user_id" id="a_user_id" value="<?php echo($this->user_id)?>" />
				<input type="hidden" name="task" id="a_task" value="post_answer" />
				<div style="float:right; margin:5px;">
					<button style="border: 1 solid #999999;" type="button" onclick="en4.questionanswer.postAnswer(0)" <?php if(!$this->user_id){echo('disabled="disabled"');}?> /><?php echo $this->translate('Submit'); ?></button>
					<a href="javascript:void(0)" onclick="en4.questionanswer.closeAnswerBox()"><span style="border: 1px solid; padding: 0pt 5px;">X</span></a>
				</div>
			</form>
	</div>
	<!-- set current page -->
	<input type="hidden" name="currentPage" id="currentPage" value="1" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo($this->user_id)?>" />
	<input type="hidden" name="search" id="search" />	
	<input type="hidden" name="qid" id="qid" value="<?php echo($this->qid)?>" />	
</div>
<script>	
    //start getting threads	
	en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');	
    en4.questionanswer.start(0,0,1,'');
    openPopup = function(url) {
      Smoothbox.open(url);
    };   
    
</script>