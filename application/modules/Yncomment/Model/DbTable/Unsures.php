<?php
class Yncomment_Model_DbTable_Unsures extends Engine_Db_Table {

    protected $_rowClass = 'Yncomment_Model_Unsure';
    protected $_custom = false;

    public function __construct($config = array()) {
        if (get_class($this) !== 'Yncomment_Model_DbTable_Unsures') {
            $this->_custom = true;
        }

        parent::__construct($config);
    }

    public function getUnsureTable() {
        return $this;
    }

    public function addUnsure(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        $row = $this->getUnsure($resource, $poster);
        if (null !== $row) {
            throw new Core_Model_Exception('Already dis-liked');
        }

        $table = $this->getUnsureTable();
        $row = $table->createRow();

        if (isset($row->resource_type)) {
            $row->resource_type = $resource->getType();
        }

        $row->resource_id = $resource->getIdentity();
        $row->poster_type = $poster->getType();
        $row->poster_id = $poster->getIdentity();
        $row->save();

        if (isset($resource->unsure_count)) {
            $resource->unsure_count++;
            $resource->save();
        }

        return $row;
    }

    public function removeUnsure(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        $row = $this->getUnsure($resource, $poster);
        if (null === $row) {
            throw new Core_Model_Exception('No unsure to remove');
        }

        $row->delete();

        if (isset($resource->unsure_count)) {
            $resource->unsure_count--;
            $resource->save();
        }

        return $this;
    }

    public function isUnsure(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        return ( null !== $this->getUnsure($resource, $poster) );
    }

    public function getUnsure(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        $table = $this->getUnsureTable();
        $select = $this->getUnsureSelect($resource)
                ->where('poster_type = ?', $poster->getType())
                ->where('poster_id = ?', $poster->getIdentity())
                ->limit(1);

        return $table->fetchRow($select);
    }

    public function getUnsureSelect(Core_Model_Item_Abstract $resource) {
        $select = $this->getUnsureTable()->select();

        if (!$this->_custom) {
            $select->where('resource_type = ?', $resource->getType());
        }

        $select
                ->where('resource_id = ?', $resource->getIdentity())
                ->order('unsure_id ASC');

        return $select;
    }

    public function getUnsurePaginator(Core_Model_Item_Abstract $resource) {
        $paginator = Zend_Paginator::factory($this->getUnsureSelect($resource));
        $paginator->setItemCountPerPage(3);
        $paginator->count();
        $pages = $paginator->getPageRange();
        $paginator->setCurrentPageNumber($pages);
        return $paginator;
    }

    public function getUnsureCount(Core_Model_Item_Abstract $resource) {
        if (isset($resource->unsure_count)) {
            return $resource->unsure_count;
        }
        $select = new Zend_Db_Select($this->getUnsureTable()->getAdapter());
        $select
                ->from($this->getUnsureTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

        if (!$this->_custom) {
            $select->where('resource_type = ?', $resource->getType());
        }

        $select->where('resource_id = ?', $resource->getIdentity());

        $data = $select->query()->fetchAll();
        return (int) $data[0]['count'];
       
    }

    public function getAllUnsures(Core_Model_Item_Abstract $resource) {
        return $this->getUnsureTable()->fetchAll($this->getUnsureSelect($resource));
    }

    public function getAllUnsuresUsers(Core_Model_Item_Abstract $resource) {
        $table = $this->getUnsureTable();
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), array('poster_type', 'poster_id'));

        if (!$this->_custom) {
            $select->where('resource_type = ?', $resource->getType());
        }

        $select->where('resource_id = ?', $resource->getIdentity());

        $users = array();
        foreach ($select->query()->fetchAll() as $data) {
            if ($data['poster_type'] == 'user') {
                $users[] = $data['poster_id'];
            }
        }
        $users = array_values(array_unique($users));

        return Engine_Api::_()->getItemMulti('user', $users);
    }
    
       /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function unsures($subject) {
        return new Engine_ProxyObject($subject, Engine_Api::_()->getDbtable('unsures', 'yncomment'));
    } 

}