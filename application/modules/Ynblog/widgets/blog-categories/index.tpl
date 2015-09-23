<?php if(count($this->categories)>0):?>
<ul class = "global_form_box"  style="margin-bottom: 15px;">
    <?php $cats = $this->categories;
          $params = $this->params;
          foreach($cats as $cat): ?>
              <li class="ynblog_categories_blogs">
                  <a class="rss_link" href="<?php echo $this->url(array('action' => 'rss','category' => $cat->category_id), 'blog_general') ?>"><img src="./application/modules/Ynblog/externals/images/rss.png" alt="RSS" title="RSS"></a>

                  <?php if($params['mode'] == '1'){
                      echo $this->htmlLink(
                      $this->url(array('user_id'=>$params['user_id'],'category'=>$cat->category_id,'sort'=>'recent'),'blog_view',true),
                      Engine_Api::_()->ynblog()->subPhrase($this->translate($cat->category_name),30),
                      array('class'=>''));
                  }
                  else {
                      echo $this->htmlLink(
                           $this->url(array('category'=>$cat->category_id,'action'=>'listing','sort'=>'recent'),'blog_general',true),
                           Engine_Api::_()->ynblog()->subPhrase($this->translate($cat->category_name),30),
                           array('class'=>''));
                  }
                      ?>
              </li>
          <?php endforeach;?>
 </ul>
<?php endif;?>