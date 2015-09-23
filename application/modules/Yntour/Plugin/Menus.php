<?php

class Yntour_Plugin_Menus
{
    public function getTourMenu($row){
        $enabled  = Zend_Registry::isRegistered('YNTOUR_ENABLED')?Zend_Registry::get('YNTOUR_ENABLED'):false;
         
        if(!$enabled){
            return false;
        }

        return array(
            'label' => $row -> label,
            'uri' => 'javascript:en4.yntour.startTour();',
            'class' => 'no-dloader',
            'title' => 'Start a tour guide',
        );
    }
    public function onMenuInitialize_CoreMiniYntour2($row)
    {
        $enabled  = Zend_Registry::isRegistered('YNTOUR_ENABLED')?Zend_Registry::get('YNTOUR_ENABLED'):false;

        if(!$enabled){
            return false;
        }

        return array(
            'label' => $row -> label,
            'uri' => 'javascript:en4.yntour.startTour();',
            'class' => 'no-dloader',
            'title' => 'Start a tour guide',
        );
    }

    public function onMenuInitialize_CoreMiniYntour($row)
    {
        return $this->getTourMenu($row);
        
    }
	public function onMenuInitialize_CoreMiniYntouradv($row)
    {

        $enabled  = Zend_Registry::isRegistered('YNTOUR_ENABLED')?Zend_Registry::get('YNTOUR_ENABLED'):false;

        if(!$enabled){
            return false;
        }

        return array(
            'label' => $row -> label,
            'uri' => 'javascript:en4.yntour.startTour();',
            'class' => 'no-dloader',
            'title' => 'Start a tour guide',
        );
    }

    public function onMenuInitialize_YntourAdminMainItem($row)
    {
        if (!Engine_Api::_() -> yntour() -> getFirstTourId())
        {
            return false;
        }
        return $row;

    }

}
