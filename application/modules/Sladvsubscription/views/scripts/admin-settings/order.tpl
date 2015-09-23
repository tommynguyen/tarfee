<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Advsubscription
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */
?>

<h2>
  <?php echo $this->translate('Advanced Membership Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
  	<form>
  		<div>
  			<div>
			  	<p><?php echo $this->translate('To reorder the level, click on their names and drag them up or down');?></p>
				<ul id="level_sort">
					<?php foreach ($this->levels as $key=>$level):?>
						<li id="<?php echo $key?>" class="level"><?php echo $level;?></li>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	</form>
		
  </div>
</div>
<script type="text/javascript">
	new Sortables($('level_sort'),{
		'onComplete' : function(element){
			 var categoryitems = element.getParent().getChildren();    
		     var ordering = {};
		     var i = 1;
		     categoryitems.each(function(el)
		     {         
		       var child_id = el.get('id');
	
		       if (child_id != undefined)
		       {
		         ordering[child_id] = i;
		         i++;
		       }
		     });
		    var url = '<?php echo $this->baseUrl();?>/admin/sladvsubscription/settings/order-ajax';
		    var request = new Request.JSON({
		      'url' : url,
		      'method' : 'POST',
		      'data' : ordering,
		      onSuccess : function(responseJSON) {
		      }
		    });
	
		    request.send();

		}
	});
</script>