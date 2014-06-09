<?php
namespace Common\Model;
use Think\Model;
class BrandlistModel extends Model {

    public function _initialize(){

    }

    public function productCategory(){
       return $this->select();

    }
}