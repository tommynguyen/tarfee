<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<ul class="generic_list_widget ynvideo_widget">
    <?php foreach ($this->videoSignatures as $signature): ?>
       <?php
            $user = Engine_Api::_()->user()->getUser($signature->user_id);
        ?>
        <?php if ($user->getIdentity()) : ?>
            <li>
                <div class="photo">
                    <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'thumb')) ?>
                </div>
                <div class="info">
                    <div class="ynvideo_title">
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                    </div>
                    <div class="ynvideo_video_count">
                        <?php echo $this->translate(array('%1$s video', '%1$s videos', $signature->video_count),
                                $this->locale()->toNumber($signature->video_count))?>
                    </div>
                </div>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>