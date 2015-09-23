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
  $this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Advalbum/externals/styles/slideview.css');


 function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 }
 function serverURL()
 {
    return "http://".$_SERVER['HTTP_HOST'];
 }
?>
<style>
#global_page_advalbum-photo-frameview {
	margin:0;
	padding:0;
}
#global_page_advalbum-photo-frameview #global_content_simple {
	margin: 0;
	padding: 0;
}
</style>
<div class="pf_main">
<div class="pf_title">
  <?php

  	$photo_title = trim($this->photo->getTitle());
	if (!$photo_title) {
		$photo_title = $this->translate('Photo #%1$s of %2$s', $this->photo->order + 1, $this->album->count());
	}

	$album_owner = $this->album->getOwner();
	echo "<b>$photo_title</b>";
	/*echo "<b>$photo_title</b>" . $this->translate(' in album <b>%1$s</b> by <b>%2$s</b>', $this->htmlLink($this->album, $this->album->getTitle(), array('target'=>'_top')), $this->htmlLink($album_owner->getHref(), $album_owner->getTitle(), array('target'=>'_top'))); */
  ?>
</div>
<?php
$fitSize = $this->photo->fitPhotoSize(array('w'=>560,'h'=>420));
$arr_photo_params = array('id' => 'media_photo');
if ($fitSize) {
	$arr_photo_params['width'] = $fitSize['w'];
	$arr_photo_params['height'] = $fitSize['h'];
}
?>
<div class="pf_photo_main">
	<div class="pf_photo">
	<table cellspacing="0" cellpadding="0" border="0" width="560">
	<tr>
		<td width="560" height="420" align="center" valign="middle">
		<center>
		<a id='media_photo_next' target='_top'>
        <?php echo $this->htmlImage($this->photo->getPhotoUrl("profile.normal"), str_replace('"', '&quot;', $this->photo->getDescription()), $arr_photo_params); ?>
		</a>
		</center>
		</td>
	</tr>
	</table>
	</div>
	<div class="pf_tag">
     <?php if($this->can_edit): ?>
      <div style="float: right; padding-right: 10px;"><?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'rotate','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'right'),"<img src='./application/modules/Advalbum/externals/images/photo_rotate_right.png' valign='absmiddle'>",array('class' => 'smoothbox'));?>&nbsp;&nbsp;<?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'rotate','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'left'),"<img src='./application/modules/Advalbum/externals/images/photo_rotate_left.png' valign='absmiddle'>",array('class' => 'smoothbox'));?></div>
      <?php endif;?>
	  <div class="pf_share">
	<?php echo $this->translate('Added');?> <?php echo $this->timestamp($this->photo->modified_date) ?>
      <?php if (!$this->message_view):?>

      - <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'advalbum_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
      - <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
      - <?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?>
      <?php endif;?>
	  </div>
	</div>
</div>
<!--[if IE 7]>
<style>
.pf_comment {
	width: 544px;
}
</style>
<![endif]-->
<div class="pf_comment" id="div_comment">
  <script language="javascript">
    var html_code_for_blog  = '<a href = "<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>"><img src = "<?php echo serverURL(); echo $this->photo->getPhotoUrl(); ?>"></a>' ;
    var url_forum = '[URL=<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>][IMG]<?php echo serverURL(); echo $this->photo->getPhotoUrl(); ?>[/IMG][/URL]';
  </script>
   <div style="font-weight:bold; vertical-align:bottom;">
          <div id ="url" style="float:left">URL</div>
          <div id="url_inactive" style="display:none; float:left"><a href="javascript:void(0)" onclick="get_url('url','<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>')">URL</a></div>

          <div id = "html_code" style="display:none; float:left">&nbsp;|&nbsp;<?php echo $this->translate("HTML code")?></div>
          <div style="float:left" id = "html_code_inactive">&nbsp;|&nbsp;<a href="javascript:void(0)" onclick='get_url("html_code",html_code_for_blog)'><?php echo $this->translate("HTML code")?></a></div>

          <div id = "bb_code" style="display:none; float:left">&nbsp;|&nbsp;<?php echo $this->translate("Forum code")?></div>
          <div id = "bb_code_inactive" style="float:left">&nbsp;|&nbsp;<a  href="javascript:void(0)" onclick='get_url("bb_code",url_forum)'><?php echo $this->translate("Forum code")?></a></div>

          <div id = "send_friend" style="display:none; float:left">&nbsp;|&nbsp;<?php echo $this->translate("Send to friend")?></div>
          <div id = "send_friend_inactive" style="float:left">&nbsp;|&nbsp;<a  href="javascript:void(0)" onclick="show_send_friend('div_send_friend')"><?php echo $this->translate("Send to friend")?></a></div>


          <div id = "send_yahoo" style="display:none; float:left">&nbsp;|&nbsp;<?php echo $this->translate("Send to yahoo")?></div>
          <div id = "send_yahoo_inactive" style="float:left">&nbsp;|&nbsp;<a  href="ymsgr:sendIM?m=%20<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>"><?php echo $this->translate("Send to yahoo")?></a></div>
        <br /></div>
      <div align="left" style="margin-top:5px;">
      <div id="div_send_friend" style="border:1px #CCCCCC solid; display:none; width:80%; padding: 10px;"  class="border_box paddingbox">
          <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() == 0): ?>
              <?php echo $this->translate("Your name or your email")?>(<font color="#FF0000">*</font>):<br />
            <input id = "name" name="name" type="text" size = "60" /><br /><br />
          <?php else: ?>
              <input type="hidden" name="name" id="name" value="<?php echo Engine_Api::_()->user()->getViewer()->username; ?>" />
          <?php endif; ?>
          <?php echo $this->translate("Recipient email");?>(<font color="#FF0000">*</font>):<br />
          <input id = "send_emails" name="send_emails" type="text" size = "60" /><br /><?php echo $this->translate("Separate multiple email addresses (up to 5) with commas.")?><br /><br />
           <?php echo $this->translate("Message:")?><br />
          <textarea id = "send_message" name="send_message" rows="2" cols="55"></textarea><br /><br />
          <div style="display:none" id="result_send"></div>
          <button name="_send" type="submit" onclick="do_send();"><?php echo $this->translate("Send!");?></button>
          <input type="hidden" name="url_send" id="url_send" value="<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>" />
          <iframe name='sendWindow' style='display:none' src=''></iframe>
      </div>
          <input onclick="copy_text(this)" readonly="readonly" name="result_url" id = "result_url" type="text" size="80" value="<?php echo selfURL();?>albums/photo/view/album_id/<?php echo $this->photo->album_id; ?>/photo_id/<?php echo $this->photo->getIdentity(); ?>"/>
     </div>
	 <div class="pf_comment_text">
  <?php echo $this->action("list", "comment", "core", array("type"=>"advalbum_photo", "id"=>$this->photo->getIdentity())); ?>
	 </div>
	 <div>&nbsp;</div>
