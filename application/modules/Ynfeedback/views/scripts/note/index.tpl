<script type="text/javascript">
function editNote(note_id)
{
	content = $('note-content-'+note_id).innerHTML;
	$('content').set('value', content.trim());
	$('note_id').set('value', note_id);
	if($$('#content-label label').length)
	{
		$$('#content-label label')[0].set('html', '<?php echo $this -> translate("Edit Note");?>');
	}
}

function deleteNote(note_id)
{
	if (confirm('<?php echo $this -> translate("Do you want to delete this note?"); ?>') ==  true)
	{
		new Request.JSON({
			'format': 'json',
			'method' : 'post',
			'url' : '<?php echo $this->url(array('controller' => 'note', 'action' => 'delete'), 'ynfeedback_extended', true); ?>',
			'data' : {
				'format' : 'json',
				'note_id' : note_id
			},
			'onRequest' : function(){
			},
			'onSuccess' : function(responseJSON, responseText)
			{
				$("ynfeedback-note-item-"+note_id).destroy();
			}
		}).send();
	}
}

</script>

<?php if (count($this -> notes)) : ?>
	<ul class="global_form_popup">
	<?php foreach ($this -> notes as $note):?>
	<li id="ynfeedback-note-item-<?php echo $note -> note_id; ?>">
		<div>
			<?php echo $this -> translate("Added on %1s", date("M d Y", strtotime($note->creation_date)));?>
		</div>
		<div id="note-content-<?php echo $note -> note_id; ?>">
			<?php echo $note -> content;?>
		</div>
		<div>
			<a href="javascript:void(0);" onclick="editNote(<?php echo $note -> note_id;?>)"><?php echo $this -> translate("Edit");?></a>
			 | 
			<a href="javascript:void(0);" onclick="deleteNote(<?php echo $note -> note_id;?>)"><?php echo $this -> translate("Delete");?></a>
		</div>
	</li>
	<?php endforeach;?>
	</ul>
<?php endif;?>
<?php echo $this -> form -> render($this);?>