<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<?php for ($x = 1; $x <= $this->video->rating; $x++): ?>
    <span class="rating_star_big_generic rating_star"></span>
<?php endfor; ?>
<?php if ((round($this->video->rating) - $this->video->rating) > 0): ?>
    <span class="rating_star_generic rating_star_half"></span>
<?php endif; ?>