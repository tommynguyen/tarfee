<?php
class Yncomment_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onRenderLayoutDefault($event) {
        $view = $event -> getPayload();
        $view -> headLink() 
                -> appendStylesheet($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/styles/style_comment.css') 
                -> appendStylesheet($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/styles/style_yncomment.css')
                -> appendStylesheet($view -> layout() -> staticBaseUrl . 'externals/fancyupload/fancyupload.css');

        $view -> headScript() -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/composer_feed_comment.js');
        $view -> headScript() -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/composer_feed_comment_tag.js');
        $view -> headScript() -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/comment_photo.js');
        $view -> headScript() 
                -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/core.js') 
                -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/composer.js') 
                -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/composer_tag.js') 
                -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/like.js') 
                -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/composer_photo.js') 
                -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Yncomment/externals/scripts/composer_link.js')
                -> appendFile($view -> layout() -> staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
                -> appendFile($view -> layout() -> staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
                -> appendFile($view -> layout() -> staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
    }

    public function onRenderLayoutMobileDefault($event) {
        return $this -> onRenderLayoutDefault($event);
    }
    public function onMenuInitialize_YncommentAdminMainActivitySettings()
    {
        $ynfeed = Engine_Api::_()->yncomment()->getEnabledModule(array('resource_type' => 'ynfeed', 'checkModuleExist' => true));
        if($ynfeed && Engine_Api::_() -> hasModuleBootstrap('ynfeed'))
        {
            return true;
        }
        return false;
    }
}
