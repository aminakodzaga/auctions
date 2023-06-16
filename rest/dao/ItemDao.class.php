<?php

require_once __DIR__.'/BaseDao.class.php';

class ItemDao extends BaseDao
{
    /**
    * constructor of dao class
    */
    public function __construct()
    {
        parent::__construct("items");
    }

    public function get_user_items($user_id)
    {
        return $this->query("SELECT * FROM items WHERE owner_id = :owner_id ORDER BY `ending` < NOW(), `ending` ASC", ['owner_id' => $user_id]);
    }
    public function get_by_id($id)
    {
        return $this->query_unique('SELECT * FROM items WHERE id = :id', ['id' => $id]);
    }

    public function check_if_owner($id, $item_id)
    {
        return $this->query("SELECT * FROM items WHERE owner_id =:owner_id AND id=:item_id", ['owner_id' => $id,'item_id' => $item_id]);
    }

    public function get_all_sorted()
    {
        return$this->query("SELECT * 
    FROM `items` 
    ORDER BY `ending` < NOW(), `ending` ASC", null);
    }
    public function check_if_ended($id)
    {
        return $this->query("SELECT * FROM items WHERE id = :id AND ending >= CURDATE() LIMIT 1", ['id' => $id]);
    }
}
