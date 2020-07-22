<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Model\UserMdoel;
use Illuminate\Support\Str;
use App\Model\TokenModel;
use Illuminate\Support\Facades\Redis;
class ApiController extends Controller
{
    //
    public function  lianxi(){
        $url="http://www.han.com/lianxi";
        $res=file_get_contents($url);
        dd($res);

            }

    public function zhuce(){
       // dd("sss");
            $email=request()->get("email");
            $name=request()->get("name");
            $pwd=request()->get("pwd");
            $time=time();
            if(empty($email)){
                $res=[
                    "errno"=>0,
                    "msg"=>"邮箱不可为空",
                ];return  $res;
            }
             if(empty($name)){
                 $res=[
                        "errno"=>0,
                         "msg"=>"账号不可为空",
                 ];return  $res;
             }
             if(empty($pwd)){
                  $res=[
                      "errno"=>0,
                      "msg"=>"密码不可为空",
                  ];return  $res ;
              }
             if(strlen($pwd) >15 ){
                 $res=[
                     "errno"=>0,
                     "msg"=>"密码过长",
                 ];return  $res ;
             }

             $ert=UserModel::where("user_name",$name)->first();
            if($ert){
                $res=[
                    "errno"=>0,
                    "msg"=>"账号已存在",
                ];return  $res ;
            }

             $emaill=UserModel::where("email",$email)->first();
             if($emaill){
                     $res=[
                               "errno"=>0,
                               "msg"=>"此邮箱已被注册",
                       ];return  $res ;
               }

                $data= new UserModel();
                $data->user_name=$name;
                $data->user_pwd=$pwd;
                $data->email=$email;
                $data->time=$time;

                if($data->save()){
                    $res=[
                        "errno"=>'ok',
                        "msg"=>"注册成功",
                    ];return  $res ;
                }
    }

        public function  Login()
        {

            $name = request()->get("name");
            $pwd = request()->get("pwd");
            $res = UserModel::where("user_name", $name)->first();

            if(!$res){
                $data = [
                    "error" => 1,
                    "msg" => "账号不存在!!!"
                ];
                return $data;
            }

            if ($res->user_pwd != $pwd) {
                $data = [
                    "error" => 1,
                    "msg" => "账号或密码错误!!!"
                ];
                return $data;

            }

            $tokenif=TokenModel::OrderBy("id",'desc')->where("user_id",$res->user_id)->first();

            if(empty($tokenif) || $tokenif->time <= time()  ){
                $token=Str::random();
                $abc =new  TokenModel();
                $abc->token=$token;
                $abc->time=time()+7000;
                $abc->user_id=$res->user_id;
                $abc->save();
                $data = [
                    "error" =>1,
                    "msg" => "ok",
                    "data"=>$token
                ];
                return $data;

            }else{
                    $token1=TokenModel::where("user_id",$res->user_id)->first();
                $data = [
                    "error" =>1,
                    "msg" => "ok",
                    "data"=>$token1->token
                ];
                return $data;

            }
                 }



        public function ShowUser(){
               // 查看redis中的集合
                    // $redis_user_id=redis::smembers("Blacklist");




                $token=request()->get("token");

                    //$user_id1= TokenModel::where("token",$token)->first();





                if(empty($token)){
                    $data = [
                        "error" =>1,
                        "msg" => "token 不可为空",
                    ];
                    return $data;
                }
                $tokenif=TokenModel::where("token",$token)->first();

            if(!$tokenif){
                $data = [
                    "error" =>1,
                    "msg" => "token 不正确",
                ];
                return $data;
            }

            if($tokenif->time<time()){
                $data = [
                    "error" =>1,
                    "msg" => "token 已过期",
                ];
                return $data;
                }
                //黑名单
                //                        // 获取 该用户的user_id  并根据用户的user_id 存入redis集合中
//                    $user_id1= TokenModel::where("token",$token)->first();
//
//                        $shuliang=redis::scard("Blacklist".$user_id1->user_id);
//                        if($shuliang >= 10){
//                            $data=[
//                                        "msg"=>1,
//                                        "data"=>"尊敬的用户,我们检测您频繁调用该接口,我们怀疑在恶意刷取信息，故此封号1小时。"
//                            ];
//                             $jihe=redis::expire("Blacklist".$user_id1->user_id,60);
//
//                                //if()
//
//
//                            return   $data;
//
//                        }
//
//                        if($shuliang==0){
//                                  $jihe=redis::expire("Blacklist".$user_id1->user_id,60);
//                        }
//
//                                 $jihe=redis::sadd("Blacklist".$user_id1->user_id,uniqid());   //


                $user_id=UserModel::where("user_id",$tokenif->user_id)->first();

                     $data = [
                                "error" =>1,
                                "msg" => "ok",
                                "data"=>[
                                             "用户名"=>$user_id->user_name,
                                             "密码"=>$user_id->user_pwd,
                                             "Email"=>$user_id->email
                                ]
                     ];
                          return $data;
        }

