<script type="text/javascript">

function multiDelete()
{
    var flag = false;
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
      if(inputs[i].checked)
        flag = true;   
  }  
  if(flag == true)
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected tour guide entries?');?>");
else
    return alert("<?php echo $this->translate('Please select tour(s) to delete.');?>");
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

<h2>
  <?php echo $this->translate('Tour Guide Plugin') ?>
</h2>

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
  <?php echo $this->translate("YNTOUR_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br />
<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th class='admin_table_short'>ID</th>
      <th><?php echo $this->translate("Title") ?></th>
	  <th><?php echo $this->translate("Url") ?></th>
      <th><?php echo $this->translate("Enabled") ?></th>
	  <th><?php echo $this->translate("View") ?></th>
      <th><?php echo $this->translate("Date") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->getTitle() ?></td>
		<td><?php echo $item->path ?></td>
        <td><?php echo $item->enabled?$this->translate("Enabled"):$this->translate("Disabled") ?></td>
		<td><?php echo $this->translate($item->view_rule) ?></td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td>
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yntour', 'controller' => 'manage', 'action' => 'item', 'id' => $item->getIdentity()),
                $this->translate("steps"),
                array('class' => '')) ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yntour', 'controller' => 'manage', 'action' => 'edit', 'id' => $item->getIdentity()),
                $this->translate("edit"),
                array('class' => '')) ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yntour', 'controller' => 'manage', 'action' => 'delete', 'id' => $item->getIdentity()),
                $this->translate("delete"),
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
      <?php echo $this->translate("There are no tour guide. Enable 'Edit Mode' then go to front end to add guide.") ?>
    </span>
  </div>
<?php endif; ?>
