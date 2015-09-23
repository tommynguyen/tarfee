<div class="headline">
    <h2>
        Extra Info
    </h2>
</div>
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

function multiModify()
{
var multimodify_form = $('multimodify_form');
if (multimodify_form.submit_button.value == 'delete')
  {
return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
}
}
function selectAll()
{
var i;
var multimodify_form = $('multimodify_form');
var inputs = multimodify_form.elements;
for (i = 1; i < inputs.length - 1; i++) {
if (!inputs[i].disabled) {
inputs[i].checked = inputs[0].checked;
}
}
}

function loginAsUser(id) {
if( !confirm('<?php echo $this->translate('Note that you will be logged out of your current account if you click ok.') ?>') ) {
return;
}
var url = '<?php echo $this->url(array('action' => 'login')) ?>';
var baseUrl = '<?php echo $this->url(array(), 'default', true) ?>';
(new Request.JSON({
url : url,
data : {
format : 'json',
id : id
},
onSuccess : function() {
window.location.replace( baseUrl );
}
})).send();
}

<?php if( $this->openUser ): ?>
window.addEvent('load', function() {
$$('#multimodify_form .admin_table_options a').each(function(el) {
if( -1 < el.get('href').indexOf('/edit/') ) {
el.click();
//el.fireEvent('click');
}
});
});
<?php endif ?>
</script>
<ul>

    <li>
        <?php
        echo $this->translate('User ID: '); echo $this->user->user_id;?>
        <?php // @todo implement link ?>

    </li>

    <li>
        <?php echo $this->translate('User Resgister IP: ')?>
        <?php // @todo implement link ?>
        <?php if( !_ENGINE_ADMIN_NEUTER ): ?>
        <?php
        $ipObj = new Engine_IP($this->user->creation_ip);
        echo $ipObj->toString()
        ?>
        <?php else: ?>
        <?php echo $this->translate('(hidden)') ?>
        <?php endif ?>
    </li>

    <li>
        <?php echo $this->translate('Last accessed IP: ').' ' ?>
        <?php // @todo implement link ?>
        <?php if( !_ENGINE_ADMIN_NEUTER ): ?>
        <?php
        $ipObj = new Engine_IP($this->user->lastlogin_ip);
        echo $ipObj->toString()
        ?>
        <?php else: ?>
        <?php echo $this->translate('(hidden)') ?>
        <?php endif ?>
    </li>

    <li>
        <?php echo $this->translate('Last logged in:') ?>
        <?php echo $this->locale()->toDateTime($this->user->lastlogin_date) ?>

    </li>
    <li>
        <?php echo $this->translate('Email: ') ?>
        <?php echo $this->user->email ?>

    </li>
    <li>

        </a>
    </li>
    <li>
        <a  class='smoothbox'href='<?php 

            switch($this->typeURL)
            {
            case 1:
                echo $this->url(array('action' => 'add', 'id' => $this->user->user_id, 'type' => 2 ));
            break;
            case 2:
                echo $this->url(array('action' => 'unban', 'user' => $this->bannedUser_id,'email'=>$this->bannedEmail_id , 'type'=>0));
             break;
          
            case 3:
                echo $this->url(array('action' => 'unban', 'id' => $this->bannedid,'type'=>1));
                break;
            
            case 4:
                echo $this->url(array('action' => 'unban', 'id' => $this->bannedid,'type'=>2));
                break;
            }

            ?>'> <?php switch($this->banText)
            {
            case 1:
            echo $this->translate('Unban');
            break;
            case 2:
            echo $this->translate('Ban');
            break;
            }?>
    </a>
    |
    <a class='smoothbox' href='<?php echo $this->url(array('action' => 'note', 'id' => $this->user->user_id));?>'>
       <?php echo $this->translate("note") ?>
</a>
<?php if (($this->superAdminCount>1 && $this->user->level_id == 1) || $this->user->level_id != 1): // @todo change this to look up actual superadmin level ?>
|
<a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $this->user->user_id));?>'>
   <?php echo $this->translate("delete") ?>
</a>
<?php endif;?>

</li>


<li>

    <?php if( $this->user->level_id != 1 ): // @todo change this to look up actual superadmin level ?>
    <a class='smoothbox' href='<?php echo $this->url(array('action' => 'login', 'id' => $this->user->user_id));?>' onclick="loginAsUser(<?php echo $this->user->user_id ?>); return false;">
       <?php echo $this->translate("Log as this user") ?>
</a>
<?php endif; ?>

</li>
</ul>