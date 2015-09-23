<?php
class Yncomment_View_Helper_SmileyToEmoticons extends Zend_View_Helper_Abstract {

    public function smileyToEmoticons($string) 
    {
        $view = Zend_Registry::get('Zend_View');
        $baseUrl = $view -> layout() -> staticBaseUrl;
        foreach (Engine_Api::_() -> yncomment() -> getEmoticons() as $emoticon) {
            $string = str_replace($emoticon -> text, "<img class='emotions_use' title = '{$view -> translate(ucwords($emoticon -> title))}' src='{$baseUrl}application/modules/Yncomment/externals/images/emoticons/{$emoticon -> image}'/>", $string);
        }
        return $string;
    }
}
