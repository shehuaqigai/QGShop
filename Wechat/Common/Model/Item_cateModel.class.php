<?php
namespace Common\Model;
use Think\Model;
class Item_cateModel extends Model {

    public function _initialize(){
    }

    public function productCategory(){
        return $this->field('id,name,img,type,pid,is_index,ordid,spid')->select();
    }
    public function add_cate($data){

        if($this->create($data,1)){
            $result = $this->add(); // 写入数据到数据库
            if($result){        // 如果主键是自动增长型 成功后返回值就是最新插入的值
                return $result;
            }else{return false;}
        }

    }
    public function getImageUrl($id){

        return $this->field("img")->where("id=".$id)->select();

    }

    public function update_cate($data){
        if($this->create($data,2)){
            $result = $this->save(); // 更新数据到数据库
            if($result){        // 如果主键是自动增长型 成功后返回值就是最新插入的值
                return $result;
            }else{return false;}
        }

    }
}