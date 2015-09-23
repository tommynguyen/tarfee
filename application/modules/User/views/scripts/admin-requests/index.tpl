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
    $('multidelete').action = en4.core.baseUrl +'admin/user/requests/multireject';
    $('ids').value = selecteditems;
    $('multidelete').submit();
}
</script>
<h2>
    <?php echo $this->translate('Manage Invite Requests') ?>
</h2>

<?php if( count($this->paginator) ): ?>
<form id='multidelete' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
</form>
<form id='multidelete_form' class="ynadmin-table" method="post" action="<?php echo $this->url();?>">
    <table class='admin_table'>
        <thead>
            <tr>
                <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <th><?php echo $this->translate("Email") ?></th>
                <th><?php echo $this->translate("Created") ?></th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->paginator as $item): ?>
            <tr>
                <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->inviterequest_id; ?>' value="<?php echo $item->inviterequest_id; ?>" /></td>
                <td><?php echo $item->email ?></td>
                <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                <td>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'user', 'controller' => 'requests', 'action' => 'view-detail', 'id' => $item->inviterequest_id),
                    $this->translate('view detail'),
                    array('class' => 'smoothbox')
                )?>
                 | 
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'user', 'controller' => 'requests', 'action' => 'approve', 'id' => $item->inviterequest_id),
                    $this->translate('approve'),
                    array('class' => 'smoothbox')
                )?>
                  | 
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'user', 'controller' => 'requests', 'action' => 'reject', 'id' => $item->inviterequest_id),
                    $this->translate('reject'),
                    array('class' => 'smoothbox')
                )?>
                  | 
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'user', 'controller' => 'requests', 'action' => 'email', 'id' => $item->inviterequest_id),
                    $this->translate('send mail'),
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
<div><?php echo $this->paginationControl($this->paginator); ?></div>
<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate("There are no requests.") ?></span>
</div>
<?php endif; ?>