                public function phpinfo(){
                             echo  phpinfo();

                }

          public function Lists(){
                //列表
               $lists=request()->get("lists");
              // $Lists=redis::lpush("goods",1); // 添加一条
               //$Lists=redis::lrange("goods",0,-1);   //全部
              $Lists = redis::llen("goods");
            //  dd($Lists);
              if($Lists==0){
                  $data = [
                      "msg" => "1",
                      "data" =>"已售完"
                  ];
                  return $data;
              }
               if($lists > $Lists){
                   $data = [
                       "msg" => "1",
                       "data" =>"所求商品数量大于所剩商品数量,请调整下购买数量,商品还剩余".$Lists."件"
                   ];
                   return $data;
               }


              if(empty($lists) || $lists<1){
                    $data = [
                        "msg" => "1",
                        "data" =>"最少购买一条"
                    ];
                    return $data;
                }

              if($lists!=1){
//                  $res = redis::lpop("goods"); // 删除一条
//                  $ress = redis::llen("goods");
//                  $lists= $lists-1;
                   $data= $this->Liststo($lists);
                 //  dd($data);
                    return $data;
              }else {
                  $res = redis::lpop("goods"); // 删除一条
                  $ress = redis::llen("goods");

                  $data = [
                      "msg" => "1",
                      "data" => "抢购成功还余剩" . $ress . "个"
                  ];
                  return $data;
              }
       // dd($Lists);
            }
        private function Liststo($lists){
            if($lists!=1){
                $res = redis::lpop("goods"); // 删除一条
                $ress = redis::llen("goods");
                $lists= $lists-1;
                $this->Liststo($lists);
            }else{
                $res=redis::lpop("goods"); // 删除一条
                $ress=redis::llen("goods");
               // dd("kkkkkkkkkkk");


            }
            $data = [
                "msg" => "1",
                "data" => "抢购成功,还余剩".$ress."个"
            ];
            return $data;
        }

    //使用Redis有序集合实现签到功能：
    public function qiandao(){
        $token = request()->get("token");
        $name = TokenModel::OrderBY("id","desc")->where("token",$token)->first();
        //判断用户是否存在
        if(!$name){
            $redisce = [
                "error" => 40007,
                "msg" => "未经授权",
            ];
            return $redisce;
        }
        //检查token是否过期
        if($name->time < time()){
            $redisce = [
                "error" => 40008,
                "msg" => "Token 已过期",
            ];
            return $redisce;
        }
        $key = "$token".$name->time;
        $namedesc = $name->user_name;
        $id = $name->user_id;
        $nameset = Redis::get($key);
        if($nameset){
            $redisce = [
                "error" => 40009,
                "msg" => "已签到!",
            ];
            return $redisce;
        }else{
            Redis::set($key,$id);
            Redis::expire($key,172800);
            $Thesame = Redis::zscore($key, $namedesc);
            if ($Thesame) {
                $names = Redis::zincrby($key, 1, $namedesc);
                $namekey = $names;
                $redisce = [
                    "error" => 0,
                    "msg" => "签到成功，已签到" . $namekey . "天",
                ];
                return $redisce;
            }else {
                Redis::zadd($key, 1, $namedesc);
                $redisce = [
                    "error" => 0,
                    "msg" => "签到成功，已签到1天",
                ];
                return $redisce;
            }
        }



    }








}
