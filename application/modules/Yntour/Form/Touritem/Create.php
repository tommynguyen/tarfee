<?php

class Yntour_Form_Touritem_Create extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Edit Tour Guide Step') -> setDescription('Please compose your new tour guide step below.') -> setAttrib('id', 'touritem_create')->setAttrib('class','global_form_popup');
        
        // $model = new Yntour_Model_DbTable_Tours;
        // $select = $model -> select()->from($model->info('name'),array('tour_id', 'label'=>new Zend_Db_Expr('substring(title,1,30)')));
        // $options = $model->getAdapter()->fetchPairs($select);

        
        // Add title
        // $this -> addElement('Text', 'title', array(
            // 'label' => 'Title',
            // 'required' => true,
            // 'allowEmpty' => false,
        // ));
        
        // $this -> addElement('Text', 'width', array(
            // 'label' => 'Width',
            // 'required' => true,
            // 'value'=>'auto',
            // 'description'=>'etc: 300px, 300pt, default is auto',
            // 'allowEmpty' => false,
         // ));
                
        $this -> addElement('Text', 'priority', array(
            'label' => 'Ordering',
            'validators'=>array('Int'),            
            'required' => true,
            'allowEmpty' => false,
         ));
         
         
        $this -> addElement('Text', 'time_delay', array(
            'label' => 'Time delay in second(s)',
            'validators'=>array('Int'),
            'value'=>'6',            
            'required' => true,
            'allowEmpty' => false,
        ));
        
        $this -> addElement('Text', 'width', array(
            'label' => 'Width (px)',
            //'validators'=>array('Int'),
            'value'=>'300',            
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

        $this -> addElement('Textarea', 'body', array(
            'label' => 'Content',
            'required' => true,
            'allowEmpty' => false,
        ));

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
            'href' =>'javascript:parent.Smoothbox.close()',
            'prependText' => Zend_Registry::get('Zend_Translate') -> _(' or '),
            'decorators' => array('ViewHelper', ),
        ));

        $this -> addDisplayGroup(array(
            'submit',
            'cancel'
        ), 'buttons');
    }

}
