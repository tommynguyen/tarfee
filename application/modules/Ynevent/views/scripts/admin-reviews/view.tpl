
<script type="text/javascript">
	
	function dismissmultiDelete()
    {
        return confirm("<?php echo $this->translate('Are you sure you want to delete the selected reviews of events or dissmiss all reports on all reviews ?'); ?>");
    }
</script>
 
<div class="ynevent_report">
<?php if (count($this->reports)): ?>

	<div class="ynevent_report_info">
		<div class="ynevent_report_info_title label">
			<div class="ynevent_report_info_label"><?php echo $this->translate("Event name:");?></div>
			<div class="ynevent_report_info_value"><td><?php echo $this->htmlLink( $this->event->getHref(),$this->string()->truncate($this->event->getTitle(),50)) ?></td></div>
			
		</div>
		<div class="ynevent_report_review label">
			<?php echo $this->translate("From: %s - To: %s", $this->locale()->toDateTime($this->event->starttime), $this->locale()->toDateTime($this->event->endtime))?>
		</div>
		<div class="ynevent_report_review label">
			<div class="ynevent_report_info_label"><?php echo $this->translate("Review");?></div>
				<div class="ynevent_report_rating">
				<?php $tempVal = $this->event->rating; ?>
				<?php for ($x = 1; $x <= 5; $x++ ): ?>
					<?php if ($x <= $tempVal):?>
						<span class="rating_star_big_generic rating_star_big"></span>
					<?php endif;?>
					<?php if ( ($x+1 > $tempVal) && ($x < $tempVal) ) :?>
						<span class="rating_star_big_generic rating_star_big_half"></span>
					<?php endif;?>
					<?php if ($x > $tempVal) :?>
						<span class="rating_star_big_generic rating_star_big_disabled"></span>
					<?php endif;?>
				<?php endfor;?>
			</div>
		</div>
		<div class="ynevent_report_review label">
			<?php echo $this->translate("By %s", $this->htmlLink( $this->event->getOwner()->getHref(),$this->string()->truncate($this->event->getOwner()->getTitle(),50)))?>
		</div>
		<div class = "ynevent_report_description label"><?php echo $this->string()->truncate($this->event->getDescription(),300) ?></div>
	</div>
	
    <form id='dismissmultidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return dismissmultiDelete()">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>
						<th>
				            <?php echo $this->translate("No.") ?>
                        </th>                        
                        <th>
				            <?php echo $this->translate("Report type") ?>
                        </th>   
                         <th>
				            <?php echo $this->translate("Description") ?>
                        </th>                    
                        <th>
							<?php echo $this->translate("By") ?>
                        </th>
                        <th>
                            <?php echo $this->translate("Report Time") ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->reports as $item): ?>
                        <tr>
                            <td><?php echo $item->getIdentity() ?></td>
                            <td><?php echo $item->type ?></td>
                            <td><?php echo $this->string()->truncate($item->content,300) ?></td>
                            <td><?php echo $this->htmlLink( $item->getOwner()->getHref(),$this->string()->truncate($item->getOwner()->getTitle(),20)) ?></td>
                            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />
        
        <div id="buttons-element" class="form-element">
        	 <button type='submit'><?php echo $this->translate("Delete Review") ?></button>
        	 <button name="dismiss" type='submit'><?php echo $this->translate("Dismiss Report") ?></button>
        	 <div style="padding-top: 7px;">
        	 	or
        		<a href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("cancel")?></a>
        	</div>
        </div>
                
    </form>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no reports posted by your members yet.") ?>
        </span>
    </div>
    <button onclick="javascript:parent.Smoothbox.close()" type="button"><?php echo $this->translate("cancel")?></button>
<?php endif; ?>
</div>