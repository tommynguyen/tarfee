<?php

class Yntour_AdminManageController extends Core_Controller_Action_Admin
{
    public function switchModeAction()
    {
        $mode = $this -> _getParam('yntourmode', 'disabled');
        $this -> _helper -> layout -> disableLayout();
        $api = Engine_Api::_() -> getApi('settings', 'core');
        $api -> setSetting('yntourmode', $mode);

    }

    public function indexAction()
    {
        $request = $this -> getRequest();

        if ($request -> isPost())
        {
            $model = new Yntour_Model_DbTable_Tours;
            foreach ($request->getPost() as $key => $value)
            {
                $item = $model->find((int)$value)->current();
                if(is_object($item)){
                    $item->delete();
                }                
            }
        }
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yntour_admin_main', array(), 'yntour_admin_main_manage');

        $params = array();
        $select = Engine_Api::_() -> yntour() -> getTourPagination();

        $this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
        $pageNumber = $this -> _getParam('page', 1);
        $paginator -> setCurrentPageNumber($pageNumber);
        $paginator -> setItemCountPerPage(10);

    }

    public function createAction()
    {
        $form = $this -> view -> form = new Yntour_Form_Admin_Tour_Create;
        $request = $this -> getRequest();

        if ($request -> isGet())
        {
            return;
        }

        if ($request -> isPost() && $form -> isValid($request -> getPost()))
        {
            $data = $form -> getValues();
            $model = new Yntour_Model_DbTable_Tours;
            $item = $model -> fetchNew();
            $item -> setFromArray($data);
            $item -> creation_date = date('Y-m-d H:i:s');
            if ($item -> save())
            {
                $this -> _redirect('admin/yntour/manage');
            }

        }
    }

    public function editAction()
    {
        $form = $this -> view -> form = new Yntour_Form_Admin_Tour_Edit;
        $request = $this -> getRequest();

        $id = $this -> _getParam('id');

        $model = new Yntour_Model_DbTable_Tours;

        $item = $model -> find($id) -> current();
        
        if ($request -> isGet())
        {
            $form -> populate($item -> toArray());
            return;
        }

        if ($request -> isPost() && $form -> isValid($request -> getPost()))
        {
            $data = $form -> getValues();    
            $item -> setFromArray($data);
            if ($item -> save())
            {
                $router = Zend_Controller_Front::getInstance() -> getRouter();
                $url = $router -> assemble(array('action' => 'index'), null, false);
                $this -> _helper -> redirector -> setPrependBase(false) -> gotoUrl($url);
            }

        }
    }

    public function deleteAction()
    {
        $form = $this -> view -> form = new Yntour_Form_Admin_Tour_Delete;

        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('id');

        $model = new Yntour_Model_DbTable_Tours;

        // Check post
        if ($this -> getRequest() -> isPost())
        {
            $db = $model -> getAdapter();
            $db -> beginTransaction();

            try
            {

                $item = $model -> find($id) -> current();
                // delete the blog entry into the database
                $item -> delete();
                $db -> commit();
            }

            catch( Exception $e )
            {
                $db -> rollBack();
                throw $e;
            }

            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Successful.')
            ));
        }
    }

    public function itemAction()
    {
        $request =  $this->getRequest();
        if ($request -> isPost())
        {
            $model = new Yntour_Model_DbTable_Touritems;
            foreach ($request->getPost() as $key => $value)
            {
                $item = $model->find((int)$value)->current();
                if(is_object($item)){
                    $item->delete();
                }                
            }
        }
        
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yntour_admin_main', array(), 'yntour_admin_main_item');
        $id = $this -> _getParam('id', 0);

        if (!$id)
        {
            $id = Engine_Api::_() -> yntour() -> getFirstTourId();
            $router = Zend_Controller_Front::getInstance() -> getRouter();
            $url = $router -> assemble(array(
                'action' => 'item',
                'id' => $id
            ), null, false);
            $this -> _helper -> redirector -> setPrependBase(false) -> gotoUrl($url);
        }
		
		$this->view->tid =  $id;
        $params = array('tour_id' => $id);
        $select = Engine_Api::_() -> yntour() -> getTouritemPagination($params);

        $this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
        $pageNumber = $this -> _getParam('page', 1);
        $paginator -> setCurrentPageNumber($pageNumber);
        $paginator -> setItemCountPerPage(10);

    }

    public function itemCreateAction()
    {
        $form = $this -> view -> form = new Yntour_Form_Admin_Touritem_Create;
        $request = $this -> getRequest();

        if ($request -> isGet())
        {
            return;
        }

        if ($request -> isPost() && $form -> isValid($request -> getPost()))
        {
            $data = $form -> getValues();
            $model = new Yntour_Model_DbTable_Touritems;
            $item = $model -> fetchNew();
            $item -> setFromArray($data);
            $item -> creation_date = date('Y-m-d H:i:s');
            if ($item -> save())
            {
                $this -> _redirect('admin/yntour/manage');
            }

        }
    }

    public function itemEditAction()
    {
        $tourId = $this -> _getParam('id', 0);

        $form = $this -> view -> form = new Yntour_Form_Admin_Touritem_Edit( array('tourId' => $tourId));
        $request = $this -> getRequest();

        $id = $this -> _getParam('item_id');

        $model = new Yntour_Model_DbTable_Touritems;

        $item = $model -> find($id) -> current();
        
        $array = $item -> toArray();
        
        $languages = $item->getLanguages();
        if(count($languages) > 0)
        {
            foreach($languages as $language)
            {
                $array['body_'.$language->language] =  $language->body;
            }
        }
        else
        {
           $array['body_'.$this->view->translate()->getLocale()]  = $array['body'];
        }
        if ($request -> isGet())
        {
            $form -> populate($array);
            return;
        }

        if ($request -> isPost() && $form -> isValid($request -> getPost()))
        {
            $data = $form -> getValues();
            $item -> setFromArray($data);
            $item -> body = $data['body_en'];
            //Save all item body to itemlanguages
            $model_language = new Yntour_Model_DbTable_Itemlanguages;
            $translate    = Zend_Registry::get('Zend_Translate');
            $languageList = $translate->getList();
            foreach($languageList as $lang)
            {
                $model_language->updateLanguage($id, $data['body_'.$lang], $lang);
            }
            if ($item -> save())
            {
                $router = Zend_Controller_Front::getInstance() -> getRouter();
                $url = $router -> assemble(array('action' => 'item'), null, false);
                $this -> _helper -> redirector -> setPrependBase(false) -> gotoUrl($url);
            }

        }
    }

    public function itemDeleteAction()
    {
        	
        $form = $this -> view -> form = new Yntour_Form_Admin_Touritem_Delete;
		

        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('item_id');

        $model = new Yntour_Model_DbTable_Touritems;

        // Check post
        if ($this -> getRequest() -> isPost())
        {
            $db = $model -> getAdapter();
            $db -> beginTransaction();

            try
            {

                $item = $model -> find($id) -> current();
                // delete the blog entry into the database
                $item -> delete();
                $db -> commit();
            }

            catch( Exception $e )
            {
                $db -> rollBack();
                throw $e;
            }

            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Successful.')
            ));
        }
    }

}
