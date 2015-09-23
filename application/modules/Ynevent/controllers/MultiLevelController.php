<?php

// public page
class Ynevent_MultiLevelController extends Core_Controller_Action_Standard{
	
	public function changeAction() {
		//echo "LONG"; exit;
		$category_id = $this->_getParam('id');
		$model_class =  $this->_getParam('model');
		$name =  $this->_getParam('name');	
		$level = $this->_getParam('level');
		$isSearch = (int)$this->_getParam('isSearch',0);
		$model =  new $model_class;
		$item =  $model->find((string)$category_id)->current();		
		
		if($category_id == ''){
			return '';
		}
		
		$options =  $model->getMultiOptions($item->getIdentity());		
		if(count($options)<2){
			return ;
		}
		$route = 'ynevent';
	 	$element = new Zend_Form_Element_Select(
				sprintf("%s_%s",$name, $level+1),
				array(
					'multiOptions'=> $options,
					'required'=>false,
					'onchange'=>"en4.ynevent.changeCategory($(this),'".$name."','".$model_class."','".$route."',$isSearch,0)",
				)
			);
		echo $element->renderViewHelper();
	}
  	
}
