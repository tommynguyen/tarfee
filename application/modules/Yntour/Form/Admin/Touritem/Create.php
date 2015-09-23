<?php

class Yntour_Form_Admin_Touritem_Create extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Create Tour Guide Step') -> setDescription('Please compose your new tour guide item below.') -> setAttrib('id', 'touritem_create');
        
        // $model = new Yntour_Model_DbTable_Tours;
        // $select = $model -> select()->from($model->info('name'),array('tour_id', 'label'=>new Zend_Db_Expr('substring(title,1,30)')));
        // $options = $model->getAdapter()->fetchPairs($select);
// 
        // $this -> addElement('Select', 'tour_id', array(
            // 'required' => true,
            // 'label' => 'Tour guide',
            // 'multiOptions' => $options
        // ));
        
        // Add title
        // $this -> addElement('Text', 'title', array(
            // 'label' => 'Title',
            // 'required' => true,
            // 'allowEmpty' => false,
        // ));
        
        // Add title
        $this -> addElement('Text', 'priority', array(
            'label' => 'Ordering',
            'validators'=>array('Int'),            
            'required' => true,
            'allowEmpty' => false,
        ));
        
        $this -> addElement('Text', 'time_delay', array(
            'label' => 'Time delay in second(s)',
            'description'=>'',
            'validators'=>array('Int'),
            'value'=>'6',      
            'required' => true,
            'allowEmpty' => false,
        ));
        
        $this -> addElement('Text', 'width', array(
            'label' => 'Width (px)',
            //'validators'=>array('Int'),
            'value'=>'20',            
            'required' => true,
            'allowEmpty' => false,
        ));
        
        $pos = array('top'=>'top','right'=>'right','bottom'=>'bottom','left'=>'left');
        $this->addElement('Select', 'position', 
                        array(
                        'label' => 'Position',
                        'placeholder'=>'position',
                        'multiOptions'=>$pos,
                        'filters' => array(
                            new Engine_Filter_Censor(),
                            ),
                        )
                    );
		
		$this -> addElement('Text', 'left_position', array(
            'label' => 'Left Position (px)',
            'validators'=>array('Int'),
            'value'=>'20',            
            'required' => true,
            'allowEmpty' => false,
        ));
        
        $this -> addElement('Text', 'top_position', array(
            'label' => 'Top Position (px)',
            'validators'=>array('Int'),
            'value'=>'20',            
            'required' => true,
            'allowEmpty' => false,
        ));
        
        // $this -> addElement('Text', 'width', array(
            // 'label' => 'Width',
            // 'required' => true,
            // 'value'=>'auto',
            // 'description'=>'etc: 300px, 300pt, default is auto',
            // 'allowEmpty' => false,
         // ));
        // Add title
        // $this -> addElement('Text', 'path', array(
            // 'label' => 'Path',
            // 'required' => true,
            // 'allowEmpty' => false,
        // ));
        
        // $this->addElement('Radio','enabled',array(
            // 'label'=>'Enabled',
            // 'value'=>1,
            // 'multiOptions'=>array(
                // '0'=>'Disable',
                // '1'=>'Enable',
            // ),
        // ));

        //get all languages
             // Languages
             $translate    = Zend_Registry::get('Zend_Translate');
             $languageList = $translate->getList();
             // Prepare language name list 
            $languageNameList  = array();
            $languageDataList  = Zend_Locale_Data::getList(null, 'language');
            $territoryDataList = Zend_Locale_Data::getList(null, 'territory');

            foreach( $languageList as $localeCode ) {
              $languageNameList[$localeCode] = Engine_String::ucfirst(Zend_Locale::getTranslation($localeCode, 'language', $localeCode));
              if (empty($languageNameList[$localeCode])) {
                if( false !== strpos($localeCode, '_') ) {
                  list($locale, $territory) = explode('_', $localeCode);
                } else {
                  $locale = $localeCode;
                  $territory = null;
                }
                if( isset($territoryDataList[$territory]) && isset($languageDataList[$locale]) ) {
                  $languageNameList[$localeCode] = $territoryDataList[$territory] . ' ' . $languageDataList[$locale];
                } else if( isset($territoryDataList[$territory]) ) {
                  $languageNameList[$localeCode] = $territoryDataList[$territory];
                } else if( isset($languageDataList[$locale]) ) {
                  $languageNameList[$localeCode] = $languageDataList[$locale];
                } else {
                  continue;
                }
              }
               $this -> addElement('Textarea', 'body_'.$localeCode, array(
                'label' => 'Body ('.$languageDataList[$localeCode].')',
                'required' => true,
                'allowEmpty' => false,
                ));
            }
        
        //end
        // Buttons
        $this -> addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this -> addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'ignore' => true,
            'link' => true,
            'href' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                'module' => 'yntour',
                'controller' => 'manage',
                'action' => 'item',
                'id'=>$this->getTourId(),
            ), 'admin_default', true),
            'prependText' => Zend_Registry::get('Zend_Translate') -> _(' or '),
            'decorators' => array('ViewHelper', ),
        ));

        $this -> addDisplayGroup(array(
            'submit',
            'cancel'
        ), 'buttons');
    }
    
    protected $_tourId = 0;
    
    public function getTourId(){
        return $this->_tourId;
    }
    public function setTourId($value){
        $this->_tourId =  $value;
        return $this;
    }

}
