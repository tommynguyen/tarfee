<?php
	$this->headScript()->appendFile('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places');
?>
<?php echo $this->form->render($this) ?>
<script type="text/javascript">
function initialize() 
{
    var input = document.getElementById('location');
    var autocomplete = new google.maps.places.Autocomplete(input);
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>


