<?php

class Yntour_IndexController extends Core_Controller_Action_Standard
{

    protected function _load($path,$bodyid)
    {
        
        $model = new Yntour_Model_DbTable_Tours;
        
        $select = $model->select()->where('bodyid=?',$bodyid)->orWhere('path=?', $path);
        
         $setting = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yntourmode', 'disable');

        if ($setting == 'disabled')
        {
            $select -> where('enabled=?', 1);
        }
        
        $tour = $model -> fetchRow($select);
        
        // tour exists
        if(!is_object($tour)){
            return ;
        }
        
        if($tour->path != $path && $tour->option == 1){
            return ;
        }
        
       

        

        if (!is_object($tour))
        {
            return array(
                'id' => 0,
                'total' => 0,
                'rows' => array(),
                'title'=>'',
                'autoplay' => 0,
                'autoclose'=>0,
                'autoclose_time_delay'=>0,
                'hash' => '',
                'view_rule'=>'all'
            );
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
       
        $result = array(
            'hash' => $tour -> hash,
            'total' => count($temp_rows),
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

    public function checkAction()
    {
        /*
        $request = $this -> getRequest();
        $this -> _helper -> layout -> disableLayout();
        $uri = parse_url(urldecode($this -> _getParam('path')));
        $path =  trim($uri['path']);
		$baseURL = $request ->getBaseUrl();
		$path = str_replace($baseURL,'', $path);        
        $bodyid = $this->_getParam('bodyid');
        $result = $this -> _load($path,$bodyid);
        echo Zend_Json::encode($result);
        */
        return;
    }

    public function editTourAction()
    {
        
        $form = $this -> view -> form = new Yntour_Form_Tour_Create;
               
        $model = new Yntour_Model_DbTable_Tours;

        $id = $this -> _getParam('tour_id', 0);
        
        $item = $model -> find($id) -> current();

        $request = $this -> getRequest();
               
        if ($request -> isGet())
        {
            if (is_object($item))
            {
                $form -> populate($item -> toArray());
            }
            else
            {                
                $uri = parse_url($request -> getServer('HTTP_REFERER'));                
                $path =  trim($uri['path']);
				$baseURL = $request ->getBaseUrl();
				$path = str_replace($baseURL,'', $path);
                $bodyid = $this->_getParam('body','global_page_user-index-home');
                $form -> populate(array('path' => $path,'bodyid'=>$bodyid));                
            }
            return;
        }

        if ($request -> isPost() && $form -> isValid($request -> getPost()))
        {
            $data = $form -> getValues();
            if (!is_object($item))
            {
                $item = $model -> fetchNew();
                $item -> creation_date = date('Y-m-d H:i:s');
                $item->setPath($form->getValue('path'));
                $item -> hash = md5(mt_rand(0, 999999) . mt_rand(0, 999999) . mt_rand(0, 99999), false);
            }
            $item -> setFromArray($data);
            if (!$item -> save())
            {
                throw      Exception("Invalid data");
            }
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Successful.')
            ));

        }
    }

    public function editStepAction()
    {
        
        $form = $this -> view -> form = new Yntour_Form_Touritem_Create;
		//https://jira.younetco.com/browse/CHECKDEMO-134 left & top doesn't have means when create new step
		$form->removeElement("left_position");
		$form->removeElement("top_position");
        $model = new Yntour_Model_DbTable_Touritems;

        $id = $this -> _getParam('touritem_id', 0);

        $request = $this -> getRequest();

        $language_select = $this->view->translate()->getLocale();
       
        if ($request -> isGet())
        {
            $x = $this->_getParam('x');
            $y = $this->_getParam('y');
            $form -> populate(array('left_position' => $x,'top_position'=>$y)); 
            return;
        }

        if ($request -> isPost() && $form -> isValid($request -> getPost()))
        {
            $data = $form -> getValues();
            $item = $model -> fetchNew();
            $item -> creation_date = date('Y-m-d H:i:s');
            $item -> setFromArray($data);
            $item -> tour_id = $request -> getParam('tour_id');
            $item -> dompath = str_replace('.is_active','',urldecode($request -> getParam('dompath')));
            $item -> save();
            $model_language = new Yntour_Model_DbTable_Itemlanguages;
            $model_language->updateLanguage($item->touritem_id, $data['body'], $language_select);
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Successful.')
            ));

        }
    }

}
