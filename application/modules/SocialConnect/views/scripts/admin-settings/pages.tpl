<h2><?php echo $this->translate("Footer Management") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
 <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Footer Pages") ?></h3>
        <?php if($this -> category):?>
        	<h4>>> <?php echo $this -> category -> category_name?></h4>
        <?php endif;?>
        <div class="add_link">
		<?php echo $this->htmlLink(
		array('route' => 'admin_default', 'module' => 'social-connect', 'controller' => 'settings', 'action' => 'add-page'),
		$this->translate('add Page'), 
		array(
		    'class' => 'buttonlink add_faq smoothbox',
		)) ?>
		</div>
		<?php if( count($this->paginator) ): ?>
		<form id='multidelete_form' class="yn_admin_form" method="post" action="<?php echo $this->url();?>">
		<table class='admin_table' style="position: relative; width: 100%">
		  <thead>
		    <tr>
		      <th><?php echo $this->translate("Title") ?></th>
		      <th style="width: 50%"><?php echo $this->translate("Content") ?></th>
		       <th><?php echo $this->translate("Category") ?></th>
		      <th><?php echo $this->translate("Options") ?></th>
		    </tr>
		  </thead>
		  <tbody id = "page-list">
		    <?php foreach ($this->paginator as $item): ?>
		      <tr id='page_item_<?php echo $item->getIdentity() ?>'>
		        <td><?php echo $this->translate($item->title) ?></td>
		        <td><?php echo strip_tags(($item->content))?></td>
		        <td><?php $row = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> findRow($item -> category_id);
				if($row)
		        	  echo $this->translate($row->category_name); ?></td>
		        <td>
		              <?php echo $this->htmlLink(
		                    array('route' => 'admin_default', 'module' => 'social-connect', 'controller' => 'settings', 'action' => 'edit-page', 'id' => $item->page_id),
		                    $this->translate('edit'),
		                     array('class' => 'smoothbox')
		              )?>
		              |
		              <?php echo $this->htmlLink(
		                    array('route' => 'admin_default', 'module' => 'social-connect', 'controller' => 'settings', 'action' => 'delete-page', 'id' => $item->page_id),
		                    $this->translate("delete"),
		                    array('class' => 'smoothbox')
		              )?> 
		        </td>
		      </tr>
		    <?php endforeach; ?>
		  </tbody>
		</table>
		</form>
		<div>
		  <?php echo $this->paginationControl($this->paginator); ?>
		</div>
		<?php else: ?>
		  <div class="tip">
		    <span>
		      <?php echo $this->translate("There are no pages.") ?>
		    </span>
		  </div>
		<?php endif; ?>
    </div>
  </div>
<script type="text/javascript">
en4.core.runonce.add(function(){
    new Sortables('page-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'settings','action'=>'sort-page'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString(),
          }
        }).send();
      }
    });
    
});
</script>