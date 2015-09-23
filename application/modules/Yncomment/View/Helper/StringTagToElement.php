<?php
class Yncomment_View_Helper_StringTagToElement extends Zend_View_Helper_Abstract {
    public function stringTagToElement($content, $actionParams) 
    {
        if(isset($actionParams['tags']))
        {
            foreach ((array) $actionParams['tags'] as $key => $tagStrValue) 
            {
                $replaceStr = 'yncomment_span_open class=yncomment_quotationyncomment_composer_tagyncomment_quotation ' . 'rel=yncomment_quotation' . $key . 'yncomment_quotation ' . 'rev=yncomment_quotation' . $tagStrValue . 'yncomment_quotation yncomment_close' . $tagStrValue . 'yncomment_span_close';
                $content = preg_replace("/" . preg_quote($tagStrValue) . "/", $replaceStr, $content);
            }
        }
        $content = preg_replace ('/<a[^>]*>/mi', '', $content); 
        $content = preg_replace ('/<\/a>/mi', '', $content); 
        return $content;
    }
}
