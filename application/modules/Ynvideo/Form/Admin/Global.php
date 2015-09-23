<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Form_Admin_Global extends Engine_Form {

    public function init() {
        $this->setTitle('Global Settings')->setDescription('These settings affect all members in your community.');

        $this->addElement('Text', 'ynvideo_ffmpeg_path', array(
            'label' => 'Path to FFMPEG',
            'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.ffmpeg.path', ''),
        ));
		
		$this->addElement('Radio', 'ynvideo_title_require', array(
	      'label' => 'Video Title',
	      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.title_require', 1),
	      'multiOptions' => array(
	        '1' => 'Mandatory.',
	        '0' => 'Optional.',
	      ),
	    ));
		
        $this->addElement('Text', 'ynvideo_jobs', array(
            'label' => 'Encoding Jobs',
            'description' => 'How many jobs do you want to allow to run at the same time?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.jobs', 2),
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(1),
            ),
        ));

        $this->addElement('Text', 'ynvideo_page', array(
            'label' => 'Listings Per Page',
            'description' => 'How many videos will be shown per page? (Enter a number between 1 and 999)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.page', 10),
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(1),
            ),
        ));

        $this->addElement('Radio', 'ynvideo_embeds', array(
            'label' => 'Allow Embedding of Videos?',
            'description' => 'Enabling this option will give members the ability to embed videos on this site in other pages using an iframe (like YouTube).',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.embeds', 1),
            'multiOptions' => array(
                '1' => 'Yes, allow embedding of videos.',
                '0' => 'No, do not allow embedding of videos.',
            ),
        ));
        
        $this->addElement('Text', 'ynvideo_number_category_per_page', array(
            'label' => 'Category Listings Per Page',
            'description' => 'Number of categories per page in the listing page? (Enter a number between 1 and 50)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.number.category.per.page', 5),
            'validators' => array(
                                array('Int', true),
                                array('GreaterThan', true, array(0)),
                                array('LessThan', true, array(51)),
                            )
        ));
        
        $this->addElement('Text', 'ynvideo_number_category_per_page', array(
            'label' => 'Category Listings Per Page',
            'description' => 'Number of categories per page in the listing page? (Enter a number between 1 and 50)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.number.category.per.page', 5),
            'validators' => array(
                                array('Int', true),
                                array('GreaterThan', true, array(0)),
                                array('LessThan', true, array(51)),
                            )
        ));
        
        $this->addElement('Text', 'ynvideo_per_category', array(
            'label' => 'Listings Per Category',
            'description' => 'Number of videos per category in the listing page? (Enter a number between 1 and 20)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.per.category', 5),
            'validators' => array(
                                array('Int', true),
                                array('GreaterThan', true, array(0)),
                                array('LessThan', true, array(21)),
                            )
        ));
        
        $this->addElement('Text', 'ynvideo_playlist_per_page', array(
            'label' => 'Playlists Per Page',
            'description' => 'Number of playlist in the My Playlist page? (Enter a number between 1 and 999)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.playlist.per.page', 10),
            'validators' => array(
                                array('Int', true),
                                array('GreaterThan', true, array(0)),
                                array('LessThan', true, array(1000)),
                            )
        ));
        
        $this->addElement('Text', 'ynvideo_friend_emails', array(
            'label' => 'Number of email',
            'description' => 'Number of email about a video a person can send at each time? (Enter a number between 1 and 50)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.friend.emails', 5),
            'validators' => array(
                                array('Int', true),
                                array('GreaterThan', true, array(0)),
                                array('LessThan', true, array(51)),
                            )
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}