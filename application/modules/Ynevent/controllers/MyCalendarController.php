<?php

class Ynevent_MyCalendarController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		//Add root node
		$table = Engine_Api::_() -> getDbTable("categories", "ynevent");
		$db = $table -> getAdapter();

		$select = $db -> select() -> from('engine4_event_categories', "max(category_id) as maxId");
		$max_id = $db -> fetchRow($select);
		$maxId = $max_id['maxId'];

		//insert root
		$data = array(
			'title' => 'All Categories',
			'pleft' => 0,
			'pright' => 2 * $maxId + 1,
			'level' => 0
		);
		$db -> insert('engine4_event_categories', $data);
		//update root's id
		$db -> update('engine4_event_categories', array(
			'category_id' => 0,
			'parent_id' => -1,
		), array('category_id =?' => $maxId + 1, ));

		//Update pleft, pright, level
		$select = $db -> select() -> from('engine4_event_categories') -> where("category_id > ?", 0);
		$categories = $db -> fetchAll($select);
		foreach ($categories as $category)
		{
			$id = $category['category_id'];
			$db -> update('engine4_event_categories', array(
				'pleft' => $id * 2 - 1,
				'pright' => $id * 2,
				'parent_id' => 0,
				'level' => 1,
			), array('category_id =?' => $id, ));
		}
	}

	public function draw_calendar($month, $year, $events = array())
	{

		/* draw table */
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

		/* table headings */

		$sun = $this -> view -> translate("Sunday");
		$mon = $this -> view -> translate("Monday");
		$tue = $this -> view -> translate("Tuesday");
		$wed = $this -> view -> translate("Wednesday");
		$thu = $this -> view -> translate("Thursday");
		$fri = $this -> view -> translate("Friday");
		$sat = $this -> view -> translate("Saturday");
		$headings = array(
			$sun,
			$mon,
			$tue,
			$wed,
			$thu,
			$fri,
			$sat
		);
		$calendar .= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';

		/* days and weeks vars now ... */
		$running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
		$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		/* row for week one */
		$calendar .= '<tr class="calendar-row">';

		/* print "blank" days until the first of the current week */
		for ($x = 0; $x < $running_day; $x++)
		:
			$calendar .= '<td class="calendar-day-np">&nbsp;</td>';
			$days_in_this_week++;
		endfor;

		/* keep going with days.... */
		for ($list_day = 1; $list_day <= $days_in_month; $list_day++)
		:
			$calendar .= '<td class="calendar-day"><div style="height:100px;">';
			/* add in the day number */
			$calendar .= '<div class="day-number">' . $list_day . '</div>';
			$month1 = $month;
			$list_day1 = $list_day;
			if ($month < 10)
			{
				$month1 = '0' . $month;
			}
			if ($list_day < 10)
			{
				$list_day1 = '0' . $list_day;
			}

			$event_day = $year . '-' . $month1 . '-' . $list_day1;

			if (count($events))
			{
				foreach ($events as $event)
				{
					$startDateObject = new Zend_Date(strtotime($event -> starttime));

					if ($viewer && $viewer -> getIdentity())
					{
						$tz = $viewer -> timezone;
						$startDateObject -> setTimezone($tz);
					}

					$startDate = $startDateObject -> toString('yyyy-MM-dd');
					$event_time = $this -> view -> locale() -> toTime($startDateObject);
					if (strcmp($startDate, $event_day) == 0)
					{
						// die();
						$href = $event -> getHref();
						$id = $event -> getIdentity();
						$startDateObject = new Zend_Date(strtotime($event -> starttime));

						$calendar .= '<a id="ynevent_myevent_' . $id . '" href="' . $event -> getHref() . '" class="ynevent">' . $event_time . "-" . $this -> view -> string() -> truncate($event -> title, 20) . '</a>';
						$divTooltip = $this -> view -> partial('_calendar_tooltip.tpl', array('event' => $event));
						$calendar .= $divTooltip;
						$calendar .= '<br>';
					}
				}
			}

			$calendar .= '</div></td>';
			if ($running_day == 6)
			:
				$calendar .= '</tr>';
				if (($day_counter + 1) != $days_in_month)
				:
					$calendar .= '<tr class="calendar-row">';
				endif;
				$running_day = -1;
				$days_in_this_week = 0;
			endif;
			$days_in_this_week++;
			$running_day++;
			$day_counter++;
		endfor;

		/* finish the rest of the days in the week */
		if ($days_in_this_week < 8 && $days_in_this_week > 1)
		:
			for ($x = 1; $x <= (8 - $days_in_this_week); $x++)
			:
				$calendar .= '<td class="calendar-day-np">&nbsp;</td>';
			endfor;
		endif;

		/* final row */
		$calendar .= '</tr>';

		/* end the table */
		$calendar .= '</table>';

		/** DEBUG * */
		$calendar = str_replace('</td>', '</td>' . "\n", $calendar);
		$calendar = str_replace('</tr>', '</tr>' . "\n", $calendar);

		/* all done, return result */
		return $calendar;
	}

}
?>
