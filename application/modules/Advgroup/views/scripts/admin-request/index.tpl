<script type="text/javascript">
  function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
</script>

<h2>
  <?php echo $this->translate("Clubs Plugin") ?>
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
  <?php echo $this->translate("GROUP_VIEWS_SCRIPTS_ADMINREQUEST_DESCRIPTION") ?>
</p>

<br/>
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br/>
<?php if( count($this->paginator) ): ?>
  <table class='advgroup_admin_tbl admin_table'>
    <thead>
      <tr>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('group.title', 'DESC');"><?php echo $this->translate("Title") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('request.creation_date', 'DESC');"><?php echo $this->translate("Request Date") ?></a></th>
        <th><?php echo $this->translate("Message") ?></th>
        <th><?php echo $this->translate("Status") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
      	<?php $group = Engine_Api::_() -> getItem('group', $item -> group_id);?>
      	<?php if($group) :?>
        <tr>
          <td><?php echo $group ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td><?php echo $item -> description ?></td>
          <td>
          	   <?php
          	   switch ($item -> status) {
				    case "0":
				        echo "<div style='color:yellow'>".$this -> translate("Pending")."</div>";
				        break;
				    case "1":
				        echo "<div style='color:green'>".$this -> translate("Accepted")."</div>";
				        break;
				    case "2":
				        echo "<div style='color:red'>".$this -> translate("Denied")."</div>";
				        break;
				    default:
				        echo "<div style='color:yellow'>".$this -> translate("Pending")."</div>";
			   }
          	   ?>
          </td>
          <td>
          	<?php if(!$item -> status) :?>
	            <?php
	            	echo $this->htmlLink(
	                	array('route' => 'default', 'module' => 'advgroup', 'controller' => 'admin-request', 'action' => 'accept', 'id' => $item->group_id, 'req_id' => $item -> getIdentity()),
	                	$this->translate("accept"),
	                	array('class' => 'smoothbox'));
				?>
				|
	          	<?php
	            	echo $this->htmlLink(
	                	array('route' => 'default', 'module' => 'advgroup', 'controller' => 'admin-request', 'action' => 'deny', 'id' => $item->group_id , 'req_id' => $item -> getIdentity()),
	                	$this->translate("deny"),
	                	array('class' => 'smoothbox'));
				?>
			<?php endif;?>
          </td>
        </tr>
        <?php endif;?>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no requests found yet.") ?>
    </span>
  </div>
<?php endif; ?>