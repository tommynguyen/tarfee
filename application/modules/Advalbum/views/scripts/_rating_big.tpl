<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<div class="advalbum_block">
    <?php for ($x = 1; $x <= $this->subject->rating; $x++): ?>
        <span class="advalbum_rating_star_generic rating_star_big"></span>
    <?php endfor; ?>
    <?php if ((round($this->subject->rating) - $this->subject->rating) > 0): ?>
        <span class="advalbum_rating_star_generic rating_star_big_half"></span>
    <?php endif; ?>
    <?php
    	$disabled_star = 5 - $this->subject->rating;
    	for ($s = 1; $s <= $disabled_star; $s++):
    ?>
          <span class="advalbum_rating_star_generic rating_star_big_disabled"></span>
    <?php endfor; ?>
</div>