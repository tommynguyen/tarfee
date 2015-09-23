<script type="text/javascript">
    function ynevent_like(ele)     
    {   
        var like ="<img class='ynevent_thumpup' src='application/modules/Ynevent/externals/images/thumb-up-icon.png'>";
        var unlike ="<img class='ynevent_thumpdown' src='application/modules/Ynevent/externals/images/thumb-down-icon.png'>"
        if (ele.className=="ynevent_like") {
            var request_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'like', 'subject' => $this->subject()->getGuid()), 'default', true); ?>';
        } else {
            var request_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'unlike', 'subject' => $this->subject()->getGuid()), 'default', true); ?>';
        }
        new Request.JSON({
            url:request_url ,
            method: 'post',
            data : {
                format: 'json',
                'type':'event',
                'id': <?php echo $this->subject()->event_id ?>
                        
            },
            onComplete: function(responseJSON, responseText) {
                if (responseJSON.error) {
                    en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                } else {
                    if (ele.className=="ynevent_like") {
                        ele.setAttribute("class", "ynevent_unlike")|| ele.setAttribute("className", "ynevent_unlike");
                        ele.title= '<?php echo $this->translate("Liked") ?>';
                        ele.innerHTML = '<?php echo $this->translate("Liked")?>';                    
                    } else {    
                        ele.setAttribute("class", "ynevent_like")|| ele.setAttribute("className", "ynevent_like"); 
                        ele.title= '<?php echo $this->translate("Like") ?>';                        
                        ele.innerHTML = '<?php echo $this->translate("Like") ?>';
                    }                   
                }
            }
        }).send();
    }
</script>

<div id='profile_status'>
	<h2>
		<span> <?php echo $this->subject->getTitle() ?>
		</span>

		<?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
			<?php if ($this->subject()->likes()->isLike($this->viewer())) : ?>
				<a title="<?php echo $this->translate("Unlike")?>"
				id="ynevent_unlike" href="javascript:void(0);"
				onClick="ynevent_like(this);" class="ynevent_unlike"> 
					<?php echo $this->translate('Liked')?>
					<!--<img class="ynevent_thumpdown"
						src="application/modules/Ynevent/externals/images/thumb-down-icon.png">-->
				</a>			
		<?php else : ?>
			<a title="<?php echo $this->translate("Like") ?>" id="ynevent_like"
				href="javascript:void(0);" onClick="ynevent_like(this);"
				class="ynevent_like"> 
				<?php echo $this->translate('Like')?>
				<!--<img class="ynevent_thumpup"
					src="application/modules/Ynevent/externals/images/thumb-up-icon.png" />-->
			</a>
            <?php endif;?>
		<?php endif; ?>
	</h2>
</div>