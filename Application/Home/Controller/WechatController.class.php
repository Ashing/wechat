<?php
/**
 * 微信授权
 */

namespace Home\Controller;


use EasyWeChat\Foundation\Application;
use Home\Service\WechatService;
use User\Api\UserApi;

include './vendor/autoload.php'; // 引入 composer 入口文件
class WechatController extends HomeController
{
    public function index(){
        $app = new Application(C('WE_CHAT'));
        // 从项目实例中得到服务端应用实例。
        $server = $app->server;
        $server->setMessageHandler(function ($message) {
            // $message->FromUserName // 用户的 openid
            // $message->MsgType // 消息类型：event, text....
            return "您好！欢迎关注我!";

            switch ($message->MsgType) {
                case 'event':
                    return '收到事件消息';
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

        });
        $response = $server->serve();
        $response->send(); // Laravel 里请使用：return $response;


    }


    public function bang()
    {
        $wechat = new WechatService();
        //获取session中的openid,如果没有就获取openid,保存到session
        if (!session('openid')){
            $back = 'Wechat/bang';
           $wechat->getOpenid($back);
        }
        $openid = session('openid');
        //根据openid到数据库查询
        $member = D('Member');
        $info = $member->getByOpenid($openid);
        if ($info){
            //登录
            if($member->login($info['uid'])){ //登录用户
                //TODO:跳转到登录前页面
                //登录成功后跳转到之前的页面
                $this->success('登录成功！',U(session('return')));
            } else {
                $this->error($member->getError());
            }
        }else{
            //跳转到登录页面进行登录
            $this->redirect('User/login');
        }
    }




    //网页授权回调地址
    public function callback()
    {
        $app = new Application(C('WE_CHAT'));
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        //将用户的openid保存到session
        session('openid',$user->id);
        //session('wechat_user',$user->toArray());
        //跳回请求地址
        if(session('back')){
            //保存到变量
            $back = session('back');
            //清理session中的back
            session('back',null);
            //跳回
            $this->redirect($back);
        }
    }

    /**
     * 添加菜单
     */
    public function addMenu()
    {
        //获取菜单模块实例
        $app = new Application(C('WE_CHAT'));
        $menu = $app->menu;

        //添加普通菜单
        $buttons = [
            [
                "type" => "click",
                "name" => "源码时代",
                "key"  => "V1001_YUAN"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "物业管理",
                        "url"  => U("Index/index",'','',true),
                    ],
                ],
            ],
        ];
        $menu->add($buttons);
    }
}