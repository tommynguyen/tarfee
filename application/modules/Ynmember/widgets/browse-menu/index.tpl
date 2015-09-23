<?php $session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile){?>
  <?php if( $this->parent_type !== 'group' ) { ?>
  <div class="headline">
    <h2>
      <?php echo $this->translate('Members') ?>
    </h2>
    <div class="tabs ynmember-menu-top">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  </div>

  <script type="text/javascript">

    // fix hook menu on class navigation
    if ( !$$('.ynmember-menu-top > ul')[0].hasClass('navigation') ) {
      $$('.ynmember-menu-top > ul')[0].addClass('navigation');      
    }
	<?php $advmenu_enable = Engine_Api::_() -> ynmember() -> checkYouNetPlugin('advmenusystem'); ?>
    <?php if(!$advmenu_enable) :?>
    // Hook addClass Active to menu Member default
    var ynmember_body_id = $$('body')[0].get('id');
	    if ( ynmember_body_id.indexOf("global_page_ynmember") >= 0) {
	 		 $$('a.menu_core_main.core_main_user')[0].getParent('li').addClass('active');
	    }
	<?php endif;?>
    // hot fix layout style theme
    <?php if ( defined("YNRESPONSIVE_ACTIVE") ): ?>
      $('global_content').addClass('template-<?php echo YNRESPONSIVE_ACTIVE; ?>');
    <?php endif; ?>
  </script>
<?php } ?>
  <?php }
  else
  {?>
    <div id='tabs'>
      <ul class="ymb_navigation_more">
      <?php 
      $max = 2;
      $count = 0;
      foreach( $this->navigation as $item ): $count ++;
      $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
          'reset_params', 'route', 'module', 'controller', 'action', 'type',
          'visible', 'label', 'href'
          )));
        if($count <= $max):?>
         <li<?php echo($item->active?' class="active"':'')?>>
              <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
          </li> 
      <?php endif; endforeach; ?>
      <?php if(count($this->navigation) > $max):?>
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
        foreach( $this->navigation as $item ): $count ++;
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
      <?php endif;?>
    </ul>
  </div>
  <script type="text/javascript">
    // Hook addClass Active to menu Member default
    var ynmember_body_id = $$('body')[0].get('id');
    if ( ynmember_body_id.indexOf("global_page_ynmember") >= 0) {
      $$('a.menu_core_main.core_main_user')[0].getParent('li').addClass('active');
    }

    jQuery(function(){
      jQuery('.ymb_show_more_menus').click(function(){
        jQuery(this).find('.ymb_listmore_option').toggle();
      })
    });    
  </script>
  <?php  }?>