<?php

?>
<style>
<!--
 .m2b_qa_content 
{
	padding: 10px; 
	border: 1px solid #AAAAAA; 
	border-top: none;
	padding-bottom:20px;
}
.tab_content
{
	
}
.space-line
{
	clear:both;
}

td.qa_tab_inactive a 
{ 
	background-color: #EEEEEE;
	background-image: url(/application/modules/Questionanswer/externals/images/whiteline.gif);
	background-repeat: repeat-x;
	background-position: top left;
	padding: 7px 10px 7px 10px;
	border: 1px solid #AAAAAA; 
	border-left: none;
	font-weight: bold; 
	display: block; 
	width:52px;
}
td.qa_tab_inactive a:hover 
{
	background-color: #F4F4F4;	
	background-image: url(/application/modules/Questionanswer/externals/images/whiteline.gif);	
	background-repeat: repeat-x; 
	background-position: top left; 
	padding: 7px 10px 7px 10px; 
	border: 1px solid #AAAAAA; 
	border-left: none; 
	font-weight: bold; 	
	display: block;
}
td.qa_tab_active a {
	background-color: #FFFFFF;	
	background-image: url(/application/modules/Questionanswer/externals/images/whiteline.gif);	
	background-repeat: repeat-x;	
	background-position: top left;
	padding: 7px 10px 8px 10px;	
	border: 1px solid #AAAAAA; 	
	border-left: none;	
	border-bottom: none;
	font-weight: bold; 	
	display: block;	
	width:52px;
}
td.qa_tab_active a:hover 
{
	background-color: #FFFFFF;
	background-image: url(/application/modules/Questionanswer/externals/images/whiteline.gif);
	background-repeat: repeat-x;
	background-position: top left;
	padding: 7px 10px 8px 10px;	
	border: 1px solid #AAAAAA; 
	border-left: none;
	border-bottom: none;
	font-weight: bold; 
	display: block;
}
#qa_tab 
{
	border-left: 1px solid #AAAAAA;
}
td.qa_tab_end 
{	
	border-bottom: 1px solid #AAAAAA;
}
.qa_line{
	padding:4px 0; 
	word-wrap: break-word; 
	text-align:justify;
}

.qa_line .qa_au_in
{
	background:none repeat scroll 0 0 #F1F1F1;
	border-top:1px solid #AAAAAA;
	padding:5px 7px; 
	float:left; 
	width:100%;
}
.qa_line .qa_au_in a:link, .qa_line .qa_au_in a:visited
{
	color:#336699; 
	font-weight:bold; 
	text-decoration:none;
}
.qa_line .qa_au_in img
{
	margin:0 2px;
}
.qa_label
{
	color:#FF0000; 
	font-weight:bold; 
	float:left;
	padding-left:6px;
}
.qa_label_r
{
	color:#336699; 
	font-weight:bold;
}
.qa_reply
{
	color:#B40205; 
	font-size:14px; 
	font-weight:bold;
}
-->
</style>
<script>
function multiModify()
{
  var multimodify_form = $('multimodify_form');
  if (multimodify_form.submit_button.value == 'delete')
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected question?")) ?>');
  }
}
</script>

<h2><?php echo $this->translate("Q&A Plugin") ?></h2>
  <div class='tabs'> 
    <ul class="navigation"> 
		<li class="active">			 
			<?php echo $this->htmlLink(array('module'=>'questionanswer','controller'=>'manage'), $this->translate('Q&A Management'), array('class'=>'class=menu_album_admin_main album_admin_main_manage')) ?>
		</li> 
		<li>			
			<?php echo $this->htmlLink(array('module'=>'questionanswer','controller'=>'manage','action' => 'viewreport'), $this->translate('View Report'), array('class'=>'class=menu_album_admin_main album_admin_main_manage')) ?> 
		</li> 		
	</ul>  
  </div> 
<p>
  <?php echo $this->translate("QUESTIONANSWER_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>  
<br />
<div class='admin_results'>
  <span style="float:left;vertical-align:middle;">
    <?php $memberCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s question found", "%s questions found", $memberCount), ($memberCount)) ?>
  </span>
  <span style="float:left;padding-left:10px;">
    <?php echo $this->paginationControl($this->paginator); ?>
  </span>
</div>
<br />
<table cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
		<td valign="bottom">
			<table cellspacing="0" cellpadding="0"><tbody><tr><td onmouseup="this.blur()" id="qa_tab" class="qa_tab_active"><a href="javascript:void(0);">Q &amp; A</a></td></tr></tbody></table>
		</td>
			<td width="100%" class="qa_tab_end">&nbsp;</td>
		</tr>
    </tbody>
</table>
<div class="m2b_qa_content">
	<div id="tab_content">
 <?php if( count($this->paginator) ): ?>    
    <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
     <?php foreach( $this->paginator as $item ): ?>
       	<div class="qa_line" style="margin-top:0px;">			
			<span style="float:left;width;48px;"><?php echo $item['question']['user_photo']?></span>
			<div style="float: left; width: 92%;padding-top:0px;margin-top;0px;">
				<div class="qa_au_in" style="margin-top:0px;">					
					<span style="float:right;"><a class='smoothbox' href='<?php echo $this->url(array('action' => 'deletequestion', 'id' => $item['question']['question_id']));?>'> delete</a></span>
					<span style="float:right">&nbsp;</span>
					<span style="float:right"><a class='smoothbox' href="<?php echo $this->url(array('action' => 'editquestion', 'id' => $item['question']['question_id']));?>" >edit</a></span>
					<?php echo $item['question']['user_link']?>
				</div>
				<br>
				<br>
				<div class="space-line" style="margin-top;0px;">
					<span class="qa_label">Q:&nbsp;</span><?php echo $item['question']['content'] ?>
				</div>
			</div>
			<div class="space-line" style="margin-top;0px;"></div>			
		</div>
		<?php foreach( $item['answers_list'] as $answer ): ?>
		<div class="qa_line" style="margin-bottom:20px;">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:0px; padding-top:0px;">
				<tr>
					<td valign="top" width="5%"><?php echo $answer['user_photo']?></td>
					<td>
						<span class="qa_label_r">&nbsp;</span><?php echo $answer['user_link']?><br /><br />
						<span class="qa_label_r">&nbsp;A: </span><?php echo $answer['content']?>			
					</td>
					<td align="right" valign="bottom" width="10%">
						<div>
						<span style="float:right;width:13px;">&nbsp;</span>
						<span style="float:right"><a class='smoothbox' href='<?php echo $this->url(array('action' => 'deleteanswer', 'id' => $answer['answer_id']));?>'> delete</a></span>
						<span style="float:right">&nbsp;</span>
						<span style="float:right"><a class='smoothbox'  href="<?php echo $this->url(array('action' => 'editanswer', 'id' => $answer['answer_id']));?>" >edit</a></span>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?php endforeach; ?>
    <?php endforeach; ?>
    </form>
 <?php endif; ?>
	<p>&nbsp;</p>
 	</div>	
</div>
<p>&nbsp;</p>
<div class='admin_results'>
  <span style="float:left;vertical-align:middle;">
    <?php $memberCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s question found", "%s questions found", $memberCount), ($memberCount)) ?>
  </span>
  <span style="float:left;padding-left:10px;margin-top:0px;">
    <?php echo $this->paginationControl($this->paginator); ?>
  </span>
</div>