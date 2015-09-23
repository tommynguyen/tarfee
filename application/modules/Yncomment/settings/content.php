<?php
$multiOptions = array('friends' => 'Friends');
$multiComposerOptions = array('addLink' => 'Link');
$include = array('group', 'album', 'advalbum', 'advgroup');
$module_table = Engine_Api::_()->getDbTable('modules', 'core');
$module_name = $module_table->info('name');
$select = $module_table->select()
        ->from($module_name, array('name', 'title'))
        ->where($module_name . '.type =?', 'extra')
        ->where($module_name . '.name in(?)', $include)
        ->where($module_name . '.enabled =?', 1);

$contentModule = $select->query()->fetchAll();
$include[] = 'friends';
foreach ($contentModule as $module) {
    if ($module['name'] != 'album' && $module['name'] != 'advalbum')
        $multiOptions[$module['name']] = $module['title'];
    if ($module['name'] == 'album' || $module['name'] == 'advalbum')
        $multiComposerOptions['addPhoto'] = 'Photo';
}

$multiComposerOptions['addSmilies'] = 'Emoticons';
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$content_array = array(
    array(
        'title' => 'Advanced Comments & Replies',
        'description' => 'Enable users to comment and reply on the content being viewed. Displays all the comments and replies on the Content View page. This widget should be placed on Content View page.',
        'category' => 'Advanced Comments',
        'type' => 'widget',
        'name' => 'yncomment.comments',
        'autoEdit' => 'true',
        'defaultParams' => array(
            'title' => '',
            'taggingContent' => array("friends", "group", "event")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'taggingContent',
                    array(
                        'label' => "Which Content Type do you want to tag in comments/replies? (‘@’ symbol is used)?",
                        'multiOptions' => $multiOptions,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'showComposerOptions',
                    array(
                        'label' =>  "Which attachment type do you want to add into comments/replies?",
                        'multiOptions' => $multiComposerOptions,
                    )
                ),
                array(
                    'Radio',
                    'showAsNested',
                    array(
                        'label' => "Do you want to enable “reply” link on  a comment in this plugin’s content? (If no, there is only one-level comment, user will not able to reply on the comment)",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'showAsLike',
                    array(
                        'label' => "Which option of Like/Dislike do you want to use?",
                        'multiOptions' => array(
                            1 => 'Like only',
                            0 => 'Both Like and Dislike'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'showDislikeUsers',
                    array(
                        'label' => "Do you want to show the user’s name who dislike the comments/replies? [Note: this setting will work only if both Like and Dislike setting is set]",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'showLikeWithoutIcon',
                    array(
                        'label' => 'How do you want to display Like, Dislike for this plugin’s content?',
                        'multiOptions' => array(
                            1 => 'Text only',
                            0 => 'Text with icon'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'showLikeWithoutIconInReplies',
                    array(
                        'label' => "How do you want to display Like, Dislike for comments/replies?",
                        'multiOptions' => array(
                            1 => 'Text only',
                            2 => 'Icon only',
                            0 => 'Text with icon',
                            3 => 'Vote up/Vote down? [Note: this setting will work only if both Like and Dislike setting is set]'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'commentsorder',
                    array(
                            'label' => 'Select the order in which comments should be displayed on your website.',
                            'multiOptions' => array(
                                    1 => 'Newer to older',
                                    0 => 'Older to newer'
                            ),
                            'value' => 1,
                    )
              ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Do you want this widget content  to be loaded by AJAX? (If yes, this action can improve webpage loading speed. If no, this action will load widget content along with page content)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
        'requirements' => array(
            'subject',
        ),
    ),
);

return $content_array;
?>