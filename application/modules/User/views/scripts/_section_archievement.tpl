<?php
	$label = Engine_Api::_()->user()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->user;
    $params = $this->params;
    $manage = ($viewer->getIdentity() == $user->getIdentity()) ;
    $create = (isset($params['create'])) ? $params['create'] : false;
	$edit = (isset($params['edit'])) ? $params['edit'] : false; 
	$archievements = $user->getAllArchievements();
	$trophies = $user->getAllTrophies();
	
	$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
	$max = $permissionsTable->getAllowed('user', $user->level_id, 'archievement_max');
    if ($max == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
        ->where('level_id = ?', $user->level_id)
        ->where('type = ?', 'user')
        ->where('name = ?', 'archievement_max'));
        if ($row) {
            $max = $row->value;
        }
    }
    $max_character = $permissionsTable->getAllowed('user', $user->level_id, 'archievement_descriptionmax');
    if ($max_character == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
        ->where('level_id = ?', $user->level_id)
        ->where('type = ?', 'user')
        ->where('name = ?', 'archievement_descriptionmax'));
        if ($row) {
            $max_character = $row->value;
        }
    }
?>

<?php if (($manage || count($archievements) || count($trophies)) ) : ?>
<div class="icon_section_profile"><i class="fa fa-trophy"></i><i class="fa fa-flag-checkered"></i></div>
<table>
  <tr>
  	<th><hr></th>  
  	<th><h3 class="section-label"><?php echo $this->translate($label);?></h3></th>
  	<th><hr></th>
  </tr>
</table>

<div class="profile-section-button">
<?php if ($manage && ($max == 0 ||  $max > (count($archievements) + count($trophies)))) :?>
	<span class="manage-section-button">
		<a href="javascript:void(0)" rel="archievement" class="create-button"><?php echo '<i class="fa fa-plus-square"></i>'?></a>
	</span>	
<?php endif;?>	
</div>

<div class="profile-section-loading" style="display: none; text-align: center">
    <img src='application/modules/User/externals/images/loading.gif'/>
</div>

<div class="profile-section-content">
<?php if ($create || $edit) : ?>
    <div id="profile-section-form-archievement" class="profile-section-form">
        <form rel="archievement" class="section-form">
            <p class="error"></p>
            <?php $item = null;?>
            <?php if ($edit && isset($params['item_id'])) : ?>
            <?php $item = Engine_Api::_()->getItem('user_archievement', $params['item_id']);?>
            <input type="hidden" name="item_id" class="item_id" id="archievement-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
            <?php endif; ?>
            <div id="archievement-title-wrapper" class="profile-section-form-wrapper">
                <label for="archievement-title"><?php echo $this->translate('*Title')?></label>
                <div class="profile-section-form-input">
                    <input type="text" id="archievement-title" name="title" value="<?php if ($item) echo htmlentities($item->title);?>"/>
                    <p class="error"></p>
                </div>
            </div>
            <div id="archievement-year-wrapper" class="profile-section-form-wrapper">                
                <label for="archievement-year"><?php echo $this->translate('*Year')?></label>
                <div class="profile-section-form-input">
                    <input type="text" name="year" id="archievement-year" value="<?php if ($item) echo $item->year?>"/>
                    <p class="error"></p>
                </div>
            </div>
            <div id="archievement-type-wrapper" class="profile-section-form-wrapper">                
                <label for="archievement-type"><?php echo $this->translate('*Type')?></label>
                <div class="profile-section-form-input">
                    <select name="type" id="archievement-type">
                    	<option value="archievement" <?php if ($item && $item->type == 'archievement') echo 'selected';?>><?php echo $this->translate('Archievement')?></option>
                    	<option value="trophy" <?php if ($item && $item->type == 'trophy') echo 'selected';?>><?php echo $this->translate('Trophy')?></option>
                    </select>
                    <p class="error"></p>
                </div>
            </div>
            <div id="archievement-short_description-wrapper" class="profile-section-form-wrapper">
                <label for="archievement-short_description"><?php echo $this->translate('Short description')?></label>
                <div class="profile-section-form-input">
                    <textarea <?php if ($max_character) echo 'maxlength="'.$max_character.'"'?> id="archievement-short_description" name="short_description"/><?php if ($item) echo $item->short_description?></textarea>
                    <?php if ($max_character) :?>
                    <p class="form-description"><?php echo $this->translate('The maximum characters is %s', $max_character)?></p>
                    <?php endif;?>
                    <p class="error"></p>
                </div>
            </div>
            <div id="archievement-icon-wrapper" class="profile-section-form-wrapper">                
                <label><?php echo $this->translate('Add icon')?></label>
                <div class="profile-section-form-input">
                    <input class="section-fileupload" id="archievement-fileupload" type="file" accept="image/*">
                    <br />
                    <span class="upload-loading" style="display: none; text-align: center">
					    <img src='application/modules/User/externals/images/loading.gif'/>
					</span>  
                    <span class="upload-status form-description"></span>
                    <input type="hidden" id="photo_id" class="upload-photos" name="photo_id" value="<?php if ($item) echo $item->photo_id?>"/>
                	<p class="error"></p>
                </div>
            </div>
            <div class="profile-section-form-buttons">
                <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
                <button rel="archievement" type="button" class="cancel-btn"><?php echo $this->translate('Cancel')?></button>
                <?php if ($edit && isset($params['item_id'])) : ?>
                <?php echo $this->translate(' or ')?>
                <a href="javascript:void(0);" class="remove-btn"><?php echo $this->translate('Remove trophy/archievement')?></a>
                <?php endif; ?>                
            </div>          
        </form>
    </div>
