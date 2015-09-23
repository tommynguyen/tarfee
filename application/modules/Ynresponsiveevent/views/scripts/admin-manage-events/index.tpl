<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"> </script>
<script src="application/modules/Ynresponsiveevent/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>
<script type = "text/javascript">
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
        $('filter_form').submit();
    }
</script>
<script type="text/javascript">
	jQuery.noConflict();
    jQuery(document).ready(function(){
        // Datepicker
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynresponsiveevent/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#end_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynresponsiveevent/externals/images/calendar.png',
            buttonImageOnly: true
        });

    });
    function multiDelete()
    {
        return confirm("<?php echo $this->translate('Are you sure you want to delete the selected events?'); ?>");
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
<p><?php echo $this -> translate("Manage Events which will be shown in slider.")?></p>
<br/>
<div class='admin_search'>   
    <?php echo $this->form->render($this); ?>
</div>
<div style="padding: 5px">  
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsiveevent', 'controller' => 'manage-events', 'action' => 'create'), $this->translate('Add New Event'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(application/modules/Event/externals/images/create.png);')) ?>
</div>
<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>
                        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
                        <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_ynresponsive1_events.event_id', 'DESC');">
                                <?php echo $this->translate("Event Id") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_ynresponsive1_events.title', 'DESC');">
                                <?php echo $this->translate("Title") ?>
                            </a>
                        </th>
                        <th class='admin_table_short'>
                             <?php echo $this->translate("Photo") ?>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_ynresponsive1_events.starttime', 'DESC');">
                                <?php echo $this->translate("Date Start") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_ynresponsive1_events.endtime', 'DESC');">
                            <?php echo $this->translate("Date End") ?></a>
                        </th>
                        <th>

                            <?php echo $this->translate("Options") ?>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->paginator as $item): 
                    	$event = Engine_Api::_() -> getItem('event', $item -> getIdentity());?>
                        <tr>
                            <td class="checksub"><input type='checkbox' class='checkbox' name='delete_<?php echo $item->event_id; ?>' value='<?php echo $item->event_id; ?>' /></td>
                            <td><?php echo $item->event_id ?></td>
                            <td><?php echo $this->htmlLink($event->getHref(), $item->title) ?></td>
                            <td>
                            	<a href="<?php echo $event->getHref(); ?>">
                            		<?php
                            		$image_url = $item -> getPhotoUrl("thumb.normal");
                            		if(!$image_url)
									{
										$image_url = $event -> getPhotoUrl("thumb.normal");
									}
                            		?>
                            		<img width="100px" src="<?php echo $image_url;?>" />
                            	 </a>
                            </td>
                            <td><?php echo $this->locale()->toDateTime($event->starttime) ?></td>
                            <td><?php echo $this->locale()->toDateTime($event->endtime) ?></td>
                            <td>
                                <a href="<?php echo $event->getHref(); ?>">
                                    <?php echo "view" ?>
                                </a>
                                |
                                <?php
                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsiveevent', 'controller' => 'manage-events', 'action' => 'edit', 'id' => $item->event_id), $this->translate('edit'), array('class' => 'smoothbox'));
                                ?>
                                |
                                <?php
                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresponsiveevent', 'controller' => 'manage-events', 'action' => 'delete', 'id' => $item->event_id), $this->translate('delete'), array('class' => 'smoothbox'));
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
         <?php echo $this->paginationControl($this->paginator, null, null, array(
		    'pageAsQuery' => true,
		    'query' => $this->formValues,
		  )); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no events posted yet.") ?>
        </span>
    </div>
<?php endif; ?>
