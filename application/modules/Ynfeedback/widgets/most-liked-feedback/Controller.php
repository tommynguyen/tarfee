<?php
class Ynfeedback_Widget_MostLikedFeedbackController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$params = array(
            'orderby' => 'like_count',
            'direction' => 'DESC'
        );
        $ideaTbl = Engine_Api::_()->getItemTable('ynfeedback_idea');
        $this -> view -> paginator = $paginator = $ideaTbl -> getIdeasPaginator($params);
        $itemCountPerPage = $this -> _getParam('itemCountPerPage', 3);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 3;
        }
        $paginator -> setItemCountPerPage($itemCountPerPage);
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
        if (!$paginator -> getTotalItemCount()) {
            $this -> setNoRender();
        }
    }
}
