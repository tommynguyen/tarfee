<?php
class Yncomment_View_Helper_FeedNCFluentList extends Zend_View_Helper_Abstract {

    /**
     * Generates a fluent list of item. Example:
     *   You
     *   You and Me
     *   You, Me, and Jenny
     * 
     * @param array|Traversable $items
     * @return string
     */
    public function feedNCFluentList($items, $translate = false, $action) {
        if (0 === ($num = count($items))) {
            return '';
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $comma = $this->view->translate(',');
        $and = $this->view->translate('and');
        $index = 0;
        $content = '';

        if ($action->likes()->getLike($viewer)) {
            if ($num == 1) {
                return $content = $this->view->translate("You ");
            } elseif ($num == 2) {
                $num = ($num - 1);
                $url = $this->view->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'activity-like', 'action_id' => $action->getIdentity(), 'call_status' => 'public', 'other' => 0, 'notIncludedId' => $viewer->getIdentity()), 'default', true);

                $escapedURL = $this->view->string()->escapeJavascript($url);
                $link = "onclick=Smoothbox.open('$escapedURL')";
                return $content = $this->view->translate('You and %1$s%3$s other%2$s', "<a href='javascript:void(0);' $link>", "</a>", $num);
            } elseif ($num > 2) {
                $num = ($num - 1);
                $url = $this->view->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'activity-like', 'action_id' => $action->getIdentity(), 'call_status' => 'public', 'other' =>0, 'notIncludedId' => $viewer->getIdentity()), 'default', true);
                $escapedURL = $this->view->string()->escapeJavascript($url);
                $link = "onclick=Smoothbox.open('$escapedURL')";
                return $content = $this->view->translate('You and %1$s%3$s others%2$s', "<a href='javascript:void(0);' $link>", "</a>", $num);
            }
        } else {
            foreach ($items as $item) {
                $href = null;
                $title = null;
                $guid = null;
                if (is_object($item)) {
                    if (method_exists($item, 'getTitle') && method_exists($item, 'getHref')) {
                        $href = $item->getHref();
                        $title = $item->getTitle();
                    } else if (method_exists($item, '__toString')) {
                        $title = $item->__toString();
                    } else {
                        $title = (string) $item;
                    }

                    if (method_exists($item, 'getGuid')) {
                        $guid = $item->getType() . ' ' . $item->getIdentity();
                    }
                } else {
                    $title = (string) $item;
                }

                if ($translate) {
                    $title = $this->view->translate($title);
                }

                if ($num == 1) {
                    if (null === $href) {
                        $content .= $title;
                    } else {
                        $content .= $this->view->htmlLink($href, $title, array('class' => 'yncomment_add_tooltip_link',
                            'rel' => $guid));
                    }
                    return $content;
                } elseif ($num > 1) {
                    if (null === $href) {
                        $content .= $title;
                    } else {
                        $content .= $this->view->htmlLink($href, $title, array('class' => 'yncomment_add_tooltip_link',
                            'rel' => $guid));
                    }
                    $num = $num - 1;
                    $url = $this->view->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'activity-like', 'action_id' => $action->getIdentity(), 'call_status' => 'public', 'other' => 0, 'notIncludedId' => $item->getIdentity()), 'default', true);
                    $escapedURL = $this->view->string()->escapeJavascript($url);
                    $link = "onclick=Smoothbox.open('$escapedURL')";
                    if ($num == 1) {
                        if ($action->likes()->getLike($viewer)) {
                            return $content .= $this->view->translate(' and %1$s%3$s other%2$s', "<a href='javascript:void(0);' $link>", "</a>", $num);
                        } else {
                            return $content .= $this->view->translate(' and %1$s%3$s other%2$s', "<a href='javascript:void(0);' $link>", "</a>", $num);
                        }
                    } else {
                        if ($action->likes()->getLike($viewer)) {
                            return $content .= $this->view->translate(' and %1$s%3$s others%2$s', "<a href='javascript:void(0);' $link>", "</a>", $num);
                        } else {
                            return $content .= $this->view->translate(' and %1$s%3$s others%2$s', "<a href='javascript:void(0);' $link>", "</a>", $num);
                        }
                    }
                }
            }
        }
    }

}