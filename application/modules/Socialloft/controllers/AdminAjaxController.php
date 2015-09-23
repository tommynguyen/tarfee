<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Bookmarklet
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */

class Socialloft_AdminAjaxController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
	     $this->view->headScript()
	      ->appendFile($this->view->baseUrl().'/application/modules/Socialloft/externals/scripts/jquery-1.8.0.min.js')
	      ->appendFile($this->view->baseUrl().'/application/modules/Socialloft/externals/scripts/jquery-ui-1.9.2.custom.min.js')
	      ->appendFile($this->view->baseUrl().'/application/modules/Socialloft/externals/scripts/jtable/jquery.jtable.min.js')
	      ->appendFile($this->view->baseUrl().'/application/modules/Socialloft/externals/scripts/core.init.js')
	      ->appendFile($this->view->baseUrl().'/application/modules/Socialloft/externals/scripts/loft.ui.js')
	      ->appendFile($this->view->baseUrl().'/application/modules/Socialloft/externals/scripts/myplugin.ui.js');

	    $this->view->headLink()
	      ->appendStylesheet($this->view->baseUrl().'/application/modules/Socialloft/externals/styles/jtable/themes/lightcolor/gray/jtable.css')
	      ->appendStylesheet($this->view->baseUrl().'/application/modules/Socialloft/externals/styles/base/jquery.ui.all.css');
	}
	
	public function openLoftPluginAction()
	{
		$current = file_get_contents('http://license.socialloft.com/se4/products.php');
        echo  $current;die();
	}
	
	public function call($js)
	{
		echo $js;die();
	}
	
	public function installedPluginAction()
	{
		$myplugins = Engine_Api::_()->getDbTable('myplugins','socialloft')->fetchAll();        
        if (count($myplugins)) {
             $checkjs="";
             foreach($myplugins as $myplugin){
                 $checkjs .= "jQuery(\".loft-packages-{$myplugin->packages}\").removeClass(\"ui-icon-close\").addClass(\"ui-icon-check\");";
                 
             }
        }        
        $this->call($checkjs);
	}
	
	public function validateLicenseAction()
	{
	    $product = $this->_getParam('product', null);
        if (!empty($product)) {
            $result =  Engine_Api::_()->socialloft()->validate_license($product);
            switch ($result['RESULT']){
                case 'EMPTY_DATA':
                case 'EMPTY':
                case 'FILE_KEY_NOT_FOUND':
                case 'SE4_KEY_CODE_NOT_FOUND':
                    $this->call('jQuery( "#loft-dialog-form-verify" ).dialog("open");');
                    break;
                case 'OK':
                    //var_dump($result);
                    $message = "Purchase Information";
                    $message .= "<br/> Domain : {$result['SERVER']['DOMAIN']} ";
                    $message .= "<br/> License Key : {$result['DATA']['KEY_CODE']} ";
                    $this->call('message_verify("'.$message.'");');
                    break;
            }
        }
	}
	
	public function verifyLicenseAction()
	{
		$product = $this->_getParam('product', null);
        $license= $this->_getParam('license', null);
        if(!empty($product) && !empty($license)){
            $result = Engine_Api::_()->socialloft()->verify_license(
                    array('name'=>$product,
                          'key_code'=>$license)
                    );
            $message = $result['RESULT'];
            $this->call('message_verify("'.$message.'");');
        }
	}
	
	public function openMyPluginAction()
	{
		$myplugin = Engine_Api::_()->getDbTable('myplugins','socialloft')->fetchAll()->toArray();		
        if (count($myplugin)) {
            for($i=0;$i<count($myplugin);$i++){
                $myplugin[$i]['key_info'] = Engine_Api::_()->socialloft()->validate_license($myplugin[$i]['packages']);
                $myplugin[$i]['key_info'] =  $myplugin[$i]['key_info']['RESULT'];
                $myplugin[$i]['packages'] = "<button class=\"loft-verify\" name=\"{$myplugin[$i]['packages']}\"></button>";
            }
        }
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['Records'] = $myplugin;
        print json_encode($jTableResult);die();
	}
}
