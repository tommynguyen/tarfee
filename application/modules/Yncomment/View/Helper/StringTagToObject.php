<?php
class Yncomment_View_Helper_StringTagToObject extends Zend_View_Helper_Abstract {

    public function stringTagToObject($content, $actionParams) {
        if(isset($actionParams['tags']))
        {
            foreach ((array) $actionParams['tags'] as $key => $tagStrValue) {
                $tag = Engine_Api::_() -> getItemByGuid($key);
                if (!$tag) {
                    continue;
                }
                $replaceStr = '<a ' . 'href="' . $tag -> getHref() . '" ' . 'rel="' . $tag -> getType() . ' ' . $tag -> getIdentity() . '" >' . $tag -> getTitle() . '</a>';
                $content = preg_replace("/" . preg_quote($tagStrValue) . "/", $replaceStr, $content);
            }
        }
        return $content;
    }
}
