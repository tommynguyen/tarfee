<script type="text/javascript">
function selectAll() {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function deleteSelected(){
    var checkboxes = $$('td input.checkbox[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
        var checked = item.checked;
        var value = item.value;
        if (checked == true && value != 'on'){
            selecteditems.push(value);
        }
    });
    $('multidelete').action = en4.core.baseUrl +'admin/user/upgrade-requests/multireject';
    $('ids').value = selecteditems;
    $('multidelete').submit();
}
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
    <?php echo $this->translate('Manage Upgrade Requests') ?>
</h2>
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br/>
<?php if( count($this->paginator) ): ?>
<form id='multidelete' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
</form>
<form id='multidelete_form' class="ynadmin-table" method="post" action="<?php echo $this->url();?>">
    <table class='admin_table'>
        <thead>
            <tr>
                <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('first_name', 'DESC');"><?php echo $this->translate("First Name") ?></a></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('last_name', 'DESC');"><?php echo $this->translate("Last Name") ?></a></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'DESC');"><?php echo $this->translate("Email") ?></a></th>
                <th><?php echo $this->translate("Membership") ?></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Created") ?></a></th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->paginator as $item): ?>
            <tr>
                <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->membershiprequest_id; ?>' value="<?php echo $item->membershiprequest_id; ?>" /></td>
                <td><?php echo $item->first_name ?></td>
                 <td><?php echo $item->last_name ?></td>
                <td><?php echo $item->email ?></td>
                <?php $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
					$package = $packagesTable->fetchRow(array(
				      'enabled = ?' => 1,
				      'package_id = ?' => (int) $item -> package_id,
				    ));?>
                <td><?php echo $package -> title . ' - '.$package->getPackageDescription() ?></td>
                <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                <td>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'user', 'controller' => 'upgrade-requests', 'action' => 'view-detail', 'id' => $item->membershiprequest_id),
                    $this->translate('view detail'),
                    array('class' => 'smoothbox')
                )?>
                 | 
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'user', 'controller' => 'upgrade-requests', 'action' => 'approve', 'id' => $item->membershiprequest_id),
                    $this->translate('approve'),
                    array('class' => 'smoothbox')
                )?>
                  | 
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'user', 'controller' => 'upgrade-requests', 'action' => 'reject', 'id' => $item->membershiprequest_id),
                    $this->translate('reject'),
                    array('class' => 'smoothbox')
                )?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</form>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s request', 'Total %s requests', $total),$total);
    echo '</p>';
}?>
<div class='buttons'>
    <button type='button' onclick="deleteSelected()"><?php echo $this->translate('Reject Selected') ?></button>
</div>

<br/>
<div> <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?></div>
<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate("There are no new requests.") ?></span>
</div>
<?php endif; ?>