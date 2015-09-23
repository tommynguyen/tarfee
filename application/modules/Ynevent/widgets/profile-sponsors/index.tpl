<div>
<?php 
	if ($this->viewer->getIdentity() && $this->canAdd) {
		echo $this->htmlLink(
				$this->url(array('controller' => 'sponsor','action' => 'create', 'event_id' => $this->event->getIdentity(), 'tab' => $this->identity), 'event_extended'),
				$this->translate('Add Sponsor'),
				array(
					'class' => 'buttonlink smoothbox ynevent_sponsor_add'
				)
		);		
	}
?>
</div>
<br />

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <div>
    <ul class="ynevent_sponsor_list">
      <?php foreach( $this->paginator as $sponsor ): ?>
       	<li>
       		<div class="ynevent_sponsor_image">
       			<div style="width: 100px; height: 100px; background:url(<?php echo ($sponsor->getPhotoUrl('thumb.normal')) ? ($sponsor->getPhotoUrl('thumb.normal')) :("application/modules/Ynevent/externals/images/no_sponsor.jpg") ; ?>) no-repeat center;"></div>
       		</div>
       		
       		<?php if ($this->viewer->getIdentity() && $this->canAdd) :?>
       		<div class="ynevent_sponsor_action">
       			<?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'sponsor', 'action' => 'edit', 'id' => $sponsor->getIdentity(), 'tab' => $this->identity), $this->translate('Edit'), array(
                	'class' => 'smoothbox',
              	)) ?>
       			 | 
       			 <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'sponsor', 'action' => 'delete', 'id' => $sponsor->getIdentity(), 'tab' => $this->identity), $this->translate('Delete'), array(
                	'class' => 'smoothbox',
              )) ?>
       		</div>
       		<?php endif; ?>
       		
       		<div class="ynevent_sponsor_content">	
       			<div><?php echo $sponsor->name; ?></div>
       			<?php if ($sponsor->url != "") : ?>
       				<?php 
       					if ($sponsor->url) {
			          		$pos = strpos($sponsor->url, "http");
					  		if ($pos === false){
							  	$sponsor->url = "http://" . $sponsor->url;
							}	
				        }
       				?>
       				<div><?php echo $this->translate("More at"); ?>: <a target="_blank" href="<?php echo $sponsor->url; ?>"><?php echo $sponsor->url; ?><a></a></div>	
       			<?php endif;?>
       			<div><?php echo $this->viewMore($sponsor->description, 64); ?></div>
       		</div>
       		
       	</li>
      <?php endforeach; ?>
    </ul>
  </div>

<?php else: ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate('No sponsors have been added in this event yet.');?>
    </span>
  </div>
<?php endif; ?>

