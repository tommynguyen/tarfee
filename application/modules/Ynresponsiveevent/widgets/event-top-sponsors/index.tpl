<?php
    $this -> headScript() -> appendFile($staticUrl . 'application/modules/Ynresponsive1/externals/scripts/jquery.jcarousel.min.js');
    $this -> headScript() -> appendFile($staticUrl . 'application/modules/Ynresponsive1/externals/scripts/jcarousel.sponsor.js'); 
?>

<div class="yntheme-event-container jcarousel-wrapper">
    <div class="jcarousel">    
        <ul>
        <?php
        if ($this -> sponsors -> getTotalItemCount() > 0): 
	        foreach($this -> sponsors as $item): ?>
		        <li class="event-sponsor-item">
		            <?php $event = Engine_Api::_() -> getItem('event', $item -> event_id);
		        		if($event):?>
		                <a href="<?php echo $event -> getHref();?>">
		        		  <img src="<?php echo $item -> getPhotoUrl('thumb.normal');?>" />
		        		</a>
		       	    <?php endif; ?>
		        </li>
	        <?php endforeach;
	     else:?>
            <li class="event-sponsor-item" style="width: 285px;">
             	<a href="#">
    		  		<img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_sponsor/e4/06/06de_84e6.jpg?c=eb88">
    			</a>
   	        </li>
   	        <li class="event-sponsor-item" style="width: 285px;">
            	<a href="#">
    		  		<img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_sponsor/fc/06/06f6_082d.png?c=3c34">
    			</a>
   	        </li>
   	        <li class="event-sponsor-item" style="width: 285px;">
              	<a href="#">
    		  		<img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_sponsor/dc/06/06d6_4d89.jpg?c=ef80">
    			</a>
   	        </li>
   	        <li class="event-sponsor-item" style="width: 285px;">
                <a href="#">
    		  		<img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_sponsor/de/06/06d8_f850.jpg?c=dc0e">
    			</a>
   	        </li>
   	        <li class="event-sponsor-item" style="width: 285px;">
                <a href="#">
    		  		<img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_sponsor/e0/06/06da_9c27.png?c=cef3">
    			</a>
   	        </li>
   	        <li class="event-sponsor-item" style="width: 285px;">
               	<a href="#">
    		  		<img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_sponsor/e2/06/06dc_b456.png?c=77fd">
    			</a>
   	        </li>
	     <?php endif;?>
        </ul>
    </div>
    <?php if ($this -> sponsors ->getTotalItemCount() > 1 || $this -> sponsors ->getTotalItemCount() == 0) : ?>
    <a href="#" class="yntheme-event-control-prev jcarousel-control-prev">&lsaquo;</a>
    <a href="#" class="yntheme-event-control-next jcarousel-control-next">&rsaquo;</a>
    <?php endif; ?>
</div>

<style>
.jcarousel-wrapper {
    position: relative;
}

/** Carousel **/
.jcarousel {
    position: relative;
    overflow: hidden;
    width: 100%;
}

.jcarousel ul {
    width: 20000em;
    position: relative;
    list-style: none;
    margin: 0;
    padding: 0;
}

.jcarousel li {
    width: 200px;
    float: left;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}

.jcarousel img {
    max-width: 100%;
    height: auto !important;
}
</style>