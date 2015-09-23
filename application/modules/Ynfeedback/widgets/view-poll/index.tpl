<div class='polls_view'>
  <h3>
    <?php echo $this->poll->title ?>
  </h3>

  <div class="poll_desc">
    <?php echo $this->poll->description ?>
  </div>

  <?php
    // poll, pollOptions, canVote, canChangeVote, hasVoted, showPieChart
	 echo $this->partial('_poll.tpl', 'ynfeedback', array(
	 	'poll' => $this->poll ,
	    'owner' => $this->owner ,
	    'viewer' => $this->viewer ,
	    'pollOptions' => $this->pollOptions,
	    'hasVoted' => $this->hasVoted,
	    'showPieChart' => $this->showPieChart,
	    'canVote' => $this->canVote ,
	    'canChangeVote' => $this->canChangeVote,
	));
  ?>
</div>


<script type="text/javascript">
  $$('.core_main_poll').getParent().addClass('active');
</script>
