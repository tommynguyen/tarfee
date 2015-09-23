<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ProfileCompleteness_View_Helper_AdminProfileWeightInput extends Zend_View_Helper_Abstract {

    public function adminProfileWeightInput($map, $type_id) {
        $meta = $map->getChild();

        if (!($meta instanceof Fields_Model_Meta)) {
            return '';
        }

        // Prepare translations
        $translate = Zend_Registry::get('Zend_Translate');

        $key = $map->getKey();
        $label = $this->view->translate($meta->label);
        $type = $meta->type;
        $field_id = $meta->field_id;
        
        $typeLabel = Engine_Api::_()->fields()->getFieldInfo($type, 'label');        
        $typeLabel = $this->view->translate($typeLabel);
        
        // Generate
        $contentClass = 'admin_field ' . $this->_generateClassNames($key, 'admin_field_');

        // Prepare params
        if ($meta->type == 'heading') {
            $containerClass = 'heading';
            $content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}">
    <span class='{$containerClass}'>
      <div class='item_handle'>
        &nbsp;
      </div>
      <div class='item_title'>
        {$label}
      </div>
    </span>
  </li>
EOF;
        } else {
            $table = Engine_Api::_()->getDbtable('weights', 'profileCompleteness');
            $select = $table->select()
                    ->where('type_id = ?', $type_id)
                    ->where('field_id = ?', $field_id);
            $row = $table->fetchRow($select);
            $containerClass = 'field';
            // Prepare params
            $weight = ($row->weight) ? ($row->weight) : 0;
            $content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}">
    <span class='{$containerClass}'>
      <div class='item_handle'>
        &nbsp;
      </div>
      <div class='item_options'>
        <input id="{$field_id}" type="text" value="{$weight}" name="{$row->field_id}"> 
      </div>
      <div class='item_title'>
        {$label}
      </div>
    </span>
  </li>
EOF;
        }
        
        return $content;
    }

    protected function _generateClassNames($key, $prefix = '') {
        list($parent_id, $option_id, $child_id) = explode('_', $key);
        return
        $prefix . 'parent_' . $parent_id . ' ' .
        $prefix . 'option_' . $option_id . ' ' .
        $prefix . 'child_' . $child_id
        ;
    }

}

?>
