<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<script type="text/javascript">
    function updateUploader()    {
        if($('photo_delete').checked) {
            $('photo_group-wrapper').style.display = 'block';
        } else {
            $('photo_group-wrapper').style.display = 'none';
        }
    }
</script>

<?php $session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile){?>
  <div class="headline">
  <h2>
    <?php echo $this->translate('Videos') ?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->menus_navigation)
        ->render();
    ?>
  </div>
</div>
  <?php }
  else
  {?>
  <div id='tabs'>
	  	<ul class="ymb_navigation_more">
		  <?php 
		  $max = 3;
		  $count = 0;
		  foreach( $this->menus_navigation as $item ): $count ++;
		  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
	        'reset_params', 'route', 'module', 'controller', 'action', 'type',
	        'visible', 'label', 'href'
	        )));
		    if($count <= $max):?>
		     <li<?php echo($item->active?' class="active"':'')?>>
          		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
        	</li>	
		  <?php endif; endforeach; ?>
		  <li class="ymb_show_more_menus">
		  	<a href="javascript:void(0)" class="ymb_showmore_menus">
		  		<i class="icon_showmore_menus">
		  			<?php echo $this-> translate("Show more");?>
		  		</i>	  		  		
		  	</a>
		  	<div class="ymb_listmore_option">
		  		<div class="ymb_bg_showmore">
		  			<i class="ymb_arrow_showmore"></i>
		  		</div>	  		
			<?php 
			 	$count = 0;
				foreach( $this->menus_navigation as $item ): $count ++;
				 $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
			        'reset_params', 'route', 'module', 'controller', 'action', 'type',
			        'visible', 'label', 'href'
			        )));
				if($count > $max):
			?>
				<div<?php echo($item->active?' class="active"':'')?>>
				     <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
				 </div>
				 <?php endif; endforeach; ?>
			</div>
		  </li>
		</ul>
	</div>
	<script type="text/javascript">
		jQuery(function(){
			jQuery('.ymb_show_more_menus').click(function(){
				jQuery(this).find('.ymb_listmore_option').toggle();
			})
		});
	</script>
  <?php  }?>

    <h2>
        <?php echo $this->translate('Edit playlist'); ?>
    </h2>
<?php echo $this->form->render($this); ?>