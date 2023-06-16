<?php

require_once __DIR__.'/BaseService.class.php';
require_once __DIR__.'/../dao/BidDao.class.php';
require_once __DIR__.'/../dao/UserDao.class.php';

class BidService extends BaseService
{
    private $item_dao;

    public function __construct()
    {
        parent::__construct(new BidDao());
        $this->item_dao = new ItemDao();
    }

    public function get_item_bids($item_id)
    {
        return $this->dao->get_item_bids($item_id);
    }

    public function delete($user, $id)
    {
        $item = $this->dao->get_item_bids($id);
        foreach ($item as &$i) {
            parent::delete($user, $i['id']);
        }
    }

    public function add($user, $entity)
    {
        $entity['bidder_id'] = $user['id'];
        $check = $this->item_dao->check_if_owner($user['id'], $entity['item_id']);
        if (!empty($check)) {
            throw new Exception("This is hack you will be traced, be prepared :)");
        }
        return parent::add($user, $entity);
    }
}
