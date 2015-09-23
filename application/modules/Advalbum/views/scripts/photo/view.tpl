
<?php
$this->headScript()
    ->appendFile($this->baseUrl() . '/externals/moolasso/Lasso.js')
    ->appendFile($this->baseUrl() . '/externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->baseUrl() . '/externals/tagger/tagger.js')
    ->appendFile($this->baseUrl() . '/application/modules/Advalbum/externals/scripts/tabcontent.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . '/application/modules/Advalbum/externals/styles/slideshow_popup.css');

function selfURL() {
	$server_array = explode ( "/", $_SERVER ['PHP_SELF'] );
	$server_array_mod = array_pop ( $server_array );
	if ($server_array [count ( $server_array ) - 1] == "admin") {
		$server_array_mod = array_pop ( $server_array );
	}
	$server_info = implode ( "/", $server_array );
	return "http://" . $_SERVER ['HTTP_HOST'] . $server_info . "/";
}
function serverURL() {
	return "http://" . $_SERVER ['HTTP_HOST'];
}
?>
<script type="text/javascript">
  var taggerInstance;
  var movenext = 1;
  en4.core.runonce.add(function() {
	    var taggerInstance = window.taggerInstance = new Tagger('media_photo_next', {
	        'title' : '<?php echo $this->string()->escapeJavascript($this->translate('ADD TAG'));?>',
	        'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
	        'createRequestOptions' : {
	          'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
	          'data' : {
	            'subject' : '<?php echo $this->subject()->getGuid() ?>'
	          }
	        },
	        'deleteRequestOptions' : {
	          'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
	          'data' : {
	            'subject' : '<?php echo $this->subject()->getGuid() ?>'
	          }
	        },
	        'cropOptions' : {
	          'container' : $('media_photo_next')
	        },
	        'tagListElement' : 'media_tags',
	        'existingTags' : <?php echo Zend_Json::encode($this->tags) ?>,
	        'suggestProto' : 'request.json',
	        'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
	        'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
	        'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
	        'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
	      });

		 // Remove the href attrib while tagging
	    var nextHref = $('media_photo_next').get('href');
	    taggerInstance.addEvents({
	      'onBegin' : function() {
	        $('media_photo_next').erase('href');
	      },
	      'onEnd' : function() {
	        $('media_photo_next').set('href', nextHref);
	      }
	    });
    var keyupEvent = function(e) {
        if( e.target.get('tag') == 'html' ||
            e.target.get('tag') == 'body' ) {
          if( e.key == 'right' ) {
            $('photo_next').fireEvent('click', e);
            //window.location.href = "<?php //echo ( $this->nextPhoto ? $this->nextPhoto->getHref() : 'window.location.href' ) ?>";
          } else if( e.key == 'left' ) {
            $('photo_prev').fireEvent('click', e);
            //window.location.href = "<?php //echo ( $this->previousPhoto ? $this->previousPhoto->getHref() : 'window.location.href' ) ?>";
          }
        }
      }
      window.addEvent('keyup', keyupEvent);

      // Add shutdown handler
      en4.core.shutdown.add(function() {
        window.removeEvent('keyup', keyupEvent);
      });

  });

  // rating
  en4.core.runonce.add(function() {
      var pre_rate = <?php echo $this->photo->rating; ?>;
      var rated = '<?php echo $this->is_rated; ?>';
      var photo_id = <?php echo $this->photo->getIdentity(); ?>;
      var total_votes = <?php echo $this->rating_count; ?>;
      var viewer = <?php echo $this->viewer()->getIdentity(); ?>;

      var rating_over = window.rating_over = function(rating) {
          if( rated == 1 ) {
              $('rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
              //set_rating();
          } else if( viewer == 0 ) {
              $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
          } else {
              $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
              for(var x=1; x<=5; x++) {
                  if(x <= rating) {
                      $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big');
                  } else {
                      $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
                  }
              }
          }
      }

      var rating_out = window.rating_out = function() {
		  if (total_votes > 0 && total_votes <= 1) {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('rating')?>";
		  }
		  else {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('ratings')?>";
		  }
          if (pre_rate != 0){
              set_rating();
          }
          else {
              for(var x=1; x<=5; x++) {
                  $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
              }
          }
      }

      var set_rating = window.set_rating = function() {
          var rating = pre_rate;
          if (total_votes > 0 && total_votes <= 1) {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('rating')?>";
		  }
		  else {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('ratings')?>";
		  }
          for(var x=1; x<=parseInt(rating); x++) {
              $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big');
          }

          for(var x=parseInt(rating)+1; x<=5; x++) {
              $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
          }

          var remainder = Math.round(rating)-rating;
          if (remainder <= 0.5 && remainder !=0){
              var last = parseInt(rating)+1;
              $('rate_'+last).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_half');
          }
      }

      var rate = window.rate = function(rating) {
          $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
          for(var x=1; x<=5; x++) {
              $('rate_'+x).set('onclick', '');
          }
          (new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('action' => 'rate', 'subject_id' => $this->photo->getIdentity()), 'album_extended', true) ?>',
              'data' : {
                  'format' : 'json',
                  'rating' : rating,
              },
              'onRequest' : function(){
                  rated = 1;
                  total_votes = total_votes + 1;
                  pre_rate = (pre_rate+rating)/total_votes;
                  set_rating();
              },
              'onSuccess' : function(responseJSON, responseText)
              {
              }
          })).send();

      }

      set_rating();
  });
