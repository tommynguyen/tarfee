<div class="tftalk_searchbox">
    <form id="filter_form" action="" method="post">
        <div class="tftalk_input">
  	         <input type="text" name="keyword" value="<?php echo $this -> keyword?>" placeholder="<?php echo $this -> translate("Searh Events & Tryouts")?>"/>
        </div>
      	<a href="<?php echo $this -> url(array('action' => 'create'), 'event_general', true)?>" class="tfadd_talk"><?php echo $this -> translate("Add Events & Tryouts")?><i class="fa fa-plus"></i></a>
        
        <div class="tffilter_box">
          	<div class="ybo_headline"><h3><?php echo $this -> translate("Filters")?></h3></div>
            
            <div class="tffilter_categorires">
      	         <?php $cat_arrays = Engine_Api::_()->getItemTable('event_category')->getCategoriesAssoc();
                    unset($cat_arrays[0]);?>
              	<h5 class="tffilter_title"><?php echo $this -> translate("Categories")?></h5>
              	<?php foreach($cat_arrays as $key => $value):?>
                    <div>
                  		<label><?php echo $value?></label>
                  		<input type="checkbox" name="categories[]" value="<?php echo $key?>" <?php if(in_array($key, $this -> categories)):?>  checked="checked" <?php endif;?>/>
                    </div>
            	<?php endforeach; ?>
            </div>

            <div class="tffilter_author">
            	<h5 class="tffilter_title"><?php echo $this -> translate("By author")?></h5>
                <div>
                	<label><?php echo $this -> translate("Professional")?></label>
                	<input type="checkbox" name="by_authors[]" value="professional" <?php if(in_array('professional', $this -> by_authors)):?>  checked="checked" <?php endif;?>/>
                </div>

            	<?php if($this -> viewer() -> getIdentity()):?>
                <div>
            		<label><?php echo $this -> translate("My networks")?></label>
            		<input type="checkbox" name="by_authors[]" value="networks" <?php if(in_array('networks', $this -> by_authors)):?>  checked="checked" <?php endif;?>/>
                </div>
            	<?php endif;?>
                <div>
                	<label><?php echo $this -> translate("All")?></label>
                	<input type="checkbox" name="by_authors[]" value="all" <?php if(in_array('all', $this -> by_authors) || !$this -> by_authors):?>  checked="checked" <?php endif;?>/>
                </div>

                <div>
                	<input type="hidden" name="page" id="page" value="<?php echo $this -> page?>" />
                	<button name="submit" id="submit" type="submit"><?php echo $this -> translate("Search")?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
 var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>
