<?php
class Ynfeedback_Widget_MostPopularIdeasController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $table = Engine_Api::_()->getItemTable('ynfeedback_idea');
        $select = $table->getIdeasSelect(array('orderby' => 'vote_count', 'direction' => 'DESC'));
        $select->limit(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfeedback_max_idea', 20));
        $this->view->ideas = $table->fetchAll($select);
    }
}
