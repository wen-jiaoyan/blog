<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use App\Model\VideoModel;
use Illuminate\Http\Request;
use App\Model\GoodsModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;


class IndexController extends Controller
{
    public function home()
    {
        //获取最新10个商品
        $goods = GoodsModel::select('goods_id','cat_id','goods_sn','goods_name','shop_price','goods_img')->orderBy('goods_id','desc')->limit(6)->get();

        foreach($goods as $k => &$v)
        {
            $v['goods_title'] = $v['goods_name'];
            $v['goods_name'] = Str::limit($v['goods_name'],20);
            //是否有视频
            $video = VideoModel::where(['goods_id'=>$v['goods_id']])->first();
            if($video){
                $v['video'] = 1;
            }
        }

        $data = [
            'goods'  => $goods
        ];
        return view('index.home',$data);
    }
}
