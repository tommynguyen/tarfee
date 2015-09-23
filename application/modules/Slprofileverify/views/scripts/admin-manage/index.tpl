<?php 
    $urlDenyAll = $this->url(array('module' => 'slprofileverify', 'controller' => 'verify', 'action' => 'deny'), 'default');
    $urlVerifyAll = $this->url(array('module' => 'slprofileverify', 'controller' => 'verify', 'action' => 'verify-all'), 'default');
?>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<h3>
  <?php echo $this->translate("Manage Verification Requests") ?>
</h3>
<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<div class='admin_results' id="count_member_verify">
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate("%s members found", $this->locale()->toNumber($count)) ?>
  </div>
</div>
<div class="admin_table_form">
<form id='multimodify_form' method="post" action="">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll();" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('level_id', 'ASC');"><?php echo $this->translate("User Level") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("Approved") ?></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Signup Date") ?></a></th>
        <th style='width: 1%;'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('request_date', 'DESC');">
                <?php 
                    if($this->enable_pending){
                        echo $this->translate("Request Date");
                    } else{
                        echo $this->translate("Verified Date");
                    }
                ?>
            </a>
        </th>
        <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
    <?php if( count($this->paginator) ): ?>
        <?php 
            $i = 1;
            foreach( $this->paginator as $item ):
            $user = $this->item('user', $item->user_id);
                if ($item->approval == 'pending'){
                    $i ++;
                }
        ?>
            <tr>
              <td><input <?php if ($item->approval == 'verified' || $item->approval == 'unverified') echo 'disabled';?> id="modify_<?php echo $item->getIdentity();?>" name='modify_<?php echo $item->getIdentity();?>' value=<?php echo $item->getIdentity();?> type='checkbox' class='checkbox selected'></td>
              <td><?php echo $item->user_id ?></td>
              <td class='admin_table_bold'>
                <?php 
                    echo $this->htmlLink($user->getHref(),
                    $this->string()->truncate($user->getTitle(), 10),
                    array('target' => '_blank'))
                ?>
              </td>
              <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
              <td class='admin_table_email'>
                <?php if( !$this->hideEmails ): ?>
                  <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
                <?php else: ?>
                  (hidden)
                <?php endif; ?>
              </td>
              <td class="admin_table_centered nowrap">
                <a href="<?php echo $this->url(array('module'=>'authorization','controller'=>'level', 'action' => 'edit', 'id' => $item->level_id)) ?>">
                  <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $item->level_id)->getTitle()) ?>
                </a>
              </td>
              <td class='admin_table_centered'>
                <?php echo ( $item->enabled ? $this->translate("Yes") : $this->translate("No") ) ?>
              </td>
              <td class="nowrap">
                <?php echo $this->locale()->toDateTime($item->creation_date) ?>
              </td>
              <td class="nowrap">
                <?php 
                    if($this->enable_pending){
                        echo $this->locale()->toDateTime($item->request_date);
                    } else{
                        echo $this->locale()->toDateTime($item->verified_date);
                    }
                ?>
              </td>
              <td class='admin_table_options'>
                <?php 
                    $urlDeny = $this->url(array('module' => 'slprofileverify', 'controller' => 'verify', 'action' => 'deny', 'id' => $item->user_id, 'type' => 'denied'), 'default');
                    $urlUnvefying = $this->url(array('module' => 'slprofileverify', 'controller' => 'verify', 'action' => 'deny', 'id' => $item->user_id, 'type' => 'unverifying'), 'default');
                    $urlVerify = $this->url(array('module' => 'slprofileverify', 'controller' => 'verify', 'action' => 'verify', 'id' => $item->user_id), 'default');
                    $urlDocument = $this->item('user', $item->user_id)->getHref() . '/tab/' . $this->tab_id;
                ?>
                <?php if($item->approval == 'verified'):?>  
                    <a class='smoothbox' href="<?php echo $urlUnvefying?>">
                    <?php echo $this->translate("Unverify") ?>
                    </a>
                    |
                    <a target= '_blank' href="<?php echo $urlDocument;?>">
                    <?php echo $this->translate("View document") ?>
                    </a>
                <?php elseif($item->approval == 'unverified'):?>  
                    <a style="text-decoration: none">
                    <?php echo $this->translate("Denied") ?>
                    </a>
                    |
                    <a class='smoothbox' href="<?php echo $urlVerify?>">
                    <?php echo $this->translate("Verify") ?>
                    </a>
                    |
                    <a target= '_blank' href="<?php echo $urlDocument?>">
                    <?php echo $this->translate("View document") ?>
                    </a>
                <?php else:?>
                    <a class='smoothbox' href="<?php echo $urlDeny?>">
                    <?php echo $this->translate("Deny") ?>
                    </a>
                    |
                    <a class='smoothbox' href="<?php echo $urlVerify?>">
                    <?php echo $this->translate("Verify") ?>
                    </a>
                    |
                    <a target= '_blank' href="<?php echo $urlDocument?>">
                    <?php echo $this->translate("View document") ?>
                    </a>
                <?php endif;?>
              </td>
            </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
  <?php if($i > 1): ?>
    <div class='buttons' id="action_verify">
      <button onclick="return verifySelected();" value="verify"><?php echo $this->translate("Verify Selected") ?></button>
      <button onclick="return denySelected();" value="deny"><?php echo $this->translate("Deny Selected") ?></button>
    </div>
  <?php endif; ?>
</form>
</div>

<script type="text/javascript">

	function selectAll()
	{
            var i;
            var multidelete_form = $('multimodify_form');
            var inputs = multidelete_form.elements;
            for (i = 1; i < inputs.length - 1; i++) {
                if(!inputs[i].disabled){
                    inputs[i].checked = inputs[0].checked;
                } 
            }
	 }

	function verifySelected()
	{
            var r=confirm('<?php echo $this->translate("Are you sure that you want to verify this profile?");?>');
            if (r===true)
            {
                url = "<?php echo $urlVerifyAll;?>";
                $('multimodify_form').set("action",url);
                $('multimodify_form').submit();
            }
	}

	function denySelected()
	{
            var array_id = '';
            $$(".selected").each(function(element){
                if(element.checked === true)
                {
                    array_id += element.get("id").substring(("modify_").length) + ',';
                }
            });
            if(array_id)
            {
                url = "<?php echo $urlDenyAll;?>";
                Smoothbox.open(url + '/array_id/' + array_id);
                array_id = '';
                return false;
            }
            else
            {
                alert('<?php echo $this->translate("Please select profile you want to deny!");?>');
            }
	}
</script>