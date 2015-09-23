<?php

class Slprofileverify_Form_Verifydocument extends Engine_Form {

    protected $_custom_field = null;

    public function init() {
        $this->setMethod("POST")
                ->setTitle($this->getView()->translate("Identity Verification"))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->addAttribs(array('id' => 'verify-document'))
                ->setDescription('Replace Description')
        ;
        // Get user
        $viewer = Engine_Api::_()->user()->getViewer();
        // Get fieldId and optionId user
        $aliasedFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($viewer);
        $fieldId = $aliasedFields['profile_type']->field_id;
        $profileTypeOptionId = $aliasedFields['profile_type']->getValue($viewer);
        $optionId = $profileTypeOptionId->value;
        // Get required verify and image required
        $requiresTbl = Engine_Api::_()->getDbTable('requires', 'slprofileverify');
        $requireRow = $requiresTbl->getRequireRow($optionId);
        $arrFieldIdRequired = Zend_Json::decode($requireRow['required']);
        $arrImageRequired = Zend_Json::decode($requireRow['image']);
        $imageRequired = array();
        if ($requireRow) {
            foreach ($arrImageRequired as $arrValues) {
                if ($arrValues[1] == 1) {
                    $imageRequired[] = $arrValues[0];
                }
            }
        }
        $this->getView()->imageIV = Engine_Api::_()->getApi('core', 'slprofileverify')->getPhotoIdentityUrl($imageRequired, null, 'identity');
        // Get structure field user
        $structure = Engine_Api::_()->fields()->getFieldsStructureFull($viewer, $fieldId, $optionId);
        $orderIndex = 0;
        $subForm = new Zend_Form_SubForm();
        $subForm->addPrefixPath('Fields_Form_Element', APPLICATION_PATH . '/application/modules/Fields/Form/Element', 'element');
        $subForm->addPrefixPath('Engine_Form_Element', 'Engine/Form/Element', 'element');
        if (count($arrFieldIdRequired)) {
            foreach ($structure as $keyTmp => $map) {
                $field = $map->getChild();
                $params = $field->getElementParams($viewer);
                $key = $map->getKey();
                $keyValue = explode('_', $key);
                if ($keyValue[0] != $fieldId || !in_array($keyValue[2], $arrFieldIdRequired)) {
                    continue;
                }
                if (!@is_array($params['options']['attribs'])) {
                    $params['options']['attribs'] = array();
                }
                $params['options']['order'] = $orderIndex++;
                if ($params['type'] != 'Heading') {
                    $inflectedType = Engine_Api::_()->fields()->inflectFieldType($params['type']);
                    $subForm->addElement($inflectedType, $key, $params['options']);
                }
            }
        }
        $this->addSubForms(array(
            'field' => $subForm
        ));

        $this->addElement('Text', 'profile_picture', array(
            'label' => $this->getView()->translate("Profile picture"),
            'required' => true
        ));
        if ($requireRow && !$requireRow['enable_profile']) {
            $this->profile_picture->setRequired(false);
        }

        $description_document = $this->getView()->translate("DOCUMENT_DEFAULT_DESCRIPTION");
        if ($requireRow['exp_document']) {
            $description_document = $requireRow['exp_document'];
        }
        $this->addElement('File', 'document', array(
            'label' => $this->getView()->translate("Upload Verification Document") . "*",
            'description' => $description_document,
            'required' => true
        ));
        $this->document->getDecorator('Description')->setOptions(array('tag' => 'div', 'id' => 'exp-document-identity', 'escape' => false));
        //echo $requireRow->image_number; die;
        if(($iImgNumber = $requireRow->image_number)){
            $this->document->setMultiFile($iImgNumber);
        }
        $this->document->setOptions(array('class' => 'input-file-block'));
        $this->document->addValidator('Extension', false, 'jpg,png,gif,jpeg');        

        $this->addElement('Checkbox', 'copy_document', array(
            'label' => $this->getView()->translate("I confirm that this is true copy of my document"),
            'value' => false,
            'required' => true,
        ));

        $this->addElement('Hidden', 'option_id', array(
            'value' => $optionId,
            'order' => 1000
        ));

        $this->addElement('Hidden', 'field_id', array(
            'value' => $fieldId,
            'order' => 2000
        ));

        $this->addElement('Hidden', 'required', array(
            'value' => $requireRow['required'],
            'order' => 3000
        ));

        $this->addElement('Button', 'submit', array(
            'label' => $this->getView()->translate("Update my profile and Upload"),
            'type' => 'submit',
            'ignore' => true
        ));

        $this->addElement('Button', 'upload', array(
            'label' => $this->getView()->translate("Upload and Continue"),
            'type' => 'submit',
            'ignore' => true
        ));
    }

    public function saveValues() {
        $this->_custom_field->saveValues();
    }

}