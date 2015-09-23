<script src="<?php $this->baseURL()?>application/modules/Ynfeedback/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynfeedback/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynfeedback/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynfeedback/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynfeedback/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">


<script type="text/javascript">
	 window.addEvent('domready', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            onSelect: function(date){
            }
        });
    });
	
	function highlightFeedback(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl+'admin/ynfeedback/feedbacks/highlight';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': id,
	            'value': value
	        },
	        'onSuccess' : function(responseJSON, responseText)
	        {
	          alert(responseJSON.message);
	        }
	    }).send();
	}
	
	function changeStatus(obj, id) {
	    var value = obj.getSelected().get('value');
	    var url = en4.core.baseUrl + 'admin/ynfeedback/feedbacks/changestatus';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': id,
	            'value': value[0]
	        },
	        'onSuccess' : function(responseJSON, responseText)
	        {
	          alert(responseJSON.message);
	        }
	    }).send();
	}
	
	function mergeSelected(){
	    var checkboxes = $$('td input.multiselect_checkbox[type=checkbox]');
	    var selecteditems = [];
	    checkboxes.each(function(item){
	      var checked = item.checked;
	      var value = item.value;
	      if (checked == true && value != 'on'){
	        selecteditems.push(value);
	      }
	    });
	    $('multiselect').action = en4.core.baseUrl +'admin/ynfeedback/feedbacks/mergeselected';
	    $('ids').value = selecteditems;
	    $('multiselect').submit();
	}
	
	function changeOrder(listby, default_direction){
	    var currentOrder = '<?php echo $this->formValues['orderby'] ?>';
	    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
	    // Just change direction
	    if( listby == currentOrder ) {
	        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
	    } 
	    else {
	        $('orderby').value = listby;
	        $('direction').value = default_direction;
	    }
	    $('filter_form').submit();
	}

    function selectAll() {
	    var hasElement = false;
	    var i;
	    var multiselect_form = $('multiselect_form');
	    var inputs = multiselect_form.elements;
	    for (i = 1; i < inputs.length; i++) {
	        if (!inputs[i].disabled && inputs[i].hasClass('multiselect_checkbox')) {
	            inputs[i].checked = inputs[0].checked;
	        }
	    }
	}
	
	function multiSelected(action){
	    var checkboxes = $$('td input.multiselect_checkbox[type=checkbox]');
	    var selecteditems = [];
	    checkboxes.each(function(item){
	      var checked = item.checked;
	      var value = item.value;
	      if (checked == true && value != 'on'){
	        selecteditems.push(value);
	      }
	    });
	    $('multiselect').action = en4.core.baseUrl +'admin/ynfeedback/feedbacks/multiselected';
	    $('ids').value = selecteditems;
	    $('select_action').value = action;
	    $('multiselect').submit();
	}
</script>
<h2><?php echo $this->translate("YouNet Feedback Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Feedback') ?></h3>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>	
<form id='multiselect_form' style="overflow: auto;" method="post" action="<?php echo $this->url();?>">
	<table class='admin_table' style="width: 100%">
	  <thead>
	    <tr>
	      <th><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('idea_id', 'ASC');"><?php echo $this->translate('ID') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'ASC');"><?php echo $this->translate('Owner') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');"><?php echo $this->translate('Post on') ?></a></th>
	      <th><?php echo $this->translate('Category') ?></th>
	      <th><?php echo $this->translate('Highlight') ?></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');"><?php echo $this->translate('Status') ?></a></th>
	      <th><?php echo $this->translate('Options') ?></th>
	    </tr>
	  </thead>
	  <tbody>
	    <?php foreach ($this->paginator as $item): ?>
	    <tr>
	    	<td><input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/></td>
	    	<td><?php echo $item -> getIdentity();?></td>
	    	<td><?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?></td>
	    	<?php if($item -> user_id != 0) :?>
	    		<?php $user = Engine_Api::_() -> getItem('user', $item -> user_id); ?>
	    		<td><?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?></td>
	    	<?php else:?>
	    		<td><?php echo $this -> translate('Guest');?></td>
	    	<?php endif;?>
	    	<td><?php echo $item->creation_date; ?></td>
	    	<?php $category = $item->getCategory();?>
	    	<td><?php echo ($category) ? $category->title : ''; ?></td>
	    	<td><input type="checkbox" class="highlight_checkbox" value="1" onclick="highlightFeedback(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->highlighted) echo 'checked'?>/></td>
	    	<td>
	    		<select onchange="changeStatus(this, <?php echo $item -> getIdentity();?>)" class='change_status'>
	    		<?php foreach($this -> statusLists as $key => $value) :?>
	    			<option <?php if($item -> status_id == $key) echo "selected"?> value='<?php echo $key;?>'><?php echo $value;?></option>
	    		<?php endforeach;?>
	    		</select>
	    	</td>
	    	<td>
	    		<!-- edit button -->
	    		<?php echo $this->htmlLink(
                array('route' => 'ynfeedback_specific', 'action' => 'edit', 'idea_id' => $item->getIdentity(), 'slug' => $item -> getSlug()), 
               		  $this->translate('Edit'), 
           		   array('class' => '')) ?>
           		|
           		<!-- delete button -->
	    		<?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'feedbacks', 'action' => 'delete',  'id' => $item->getIdentity()), 
               		  $this->translate('Delete'), 
           		   array('class' => 'smoothbox')) ?>
           		|
           		<!-- merge button -->
	    		<?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'feedbacks', 'action' => 'mergeselected',  'ids' => $item->getIdentity()), 
               		  $this->translate('Merge'), 
           		   array('class' => '')) ?>
           		|
           		<!-- email follower button -->
	    		<?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'feedbacks', 'action' => 'email',  'id' => $item->getIdentity()), 
               		  $this->translate('Email to Followers'), 
           		   array('class' => 'smoothbox')) ?>
           		|
           		<!-- note-->
           		<?php $noteCount = $item -> getNoteCount();?>
	    		<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'note', 'feedback_id' => $item->getIdentity() ), 'ynfeedback_extended');?>"><?php echo $this->translate("Note") . "($noteCount)";?></a>
	    		|
	    		<!-- statistic button -->
	    		<?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'feedbacks', 'action' => 'statistic',  'id' => $item->getIdentity()), 
               		  $this->translate('View Statistic'), 
           		   array()) ?>
       		   |
           		<!-- make decision-->
           		<?php
           			$labelDecision = "";
           			if(empty($item -> decision) || $item -> decision == "")
					{
           				$labelDecision = $this->translate('Make Decision');
					}
					else
					{
           				$labelDecision = $this->translate('Edit Decision');
           			}
           		?>
           		<?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'feedbacks', 'action' => 'decision',  'id' => $item->getIdentity()), 
               		$labelDecision , 
           		   array('class' => 'smoothbox')) ?>
	    	</td>
	    </tr>
	    <?php endforeach; ?>
	  </tbody>
	</table>
</form>
<?php if ($this->paginator->getTotalItemCount()) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
<div class='buttons'>
    <button type='button' onclick="multiSelected('Delete')"><?php echo $this->translate('Delete Selected') ?></button>
    <button type='button' onclick="mergeSelected()"><?php echo $this->translate('Merge Selected') ?></button>
</div>
<br/>
<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no feedbacks.') ?>
    </span>
  </div>
<?php endif; ?>
