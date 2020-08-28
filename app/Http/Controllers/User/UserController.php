<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\str;

class UserController extends Controller
{
    public function loginView(Request $request)
    {
        $redirect_uri = $request->get('redirect','http://www.1910.com');
        $data = [
            'redirect_uri' => $redirect_uri
        ];
        return view('user.login',$data);
    }

    public function login(Request $request)
    {
        // TODO 验证登录
        $redirect_uri = $request->input('redirect');        //跳转

        $info = $request->post('user_name');
        $pass = $request->post('user_pass');
        $u=UserModel::where(['email'=>$info])->orwhere(['user_name'=>$info])->first();
        if(empty($u))
        {
           $data = [
               'redirect' => 'login?redirect_uri='.$redirect_uri,
               'msg' => "用户名或密码不正确,请重新登录"
           ];
            return view('user.302', $data);
        }
        if(password_verify($pass,$u->password)){
            $token=UserModel::webLogin($u->user_id,$u->user_name);
            Cookie::queue('token',$token,60*24*30,'/','1910.com',false,true);      //120分钟
            $data = [
                'redirect' => $redirect_uri,
                'msg' => "登录成功"
            ];
           return view('user.302',$data);
        }else{
            $data=[
                'redirect' => 'login?redirect_uri='.$redirect_uri,
                'msg' => "用户名或密码不正确"
            ];
            return view('user.302',$data);
        }
        return redirect($redirect_uri);
    }
    public function checkToken(Request  $request){
        $token=$request->get('token');
        if(empty($token)){
            $response=[
                'error'=>400003,
                'msg'=>'未授权'
            ];
            return $response;
        }
        $token_key='h:login_info:'.$token;
        $u=Redis::hGetAll($token_key);
        if($u){
            $response=[
                'error'=>0,
                'msg'=>ok,
                'data'=>[
                    'u'=>$u
                ]
            ];
        }else{
            $response=[
                'error'=>400003,
                'msg'=>'未授权'
            ];
        }
         return $response;
    }

public function logout(Request $request)
{
    $redirect_uri=$request->get('redirect',env('SHOP_DOMAIN'));
    $token_key='h:login_info:'.$_SERVER['token'];
    Redis::del($token_key);
    return redirect($redirect_uri);
}

}