</script>
<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>
<?php $album_owner = $this->album->getOwner(); ?>
<div id="lightbox">
<div class="generic_layout_container layout_right layout_advalbum_photo_view_right">
  <div id="pettabs" class="tabs_alt tabs_parent" >
        <ul>
        <li><a href ="" rel="Photos" class="selected"><?php echo $this->translate('Photos');?></a></li>
		    <?php if(count($this->paginator)>0 || $this->paginator->count()>0) { ?>
        	<li><a href="" rel="Other Albums"><?php echo $this->translate('Other Albums');?></a></li>
		<?php } ?>
        </ul>
        </div>
     <div id="Photos" style="width: 100%;">
      <div class="global_form_box" style="padding: 0px; width:195px;">
      <div id="thumbs">
            <ul class="thumbps" style="overflow: auto; max-height: 547px; padding: 10px 0px 5px 13px; clear: both;">
             <?php foreach($this->paginatorp as $photo): ?>
                <li>
                    <a class="thumb" href="<?php echo $photo->getHref(); ?>" >
                    <img style="width: 76px; height: 57px;" src="<?php echo $photo->getPhotoUrl("thumb.normal"); ?>" alt="<?php echo $photo->getTitle(); ?>" />
                    </a>
                </li>
                 <?php endforeach; ?>
            </ul>
        </div>
    <?php if( $this->paginator->count() > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum")) ?>
    <?php endif; ?>
    </div>
 </div>

<?php if(count($this->paginator)>0 || $this->paginator->count()>0) { ?>
<div id="Other Albums">
     <div class="global_form_box" style="padding: 0px; width: 195px; clear: both">
    <div class="layout_other_albums">
    <?php
    $album_listing_id = 'photo_view_album_others';
    $no_albums_message = "";
    echo $this->partial('_albumlist.tpl', 
    array('paginator'=>$this->paginator, 
    'album_listing_id'=> $album_listing_id, 
    'no_albums_message'=>$no_albums_message,
    'no_author_info'=>1, 
    'no_bottom_space'=>1,
	'class_mode' => 'ynalbum-grid-view',
	'view_mode' => 'grid',));
    ?>
     </div>
    <?php if( $this->paginator->count() > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum")) ?>
    <?php endif; ?>
    </div>
 </div>
 <?php } ?>
</div>
<script type="text/javascript">
  var mypets=new ddtabcontent("pettabs")
  if (mypets) {
    mypets.setpersist(false)
    mypets.setselectedClassTarget("link")
    mypets.init(200000)
  }
 function test_active(index,tab){
    hide = document.getElementById(tab);
    show = document.getElementById(tab+"_inactive");
    if(hide && show)
    {
        if (hide.style.display != "none" && tab != index) {
            hide.style.display = "none";
            show.style.display = "";
        }
    }
}
function get_active(show){
    test_active(show,"url");
    test_active(show,"html_code");
    test_active(show,"bb_code");
    test_active(show,"send_friend");
    test_active(show,"send_yahoo");
    test_active(show,"get_blog");
}
function get_url(show,get){
    document.getElementById(show).style.display="";
    document.getElementById(show+"_inactive").style.display="none";
    document.getElementById("result_url").value = get;
    document.getElementById("result_url").style.display = "";
    document.getElementById("div_send_friend").style.display = "none";
    get_active(show);
 }
 function show_send_friend(div){
	 document.getElementById("send_friend").style.display = "";
	 document.getElementById("send_friend_inactive").style.display = "none";
	 test_active("send_friend","url");
	 document.getElementById(div).style.display = "";
	 document.getElementById("result_url").style.display = "none";
	 get_active("send_friend");
 }
 function copy_text(input_id){
    input_id.select();
}
function check_send(error_message,result){
    div_tab = document.getElementById("result_send");
    div_tab.style.display = "";
    if (error_message != ""){
        div_tab.className = "error";
        div_tab.innerHTML = "<img src='./application/modules/Advalbum/externals/images/error.gif' border='0' class='buttonlink smoothbox'> "+error_message+"<br><br>";
    }
    else {
        div_tab.className = "success";
        div_tab.innerHTML = "<img src='./application/modules/Advalbum/externals/images/success.gif' class='buttonlink smoothbox' border='0'> "+result+"<br><br>";
    }
}
 function do_send() {
      var send_emails   =  $("send_emails").value;
      var send_name   = $("name").value;
      var send_message   =  $("send_message").value;
      var url_send   =   $("url_send").value;
        new Request.JSON({
          url: '<?php echo $this->url(array('module'=>'advalbum','controller'=>'photo','action'=>'send-image'), 'default') ?>',
          data: {
            'format': 'json',
            'send_emails': send_emails,
            'send_name':send_name,
            'send_message': send_message,
            'url_send': url_send
          },
           onSuccess: function(response) {
                window.check_send(response.error_message, response.result);
            }
        }).send();
  }
  function openPopup(url)
    {
    	if(window.innerWidth <= 480)
      {
      	Smoothbox.open(url, {autoResize : true, width: 300});
      }
      else
      {
      	Smoothbox.open(url);
      }
    }

  </script>
<div style="overflow-x: hidden;">
<h2>
  <?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->htmlLink($this->album, $this->album->getTitle())); ?>
</h2>

<div id="boxbox_1" style="padding-top: 2px">
<div class="layout_middle" style="margin:0;padding: 0px 0px 0px 5px;">
  <?php if (!$this->message_view):?>
  <div class="albums_viewmedia_nav">
     <?php if ($this->album->count() > 1): ?>
    <div style="float: right; border: #DDDDDD solid 1px; -moz-border-radius:10px 10px 10px 10px; padding:3px 4px 0;">
      &nbsp;
      <?php echo $this->htmlLink(( $this->previousPhoto ? $this->previousPhoto->getHref(array('album_virtual' => $this -> album_virtual)) : null ), "<img src='./application/modules/Advalbum/externals/images/prev.png'/>", array('id' => 'photo_prev')) ?>
      &nbsp;
      <?php echo $this->htmlLink(( $this->nextPhoto ? $this->nextPhoto->getHref(array('album_virtual' => $this -> album_virtual)) : null ), "<img src='./application/modules/Advalbum/externals/images/next.png'/>", array('id' => 'photo_next')) ?>
    </div>
    <?php endif; ?>
       <?php if( $this->photo->getTitle() ): ?>
      <div class="albums_viewmedia_info_title" style="font-weight: bold; font-size: 20px;">
      <?php echo $this->photo->getTitle(); ?>
      </div>
    <?php endif; ?>
    <div style="clear:both"></div>
  </div>
  <?php endif;?>
<div class='albums_viewmedia' style="margin: 10px 0px 0px 20px; padding:0; max-width: none;">
   <div class='albums_viewmedia_info'>
        <div class='album_viewmedia_container' id='media_photo_div' style="text-align: center;">
        <?php
        $photoUrl = "javascript:;";
        $photoUrl = $this->escape($this->nextPhoto->getHref(array('album_virtual' => $this -> album_virtual)));
        if(!$this->featured_view):
        ?>
          <a id='media_photo_next' href='<?php echo $photoUrl ?>'>
            <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
              'id' => 'media_photo',
            )); ?>
          </a>
        <?php else: ?>
        <a style="cursor:default" id='media_photo_next' >
               <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
              'id' => 'media_photo',
            )); ?>
          </a>
        <?php endif;?>
        </div>
		<?php
         	$href = "https://maps.google.com/?q=" . urlencode($this->photo->location);
         	$location = sprintf("<a href='javascript:void(0)' onclick='openLocation(%s)'  title='%s'>%s</a>", "\"{$href}\"", Advalbum_Api_Core::shortenText($this->photo->location, 70), $this->photo->location);
        ?>
    <div class="albums_viewmedia_info_date_not_popup">
    	<div id="advalbum_rating" class="rating" onmouseout="rating_out();" style='float:left;'>
    		<span id="rate_1" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
    		<span id="rate_2" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
    		<span id="rate_3" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
    		<span id="rate_4" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
    		<span id="rate_5" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
    	    <span id="rating_text" class="rating_text advalbum_rating_text"><?php echo $this->translate('click to rate'); ?></span>
    	</div>
    	<div style='float:right; padding-right:15px;'>
          <?php
          if (!empty($this->photo->taken_date) && Zend_Date::isDate($this->photo->taken_date, 'YYYY-MM-dd')) {
             echo $this->photo->taken_date;
             if ($this->photo->location)
                echo ' - ';
          }
          if ($this->photo->location) {
          	echo $location;
          }
          ?>
        </div>
    </div>
     <?php if ($this->photo->description): ?>
     	<div class="thumbs_album_slide_description"><?php echo $this->photo->description ?></div>
     <?php endif; ?>

    <div class="albums_viewmedia_info_tags" id="media_tags" style="display: none;  text-align: left; padding-left: 5px; clear:both;">
      <?php echo $this->translate('Tagged:');?>
    </div>
	<div class="albums_viewmedia_info_date_not_popup photo_options">
      <div style="float: left">
      <?php if( $this->canTag ): ?>
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tag'), array('onclick'=>'taggerInstance.begin();')) ?>
      -
      <?php endif; ?>
      <?php if (!$this->message_view):?>

      <?php $url = $this->url(array('module'=> 'activity', 'controller' => 'index', 'action' => 'share',  'type' => 'advalbum_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'),'default', true);?>
      <a href="javascript:;" onclick="openPopup('<?php echo $url?>')"><?php echo $this->translate("Share")?></a>
      <?php $url = $this->url(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'),'default', true);?>
      - <a href="javascript:;" onclick="openPopup('<?php echo $url?>')"><?php echo $this->translate("Report")?></a>
      <?php $url = $this->url(array('module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'),'user_extended', true);?>
      - <a href="javascript:;" onclick="openPopup('<?php echo $url?>')"><?php echo $this->translate("Make Profile Photo")?></a>
      - <?php echo $this->htmlLink(array(
              'route' => 'album_photo_specific',
							'action' => 'download-photo',
							'album_id' => $this->photo->album_id,
					    'photo_id' => $this->photo->getIdentity()),
							$this->translate('Download'), array(
					    )) ?>
      <?php endif;?>
      </div>
     <?php if($this->can_edit): ?>
     	<div class="rotate_options">
	      <div class="rotate_photo"><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'flip','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'vertical'),"<img src='./application/modules/Advalbum/externals/images/photo_flip_vertical.png'>",array('class' => 'smoothbox', 'title'=>$this->translate('flip vertical')));?></div>
	      <div class="rotate_photo"><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'flip','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'horizontal'),"<img src='./application/modules/Advalbum/externals/images/photo_flip_horizontal.png'>",array('class' => 'smoothbox', 'title'=>$this->translate('flip horizontal')));?></div>
	      <div class="rotate_photo"><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'rotate','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'left'),"<img src='./application/modules/Advalbum/externals/images/photo_rotate_left.png'>",array('class' => 'smoothbox', 'title'=>$this->translate('rotate left')));?></div>
	      <div class="rotate_photo"><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'rotate','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'right'),"<img src='./application/modules/Advalbum/externals/images/photo_rotate_right.png'>",array('class' => 'smoothbox', 'title'=>$this->translate('rotate right')));?></div>
      </div>
      <?php endif;?>
	</div>
    </div>
  </div>
  <!-- like pretty photo -->
    <div id="div_photo_view_more">
    <div id="div_minitoolbar" style="padding:8px 0px 8px 0px;">
        <!-- AddThis Smart Layers BEGIN -->
				<!-- Go to http://www.addthis.com/get/smart-layers to customize -->
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4e4e2c280039ea82"></script>
				<script type="text/javascript">
				  addthis.layers({
				    'theme' : 'transparent',
				    'share' : {
				      'position' : 'left',
				      'numPreferredServices' : 5
				    },
				  });
				</script>
				<!-- AddThis Smart Layers END -->
        <style>
        .minitoolbar {}
        .minitoolbar div { font-size: 90%; }
        .minitoolbar a {text-decoration: none;}
        </style>

      <script type="text/javascript">
        var html_code_for_blog  = '<a href = "<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>"><img src = "<?php echo serverURL(); echo $this->photo->getPhotoUrl(); ?>"></a>' ;
        var url_forum = '[URL=<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>][IMG]<?php echo serverURL(); echo $this->photo->getPhotoUrl(); ?>[/IMG][/URL]';
      </script>
       <div class="minitoolbar" style="vertical-align:bottom; padding-left: 2px;">
          <div id = "url" style="float:left"><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_url.png" alt="" align="absmiddle"> <?php echo $this->translate("URL")?></div>
          <div id="url_inactive" style="display:none; float:left"><a href="javascript:void(0)" onclick="get_url('url','<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>')"><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_url.png" alt="" align="absmiddle"> <?php echo $this->translate("URL")?></a></div>
          <div style="float:left">&nbsp; | &nbsp;</div>
          <div id = "html_code" style="display:none; float:left"><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_html.png" alt="" align="absmiddle"> <?php echo $this->translate("HTML code")?></div>
          <div style="float:left" id = "html_code_inactive"><a href="javascript:void(0)" onclick='get_url("html_code",html_code_for_blog)'><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_html.png" alt="" align="absmiddle"> <?php echo $this->translate("HTML code")?></a></div>
          <div style="float:left">&nbsp; | &nbsp;</div>
          <div id = "bb_code" style="display:none; float:left"><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_forum.png" alt="" align="absmiddle"> <?php echo $this->translate("Forum code")?></div>
          <div id = "bb_code_inactive" style="float:left"><a  href="javascript:void(0)" onclick='get_url("bb_code",url_forum)'><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_forum.png" alt="" align="absmiddle"> <?php echo $this->translate("Forum code")?></a></div>
          <div style="float:left">&nbsp; | &nbsp;</div>
          <div id = "send_friend" style="display:none; float:left"><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_send.png" alt="" align="absmiddle"> <?php echo $this->translate("Send to friend")?></div>
          <div id = "send_friend_inactive" style="float:left"><a  href="javascript:void(0)" onclick="show_send_friend('div_send_friend')"><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_send.png" alt="" align="absmiddle"> <?php echo $this->translate("Send to friend")?></a></div>
          <div style="float:left">&nbsp; | &nbsp;</div>
          <div id = "send_yahoo" style="display:none; float:left"><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_yahoo.png" alt="" align="absmiddle"> <?php echo $this->translate("Send to yahoo")?></div>
          <div id = "send_yahoo_inactive" style="float:left"><a  href="ymsgr:sendIM?m=%20<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>"><img src="<?php echo $this->baseUrl(); ?>/application/modules/Advalbum/externals/images/icons/icon_yahoo.png" alt="" align="absmiddle"> <?php echo $this->translate("Send to yahoo")?></a></div>
        </div>
        <div style="clear:both"></div>
        </div>
        <div class="photo_view_bottom">
        <div id="photo_view_form_comment" style="padding-left:4px;">
          <div id="div_send_friend" style="border:1px #CCCCCC solid; display:none; width:90%; padding: 10px;"  class="border_box paddingbox">
              <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() == 0): ?>
                  <?php echo $this->translate("Your name or your email")?>(<font color="#FF0000">*</font>):<br />
                <input id = "name" name="name" type="text" size = "60" /><br /><br />
              <?php else: ?>
                  <input type="hidden" name="name" id="name" value="<?php echo Engine_Api::_()->user()->getViewer()->username; ?>" />
              <?php endif; ?>
              <?php echo $this->translate("Recipient email");?>(<font color="#FF0000">*</font>):<br />
              <input id = "send_emails" name="send_emails" type="text" size = "60" />
              <br /><?php echo $this->translate("Separate multiple email addresses (up to 5) with commas.")?><br /><br />
               <?php echo $this->translate("Message:")?><br />
              <textarea id = "send_message" name="send_message" rows="2" cols="62"></textarea><br /><br />
              <div style="display:none" id="result_send"></div>
              <button name="_send" type="submit" onclick="do_send();"><?php echo $this->translate("Send!");?></button>
              <input type="hidden" name="url_send" id="url_send" value="<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>" />
              <iframe name='sendWindow' style='display:none' src=''></iframe>
          </div>
          <input onclick="copy_text(this)" readonly="readonly" name="result_url" id = "result_url" type="text" size="66" value="<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>"/>
        <br>
        <div style="margin-top:10px;"></div>
      <?php echo $this->action("list", "comment", "core", array("type"=>"advalbum_photo", "id"=>$this->photo->getIdentity())); ?>
      </div>
        </div>
    </div>
  </div>
  </div>
  </div> <!-- end overflow hidden content -->
