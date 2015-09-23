<?php

class Ynnotification_View_Helper_FormYnnotificationbrowser extends Zend_View_Helper_Abstract{
	
	public function formYnnotificationbrowser(){
		
		$html =  
		'
		<h3>Audio Formats and Browser Support</h3>
		<table class="ynnotification_table">
<tbody><tr>
<th width="25%" align="left">Browser</th>
<th width="25%" align="left">MP3</th>
<th width="25%" align="left">Wav</th>
</tr>
<tr>
<td>Internet Explorer 9+</td>
<td>YES</td>
<td>NO</td>
</tr>
<tr>
<td>Chrome 6+</td>
<td>YES</td>
<td>YES</td>
</tr>
<tr>
<td>Firefox 3.6+</td>
<td>NO</td>
<td>YES</td>
</tr>
<tr>
<td>Safari 5+</td>
<td>YES</td>
<td>YES</td>
</tr>
<tr>
<td>Opera 10+</td>
<td>NO</td>
<td>YES</td>
</tr>
</tbody></table>';
		
		
		
		return $html;
	}
}
