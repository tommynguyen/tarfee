<?php

?>
<?php
	$this->headLink()
    	->appendStylesheet($this->baseUrl() . '/application/modules/Questionanswer/externals/styles/main.css');  
?>	
<div id='global_content_wrapper'> 
    <div id='global_content'> 
<h2><?php echo $this->translate("Q&A Plugin") ?></h2>
  <div class='tabs'> 
    <ul class="navigation"> 
		<li> 
			<?php echo $this->htmlLink(array('module'=>'questionanswer','controller'=>'manage'), $this->translate('Q&A Management'), array('class'=>'class=menu_album_admin_main album_admin_main_manage')) ?> 
		</li> 
		<li class="active"> 
			<?php echo $this->htmlLink(array('module'=>'questionanswer','controller'=>'manage','action' => 'viewreport'), $this->translate('View Report'), array('class'=>'class=menu_album_admin_main album_admin_main_manage')) ?> 
		</li> 		
	</ul>  
  </div> 

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("VIEW_REPORT_DESCRIPTION") ?>
</p>

<br />
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

function multiModify()
{
  var multimodify_form = $('multimodify_form');
  if (multimodify_form.submit_button.value == 'delete')
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
  }
}

function selectAll()
{
  var i;
  var multimodify_form = $('multimodify_form');
  var inputs = multimodify_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}

function loginAsUser(id) {
  if( !confirm('<?php echo $this->translate('Note that you will be logged out of your current account if you click ok.') ?>') ) {
    return;
  }
  var url = '<?php echo $this->url(array('action' => 'login')) ?>';
  var baseUrl = '<?php echo $this->url(array(), 'default', true) ?>';
  (new Request.JSON({
    url : url,
    data : {
      format : 'json',
      id : id
    },
    onSuccess : function() {
      window.location.replace( baseUrl );
    }
  })).send();
}
</script>
<br />

<div class='admin_results'>
  <div>
    <?php $reportCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s report found", "%s reports found", $reportCount), ($reportCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />

<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multimodify'));?>" onSubmit="multiModify()">
  <table class='admin_report_table' style="width:100%;">
    <thead>
      <tr>
        <th><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th><?php echo $this->translate("ID") ?></th>
        <th><?php echo $this->translate("username") ?></a></th>
        <th width="300"><?php echo $this->translate("Detail") ?></th>
        <th><?php echo $this->translate("Reason") ?></th>
		<th><?php echo $this->translate("Question Detail") ?></th>
        <th><?php echo $this->translate("Posted Date") ?></th>
        <th class='admin_table_options'><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td><input name='id[]' value='<?php echo $item['id']; ?>' type='checkbox' class='checkbox' /></td>
            <td><?php echo $item['id'] ?></td>
            <td class='admin_table_bold'>              
              <?php echo $this->htmlLink($this->item('user', $item['user_id'])->getHref(), $item['username'], array('target' => '_blank')) ?>
            </td>
            <td class="content"><?php echo $item['content'] ?></td>
            <td class='admin_table_email'>              
              <?php echo $item['report_type']; ?>
            </td>
			<td class='admin_table_centered'>			  
			  <?php echo $this->htmlLink(array('module'=>'questionanswer','controller'=>'manage','qid'=> $item['report_url']), $this->translate('View')) ?> 
			</td>
            <td><?php echo $item['posted_date'] ?></td>
            <td class='admin_table_centered'>
               <a class='smoothbox' href='<?php echo $this->url(array('action' => 'deletereport', 'id' => $item['id']));?>'>
                  <?php echo $this->translate("delete") ?>
                </a>                            
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>    
    <button type='submit' name="submit_button" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
</form>
</div></div>

