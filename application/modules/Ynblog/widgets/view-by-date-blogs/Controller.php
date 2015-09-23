<?php
class Ynblog_Widget_ViewByDateBlogsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
      if(Engine_Api::_()->core()->hasSubject('user')){
        $user = Engine_Api::_()->core()->getSubject('user');
        $url_string = "talks/".$user->getIdentity();
      }
      else if( Engine_Api::_()->core()->hasSubject('blog') ) {
        $blog = Engine_Api::_()->core()->getSubject('blog');
        $user = $blog->getOwner();
        $url_string = "talks/".$user->getIdentity();
     }
     else{
       $url_string = "talks/listing";
      }
      $this->view->url_string = $url_string;
  }
}