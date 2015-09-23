<?php
class Ynresponsive1_Api_Core extends Core_Api_Abstract {

    protected $_isMobile = null;
    function isMobile() {
        if (null === $this -> _isMobile) {
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                if (preg_match('/(android|iphone|ipad|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $user_agent)) {
                    $this -> _isMobile = true;
                }

            } else {
                $this -> _isMobile = false;
            }
        }
        return $this -> _isMobile;
    }

    public function getGroupTimeAgo($group) {
        $view = Zend_Registry::get("Zend_View");
        $now = time();
        $timeAgo = "Active ";
        $date = strtotime($group -> creation_date);
        $years = date('Y', $now) - date("Y", $date);
        $months = date('n', $now) - date("n", $date);
        $days = date('j', $now) - date("j", $date);
        if ($months < 0) {
            $months = 12 + $months;
            $years = $years - 1;
        }
        if ($days < 0) {
            $days = 30 + $days;
            $months = $months - 1;
        }
        if ($years) {
            $timeAgo .= $view -> translate(array("%s year ago", "%s years ago", $years), $years);
            return $timeAgo;
        } else if ($months) {
            $timeAgo .= $view -> translate(array("%s month ago", "%s months ago", $months), $months);
            return $timeAgo;
        } else if ($days) {
            $timeAgo .= $view -> translate(array("%s day ago", "%s days ago", $days), $days);
            return $timeAgo;
        } else
            return $view -> translate("Active today");
    }

}