<!--[if IE 8]>
<style>
.pf_comment {
	overflow-y: scroll;
}
</style>
<![endif]-->
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
    background-image:url("./application/modules/Core/externals/images/foreground_bg.png");
    background-repeat:repeat-x;
    border:3px solid #D0E2EC;
    padding:5px;
    text-align:center;
    width:560px;
}
.tabs_alt {
    margin:0 !important;
    padding-top:5px !important;
}
#global_page_advalbum-photo-view .tabs_alt > ul > li > a.selected {
    -moz-border-radius:3px 3px 0 0;
    background :#FFFFFF;
    border-color:#CAD9A1 #CAD9A1 -moz-use-text-color;
    color:#000000;
    padding:5px 6px;
    position:relative;
    top:0px;
}

#global_page_advalbum-photo-view .tabs_alt > ul > li > a:hover {
    -moz-border-radius:3px 3px 0 0;
    background :#FFFFFF;
    border-color:#CAD9A1 #CAD9A1 -moz-use-text-color;
    color:#000000;
    padding:5px 6px;
    position:relative;
    top:0px;
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
height:79px;
margin:0px 4px 0 0;
}

</style>
<script type="text/javascript">
  /*var mypets=new ddtabcontent("pettabs")
    mypets.setpersist(false)
    mypets.setselectedClassTarget("link")
    mypets.init(200000)
*/
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
                check_send(response.error_message, response.result);
            }
        }).send();
  }
  </script>
</div>
</div>
<script type="text/javascript">
function fixLinks() {
	var arrLinks = document.links;
	for (idxL=0; idxL<arrLinks.length;idxL++) {
		jsPos = arrLinks[idxL].href.indexOf('/profile/');
		if (jsPos>=0) {
			arrLinks[idxL].setAttribute("target","_top");
		}
	}
}
function do_onload() {
	fixLinks();
	if (parent.loading_complete) {
		parent.loading_complete(<?php echo $this->photo->getIdentity(); ?>);
	}
}
document.onload = do_onload();


function doCountView() {
	/*
	urlContent = '<?php echo $this->baseUrl() . $this->photo->getHref(array('action'=>'frameviewcount','vcode'=>$this->photo->frameviewCode())); ?>';
	objCount = document.getElementById("iframe_count_<?php echo $this->photo->getIdentity(); ?>");
	if (objCount) {
		objCount.innerHTML = '<iframe border="0" frameborder="0" scrolling="no" width="1" height="1" marginwidth="0" marginheight="0" src="' + urlContent + '"></iframe>';
	}
	*/
}

function doCountView2() {
/*
	tmpObj = false;
	if (window.XMLHttpRequest)
		tmpObj = new XMLHttpRequest();
	else
		tmpObj = new ActiveXObject("Microsoft.XMLHTTP");
	if (tmpObj)
	{
		tmpObj.onreadystatechange = function()
		{
			if (tmpObj.readyState == 0) { }
			if (tmpObj.readyState == 1) { }
			if (tmpObj.readyState == 2) { }
			if (tmpObj.readyState == 3) { }
			if (tmpObj.readyState == 4 && tmpObj.status == 200)
			{
				alert('<?php echo $this->photo->getIdentity(); ?>');
			}
		}
		urlContent = '<?php echo $this->baseUrl() . $this->photo->getHref(array('action'=>'frameviewcount','vcode'=>$this->photo->frameviewCode())); ?>';
		if (urlContent.indexOf("?")==-1)
			urlContent = urlContent + "?";
		else
			urlContent = urlContent + "&";
		dateObj = new Date();
		randContent = dateObj.getTime();

		urlContent = urlContent + randContent;

		tmpObj.open("GET", urlContent, true);
		tmpObj.send(null);
	}
*/
}
var counted = false;
function do_count() {
	if (!counted) {
		doCountView();
		counted = true;
	}
}
</script>