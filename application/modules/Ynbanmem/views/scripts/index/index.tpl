
<div class="headline">
    <h2>
        <?php echo $this->translate('Member Management');?>
    </h2>
    <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
        <?php
		
        // Render the menu
        echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
        ?>
    </div>
    <?php endif;?>
</div>

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
function multiDelete()
{
  var i;
  var multimodify_form = $('multimodify_form');
  var inputs = multimodify_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (inputs[i].checked == true){
        return confirm("<?php echo $this->translate("Are you sure you want to delete these account?") ?>");
    }
  }
  alert("You don\'t choose any account to delete");  
  return false;
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
function multiDelete(url)
	{
		var Checkboxs = document.forms[1].elements;
		var values = "";
		for(var i = 0; i < Checkboxs.length; i++) 
		{	
			 var type = Checkboxs[i].type;
		     if (type=="checkbox" && Checkboxs[i].checked)
			 {				 
		       	values += "," + Checkboxs[i].value;				
		     }
		}
        if(values == "")
        {
            alert("You don\'t choose any user");  
            return false;
        }
		else if(values != "")
		{
			values = "(" + values + ")";
		}
		url += "?userIds=" + values;
		Smoothbox.open(url);
		
	}
function multiUnban(url)
	{
		var Checkboxs = document.forms[1].elements;
		var values = "";
		for(var i = 0; i < Checkboxs.length; i++) 
		{	
			 var type = Checkboxs[i].type;
		     if (type=="checkbox" && Checkboxs[i].checked)
			 {				 
		       	values += "," + Checkboxs[i].value;				
		     }
		}
        if(values == "")
        {
            alert("You don\'t choose any user");  
            return false;
        }
		else if(values != "")
		{
			values = "(" + values + ")";
		}
		url += "?unbanList=" + values;
		
		Smoothbox.open(url);
		
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

<?php if( $this->openUser ): ?>
window.addEvent('load', function() {
  $$('#multimodify_form .admin_table_options a').each(function(el) {
    if( -1 < el.get('href').indexOf('/edit/') ) {
      el.click();
      //el.fireEvent('click');
    }
  });
});
<?php endif ?>
</script>

<br />
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

 <div class="tip">
    <span>
        <?php echo $this->translate('Add more %1$shere%2$s.', '<a target = "_blank" href="'.$this->url(array('action' => 'add'), 'ynbanmem_general').'">', '</a>'); ?>
      
    </span>
  </div>
<br/>
<div class="ynbanmem_table_form">
    <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
        <table class='ynbanmem_table'>
           <thead>
                <tr>
                    <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                    <th style='width: 1%;'>
                        <a href="javascript:void(0);" onclick="changeOrder('user_id', 'DESC');">
                            <?php echo $this->translate("ID") ?>
                        </a>
                    </th>
                    <th style='width: 1%;'>
                        <a href="javascript:void(0);" onclick="changeOrder('username', 'ASC');">
                            <?php echo $this->translate('Username') ?>
                        </a>
                    </th>
                    <th style='width: 1%;'>
                        <a href="javascript:void(0);" onclick="changeOrder('username_mod', 'ASC');">
                            <?php echo $this->translate('Mod/Admin') ?>
                        </a>
                    </th>
                    <th style='width: 1%;'>
                        <a href="javascript:void(0);" >
                            <?php echo $this->translate("Reason") ?>
                        </a>
                    </th>
                    <th style='width: 1%;'>
                        <a href="javascript:void(0);" onclick="changeOrder('stop_date', 'ASC');">
                            <?php echo $this->translate("Expiry Date") ?>
                        </a>
                    </th>

                    <th style='width: 1%;'>
                        <a href="javascript:void(0);">
                            <?php echo $this->translate("Options") ?>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>

                <?php if( count($this->paginator) ): ?>
                <?php foreach( $this->paginator as $item ): ?>
                <tr >
                    <td><input <?php if ($item['user'][0]['level_id'] == 1) echo 'disabled';?> name='modify_<?php echo $item['user'][0]['user_id'];?>' value=<?php echo $item['user'][0]['user_id'];?> type='checkbox' class='checkbox'></td>
                    <td><?php echo $item['user'][0]['user_id'] ?></td>
                    <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item['user'][0]['user_id'])->getHref(), $this->item('user', $item['user'][0]['user_id'])->username, array('target' => '_blank')) ?></td>
					
                    <td class='admin_table_user'><?php 
                        if(count($item[extra_info]) != 0)
                        {
                        $user = Engine_Api::_()->getItem('user', $item['extra_info'][0]['admin']);
                        echo $this->htmlLink($user->getHref(), $user->username, array('target' => '_blank'));
                        }
                        else
                            echo $this->translate("N/A");
                        ?></td>
                    <td> <?php 
                         if(count($item[extra_info]) != 0)
                        {
                        echo $item['extra_info'][0]['reason'];
                        }
                        else
                            echo $this->translate("N/A");
                        ;?>
                        </td>
                    <td > <?php 
                        if(count($item[extra_info]) != 0)
                        {
                       if($item['extra_info'][0]['expiry_date'] == '0000-00-00 00:00:00')
                        echo $this->translate('Unlimited time');
                        else
                        echo $this->locale()->toDateTime($item['extra_info'][0]['expiry_date']);
                        }
                        else
                            echo $this->translate("N/A");
                        
                        ?>
                    </td>
                    <td class='admin_table_options'>
					 <?php if ($this->viewer->isAdmin() || Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'ban') ): // @todo change this to look up actual superadmin level ?>
                        <a  class='smoothbox'href='<?php 
							echo $this->url(array('action' => 'unban', 'id' => $item['banned_id'],'type'=>1));?>'>
                        <?php echo $this->translate("unban") ?></a>
					<?php endif; ?>
                    <?php if ($this->viewer->isAdmin() || Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'note') ): // @todo change this to look up actual superadmin level ?>
                    </br>
                    <a class='smoothbox' href='<?php echo $this->url(array('action' => 'note', 'id' => $item['user'][0]['user_id']));?>'>
                           <?php 
							 if ($item['user'][0]['note'] != NULL)
								echo $this->translate("edit note");
							else echo $this->translate("add note");?>
                    </a>
                    <?php endif;?>
                    <?php if (($this->superAdminCount>1 && $item['user'][0]['level_id']==1 && Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'delete')) || ( $item['user'][0]['level_id'] !=1 && Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'delete')) )                                                                                                 : // @todo change this to look up actual superadmin level ?>
                     </br>
                    <a class='smoothbox' href='<?php 
                       
                       echo $this->url(array('action' => 'delete', 'id' => $item['user'][0]['user_id']));?>'>
                       <?php echo $this->translate("delete") ?>
                       
                    </a>
                <?php endif;?>
                <?php if($this->viewer->isAdmin() || Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'login')): // @todo change this to look up actual superadmin level ?>
                 </br>
                <a  href='<?php echo $this->url(array('action' => 'login', 'id' => $item['user'][0]['user_id']));?>' onclick="loginAsUser(<?php echo $item['user'][0]['user_id'] ?>); return false;">
                   <?php echo $this->translate("login") ?>
                </a>
                   <?php endif; ?>
                   
        </td>
				</tr>
    <?php endforeach; ?>
    <?php endif; ?>
</tbody>
</table>

<div style="float: right;">
        <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
        //'params' => $this->formValues,
        )); ?>
    </div>
<br/>
<?php if ($this->viewer->isAdmin()  || Engine_Api::_()->authorization()->isAllowed('ynbanmem', $this->viewer, 'ban')): // @todo change this to look up actual superadmin level ?>

<div class='buttons'>
     <input name="type" value="email" type="hidden">
    <button type='button' name="submit_button" value="unban" style="float: left;" onclick="multiUnban('<?php echo $this->url(array('action' => 'multi-unban','type'=>'username'));?>')"><?php echo $this->translate("Unban Selected") ?></button>
	<button type='button' name="submit_button" value="delete" style="float: right;" onclick="multiDelete('<?php echo $this->url(array('action' => 'multi-delete'));?>')" ><?php echo $this->translate("Delete Selected") ?></button>
</div>
<?php endif; ?>
</form>
</div>

<?php else:?>
<div class="tip">
    <span>       

        <?php echo $this->translate('Nobody has been banned yet. Add the user name to banned list %1$shere%2$s.', '<a  href="'.$this->url(array('action' => 'add', 'type' => 1), 'ynbanmem_general').'">', '</a>'); ?>

    </span>
</div>
<?php endif; ?>