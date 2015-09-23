<?php
	$label = Engine_Api::_()->user()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->user;
    $params = $this->params;
    $manage = ($viewer->getIdentity() == $user->getIdentity()) ;
    $create = (isset($params['create'])) ? $params['create'] : false;
	$edit = (isset($params['edit'])) ? $params['edit'] : false; 
	$experience = $user->getAllExperiences();
	$enable = Engine_Api::_()->user()->checkSectionEnable($user, 'experience');
?>

<?php if (($manage || count($experience)) && $enable) : ?>

<div class="icon_section_profile"><i class="fa fa-suitcase"></i></div>
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
		<a href="javascript:void(0)" rel="experience" class="create-button"><?php echo '<i class="fa fa-plus-square"></i>'?></a>
	</span>	
<?php endif;?>	
</div>
<div class="profile-section-loading" style="display: none; text-align: center">
    <img src='application/modules/User/externals/images/loading.gif'/>
</div>

<div class="profile-section-content">
<?php if ($create || $edit) : ?>
    <div id="profile-section-form-experience" class="profile-section-form">
        <form rel="experience" class="section-form">
            <p class="error"></p>
            <?php $item = null;?>
            <?php if ($edit && isset($params['item_id'])) : ?>
            <?php $item = Engine_Api::_()->getItem('user_experience', $params['item_id']);?>
            <input type="hidden" name="item_id" class="item_id" id="experience-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
            <?php endif; ?>
            <div id="experience-title-wrapper" class="profile-section-form-wrapper">
                <label for="experience-title"><?php echo $this->translate('*Position')?></label>
                <div class="profile-section-form-input">
                    <input type="text" id="experience-title" name="title" value="<?php if ($item) echo htmlentities($item->title);?>"/>
                    <p class="error"></p>
                </div>
            </div>
            <div id="experience-company-wrapper" class="profile-section-form-wrapper">                
                <label for="experience-company"><?php echo $this->translate('*Company Name')?></label>
                <div class="profile-section-form-input">
                    <input type="text" id="experience-company" autocomplete="off" name="company" value="<?php if ($item) echo htmlentities($item->company);?>"/>
                    <p class="error"></p>
                </div>
            </div>
            <div id="experience-time_period-wrapper" class="profile-section-form-wrapper">                
                <label><?php echo $this->translate('*Time Period')?></label>
                <div class="profile-section-form-input form-input-4item">
                    <div>
                        <select name="start_month" id="experience-start_month" value="<?php if ($item) echo $item->start_month?>">
                            <?php $month = array('Choose...', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')?>
                            <?php foreach ($month as $key => $value) : ?>
                            <option value="<?php echo $key?>" <?php if ($item && $item->start_month == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="start_year" id="experience-start_year" value="<?php if ($item) echo $item->start_year?>"/>
                         - 
                        <select name="end_month" id="experience-end_month">
                            <?php foreach ($month as $key => $value) : ?>
                            <option value="<?php echo $key?>" <?php if ($item && $item->end_month == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="end_year" id="experience-end_year" value="<?php if ($item) echo $item->end_year?>"/>
                        <label id="experience-present"><?php echo $this->translate('Present')?></label>
                    </div>
                    <div class="profile-section-form-input-checkbox">
                        <input type="checkbox" id="experience-current" name="current" value="1" <?php if ($item && !$item->end_year) echo 'checked'?>/>
                        <label for="experience-current"><?php echo $this->translate('I currently work here')?></label>
                    </div>
                    <p class="error"></p>
                </div>
            </div>
            <div id="experience-description-wrapper" class="profile-section-form-wrapper">
                <label for="experience-description"><?php echo $this->translate('Description')?></label>
                <div class="profile-section-form-input">
                    <textarea id="experience-description" name="description"/><?php if ($item) echo $item->description?></textarea>
                    <p class="error"></p>
                </div>
            </div>
            <div class="profile-section-form-buttons">
                <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
                <button rel="experience" type="button" class="cancel-btn"><?php echo $this->translate('Cancel')?></button>
                <?php if ($edit && isset($params['item_id'])) : ?>
                <?php echo $this->translate(' or ')?>
                <a href="javascript:void(0);" class="remove-btn"><?php echo $this->translate('Remove Experience')?></a>
                <?php endif; ?>                
            </div>          
        </form>
    </div>
    <script type="text/javascript">
        //add event for form
        window.addEvent('domready', function() {
            var current = $('experience-current');
            if (current) {
                if (current.checked) {
                    $('experience-end_month').hide();
                    $('experience-end_year').hide();
                }
                else {
                    $('experience-present').hide();
                }
                
                current.addEvent('change', function() {
                    if (current.checked) {
                        $('experience-end_month').hide();
                        $('experience-end_year').hide();
                        $('experience-present').show();
                    }
                    else {
                        $('experience-present').hide();
                        $('experience-end_month').show();
                        $('experience-end_year').show();
                    }
                });
            }
        });
  	</script>
<?php endif;?>
	<div class="profile-section-list">
		<?php if (count($experience) > 0) : ?>
	    <ul id="experience-list" class="section-list">
	    <?php foreach ($experience as $item) :?>
	    <li class="section-item" id="experience-<?php echo $item->getIdentity()?>">
	    	<div class="sub-section-item">
	            <?php 
	                $start_month = ($item->start_month) ? $item->start_month : 1;
	                $start_date = date_create($item->start_year.'-'.$start_month.'-'.'1');
	                if ($item->start_month) {
	                    $start_time = date_format($start_date, 'M Y');
	                }
	                else {
	                    $start_time = date_format($start_date, 'Y');
	                }
	                if ($item->end_year) {
	                    $end_month = ($item->end_month) ? $item->end_month : 1;
	                    $end_date = date_create($item->end_year.'-'.$end_month.'-'.'1');
	                    if ($item->end_month) {
	                        $end_time = date_format($end_date, 'M Y');
	                    }
	                    else {
	                        $end_time = date_format($end_date, 'Y');
	                    }
	                }
	                else {
	                    $end_date = date_create();
	                    $end_time = $this->translate('Present');
	                }
	                $diff = date_diff($start_date, $end_date);
	
	            ?>
	
	            <div class="experience-position section-title">
	                <?php echo $item->title?>
	            </div>
	
	            <div class="experience-company section-head-title">
	                <span class="company-name"><i class="fa fa-building"></i> <?php echo $item->company;?></span>
	            </div>
				
				<div class="experience-time">                
	                <span class="start-time time"><?php echo $start_time?></span>
	                <span class="time">-</span>
	                <span class="end-time time"><?php echo $end_time?></span>
	                <?php $period = $diff->format('%y')*12 + $diff->format('%m') + 1; ?>
	
	                <?php if ($period > 12) : ?>
	                    <?php $years = intval($period / 12);  $months = $period % 12;?>
	                    <span class="period time">
	                        (<?php
	                        echo ($years > 0) ? $this->translate(array('%s year','%s years',$years),$years) . ' '  : '';
	                        echo ($months > 0) ? $this->translate(array('month_diff','%s months',$months),$months). ' ' : '';
	                        ?>)
	                    </span>
	                <?php else: ?>
	                    <span class="period time">(<?php echo $this->translate(array('month_diff','%s months',$period),$period)?>)</span>
	                <?php endif; ?>
	            </div>
	            
		        <?php if ($item->description) : ?>
	            <div class="section-description experience-description"><?php echo $item->description?></div>
		        <?php endif;?>
		
		        <?php if ($manage) : ?>
		        <a href="javascript:void(0);" class="edit-btn"><i class="fa fa-pencil"></i></a>
		        <?php endif; ?>
	        </div>
	    </li>
	    <?php endforeach;?>    
	    </ul>
	    <?php else: ?>
	    <div class="tip">
			<span><?php echo $this->translate('You don\'t have any experience!')?></span>
		</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
