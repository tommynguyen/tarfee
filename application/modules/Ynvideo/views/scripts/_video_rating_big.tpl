<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
if($this -> video -> parent_type == 'user_playercard'):
?>
<div class="tf_video_rating" title="<?php echo number_format($this->video->getRating(), 2);?>">
    <?php for ($x = 1; $x <= $this->video->getRating(); $x++): ?>
        <span class="rating_star_generic"><i class="fa fa-star"></i></span>
    <?php endfor; ?>
    <?php if ((round($this->video->getRating()) - $this->video->getRating()) > 0): $x ++; ?>
        <span class="rating_star_generic"><i class="fa fa-star-half-o"></i></span>
    <?php endif; ?>
    <?php if ($x <= 5) :?>
        <?php for (; $x <= 5; $x++ ) : ?>
            <span class="rating_star_generic"><i class="fa fa-star-o"></i></span>   
        <?php endfor; ?>
    <?php endif; ?>
    
</div>
<?php endif;?>