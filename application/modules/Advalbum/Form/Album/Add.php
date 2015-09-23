<?php
class Advalbum_Form_Album_Add extends Engine_Form
{
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $this->setTitle('Add New Virtual Album');

    $this->addElement('Text', 'title', array(
      'label' => 'Album Title',
      'required' => true,
      'notEmpty' => true,
      'validators' => array(
        'NotEmpty',
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '64'))
      )
    ));
    $this->title->getValidator('NotEmpty')->setMessage("Please specify an album title");

    // prepare categories
    $categories = Engine_Api::_()->advalbum()->getCategories();
    if (count($categories)!=0){
      $categories_prepared[0]= "";
      foreach ($categories as $category){
        $categories_prepared[$category->category_id]= $category->category_name;
      }

      // category field
      $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $categories_prepared
          ));
    }

    $this->addElement('Textarea', 'description', array(
      'label' => 'Album Description',
      'rows' => 2,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      )
    ));

    $this->addElement('Checkbox', 'search', array(
      'label' => "Show this album in search results",
    ));

    // View
    $availableLabels = array(
      'everyone'       => 'Everyone',
      'owner_network' => 'Friends and Networks',
      'owner_member_member'  => 'Friends of Friends',
      'owner_member'         => 'Friends Only',
      'owner'          => 'Just Me'
    );

    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('advalbum_album', $user, 'auth_view');
    $options = array_intersect_key($availableLabels, array_flip($options));

    // View
    $this->addElement('Select', 'auth_view', array(
      'label' => 'Privacy',
      'description' => 'Who may see this album?',
      'multiOptions' => $options,
    ));

    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('advalbum_album', $user, 'auth_comment');
    $options = array_intersect_key($availableLabels, array_flip($options));

    // Comment
    $this->addElement('Select', 'auth_comment', array(
      'label' => 'Comment Privacy',
      'description' => 'Who may post comments on this album?',
      'multiOptions' => $options
    ));

    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('advalbum_album', $user, 'auth_tag');
    $options = array_intersect_key($availableLabels, array_flip($options));

    // Submit!
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Album',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));

   
	$this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
