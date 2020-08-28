<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class TestModel extends Model{
    public function goodslist(){
     //取出数据或缓存
        $list=[];
        $response=[
            'error'=>0,
            'msg'=>ok,
            'data'=>[
                'list'=>$list
            ]
        ];
        return $response;
    }

}
