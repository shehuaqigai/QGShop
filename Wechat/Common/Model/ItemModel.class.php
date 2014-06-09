<?php
namespace Common\Model;
use Think\Model;
class ItemModel extends Model {

    public function _initialize(){
    }

    public function productList(){
        return $this->field('id,cate_id,intro,add_time,img,price,title,status,buy_num')->select();
    }
}