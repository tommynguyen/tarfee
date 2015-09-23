<h2><?php echo $this->translate("Contact Importer Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>

<div class='admin_results'>
  <div>
    <?php $providerCount = count($this->paginator) ?>
    <?php echo $this->translate(array("%s providers found", "%s providers found", $providerCount), ($providerCount)) ?>
  </div>
</div>

<br />
<form  action="<?php echo $this->url(array('module' => 'contactimporter', 'controller' => 'admin-manage', 'action' => 'edit'),'default',true)?>" method="post" id="multimodify_form">
  <table class="admin_table">
    <thead>
      <tr>
        <th><?php echo $this->translate('Service');?></th>       
        <th style="width: 1%;"><?php echo $this->translate('Logo');?></th>
        <th class="admin_table_centered" style="width: 1%;"><?php echo $this->translate('Enabled/Disabled');?></th>
        <th class="admin_table_centered" style="width: 1%;"><?php echo $this->translate('Order'); ?></th>       
        <th class="admin_table_options" style="width: 1%;"><?php echo $this->translate('Options');?></th>
      </tr>
    </thead>
    <tbody>
    <?php if( count($this->paginator) ): ?>     
        <?php foreach( $this->paginator as $item ): ?>
       <tr>
            <td><?php echo $item->title?></td>
            <td class="admin_table_bold"><img width="60px" src="<?php echo $this->baseUrl().'/application/modules/Contactimporter/externals/images/'.$item->logo.'.png'?>"></td>
            <td class="admin_table_centered">
              
                <?php echo ($item->enable?"enabled":"disabled")?>  
            </td>
            <td class="admin_table_centered"><?php echo $item->order?></td>
            <td class="admin_table_options">
              <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'contactimporter', 'controller' => 'admin-manage', 'action' => 'edit', 'name' => $item->name),
                  'edit',
                  array('class' => 'smoothbox')) ?>
                              
            </td>
          </tr>
          <?php endforeach; ?>
       <?php endif;?>
       </tbody>
  </table>
  <br>
</form>