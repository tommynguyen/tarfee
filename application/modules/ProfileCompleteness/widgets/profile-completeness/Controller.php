<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ProfileCompleteness_Widget_ProfileCompletenessController extends Engine_Content_Widget_Abstract
{

    private function sum($filled, $empty)
    {
        $sum = 0;
        foreach ($filled as $key => $value)
        {
            $sum += $value;
        }
        foreach ($empty as $key => $value)
        {
            $sum += $value;
        }
        return $sum;
    }

    private function getPercentInfoProfileCompleted($filled, $empty)
    {
        if (empty($empty))
        {
            return 100;
        }
        if (empty($filled))
        {
            return 0;
        }

        $sum = $this->sum($filled, $empty);
        $per = 0;

        foreach ($filled as $key => $value)
        {
            $per += $value / $sum;
        }
        return round($per * 100);
    }

    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity())
        {
            if (Engine_Api::_()->core()->hasSubject())
            {
                $subject = Engine_Api::_()->core()->getSubject('user');
                if ($subject->user_id != $viewer->user_id)
                {
                    return $this->setNoRender();
                }
            }
            $emptyField = array();
            $filledField = array();

            $table = Engine_Api::_()->getDbtable('weights', 'profileCompleteness');
			// check profile photo
            if ($viewer->photo_id != 0)
            {
                $filledField['photo'] = $table -> getGlobalWeight(0);
            }
            else
            {
                $emptyField['photo'] = $table -> getGlobalWeight(0);
            }
			
			// check profile url
            if ($viewer->username != "")
            {
                $filledField['username'] = $table -> getGlobalWeight(-4);
            }
            else
            {
                $emptyField['username'] = $table -> getGlobalWeight(-4);
            }
			
			/*
			// check sport like/follow
			if($table -> isSportLikeOrFollow())
			{
				$filledField['sportlike'] = $table -> getGlobalWeight(-1);
			}
			else
			{
				$emptyField['sportlike'] = $table -> getGlobalWeight(-1);
			}*/
			
			// check club follow
			if($table -> isClubFollow())
			{
				$filledField['clubfollow'] = $table -> getGlobalWeight(-2);
			}
			else
			{
				$emptyField['clubfollow'] = $table -> getGlobalWeight(-2);
			}
			
			// check video uplaod
			if($table -> isVideoUpload())
			{
				$filledField['videoupload'] = $table -> getGlobalWeight(-2);
			}
			else
			{
				$emptyField['videoupload'] = $table -> getGlobalWeight(-2);
			}

            $select = $table->select()->setIntegrityCheck(false);
            $select->from(array('v' => 'engine4_user_fields_values'))
                    ->where("v.item_id = ? AND v.field_id = 1", $viewer->getIdentity());
            $row = $table->fetchRow($select);
            $user_type = $row->value;

            $select = $table->select()->setIntegrityCheck(false);
            $select->from(array('w' => 'engine4_profilecompleteness_weights'))
                    ->where('w.type_id = ?', $user_type);
            $rows = $table->fetchAll($select);
            foreach ($rows as $row)
            {
                $select = $table->select()->setIntegrityCheck(false);
                $select->from(array('v' => 'engine4_user_fields_values'))
                        ->where('v.item_id = ?', $viewer->getIdentity())
                        ->where('v.field_id = ?', $row->field_id);
                $r = $table->fetchRow($select);
                $select = $table->select()->setIntegrityCheck(false);
                $select->from(array('map' => 'engine4_user_fields_maps'), array())
                        ->from(array('meta' => 'engine4_user_fields_meta'), array('meta.label'))
                        ->where('map.field_id = 1')
                        ->where('map.option_id = ?', $row->type_id)
                        ->where('map.child_id = ?', $row->field_id)
                        ->where('map.child_id = meta.field_id');
                $r1 = $table->fetchRow($select);

				if (is_object($r) && $r->value != '')
                {
                    $filledField[$r1->label] = $row->weight;
                }
                else if (is_object($r1))
                {
                    $emptyField[$r1->label] = $row->weight;
                }
            }

            $select = $table->select()->setIntegrityCheck(false);
            $select->from(array('s' => 'engine4_profilecompleteness_settings'));
            $row = $table->fetchRow($select);
            $this->view->color = $row->color;
            $this->view->percent_completed = $this->getPercentInfoProfileCompleted($filledField, $emptyField);

            if (empty($emptyField) || ($this->view->percent_completed == 100))
            {
                if ($row->view == 1)
                {
                    return $this->setNoRender();
                }
            }
            arsort($emptyField);
            $this->view->emptyField = $emptyField;
            $this->view->sum = $this->sum($filledField, $emptyField);
            $this->view->link_UpdateProfile = $this->view->htmlLink(array(
                'route' => 'default',
                'module' => 'user',
                'controller' => 'edit',
                'action' => 'profile'), Zend_Registry::get('Zend_Translate')->_('Update Profile')
            );
        }
        else
        {
            $this->setNoRender();
        }
    }

}

?>
