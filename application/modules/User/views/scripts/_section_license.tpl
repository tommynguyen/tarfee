<?php
	$label = Engine_Api::_()->user()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->user;
    $params = $this->params;
    $manage = ($viewer->getIdentity() == $user->getIdentity()) ;
    $create = (isset($params['create'])) ? $params['create'] : false;
	$edit = (isset($params['edit'])) ? $params['edit'] : false; 
	$licenses = $user->getAllLicenses();
	$certificates = $user->getAllCertificates();
	$enable = Engine_Api::_()->user()->checkSectionEnable($user, 'license');
?>

 <?php if (($manage || count($licenses) || count($certificates)) && $enable) : ?>
 	
<div class="icon_section_profile"><i class="fa fa-bookmark"></i></div>
<table>
  <tr>
  	<th><hr></th>  
  	<th><h3 class="section-label"><?php echo $this->translate($label);?></h3></th>
  	<th><hr></th>
  </tr>
</table>

<div class="profile-section-button">
<?php if ($manage) :?>
	<span class="manage-section-button">
		<a href="javascript:void(0)" rel="license" class="create-button"><?php echo '<i class="fa fa-plus-square"></i>';?></a>
	</span>	
<?php endif;?>	
</div>

<div class="profile-section-loading" style="display: none; text-align: center">
    <img src='application/modules/User/externals/images/loading.gif'/>
</div>

<div class="profile-section-content">
<?php if ($create || $edit) : ?>
    <div id="profile-section-form-license" class="profile-section-form">
        <form rel="license" class="section-form">
            <p class="error"></p>
            <?php $item = null;?>
            <?php if ($edit && isset($params['item_id'])) : ?>
            <?php $item = Engine_Api::_()->getItem('user_license', $params['item_id']);?>
            <input type="hidden" name="item_id" class="item_id" id="license-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
            <?php endif; ?>
            <div id="license-title-wrapper" class="profile-section-form-wrapper">
                <label for="license-title"><?php echo $this->translate('*Name')?></label>
                <div class="profile-section-form-input">
                    <input type="text" id="license-title" name="title" value="<?php if ($item) echo htmlentities($item->title);?>"/>
                    <p class="error"></p>
                </div>
            </div>
            <div id="license-type-wrapper" class="profile-section-form-wrapper">                
                <label for="license-type"><?php echo $this->translate('*Type')?></label>
                <div class="profile-section-form-input">
                    <select name="type" id="license-type">
                    	<option value="license"><?php echo $this->translate('License')?></option>
                    	<option value="certificate"><?php echo $this->translate('Certificate')?></option>
                    </select>
                    <p class="error"></p>
                </div>
            </div>
            <div id="license-number-wrapper" class="profile-section-form-wrapper">
                <label for="license-number"><?php echo $this->translate('Number')?></label>
                <div class="profile-section-form-input">
                    <input type="text" id="license-number" name="number" value="<?php if ($item) echo htmlentities($item->title);?>"/>
                    <p class="error"></p>
                </div>
            </div>
            <div id="license-time-wrapper" class="profile-section-form-wrapper">                
                <label><?php echo $this->translate('*Time')?></label>
                <div class="profile-section-form-input form-input-2item">
                    <div>
                        <select name="month" id="license-month" value="<?php if ($item) echo $item->month?>">
                            <?php $month = array('Choose...', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')?>
                            <?php foreach ($month as $key => $value) : ?>
                            <option value="<?php echo $key?>" <?php if ($item && $item->month == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="year" id="license-year" value="<?php if ($item) echo $item->year?>"/>
                    </div>
                    <p class="error"></p>
                </div>
            </div>
            <div id="license-icon-wrapper" class="profile-section-form-wrapper">                
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
                <button rel="license" type="button" class="cancel-btn"><?php echo $this->translate('Cancel')?></button>
                <?php if ($edit && isset($params['item_id'])) : ?>
                <?php echo $this->translate(' or ')?>
                <a href="javascript:void(0);" class="remove-btn"><?php echo $this->translate('Remove license/certificate')?></a>
                <?php endif; ?>                
            </div>          
        </form>
    </div>
<?php endif;?>
	<div class="profile-section-list">
		<?php if (count($licenses) > 0) : ?>
	    <div class="label-type"><?php echo $this->translate('Licenses')?></div>
	    <ul id="license-list" class="section-list">
	    <?php foreach ($licenses as $item) :?>
	    <li class="section-item" id="license-<?php echo $item->getIdentity()?>">
	    	<div class="sub-section-item">
		    	<div class="license-icon icon">
	            	<?php echo $this->itemPhoto($item, 'thumb.icon')?>
	            </div>
	            <div class="content">
		            <div class="license-title">
		            	<span class="section-title"><?php echo $item->title?></span>
		            </div>
		            <div class="license-number">
		            	<span><?php echo $item->number?></span>
		            </div>
		            <div class="license-time">
		            	<?php 
			                $month = ($item->month) ? $item->month : 1;
			                $time = date_create($item->year.'-'.$month.'-'.'1');
			                if ($item->month) {
			                    $time = date_format($time, 'M Y');
			                }
			                else {
			                    $time = date_format($time, 'Y');
			                }
			            ?>
		            	<span class="time"><?php echo $time?></span>
		            </div>
			        <?php if ($manage) : ?>
			        <a href="javascript:void(0);" class="edit-btn"><i class="fa fa-pencil"></i></a>
			        <?php endif; ?>
		        </div>
			</div>		      
	    </li>
	    <?php endforeach;?>    
	    </ul>
	    <?php endif;?>
	    <?php if (count($certificates) > 0) : ?>
	    <div class="label-type"><?php echo $this->translate('Certificates')?></div>
	    <ul id="certificate-list" class="section-list">
	    <?php foreach ($certificates as $item) :?>
	    <li class="section-item" id="license-<?php echo $item->getIdentity()?>">
	    <div class="sub-section-item">
	    	<div class="certificate-icon icon">
            	<?php echo $this->itemPhoto($item, 'thumb.icon')?>
            </div>
            <div class="content">
            <div class="certificate-title">
            	<span class="section-title"><?php echo $item->title?></span>
            </div>
            <div class="certificate-number">
            	<span><?php echo $item->number?></span>
            </div>
            <div class="certificate-time">
            	<?php 
	                $month = ($item->month) ? $item->month : 1;
	                $time = date_create($item->year.'-'.$month.'-'.'1');
	                if ($item->month) {
	                    $time = date_format($time, 'M Y');
	                }
	                else {
	                    $time = date_format($time, 'Y');
	                }
	            ?>
            	<span class="time"><?php echo $time?></span>
            </div>
	        <?php if ($manage) : ?>
	        <a href="javascript:void(0);" class="edit-btn"><i class="fa fa-pencil"></i></a>
	        <?php endif; ?>
	  	</div>
	    </li>
	    <?php endforeach;?>    
	    </ul>
	    <?php endif;?>
	    <?php if (!count($certificates) && !count($licenses)) :?>
	    <div class="tip">
			<span><?php echo $this->translate('You don\'t have any licenses/certificates!')?></span>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>