</div>
<style type="text/css">
.paginationControl {
    -moz-border-radius:3px 3px 3px 3px;
    border:0 solid #D0E2EC;
    clear:both;
    float:right;
    padding-right:10px;
    font-size: 8pt;
}
.paginationControl > li > a {
    padding:0.1em 0.2em;
}
div.albums_viewmedia_info {
    -moz-border-radius:3px 3px 3px 3px;
    border:3px solid #D0E2EC;
    padding:5px;
    text-align:center;
    width:auto;
    min-width: 650px;
}
.tabs_alt {
    margin:0 !important;
    padding-top:5px !important;
}
#global_page_advalbum-photo-view .tabs_alt > ul {
	border: 0;
}
#global_page_advalbum-photo-view .tabs_alt > ul > li > a {
	padding: 5px 6px;
}
#global_page_advalbum-photo-view .tabs_alt > ul > li > a.selected {
    -moz-border-radius:3px 3px 0 0;
    background :#FFFFFF;
    border-color:#CAD9A1 #CAD9A1 -moz-use-text-color;
    color:#000000;
    padding:5px 6px;

}

#global_page_advalbum-photo-view .tabs_alt > ul > li > a:hover {
    -moz-border-radius:3px 3px 0 0;
    background :#FFFFFF;
    border-color:#CAD9A1 #CAD9A1 -moz-use-text-color;
    color:#000000;
    padding:5px 6px;

}
ul.thumbps
{
    padding-top: 15px;
    padding-left: 25px;
    padding-bottom: 5px;
    overflow: hidden;
}
#global_page_advalbum-photo-view  .tabs_alt > ul {
    margin-bottom: 10px;
    height: 15px;
}

ul.thumbps > li {
float:left;
height:62px;
margin:0px 4px 0 0;
}
</style>