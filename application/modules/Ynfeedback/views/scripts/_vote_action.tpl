<div class="ynfeedback-vote-action">
	<?php if ($this->viewer()->getIdentity()):?>		
		<div class="ynfeedback-vote-count">
			<span class="ynfeedback-vote-count-number"><?php echo $this->feedback->votes()->getVoteCount();?></span>
			<span class="ynfeedback-vote-count-text"><?php echo $this->translate(array("yf_vote", "Votes", $this->feedback->votes()->getVoteCount()), $this->feedback->votes()->getVoteCount()) ;?></span>
		</div>

		<?php if ($this->feedback->votes()->isVoted()):?>
			<a class="unvote" href="javascript:void(0)" title="<?php echo $this->translate("Unvote");?>" onclick="ynfeedback.unvote(<?php echo $this->feedback->getIdentity();?>, <?php echo $this->widget_id?>)">
				<i class="fa fa-check"></i> <?php echo $this->translate("Voted");?>
			</a>
		<?php else:?>
			<a class="vote" href="javascript:void(0)" title="<?php echo $this->translate("Vote");?>" onclick="ynfeedback.vote(<?php echo $this->feedback->getIdentity();?>, <?php echo $this->widget_id?>)">
				<i class="fa fa-thumbs-o-up"></i> <?php echo $this->translate("Vote");?>
			</a>
		<?php endif;?>

	<?php else:?>
		<div class="ynfeedback-vote-count">
			<span class="ynfeedback-vote-count-number"><?php echo $this->feedback->votes()->getVoteCount();?></span>
			<span class="ynfeedback-vote-count-text"><?php echo $this->translate(array("yf_vote", "Votes", $this->feedback->votes()->getVoteCount()), $this->feedback->votes()->getVoteCount()) ;?></span>
		</div>

		<a class="guest_vote">
			<i class="fa fa-check"></i> <?php echo $this->translate("Vote");?>
		</a>		
	<?php endif;?>
</div>