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
    return confirm("<?php echo $this->translate('Are you sure you want to delete the selected tour guide entrie(s)?');?>");
  else
    return alert("<?php echo $this->translate('Please select step(s) to delete.');?>");
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
    <?php echo $this->translate('Select a tour') ?>
    <?php $ele = new Engine_Form_Element_Select('tour_id',array(
        'label'=>'Element',
        'multiOptions'=> Engine_Api::_()->yntour()->getTourOptions(),
		'value'=>$this->tid,
        'onchange'=>'window.location.href="' .$this->url(array('module'=>'yntour','controller'=>'manage','action'=>'item'),'admin_default',true). '/id/"+this.options[this.selectedIndex].value',
    ));
    echo $ele->renderViewHelper();
    ?>
</p>

<p>
  <?php echo $this->translate("YNTOUR_VIEWS_SCRIPTS_ADMINITEM_INDEX_DESCRIPTION") ?>
</p>


<br />
<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th class='admin_table_short'>ID</th>
      <th><?php echo $this->translate("Order") ?></th>
      <th><?php echo $this->translate("Time delay(s)") ?></th>
      <th><?php echo $this->translate("Content") ?></th>
      <th><?php echo $this->translate("Date") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->priority?></td>
        <td><?php echo $item->time_delay?></td>
        <td><?php echo substr(strip_tags($item->body) , 0, 200)?></td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td>
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yntour', 'controller' => 'manage', 'action' => 'item-edit','id'=>$item->tour_id,'item_id' => $item->getIdentity()),
                $this->translate("edit"),
                array('class' => '')) ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yntour', 'controller' => 'manage', 'action' => 'item-delete','id'=>$item->tour_id, 'item_id' => $item->getIdentity()),
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
      <?php echo $this->translate("There are no tour guide entries.") ?>
    </span>
  </div>
<?php endif; ?>
