<div class='generic_layout_container layout_event_browse_search'>
        <?php echo $this->formFilter->setAttrib('class', 'filters')->render($this) ?>
</div>

<div class="generic_layout_container quicklinks">
      <ul class="navigation">
        <li>
          <?php echo $this->htmlLink(array('route' => 'event_general', 'action' => 'create'), $this->translate('Create New Event'), array(
            'class' => 'buttonlink icon_event_create'
          )) ?>
        </li>
      </ul>
</div>