<?php endif;?>
	<div class="profile-section-list">
		<?php if (count($trophies) > 0) : ?>
	    <div class="label-type"><?php echo $this->translate('Trophies')?></div>
	    <ul id="trophies-list" class="section-list">
	    <?php foreach ($trophies as $item) :?>
	    <li class="section-item" id="archievement-<?php echo $item->getIdentity()?>">
	    	<div class="sub-section-item">
		    	<div class="trophy-icon icon">
	            	<?php echo $this->itemPhoto($item, 'thumb.icon')?>
	            </div>
	            <div class="content">
		            <div class="trophy-title">
		            	<span class="section-title"><?php echo $item->title?></span>
		            </div>
		            <div class="trophy-year">
		            	<span><?php echo $item->year ?></span>
		            </div>
		            <?php if ($item->short_description) :?>
		        	<div class="trophy-short_description">
		            	<span class="section-description"><?php echo $item->short_description?></span>
		            </div>
		            <?php endif;?>
	            </div>
		        <?php if ($manage) : ?>
		        <a href="javascript:void(0);" class="edit-btn"><i class="fa fa-pencil"></i></a>
		        <?php endif; ?>
	        </div>
	    </li>
	    <?php endforeach;?>    
	    </ul>
	    <?php endif;?>
	    <?php if (count($archievements) > 0) : ?>
	    <div class="label-type"><?php echo $this->translate('Archievements')?></div>
	    <ul id="archievements-list" class="section-list">
	    <?php foreach ($archievements as $item) :?>
	    <li class="section-item" id="archievement-<?php echo $item->getIdentity()?>">
	    	<div class="sub-section-item">
		    	<div class="archievement-icon icon">
	            	<?php echo $this->itemPhoto($item, 'thumb.icon')?>
	            </div>
	            <div class="content">
		            <div class="archievement-title">
		            	<span class="section-title"><?php echo $item->title?></span>
		            </div>
		            <div class="archievement-year">
		            	<span><?php echo $item->year ?></span>
		            </div>
		            <?php if ($item->short_description) :?>
		        	<div class="archievement-short_description">
		            	<span class="section-description"><?php echo $item->short_description?></span>
		            </div>
		            <?php endif;?>
	            </div>
		        <?php if ($manage) : ?>
		        <a href="javascript:void(0);" class="edit-btn"><i class="fa fa-pencil"></i></a>
		        <?php endif; ?>
	        </div>
	    </li>
	    <?php endforeach;?>    
	    </ul>
	    <?php endif;?>
	    <?php if (!count($trophies) && !count($archievements)) :?>
	    <div class="tip">
			<span><?php echo $this->translate('You don\'t have any trophies/archievements!')?></span>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>