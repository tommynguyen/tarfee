<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Advsubscription
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */

class Sladvsubscription_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    $setting =  Engine_Api::_()->getApi('settings', 'core');
    
    $api = Engine_Api::_()->sladvsubscription();
    
	$this->addElement('Text','odd_header_column_color',array(
		'label'=>'Odd header column background color',
		'value'=> $setting->getSetting('advsubscription.odd_header_column_color', '#b3b3b3')
	));
	
	$this->addElement('Text','even_header_column_color',array(
		'label'=>'Even header column background color',
		'value'=> $setting->getSetting('advsubscription.even_header_column_color', '#60B454')
	));
	
	$this->addElement('Text','odd_row_color',array(
		'label'=>'Odd row background color',
		'value'=> $setting->getSetting('advsubscription.odd_row_color', '#FFFFFF')
	));
	
	$this->addElement('Text','even_row_color',array(
		'label'=>'Even row background color',
		'value'=> $setting->getSetting('advsubscription.even_row_color', '#EBEBEB')
	));
	
	$this->addElement('Select','feature_text_font_size',array(
		'label'=>'Feature text style format',
		'value'=> $setting->getSetting('advsubscription.feature_text_font_size', '11'),
		'multiOptions'=>$api->_setting_font_size
	));
	
	$this->addElement('Select','feature_text_font_style',array(
		'value'=> $setting->getSetting('advsubscription.feature_text_font_style', 'normal'),
		'multiOptions'=>$api->_setting_font_style
	));
	
	$this->addElement('Select','feature_text_font_type',array(
		'value'=> $setting->getSetting('advsubscription.feature_text_font_type', 'normal'),
		'multiOptions'=>$api->_setting_font_type
	));
	
	$this->addElement('Select','column_header_font_size',array(
		'label'=>'Column header font size, style and family',
		'value'=> $setting->getSetting('advsubscription.column_header_font_size', '18'),
		'multiOptions'=>$api->_setting_font_size
	));
	
	$this->addElement('Select','column_header_font_style',array(
		'value'=> $setting->getSetting('advsubscription.column_header_font_style', 'bold'),
		'multiOptions'=>$api->_setting_font_style
	));
	
	$this->addElement('Select','column_header_font_type',array(
		'value'=> $setting->getSetting('advsubscription.column_header_font_type', 'normal'),
		'multiOptions'=>$api->_setting_font_type
	));
	
	
	$this->addElement('Select','cell_text_font_size',array(
		'label'=>'Cell text style format',
		'value'=> $setting->getSetting('advsubscription.cell_text_font_size', '11'),
		'multiOptions'=>$api->_setting_font_size
	));
	
	$this->addElement('Select','cell_text_font_style',array(
		'value'=> $setting->getSetting('advsubscription.cell_text_font_style', 'normal'),
		'multiOptions'=>$api->_setting_font_style
	));
	
	$this->addElement('Select','cell_text_font_type',array(
		'value'=> $setting->getSetting('advsubscription.cell_text_font_type', 'normal'),
		'multiOptions'=>$api->_setting_font_type
	));
	
	$this->addElement('Text','ticker_image_link',array(
		'label' => 'Checked image',
		'value'=> $setting->getSetting('advsubscription.ticker_image_link', $api->_link_check_default)
	));
	
	$this->addElement('File','ticker_image_file',array(
		'label' => 'Or Upload an icon (24 x 24 px)',		
	));
	$this->ticker_image_file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	$this->addElement('Text','x_image_link',array(
		'label' => 'X image',
		'value'=> $setting->getSetting('advsubscription.x_image_link', $api->_link_uncheck_default)
	));
	
	$this->addElement('File','x_image_file',array(
		'label' => 'Or Upload an icon (24 x 24 px)',		
	));	
	$this->x_image_file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	$this->addElement('Text','most_popular_icon',array(
		'label' => 'Most popular icon',
		'value'=> $setting->getSetting('advsubscription.most_popular_icon', $api->_link_popular_default)
	));
	
	$this->addElement('File','most_popular_file',array(
		'label' => 'Or Upload an icon (46 x 46 px)',		
	));
	$this->most_popular_file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	$this->addElement('Select','price_font_size',array(
		'label'=>'Price style format',
		'value'=> $setting->getSetting('advsubscription.price_font_size', '24'),
		'multiOptions'=>$api->_setting_font_size
	));
	
	$this->addElement('Select','price_font_style',array(
		'value'=> $setting->getSetting('advsubscription.price_font_style', 'normal'),
		'multiOptions'=>$api->_setting_font_style
	));
	
	$this->addElement('Select','price_font_type',array(
		'value'=> $setting->getSetting('advsubscription.price_font_type', 'normal'),
		'multiOptions'=>$api->_setting_font_type
	));
	
	$this->addElement('Text','price_font_color',array(
		'value'=> $setting->getSetting('advsubscription.price_font_color', '#FF6C00')
	));
	
	$this->addElement('Text','menu_background_color',array(
		'label'=>'Upgrade button background color',
		'value'=> $setting->getSetting('advsubscription.menu_background_color', '#EEE')
	));
	
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}