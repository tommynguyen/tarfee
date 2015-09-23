<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script type="text/javascript">
function validateForm()
{
    var j;
    var counter = 0;
    for (j=0;j<arr.length;j++)
    {
        if((!document.getElementById(arr[j]).value.match(/^[\+]?[\d\,]*\.?[\d]*$/))){
            alert("please enter a valid decimal number");
            return false;
        }
        if((document.getElementById(arr[j]).value < 0)){
            alert("Please pick a number greater than 0");
            return false;
        }
        if(document.getElementById(arr[j]).value == 0){
            counter++;
        }
    }
    if(counter == arr.length){
        alert("Not allow all field with 0 or leave blank at the same time!");
        return false;
    }
    return true;
}
</script>
<?php if(!empty($this->secondLevelMaps)): ?>
<form class="global_form_smoothbox" method="post" action="<?php echo $this->url(array('action' => 'edit')); ?>" onSubmit="return validateForm()">
    <div>
        <div>
            <h3>Edit weight of profile fields</h3>
            <div>
                <ul class="admin_fields">
                    <?php $fields = array();?>
                    <?php foreach ($this->secondLevelMaps as $map): ?>
                        <?php
                            $table = Engine_Api::_()->getDbtable('weights', 'profileCompleteness');
                            $select = $table->select()
                                    ->where('type_id = ?', $this->type)
                                    ->where('field_id = ?', $map->getChild()->field_id);
                            
                            $row = $table->fetchRow($select);
                            $fields[] = $row->field_id;
                        ?>
                        <?php echo $this->adminProfileWeightInput($map, $this->type) ?>
                    <?php endforeach; ?>
                </ul>
                <br />
                <button id="execute" type="submit" name="execute">Save Changes</button>
                or
                <a id="cancel" onclick="parent.Smoothbox.close();" href="javascript:void(0);" type="button" name="cancel">cancel</a>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">  
    var arr = new Array();
    var i=0;
    <?php foreach ($fields as $field):?>
        <?php if($field != null): ?>
            arr[i] = <?php echo $field; ?>;
            i++;
        <?php endif;?>
    <?php endforeach;?>

    if (arr.length == 0)
    {
        $$("input").each(function(e){
			arr[i] = e.get("id");
			i++
		});
    }
    
</script>
<?php endif; ?>
<?php if(empty($this->secondLevelMaps)): ?>
<h2>
    Profile Field is Empty !
</h2>
<?php endif; ?>

