<?php if( $this->canUpload ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'group_extended',
        'controller' => 'album',
        'action' => 'create',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Create Album'), array(
        'class' => 'buttonlink icon_group_photo_new'
    )) ?>
  <?php endif; ?>

<ul class = "global_form_box" style="background: none; margin-bottom: 10px;  padding :15px 15px 0px 20px;">
  <?php if( $this->paginator->getTotalItemCount() > 0 ):
          $group = $this->group?>
  <ul class="thumbs">
    <?php foreach( $this->paginator as $album ): ?>
     <li style="height:auto;margin-bottom: 10px;">
        <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>"  style="padding:1px;">
          <?php $photo = $album->getFirstCollectible();
                if($photo):?>
            <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal');?>)"></span>
          <?php else:?>
            <span style="background-image: url(./application/modules/Advgroup/externals/images/nophoto_group_thumb_normal.png)"></span>
          <?php endif;?>
        </a>
        <p class="thumbs_info">
          <?php $title = Engine_Api::_()->advgroup()->subPhrase($album->getTitle(),70);
                if($title == '') $title = "Untitle Album";
                echo $this->htmlLink($album->getHref(),"<b>".$title."</b>");?>
          <br/>
          <?php echo $this->translate('By');?>
          <?php if($album->user_id != 0 ){
              $name = Engine_Api::_()->advgroup()->subPhrase($album->getMemberOwner()->getTitle(),18);
              echo $this->htmlLink($album->getMemberOwner()->getHref(), $name, array('class' => 'thumbs_author'));
            }
             else{
              $name = Engine_Api::_()->advgroup()->subPhrase($group->getOwner()->getTitle(),18);
              echo $this->htmlLink($group->getOwner()->getHref(), $name, array('class' => 'thumbs_author'));
             }
          ?>
          <br />
          <?php echo $this->timestamp($album->creation_date) ?>
        </p>
      </li>
   <?php endforeach;?>
  </ul>
  <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No albums have been uploaded to this group yet.');?>
    </span>
  </div>
  <style type="text/css">
	.layout_advgroup_profile_albums ul.global_form_box {
		padding: 15px 0 0!important;
	}
  </style>
  <?php endif; ?>
</ul>  