<?php
$session = new Zend_Session_Namespace('mobile');
$staticBaseUrl = $this->baseUrl();
if(!$session -> mobile)
{
	$this->headScript()
		 	->appendFile($staticBaseUrl . '/application/modules/Ynevent/externals/scripts/jquery-1.7.1.min.js')
			->appendFile($staticBaseUrl . '/application/modules/Ynevent/externals/scripts/jquery.tools.min.js');;
	$this->headLink() ->prependStylesheet($staticBaseUrl . '/application/css.php?request=application/modules/Ynevent/externals/styles/calendar_tooltip.css');
}
?>
<?php 
if(!$session -> mobile):?>
<script type="text/javascript">
	    jQuery.noConflict();
	    <?php if ($this->enableTooltip) : ?>
		    jQuery(document).ready(function(){
		        jQuery(document).delegate(".ynevent", "mouseenter", function() 
		        {
		            if (!jQuery(this).data("tooltip")) 
		            {
		                tip = jQuery(this);                  
		                tip.tooltip();
		                tip.trigger("mouseenter");
		            }
		        });
		    });
   		<?php endif;?>
   		
</script>
<?php endif;?>
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
<div class="ynevent-action-view-method">
  <div class="ynevent_home_page_list_content" rel="map_view">
    	<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
    	<a href="javascript:;"   class="ynevent_home_page_list_content_icon tab_icon_calendar active"></a>
  </div>
  <div class="ynevent_home_page_list_content" rel="map_view">
    	<div class="ynevent_home_page_list_content_tooltip acitve"><?php echo $this->translate('List View')?></div>
    	<a href="<?php echo $this -> url(array('action' => 'manage'), 'event_general', true);?>" class="ynevent_home_page_list_content_icon tab_icon_list_view"></a>
  </div>
 </div>
<?php
/* date settings */

$month = $this->month;

$year = $this->year;

/* select month control */
$select_month_control = '<select name="month" id="ynevent_month">';
for ($x = 1; $x <= 12; $x++) {
    $m = $this->translate(date('F', mktime(0, 0, 0, $x, 1, $year)));
    $select_month_control.= '<option value="' . $x . '"' . ($x != $month ? '' : ' selected="selected"') . '>' . $m . '</option>';
}

$select_month_control.= '</select>';

/* select year control */
$year_range = 7;
$select_year_control = '<select name="year" id="ynevent_year">';
for ($x = ($year - floor($year_range / 2)); $x <= ($year + floor($year_range / 2)); $x++) {
    $select_year_control.= '<option value="' . $x . '"' . ($x != $year ? '' : ' selected="selected"') . '>' . $x . '</option>';
}
$select_year_control.= '</select>';

/* "next month" control */
$next_month_link = '<a href="' . $this->url(array('action' => 'calendar'), 'event_general') . '?month=' . ($month != 12 ? $month + 1 : 1) . '&amp;year=' . ($month != 12 ? $year : $year + 1) . '" class="control control-ynevent-next"><img class="ynevent_arrow" src="application/modules/Ynevent/externals/images/next_rtl_calendar.png" /></a>';
$previous_month_link = '<a href="' . $this->url(array('action' => 'calendar'), 'event_general') . '?month=' . ($month != 1 ? $month - 1 : 12) . '&amp;year=' . ($month != 1 ? $year : $year - 1) . '" class="control control-ynevent-prev"><img src="application/modules/Ynevent/externals/images/previous-ltr_calendar.png" /></a>';

/* bringing the controls together */
$label = $this->translate("Go");
$controls = '<form class="ynevent_mycalendar_form" method="get">' . $select_month_control . $select_year_control . '<button onclick="getData()" name="submit" value="Go">'.$label.'</button>' . $previous_month_link . '' . $next_month_link . ' </form>';


/* draws a calendar */

$events = $this->events;
$m =$this->translate( date('F',mktime(0,0,0,$month,1,$year)));
echo '<h3 style="padding-right:15px;">' . $m . ' ' . $year . '</h3>';

$url = $this->url(array('action' => 'promote-calendar', 'month' => $this->month, 'year' => $this->year), 'event_general');
?>
<a onclick="checkOpenPopup('<?php echo $url?>')" class="buttonlink ynevent_promote_calendar" href="javascript:;"><?php echo $this->translate('Promote This Calendar')?></a>
<?php

echo '<div id="ynevent_myCalendar">';
echo '<div class="mycalendar_controls">' . $controls . '</div>';
echo $this->calendar; //draw_calendar($month, $year, $events);
echo '</div>';
echo '<br /><br />';
?>

