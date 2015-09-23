

<div class="headline">
    <h2>
        <?php echo $this->translate('Ban Members');?>
    </h2>
    <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
        <?php
		
        // Render the menu
        echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
        ?>
    </div>
    <?php endif;?>
</div>

<?php if($this->noview):?>
	<div class="tip">
      <span>
           <?php echo $this->translate('There are no user yet.');?>
      </span>
           <div style="clear: both;"></div>
       </div>	
<?php else: ?>
	


<?php if(count($this->paginator)>0):?>
	<div class='ynbanmem_manage_users_results'>
	  <div>
	    <?php $count = $this->paginator->getTotalItemCount() ?>
	    <?php echo $count.$this->translate(' ips found of '). $this->user ?>
	  </div>
	  <div>
	    <?php echo $this->paginationControl($this->paginator, null, null, array(
	      'pageAsQuery' => true,
	      'query' => $this->$form,
	      //'params' => $this->formValues,
	    )); ?>
	  </div>
	</div>
	
	<br />
	
	<div class="ynbanmem_table_form">
	<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
	  <table class='ynbanmem_table'>
	    <thead>
	      <tr>
	        <th ><?php echo $this->translate("IP") ?></th>
	        <th ><?php echo $this->translate("Date login") ?></th>	
	      </tr>
	    </thead>
	    <tbody>
	      
	        <?php
	        
	         foreach( $this->paginator as $item ):?>
	          <tr>	            
	            <td><?php echo $item->ip; ?></td>
	            <td><?php echo $item->creation_date; ?></td>	          
	          </tr>
	        <?php endforeach; ?>
	      
	    </tbody>
	  </table>
	  <br />
	 
	</form>
	</div>
<?php else:?>
	<div class="tip">
      <span>
           <?php echo $this->translate('There are no ip yet.');?>
      </span>
           <div style="clear: both;"></div>
       </div>	
<?php endif;?>

<?php endif;?>