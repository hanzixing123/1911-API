<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Model\UserMdoel;
use Illuminate\Support\Str;
use App\Model\TokenModel;
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
                $token=request()->get("token");

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


}
