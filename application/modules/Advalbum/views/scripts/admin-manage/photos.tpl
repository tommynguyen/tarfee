<script type="text/javascript">
en4.core.runonce.add(function()
{
     $$('th.admin_table_checkbox input[type=checkbox]').addEvent('click',
     function(){
         var checkboxes = $$('td.delete_photos input[type=checkbox]');
         checkboxes.each(function(item, index){
                item.checked =  $('check_all').checked;
           });
     })});

  var delectSelected =function(){
    var checkboxes = $$('td.delete_photos input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    if (selecteditems.length==0) {
      alert("Please select a photo to delete.");
      return;
    }
    $('ids').value = selecteditems;
    $('delete_selected').submit();
}

//change order of album table's columns
  function changeOrder(listby, default_direction){
      var currentOrder = '<?php echo $this->formValues['orderby'] ?>';
      var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
        // Just change direction
        if( listby == currentOrder ) {
          $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
        } else {
          $('orderby').value = listby;
          $('direction').value = default_direction;
        }
        $('filter_form').submit();
  }
</script>
 <script type="text/javascript">
    function photo_good(photo_id,checkbox)
    {
            var status = 0;
            if(checkbox.checked==true)
                status = 1;
            else
                status = 0;
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'advalbum', 'controller' => 'manage', 'action' => 'featured'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'photo_id' : photo_id,
                'good' : status
              },
              'onRequest' : function(){

              },
              'onSuccess' : function(responseJSON, responseText)
              {

              }
            }).send();

    }
</script>
<h2>
  <?php echo $this->translate('View Photos') ?>
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

<p>
  <?php echo $this->translate("PHOTO_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br/>
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br />
<?php if( count($this->paginator) ): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_checkbox'><input id="check_all" type='checkbox' class='checkbox' /></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('photo_id', 'DESC')"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('title', 'ASC')"><?php echo $this->translate('Title') ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('owner_title', 'DESC')"><?php echo $this->translate('Owner') ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('album_title', 'DESC')"><?php echo $this->translate('Album') ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('photo_good', 'DESC')"><?php echo $this->translate("Featured") ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('view_count', 'DESC')"><?php echo $this->translate('Views') ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('creation_date', 'DESC')"><?php echo $this->translate('Creation Date') ?></a></th>
        <th><?php echo $this->translate('Options') ?></th>
      </tr>
    </thead>
    <tbody>
        <?php foreach ($this->paginator as $item): ?>
        <?php $album = Engine_Api::_()->getItem('advalbum_album', $item->album_id); ?>
        <?php if($item->album_id == 0) continue; ?>
          <tr>
            <td class="delete_photos"><input type='checkbox' class='checkbox' value="<?php echo $item->getIdentity() ?>"/></td>
            <td><?php echo $item->getIdentity() ?></td>
           	<td><?php if(strlen($item->getTitle())>20) echo substr($item->getTitle(), 0, 20)."..."; else echo $item->getTitle(); ?></td>
            <td><?php echo $this->user($album->owner_id)->getTitle() ?></td>
            <td>
            <?php echo $this->htmlLink($album->getHref(), Advalbum_Api_Core::shortenText($this->translate($album->getTitle()), 20), array('title' => $this->translate($album->getTitle())))?>
            </td>
             <?php $flag =  $item->getFeatured() ?>
            <td>
	            <div id='advalbum_content_<?php echo $item->getIdentity() ?>' style ="text-align: center;" >
		            <?php if($flag == true): ?>
		            <input type="checkbox" id='goodphoto_<?php echo $item->getIdentity(); ?>'  onclick="photo_good(<?php echo $item->getIdentity(); ?>,this)" checked />
		          	<?php else: ?>
		           	<input type="checkbox" id='goodphoto_<?php echo $item->getIdentity(); ?>'  onclick="photo_good(<?php echo $item->getIdentity(); ?>,this)" />
		          	<?php endif; ?>
	          	</div>
          	</td>
            <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
            <td>
              <a href="<?php echo $item->getHref() ?>">
                <?php echo $this->translate('view') ?>
              </a>
              |  <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'advalbum', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->getIdentity()),
                  $this->translate('delete'),
                  array('class' => 'smoothbox')) ?>
            </td>
          </tr>
        <?php endforeach; ?>
    </tbody>
  </table>

  <br/>

 <div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>

<br />

<div>
<?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
    array(
    'pageAsQuery' => false,
    'query' => $this->formValues
)); ?>
</div>


<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no photos posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>