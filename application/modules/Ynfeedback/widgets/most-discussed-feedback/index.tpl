<ul class="ynfeedback-list-most-item">
<?php foreach ($this->paginator as $idea):?>
    <?php echo $this->partial('_feedback_item.tpl', 'ynfeedback', array(
        'idea' => $idea,
        'filter' => 'discuss'
    )); ?>
<?php endforeach;?>
</ul>