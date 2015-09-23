<?php

class Yntour_Api_Core
{
    /**
     * @param string $path  etc  /member/home
     * @param string $bodyId  etc global_path-user-index-home
     * @return array|null
     * 
     */
    public function getTour($path,$bodyid)
    {
        $forceEnable  = false;
        //if(Zend_Registry::get('YNTOUR_STATUS_ENABLED')){
        //    $forceEnable = true;
        //}
               
        $result = array(
            'id' => 0,
            'enable' => false,
            'total' => 0,
            'rows' => array(),
            'title'=>'',
            'autoplay' => 0,
            'autoclose'=>0,
            'autoclose_time_delay'=>0,
            'hash' => '',
            'view_rule'=>'all'
        );
        
        $this->view  = Zend_Registry::get('Zend_View');
        
        $model = new Yntour_Model_DbTable_Tours;
        
        $select = $model->select()->where('bodyid=?',$bodyid)->orWhere('path=?', $path);
        $tour = $model -> fetchRow($select);
                
        // tour exists
        if(!is_object($tour)){
            return $result;
        }
        
        $viewer = Engine_Api::_()->user()->getViewer(); 
        $id = $viewer->getIdentity();
        
        if($tour->view_rule == 'members' && $id == 0){
            return $result;
        }
        //if($tour->view_rule == 'guests' && $viewer->level_id > 2)  
        //{
           //return $result;
        //}
        
        // 0: path only, check exact path
        // 1: body_id. all page that has same body id                        
        
        if($tour->path != $path && $tour->option == 0 || ($tour->enabled == 0 && $viewer->level_id > 2 ) ){//
            return $result;
        }
        
        if (!is_object($tour))
        {
            return $result;
        }

        $model = new Yntour_Model_DbTable_Touritems;
        $select = $model -> select() -> where('tour_id=?', $tour -> getIdentity()) -> order('priority');
        $rows = $model -> fetchAll($select) -> toArray();
        $temp_rows = array();
        
        $language_select = $this->view->translate()->getLocale();
        $model_language = new Yntour_Model_DbTable_Itemlanguages;
        foreach($rows as $row)
        {
            $body = $model_language->getLanguage($row['touritem_id'],$language_select)->body;
            if($body)
                $row['body'] = $body; 
            $row['width'] = intval($row['width']);    
            $temp_rows[] =  $row;
        }
        
        $total = count($temp_rows);
       
        $result = array(
            'hash' => $tour -> hash,
            'total' => $total,
            'enable' => $forceEnable | $total , 
            'id' => $tour -> getIdentity(),
            'title'=>$tour->title,
            'rows' => $temp_rows,
            'autoplay' => $tour -> autoplay,
            'autoclose'=>$tour->autoclose,
            'autoclose_time_delay'=>$tour->autoclose_time_delay,
            'hash' => $tour -> hash,
            'view_rule'=>$tour->view_rule
        );
        
        
        return $result;
    }
    
    public function getItemByPathHash($path_hash)
    {
        $model = new Yntour_Model_DbTable_Tours;
        $select = $model -> select() -> where('path_hash=?', $path_hash);
        $item = $model -> fetchRow($select);
        return $item;
    }
    public function getItemByPath($path)
    {
        $model = new Yntour_Model_DbTable_Tours;
        $select = $model -> select() -> where('path=?', $path);
        $item = $model -> fetchRow($select);
        return $item;
    }
    
    public function getItemByBodyId($bodyid)
    {
        $model = new Yntour_Model_DbTable_Tours;
        $select = $model -> select() -> where('bodyid=?', $bodyid);
        $item = $model -> fetchRow($select);
        return $item;
    }

    public function getTourPagination($params = array())
    {
        $model = new Yntour_Model_DbTable_Tours;
        $select = $model -> select();
        if (empty($params))
        {

            return $select;
        }

        return $select;

    }

    public function getTourItemPagination($params = array())
    {
        $model = new Yntour_Model_DbTable_Touritems;
        $select = $model -> select() -> order('priority');
        if (empty($params))
        {
            return $select;
        }

        if (isset($params['tour_id']) && ($tour_id = $params['tour_id']) != null)
        {
            $select -> where('tour_id=?', $tour_id);
        }
        return $select;

    }

    public function getFirstTourId()
    {
        $model = new Yntour_Model_DbTable_Tours;
        $item = $model -> fetchRow($model -> select());
        if (!is_object($item))
        {
            return 0;
        }
        return $item -> getIdentity();
    }

    public function getTourOptions()
    {
        $model = new Yntour_Model_DbTable_Tours;
        $select = $model -> select() -> from($model -> info('name'), array(
            'tour_id',
            'title'
        ));
        return $model -> getAdapter() -> fetchPairs($select);
    }

    public function getDelayRange()
    {
        for ($i = 1; $i < 60; ++$i)
        {

        }
    }

    public function loadTour($path, $mode)
    {        
        $model = new Yntour_Model_DbTable_Tours;
        $select = $model -> select() -> where('path=?', urldecode($path));

        $setting = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yntourmode', 'disable');
        
        if ($setting == 'disabled')
        {
            $select -> where('enabled=?', 1);
        }

        $tour = $model -> fetchRow($select);

        if (!is_object($tour))
        {
            return array(
                'id' => 0,
                'total' => 0,
                'rows' => array(),
                'autoplay' => 0,
                'autoclose' => 0,
                'autoclose_time_delay' => 0,
                'hash' => '',
                'view_rule' => 'all'
            );
        }

        $model = new Yntour_Model_DbTable_Touritems;
        $select = $model -> select() -> where('tour_id=?', $tour -> getIdentity()) -> order('priority');
        $rows = $model -> fetchAll($select) -> toArray();

        $result = array(
            'hash' => $tour -> hash,
            'total' => count($rows),
            'id' => $tour -> getIdentity(),
            'rows' => $rows,
            'autoplay' => $tour -> autoplay,
            'autoclose' => $tour -> autoclose,
            'autoclose_time_delay' => $tour -> autoclose_time_delay,
            'hash' => $tour -> hash,
            'view_rule' => $tour -> view_rule
        );
        return $result;

    }

}
