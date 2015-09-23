<div id='profile_options'>
<?php $session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile){?>
  <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('_navIcons.tpl', 'ynevent'))
      ->render();
  }
  else
  {?>
  	<ul>
	  <?php 
	  $max = 3;
	  $count = 0;
	  foreach( $this->navigation as $link ): $count ++;
	    if($count <= $max):?>
	    <li>
	      <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
	        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
	        'target' => $link->get('target'),
	      )) ?>
	    </li>    	
	  <?php endif; endforeach; ?>
	  <?php if($count > $max){ ?>
	  <li class="ymb_show_more_option">
	  	<a href="javascript:void(0)" class="ymb_showmore_event">
	  		<i class="icon_showmore_event">
	  			Show more
	  		</i>	  		  		
	  	</a>
	  	<div class="ymb_listmore_option">
	  		<div class="ymb_bg_showmore">
	  			<i class="ymb_arrow_showmore"></i>
	  		</div>	  		
		<?php 
		 	$count = 0;
			foreach( $this->navigation as $link ): $count ++;
			if($count > $max):
		?>
			<div>
			      <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
			        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
			        'target' => $link->get('target'),
			      )) ?>
			 </div>
			 <?php endif; endforeach; ?>
		</div>
	  </li>
	  <?php } ?>
	</ul>
	<script type="text/javascript">
		jQuery(function(){
			jQuery('.ymb_show_more_option').click(function(){
				jQuery(this).find('.ymb_listmore_option').toggle();
			})
		});
	</script>
  <?php  }?>
  
</div>
