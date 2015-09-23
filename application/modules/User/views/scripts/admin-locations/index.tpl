<?php 
$item = array('', 'Country', 'Province/State', 'City');
$child_item = array('Country', 'Province/State', 'City');
$child_items = array('countries', 'provinces/states', 'cities');
$level = 0;
if ($this->location) $level = intval($this->location->level) + 1;
?>
<h2><?php echo $this->translate("Manage Locations") ?></h2>
  <div class='clear'>
	<div class='settings'>
		<form class="global_form">
		  <div>
		    <?php if ($this->location) :?>
		    <div class="breadcrumb">
			<?php foreach($this->location->getParents() as $node): ?>
				<?php echo $node; ?>
				&raquo;
		     <?php endforeach; ?>
		     <?php echo $this->location?>
		     </div>
		     <?php endif;?>
		    <br />
		    <?php if ($this->location) :?>
		    <div class="location-title"><?php echo $this->translate('%s: %s', $item[$level], $this->location)?></div>
		    <br />
		    <?php endif;?>
		    
		    <?php if ($level <= 2) :?>
		 	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'locations', 'action' => 'add', 'id' => $this->id), '+ '.$this->translate('Add %s', $child_item[$level]), array(
		  	'class' => 'smoothbox')) ?>
			<br /> <br />
		      <?php if(count($this->locations)>0):?>
		     <table class='admin_table'>
		      <thead>
		        <tr>
		          <th><?php echo $this->translate($child_item[$level]) ?></th>
		          <th><?php echo $this->translate('Number of %s', $child_items[$level + 1]) ?></th>
		          <th><?php echo $this->translate("Actions") ?></th>
		        </tr>
		
		      </thead>
		      <tbody>
		        <?php foreach ($this->locations as $location): ?>
		                <tr>
		                  <td>
		                      <?php echo $location?>
		                  </td>
		                  <td>
		                  <?php $childs = Engine_Api::_()->getItemTable('user_location')->getLocations($location->getIdentity());	?>
						  <?php echo count($childs)?>                      
		                  </td>
		                  <td>
		                  	<?php if ($level < 2) :?>
		                  	<?php echo $this->htmlLink($location->getHref(), $this->translate("view %s", $child_items[$level + 1]), array()) ?>
		                    |
		                  	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'locations', 'action' => 'add', 'id' =>$location->getIdentity()), $this->translate("add %s", strtolower($child_item[$level + 1])), array(
		                      'class' => 'smoothbox',
		                    )) ?>
		                  	|
		                  	<?php endif;?>
		                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'locations', 'action' => 'edit', 'id' =>$location->getIdentity()), $this->translate("edit"), array(
		                      'class' => 'smoothbox',
		                    )) ?>
		                    |
		                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'locations', 'action' => 'delete', 'id' =>$location->getIdentity()), $this->translate("delete"), array(
		                      'class' => 'smoothbox',
		                    )) ?>               
		
		                  </td>
		                </tr>                  
		               <?php endforeach; ?>
		      </tbody>
		    </table>
		  <?php else:?>
		  <br/>
		  <div class="tip">
		  <span><?php echo $this->translate("There are no %s.", $child_items[$level]) ?></span>
		  </div>
		  <?php endif;?>
		    <br/>
		   <?php endif;?> 
		</div>
		</form>
	</div>
</div>
     