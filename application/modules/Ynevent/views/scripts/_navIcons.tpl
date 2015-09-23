<script type="text/javascript">
function checkOpenPopup(url)
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
<?php
$subject = Engine_Api::_()->core()->getSubject('event');
?>
<ul>
    <?php foreach ($this->container as $link): ?>
        <li>
            <?php
        	 $arr_custom = array("smoothbox menu_ynevent_profile ynevent_profile_share", 
        	 "smoothbox menu_ynevent_profile ynevent_profile_invite",
					 "smoothbox menu_ynevent_profile ynevent_profile_style",
					 "smoothbox menu_ynevent_profile ynevent_profile_promote"
					 );
         		if(in_array($link->getClass(), $arr_custom)): 
         		$class = str_replace("smoothbox", "", $link->getClass())?>
            <a style = "<?php echo 'background-image: url(' . $link->get('icon') . ');' ?>" target = "<?php echo $link->get('target')?>" class = "buttonlink <?php echo $class?>" href = "javascript:;" onclick = "checkOpenPopup('<?php echo $link->getHref()?>')"><?php echo $this->translate($link->getLabel())?></a>
					<?php	else:
							echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
                'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
                'style' => 'background-image: url(' . $link->get('icon') . ');',
                'target' => $link->get('target'),
            ));
						endif;
            ?>
        </li>
    <?php endforeach; ?>
</ul>