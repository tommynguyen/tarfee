<?php
class Ynblog_Model_DbTable_Categories extends Engine_Db_Table
{
  /*----- Properties -----*/
  protected $_rowClass = 'Ynblog_Model_Category';
  protected $_name = 'blog_categories';
  protected $_type = 'blog_category';
  
  /*----- Get Category Function -----*/
  public function getCategory($category_id){
    return $this->find($category_id)->current();
  }
  /*----- Get Category List Function -----*/
  public function getCategories(){
    $select = $this->select()->order('category_name ASC');
    $result = $this->fetchAll($select);
    return $result;
  }
  /*----- Get Categories Array -----*/
  public function getCategoriesAssoc(){
    $categories = $this->getCategories();
    $data = array();
    $data[0] ="";
    foreach($categories as $category){
      $data[$category->category_id] = $category->category_name;
    }
    return $data;
  }
  /*----- Get User Categories List -----*/
  public function getUserCategories($user_id = 0){
    //Get table name
    $blog_table_name = Engine_Api::_()->getItemTable('blog')->info('name');
    $cat_table_name = $this->info('name');

    // Query
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->from($cat_table_name, array('category_name'))
        ->joinLeft($blog_table_name, "$blog_table_name.category_id = $cat_table_name.category_id")
        ->group("$cat_table_name.category_id")
        ->where($blog_table_name.'.owner_id = ?', $user_id)
        ->where($blog_table_name.'.draft = ?', "0")
        ->where($blog_table_name.'.search = ?',"1")
        ->where($blog_table_name.'.is_approved = ?',"1");

    return $this->fetchAll($select);
  }
}
?>
