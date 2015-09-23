<style>
	.admin_table
	{
		width: 100%;
	}
	.admin_table .input_container
	{
		text-align: center;
	}
</style>

<script type="text/javascript">

var set_show  = function(obj, id){
    var url = en4.core.baseUrl + 'admin/ynfeedback/polls/set-show';
    new Request.JSON({
        url: url,
        method: 'post',
        data: {
            'id': id,
        },
        'onSuccess' : function(responseJSON, responseText)
	        {
	          if(!responseJSON.error_code)
	          {
	          	  if(responseJSON.type == "show")
	          	  {
	          	  	obj.checked = 1;
	          	  }
	          	  else if(responseJSON.type == "unshow")
	          	  {
	          	  	obj.checked = 0;
	          	  }
	          }
	        }
	    }).send();
  };

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected polls?") ?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<h2><?php echo $this->translate("YouNet Feedback Plugin") ?></h2>

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
  <?php echo $this->translate("YNFEEDBACK_ADMIN_POLL_DESCRIPTION") ?>
</p>

<div class="add_link">
<?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'polls', 'action' => 'create'),
    $this->translate('Add New Poll'), 
    array(
        'class' => 'buttonlink add_faq smoothbox',
    )) ?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Title") ?></th>
        <th><?php echo $this->translate("Posted on") ?></th>
        <th><?php echo $this->translate("Votes") ?></th>
        <th><?php echo $this->translate("Show") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->poll_id;?>' value='<?php echo $item->poll_id ?>' /></td>
          <td><?php echo $item->poll_id ?></td>
          <td title="<?php echo $this->escape($item->getTitle()) ?>">
            <?php echo $item->getTitle() ?>
          </td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td><?php echo $item->vote_count ?></td>
          <td class="input_container"><input name='set_show_poll' type="radio" onclick="set_show(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->show) echo 'checked'?>/></td>
          <td>
          	 <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'polls', 'action' => 'edit', 'id' => $item->poll_id),
                $this->translate("Edit"),
                array('class' => 'smoothbox')) ?>
           |
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'polls', 'action' => 'delete', 'id' => $item->poll_id),
                $this->translate("Delete"),
                array('class' => 'smoothbox')) ?>
           |
            <?php echo $this->htmlLink(
                array('route' => 'ynfeedback_general', 'action' => 'show-result', 'id' => $item->poll_id),
                $this->translate("Show Result"),
                array('class' => 'smoothbox')) ?>     
          </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <br />

  <div class='buttons'>
    <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  </form>

  <br/>

  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no polls created yet.") ?>
    </span>
  </div>
<?php endif; ?>
