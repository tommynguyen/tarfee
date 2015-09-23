<script type="text/javascript">
    function multiDelete()
    {
        return confirm("<?php echo $this->translate('Are you sure you want to delete the selected sponsors?'); ?>");
    }

    en4.core.runonce.add(function(){
		$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
			$$('td.checksub input[type=checkbox]').each(function(i){
	 			i.checked = $$('th.admin_table_short input[type=checkbox]')[0].checked;
			});
		});
		$$('td.checksub input[type=checkbox]').addEvent('click', function(){
			var checks = $$('td.checksub input[type=checkbox]');
			var flag = true;
			for (i = 0; i < checks.length; i++) {
				if (checks[i].checked == false) {
					flag = false;
				}
			}
			if (flag) {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = true;
			}
			else {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = false;
			}
		});
	});
</script>
<h2><?php echo $this->translate("YouNet Responsive Event Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<div style="padding: 5px">  
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsiveevent', 'controller' => 'manage-sponsors', 'action' => 'create'), $this->translate('Add New Sponsor'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(application/modules/User/externals/images/friends/add.png);')) ?>
</div>
<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>
                        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
                        <th>
                            <?php echo $this->translate("Event") ?>
                        </th>
                        <th style="width: 40%">
                             <?php echo $this->translate("Logo") ?>
                        </th>
                        <th style="width: 10%">
                             <?php echo $this->translate("Options") ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->paginator as $item): 
                    	$event = Engine_Api::_() -> getItem('event', $item -> event_id);
                    	if($event):?>
                        <tr>
                            <td class="checksub"><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value='<?php echo $item -> getIdentity(); ?>' /></td>
                            <td><?php echo $this->htmlLink($event->getHref(), $event->title) ?></td>
                            <td>
                            	<a href="<?php echo $event->getHref(); ?>">
                            		<?php $image_url = $item -> getPhotoUrl("thumb.normal");?>
                            		<img width="100px" src="<?php echo $image_url;?>" />
                            	 </a>
                            </td>
                            <td>
                                <?php
                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsiveevent', 'controller' => 'manage-sponsors', 'action' => 'edit', 'id' => $item -> getIdentity()), $this->translate('edit logo'), array('class' => 'smoothbox'));
                                ?>
                                |
                                <?php
                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsiveevent', 'controller' => 'manage-sponsors', 'action' => 'delete', 'id' => $item -> getIdentity()), $this->translate('delete'), array('class' => 'smoothbox'));
                                ?>
                            </td>
                        </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />

        <div class='buttons'>
            <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
        </div>
    </form>

    <br />

    <div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no sponsors posted yet.") ?>
        </span>
    </div>
<?php endif; ?>
