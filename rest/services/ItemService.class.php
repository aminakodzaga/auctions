<?php

require_once __DIR__.'/BaseService.class.php';
require_once __DIR__.'/../dao/ItemDao.class.php';
require_once __DIR__.'/../dao/UserDao.class.php';

class ItemService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new ItemDao());
    }

    public function get_user_items($user)
    {
        return $this->dao->get_user_items($user['id']);
    }

    public function get_all_sorted()
    {
        return $this->dao->get_all_sorted();
    }

    public function add($user, $entity)
    {
        $entity['owner_id'] = $user['id'];
        return parent::add($user, $entity);
    }


    public function delete($user, $id)
    {
        $item = $this->dao->get_by_id($id);
        if ($item['owner_id'] != $user['id']) {
            throw new Exception("This is hack you will be traced, be prepared :)");
        }
        parent::delete($user, $id);
    }

    public function check_if_ended($id)
    {
        return $this->dao->check_if_ended($id);
    }
}
