<ul class = "video-items" id="players-of-week">
<?php foreach( $this->videos as $row): ?>
	<li class="video-item">
		<?php
        		echo $this->partial('_players_of_week.tpl', 'ynvideo', array(
        			'video' => $row
        		));
            ?>
	</li>
<?php endforeach;?>
</ul>
<script type="text/javascript">
function addEyeOn(itemId) 
{
    $('user_eyeon_'+itemId).set('html', '<a class="actions_generic" href="javascript:void(0);"><span><i class="fa fa fa-spinner fa-pulse"></i></span></a>');
    new Request.JSON({
        'url': '<?php echo $this->url(array('action'=>'add-eye-on'),'user_playercard', true)?>',
        'method': 'post',
        'data' : {
            'id' : itemId
        },
        'onSuccess': function(responseJSON, responseText) {
            if (responseJSON.status == true) {
                html = '<a class="actions_generic eye-on eye_on" href="javascript:void(0);" onclick="removeEyeOn('+itemId+')"><span><i class="fa fa-eye-slash"></i></span></a>';
                $('user_eyeon_'+itemId).set('html', html);
            }
            else {
                alert(responseJSON.message);
            }            
        }
    }).send();
}

function removeEyeOn(itemId){
	$('user_eyeon_'+itemId).set('html', '<a class="actions_generic" href="javascript:void(0);"><span><i class="fa fa fa-spinner fa-pulse"></i></span></a>');
    new Request.JSON({
        'url': '<?php echo $this->url(array('action'=>'remove-eye-on'),'user_playercard', true)?>',
        'method': 'post',
        'data' : {
            'id' : itemId
        },
        'onSuccess': function(responseJSON, responseText) {
            if (responseJSON.status == true) {
                html = '<a class="actions_generic eye_on" href="javascript:void(0);" onclick="addEyeOn('+itemId+')"><span><i class="fa fa-eye"></i></span></a>';
                $('user_eyeon_'+itemId).set('html', html);
            }
            else {
                alert(responseJSON.message);
            }            
        }
    }).send();
}
</script>