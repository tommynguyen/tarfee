<link type="text/css" href="application/modules/Ynevent/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script src="application/modules/Ynevent/externals/scripts/jquery-1.7.1.min.js"></script>
<script src="application/modules/Ynevent/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>
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
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynevent/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#end_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynevent/externals/images/calendar.png',
            buttonImageOnly: true
        });

    });
     
			
</script>

<script type="text/javascript">

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
    
    function setFeatured(event_id,obj){
    	
            var element = document.getElementById('ynevent_content_'+event_id);
            var checkbox = document.getElementById('ynevent_'+event_id);
                        
            var status = 0;
            
            if(obj.checked==true) status = 1;
            else status = 0;
            var content = element.innerHTML;
            element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Ynevent/externals/images/loading.gif'></img>";
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'manage', 'action' => 'featured'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'event_id' : event_id,                
                'status' : status
              },
              'onRequest' : function(){
              },
              'onSuccess' : function(responseJSON, responseText)
              {
                element.innerHTML = content;
                checkbox = document.getElementById('ynevent_'+event_id);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
              }
            }).send();
            
    }  
</script>





<h2><?php echo $this->translate("Events Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<p>
    <?php echo $this->translate("YNEVENT_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<div class='admin_search'>   
    <?php echo $this->form->render($this); ?>
</div>

<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>
                        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
                        <th class='admin_table_short'>ID</th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');">
                                <?php echo $this->translate("Title") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'DESC');">
                                <?php echo $this->translate("Owner") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_event_events.view_count', 'DESC');">
                                <?php echo $this->translate("Views") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_event_events.creation_date', 'DESC');">
                                <?php echo $this->translate("Date Created") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_event_events.starttime', 'DESC');">
                                <?php echo $this->translate("Date Start") ?>
                            </a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_event_events.endtime', 'DESC');">
                            <?php echo $this->translate("Date End") ?></a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_event_events.featured', 'DESC');">
                            <?php echo $this->translate("Featured") ?></a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_event_events.repeat_type', 'DESC');">
                            <?php echo $this->translate("Repeat Type") ?></a>
                        </th>
                        <th>

                            <?php echo $this->translate("Options") ?>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->paginator as $item): ?>
                        <tr>
                            <td class="checksub"><input type='checkbox' class='checkbox' name='delete_<?php echo $item->event_id; ?>' value='<?php echo $item->event_id; ?>' /></td>
                            <td><?php echo $item->event_id ?></td>
                            <td><?php echo $this->htmlLink($item->getHref(), $item->title) ?></td>
                            <td><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()); ?></td>
                            <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
                            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                            <td><?php echo $this->locale()->toDateTime($item->starttime) ?></td>
                            <td><?php echo $this->locale()->toDateTime($item->endtime) ?></td>
                            <td>
                            	<div id='ynevent_content_<?php echo $item->event_id; ?>' style ="text-align: center;" >
                            		<input type="checkbox" id='ynevent_<?php echo $item->event_id; ?>' onclick="setFeatured(<?php echo $item->event_id; ?>,this)" <?php if($item->featured==1) echo "checked";?>  />
                            		 
                            	</div>
                            </td>
                            <td>
	                            <?php 
	                            	$type = "no-repeat";
	                            	switch($item->repeat_type){
										case 0: $type = "no-repeat";
											break;
										case 1: $type = "daily";
											break; 
										case 7: $type= "weekly";
											break;
										case 30: $type = "monthly";
											break;	
										default: $type = "no-repeat";
											break;
	                            	} 
									echo $type;
	                            ?>
                            </td>
                            <td>
                                <a href="<?php echo $item -> getHref() ?>">
                                    <?php echo "view" ?>
                                </a>
                                |
                                <?php
                                echo $this->htmlLink(array('route' => 'default', 'module' => 'ynevent', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->event_id), $this->translate('delete'), array('class' => 'smoothbox'));
                                ?>
                                |
                                <br />                                
                                <?php
                                if($item -> event_of_date)
								{
									echo $this -> translate("event of the day")."<br/>(".$item -> event_of_date.")";
								}
								else 
								{
									echo $this->htmlLink(array('route' => 'default', 'module' => 'ynevent', 'controller' => 'admin-manage', 'action' => 'event-of-day', 'id' => $item->event_id), $this->translate('event of the day'), array('class' => 'smoothbox'));
								}
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
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no events posted by your members yet.") ?>
        </span>
    </div>
<?php endif; ?>
