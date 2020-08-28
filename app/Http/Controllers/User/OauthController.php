<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Model\GithubUserModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OauthController extends Controller
{
    public function github()
    {
        // 接收code
        $code = $_GET['code'];
        $token = $this->getToken($code);
        $git_user = $this->getGithubUserInfo($token);
        //判断用户是否已存在，不存在则入库新用户
        $u = GithubUserModel::where(['guid'=>$git_user['id']])->first();
        if($u)          //存在
        {
            // TODO 登录逻辑
            $this->webLogin($u->uid);

        }else{          //不存在

            //在 用户主表中创建新用户  获取 uid
            $new_user = [
                'user_name' => Str::random(10)              //生成随机用户名，用户有一次修改机会
            ];
            $uid = UserModel::insertGetId($new_user);

            // 在 github 用户表中记录新用户
            $info = [
                'uid'                   => $uid,       //作为本站新用户
                'guid'                  => $git_user['id'],         //github用户id
                'avatar'                =>  $git_user['avatar_url'],
                'github_url'            =>  $git_user['html_url'],
                'github_username'       =>  $git_user['name'],
                'github_email'          =>  $git_user['email'],
                'add_time'              =>  time()
            ];

            $guid = GithubUserModel::insertGetId($info);        //插入新纪录

            // TODO 登录逻辑
            $this->webLogin($uid);
        }

        //将 token 返回给客户端
        Cookie::queue('token',$this->token,120,'/');      //120分钟
        return redirect('/');       //登录成功 返回首页
    }

    /**
     * 根据code 换取 token
     */
    protected function getToken($code)
    {
        $url = 'https://github.com/login/oauth/access_token';
        //post 接口  Guzzle or  curl
        $client = new Client();
        $response = $client->request('POST',$url,[
            'form_params'   => [
                'client_id'         => env('OAUTH_GITHUB_ID'),
                'client_secret'     => env('OAUTH_GITHUB_SEC'),
                'code'              => $code
            ]
        ]);
        parse_str($response->getBody(),$str);
        return $str['access_token'];
    }
    /**
     * 获取github个人信息
     * @param $token
     */
    protected function getGithubUserInfo($token)
    {
        $url = 'https://api.github.com/user';
        //GET 请求接口
        $client = new Client();
        $response = $client->request('GET',$url,[
            'headers'   => [
                'Authorization' => "token $token"
            ]
        ]);
        return json_decode($response->getBody(),true);
    }




    /**
     * WEB登录逻辑
     */
    protected function webLogin($uid)
    {
        $token = UserModel::generateToken($uid);
        //服务器保存 token
        $token_key = 'h:login_info:'.$token;
        $login_info = [
            'token'         => $token,                      // 用户token
            'uid'           => $uid,                        // 用户主表 uid
            'login_time'    => date('Y-m-d H:i:s'),    //登录时间
            'login_ip'      => $_SERVER['REMOTE_ADDR'],     //客户端登录IP
        ];
        Redis::hMset($token_key,$login_info);
        Redis::expire($token_key,7200);     // 登录有效期 2 个小时

        $this->token = $token;
        //将 uid 与 token写入 seesion    （session使用Redis存储）
        session(['uid'=>$uid]);

    }

    /**
     * 绑定github账号
     */
    public function bindGithub(){
        $code = $_GET['code'];
        //获取token
        $token = $this->getToken($code);
        //获取用户信息
        $u = $this->getGithubUserInfo($token);

        //检查用户是否已存在 是否已绑定
        $github_id = $u['id'];
        $git_user = GithubUserModel::where(['guid'=>$github_id])->first();

        if($git_user)       //用户已存在
        {

        }else{              // 新用户绑定
            // 在 github 用户表中记录新用户
            $info = [
                'uid'                   =>  $_SERVER['uid'],       //作为本站新用户
                'guid'                  =>  $u['id'],         //github用户id
                'avatar'                =>  $u['avatar_url'],
                'github_url'            =>  $u['html_url'],
                'github_username'       =>  $u['name'],
                'github_email'          =>  $u['email'],
                'add_time'              =>  time(),
            ];

            GithubUserModel::insertGetId($info);        //插入新纪录
        }

        return redirect('/user/center');


    }
}
