<script type="text/javascript">
	var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
    var changeOrder = function(order, default_direction){
        // Just change direction
        if( order == currentOrder ) {
            $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
        } else {
            $('order').value = order;
            $('direction').value = default_direction;
        }
        
        var checks = $$('td.checksub input[type=checkbox]');
		
		for (i = 0; i < checks.length; i++) {
			checks[i].checked = false;
			console.log(checks[i].checked);
		}
			
        $('multidelete_form').submit();
    }
    function multiDelete()
    {
        return confirm("<?php echo $this->translate('Are you sure you want to delete the selected reviews of events or dissmiss all reports on all reviews ?'); ?>");
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


<h2><?php echo $this->translate("Events Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<p>
    <?php echo $this->translate("YNEVENT_VIEWS_SCRIPTS_ADMINREVIEWS_INDEX_DESCRIPTION") ?>
</p>

<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>
                        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
                        <th>
				            <?php echo $this->translate("Review") ?>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_event_events.title', 'DESC');">
                                <?php echo $this->translate("Event") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('report_count', 'DESC');">
                                <?php echo $this->translate("Report Times") ?>
                            </a>
                        </th>
                        <th>
							<?php echo $this->translate("Create Date") ?>
                        </th>
                        <th>
                            <?php echo $this->translate("Latest Report") ?>
                        </th>
                        <th>
                            <?php echo $this->translate("Options") ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->paginator as $item): ?>
                        <tr>
                            <td class="checksub"><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value='<?php echo $item->getIdentity(); ?>' /></td>
                            <td><?php echo $this->string()->truncate($item->body,300) ?></td>
                            <td><?php echo $this->htmlLink( $this->url(array('id' => $item->event_id), 'event_profile'),$this->string()->truncate($item->title,10)) ?></td>
                            <td><?php echo $this->locale()->toNumber($item->report_count) ?></td>
                            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                            <td><?php if($item->getLastReport())  echo $this->locale()->toDateTime($item->getLastReport()); else echo $this->translate("N/A") ?></td>
                            <td>
                               <?php 
                               	echo $this->htmlLink(
                                        array('route' => 'admin_default', 'module' => 'ynevent', 'controller' => 'reviews', 'action' => 'view', 'event_id' => $item->event_id,  'id' => $item->getIdentity()), 'view', array('class' => 'smoothbox',
                                ))
                               	?>
                                |
                                <?php
                                echo $this->htmlLink(
                                        array('route' => 'admin_default', 'module' => 'ynevent', 'controller' => 'reviews', 'action' => 'delete', 'id' => $item->getIdentity()), $this->translate('delete'), array('class' => 'smoothbox',
                                ))
                                ?>
                                |                               
                                <?php
                                echo $this->htmlLink(
                                        array('route' => 'admin_default', 'module' => 'ynevent', 'controller' => 'reviews', 'action' => 'dismiss-report', 'id' => $item->getIdentity()), $this->translate('dismiss reports'), array('class' => 'smoothbox',
                                ))
								?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />
		<input type="hidden" name="order" value="<?php echo $this->formValues['order'] ?>" id="order">
		<input type="hidden" name="direction" value="<?php echo $this->formValues['direction'] ?>" id="direction">
        <div class='buttons' style="float: left; margin-right: 10px;">
            <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
        </div>
        
        <div class='buttons'>
            <button name="dismiss" type='submit'><?php echo $this->translate("Dismiss Selected") ?></button>
        </div>
    </form>

    <br />

    <div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no events posted by your members yet.") ?>
        </span>
    </div>
<?php endif; ?>
