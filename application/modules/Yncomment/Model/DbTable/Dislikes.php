<?php
class Yncomment_Model_DbTable_Dislikes extends Engine_Db_Table {

    protected $_rowClass = 'Yncomment_Model_Dislike';
    protected $_custom = false;

    public function __construct($config = array()) {
        if (get_class($this) !== 'Yncomment_Model_DbTable_Dislikes') {
            $this->_custom = true;
        }

        parent::__construct($config);
    }

    public function getDislikeTable() {
        return $this;
    }

    public function addDislike(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        $row = $this->getDislike($resource, $poster);
        if (null !== $row) {
            throw new Core_Model_Exception('Already dis-liked');
        }

        $table = $this->getDislikeTable();
        $row = $table->createRow();

        if (isset($row->resource_type)) {
            $row->resource_type = $resource->getType();
        }

        $row->resource_id = $resource->getIdentity();
        $row->poster_type = $poster->getType();
        $row->poster_id = $poster->getIdentity();
        $row->save();

        if (isset($resource->dislike_count)) {
            $resource->dislike_count++;
            $resource->save();
        }

        return $row;
    }

    public function removeDislike(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        $row = $this->getDislike($resource, $poster);
        if (null === $row) {
            throw new Core_Model_Exception('No like to remove');
        }

        $row->delete();

        if (isset($resource->dislike_count)) {
            $resource->dislike_count--;
            $resource->save();
        }

        return $this;
    }

    public function isDislike(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        return ( null !== $this->getDislike($resource, $poster) );
    }

    public function getDislike(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {
        $table = $this->getDislikeTable();
        $select = $this->getDislikeSelect($resource)
                ->where('poster_type = ?', $poster->getType())
                ->where('poster_id = ?', $poster->getIdentity())
                ->limit(1);

        return $table->fetchRow($select);
    }

    public function getDislikeSelect(Core_Model_Item_Abstract $resource) {
        $select = $this->getDislikeTable()->select();

        if (!$this->_custom) {
            $select->where('resource_type = ?', $resource->getType());
        }

        $select
                ->where('resource_id = ?', $resource->getIdentity())
                ->order('dislike_id ASC');

        return $select;
    }

    public function getDislikePaginator(Core_Model_Item_Abstract $resource) {
        $paginator = Zend_Paginator::factory($this->getDislikeSelect($resource));
        $paginator->setItemCountPerPage(3);
        $paginator->count();
        $pages = $paginator->getPageRange();
        $paginator->setCurrentPageNumber($pages);
        return $paginator;
    }

    public function getDislikeCount(Core_Model_Item_Abstract $resource) {
        if (isset($resource->dislike_count)) {
            return $resource->dislike_count;
        }
        $select = new Zend_Db_Select($this->getDislikeTable()->getAdapter());
        $select
                ->from($this->getDislikeTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

        if (!$this->_custom) {
            $select->where('resource_type = ?', $resource->getType());
        }

        $select->where('resource_id = ?', $resource->getIdentity());

        $data = $select->query()->fetchAll();
        return (int) $data[0]['count'];
       
    }

    public function getAllDislikes(Core_Model_Item_Abstract $resource) {
        return $this->getDislikeTable()->fetchAll($this->getDislikeSelect($resource));
    }

    public function getAllDislikesUsers(Core_Model_Item_Abstract $resource) {
        $table = $this->getDislikeTable();
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
    public function dislikes($subject) {
        return new Engine_ProxyObject($subject, Engine_Api::_()->getDbtable('dislikes', 'yncomment'));
    } 

}