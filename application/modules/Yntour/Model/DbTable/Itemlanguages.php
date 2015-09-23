<?php

class Yntour_Model_DbTable_Itemlanguages extends Engine_Db_Table
{   
    protected $_rowClass = 'Yntour_Model_Itemlanguage';
    public function updateLanguage($item_id, $body, $local)
    {
        $select = $this->select()
                    ->where("item_id = ?", $item_id)
                    ->where("language = ?", $local)->limit(1);
        $row = $this->fetchRow($select);
        if($row)
        {
           $row->body = $body;
           $row->save();  
        }
        else
        {
             $row = $this->createRow();
             $row->item_id =  $item_id;
             $row->language =  $local;
             $row->body =  $body;
             $row->creation_date =  date('Y-m-d H:i:s');
             $row->save();
        }
    }
    public function getLanguage($item_id,$local)
    {
         $select = $this->select()
                    ->where("item_id = ?", $item_id)
                    ->where("language = ?", $local)->limit(1);
        $row = $this->fetchRow($select);
        return $row;
    }
}
