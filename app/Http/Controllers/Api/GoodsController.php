<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\GoodsModel;

class GoodsController extends Controller
{
    /**
     * 首页商品
     * @return array
     */
    public function home()
    {
        $list = GoodsModel::select("goods_id","goods_name","goods_img","shop_price")->orderBy("goods_id","desc")->limit(9)->get();
        $response = [
            'errno' => 0,
            'msg'   => 'ok',
            'data'  => [
                'list'  => $list
            ]
        ];
        return $response;
    }

}
