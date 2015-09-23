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
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sladvsubscription/externals/scripts/mooRainbow.js');
    
  $this->headLink()
    ->prependStylesheet($this->layout()->staticBaseUr.'application/modules/Sladvsubscription/externals/styles/mooRainbow.css');
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

    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script type="text/javascript">
window.addEvent('domready', function() { 
	$('feature_text_font_type').inject($('feature_text_font_size'),'after');
	$('feature_text_font_type-wrapper').hide();
	$('feature_text_font_style').inject($('feature_text_font_size'),'after');
	$('feature_text_font_style-wrapper').hide();

	$('column_header_font_type').inject($('column_header_font_size'),'after');
	$('column_header_font_type-wrapper').hide();
	$('column_header_font_style').inject($('column_header_font_size'),'after');
	$('column_header_font_style-wrapper').hide();
	
	$('cell_text_font_type').inject($('cell_text_font_size'),'after');
	$('cell_text_font_type-wrapper').hide();
	$('cell_text_font_style').inject($('cell_text_font_size'),'after');
	$('cell_text_font_style-wrapper').hide();

	var myPictures=['ticker_image_link','most_popular_icon','x_image_link'];
	for (var i=0;i<myPictures.length;i++)
	{
		$(myPictures[i]).hide();
		var image = new Element('img',{
			'src' : '<?php echo $this->baseUrl()?>/' + $(myPictures[i]).value
		});
		image.inject($(myPictures[i]),'after');
	}
	$('most_popular_file').inject($('most_popular_icon-element'),'after');
	$('most_popular_file-label').getChildren()[0].inject($('most_popular_icon-element'),'after');
	$('most_popular_file-wrapper').hide();

	$('ticker_image_file').inject($('ticker_image_link-element'),'after');
	$('ticker_image_file-label').getChildren()[0].inject($('ticker_image_link-element'),'after');
	$('ticker_image_file-wrapper').hide();

	$('x_image_file').inject($('x_image_link-element'),'after');
	$('x_image_file-label').getChildren()[0].inject($('x_image_link-element'),'after');
	$('x_image_file-wrapper').hide();

	$('price_font_color').inject($('price_font_size'),'after');
	$('price_font_color-wrapper').hide();
	$('price_font_style').inject($('price_font_size'),'after');
	$('price_font_style-wrapper').hide();
	$('price_font_type').inject($('price_font_size'),'after');
	$('price_font_type-wrapper').hide();
	

	var myColors=["odd_header_column_color","even_header_column_color","odd_row_color","even_row_color","price_font_color","menu_background_color"];
	for (var i=0;i<myColors.length;i++)
	{
		var name_id = i + 1;
		var field = myColors[i];
		var input = new Element('input',{
				'id' : 'myRainbow'+name_id,
				'name' : 'myRainbow'+name_id,
				'type' : 'image',
				'src' : '<?php echo $this->baseUrl()?>/application/modules/Sladvsubscription/externals/images/rainbow.png'
			});
		input.inject($(field),'after');
		new MooRainbow('myRainbow'+name_id, { 	
			'id' : 'demoRainbow' + name_id,	
			'input_value' : field,	
			'startColor': [hexToR($(field).value), hexToG($(field).value), hexToB($(field).value)],
			'onChange': function(color) {
				$(this.options.input_value).value = color.hex;
			}
		});
	}
});	
function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}
</script>