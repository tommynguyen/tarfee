<div id="ynevent-shareinfo">
    <input type='hidden' value='<?php echo $this->event_id?>' id='event_id' />
    <input type='hidden' value='<?php echo $this->token?>' id='token' />
</div>
<!--
<div class="ynevent-addthis ynevent-share"><span><?php echo $this->translate("Shares"); ?></span><span class="ynevent-question">?</span><span class="ynevent-value" id="share_value"><?php echo $this->shares;?></span></div>
<div class="ynevent-addthis ynevent-click"><span><?php echo $this->translate("Clicks"); ?></span><span class="ynevent-question">?</span><span class="ynevent-value"><?php echo $this->clicks;?></span></div>
<div class="ynevent-addthis viral"><span><?php echo $this->translate("Viral Liftt"); ?></span><span class="ynevent-question">?</span><span class="ynevent-value"><?php echo $this->viralLift;?>%</span></div>
-->
<!-- AddThis Button BEGIN -->
<?php $server = $_SERVER["SERVER_NAME"];?>
<div class="addthis_toolbox addthis_default_style " onclick="share();" {literal}
     addthis:url="<?php echo 'http://'.$server.$this->event->getHref().'?user='.$this->user_id;?>"
     addthis:title="Share this page now"
     addthis:description="Share this page now"{/literal}>
    <a class="addthis_button_facebook"></a>
    <a class="addthis_button_twitter"></a>
    <a class="addthis_button_preferred_3"></a>
    <a class="addthis_button_compact"></a>
    <a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">
    var addthis_config = {
        "data_track_addressbar":false
    };

</script>
<script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $this->pubid?>"></script>
<script type="text/javascript">
    function share() {
        var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'event/event/share',
            'data' : {
                'event_id' : <?php echo $this->event->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
            	$('share_value').innerHTML = responseObject.share;
            }
        });
        request.send();  
    }
</script>
<!-- AddThis Button END -->