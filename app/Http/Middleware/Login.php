<?php

namespace App\Http\Middleware;

use App\Model\UserModel;
use Closure;
use App\Model\TokenModel;
use Illuminate\Support\Facades\Redis;
class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        $token=request()->get("token");
        $user=TokenModel::OrderBy("id","desc")->where("token",$token)->first();//倒叙查询token
       // dd($user);die;
        //根据token和用户id查找当前对应的token，以免token重复
            $user1 = TokenModel::where("token", $token)->where("user_id", $user['user_id'])->first();

        //————————————————— 查看token是否正确以及是否过期 是否是服用用户对用的token ——————————

        if(empty($token) ){
                $data=[
                        "msg"=>1,
                        "data"=>"未携带token,未授权"
                ];

                //dd("ok");
                //return $data;
                print_r($data);die;
        }

               if(!$user1){
                   $data=[
                       "msg"=>1,
                       "data"=>"token错误"
                   ];
                   print_r($data);die;
                   //return $data;
               }
                if($user1->time < time() ){
                    $data=[
                        "msg"=>1,
                        "data"=>"token已失效"
                    ];
                    print_r($data);die;
                    //return $data;

                }
        //——————————————————————————————————————————————————

        //——————————————————————  查看用户当天访问次数   ———————————————————————
        $luyou=request()->route()->getActionName();//查看当前路由

        $cishu=redis::zadd($user->user_id,time(),$user->user_id.'+'.time());//方法一
        //$cishu=redis::zadd($luyou,time(),$user->user_id.'+'.time());//方法二

        $cishu=redis::zcard($user->user_id);
        print_r($cishu);

        //集合添加一条，名称是当前路由，分数是当前时间，值是ID拼接时间

        //$res=redis::zrange($luyou,0,-1,'withsocres');//该接口的所有请求数据
        //dd($res);//die;
        //$cishu=redis::where("")->zcard();

        //——————————————多少次——————————————————————————————
        $user_id1= TokenModel::where("token",$token)->first();
        $names = UserModel::where("user_id",$user_id1->user_id)->first();
        //获取访问的当前路径
        $desc = request()->route()->getActionName();
        $field = "user:".$names->time."path:".$desc;
        $kye = $names->Email.$names->time;
        //查询
        $name_desc = Redis::hget($kye,$field);
        if($name_desc){
            //自动递增
            $Seslle = Redis::hincrby($kye,$field,1);
        }else{
            //加一
            $Seslle = Redis::hset($kye,$field,1);
        }






        //———————————————————— 黑名单  ————————————————————————
        // 获取 该用户的user_id  并根据用户的user_id 存入redis集合中
        $user_id1= TokenModel::where("token",$token)->first();
        $shuliang=redis::scard($luyou.$user_id1->user_id);
        if($shuliang >= 10){
            $data=[
                "msg"=>1,
                "data"=>"尊敬的用户,我们检测您频繁调用该接口,我们怀疑在恶意刷取信息，故此封号1小时。"
            ];
            $jihe=redis::expire($luyou.$user_id1->user_id,60);
                print_r($data);die;
                //return   $data;
        }
        if($shuliang==1){
            $jihe=redis::expire($luyou.$user_id1->user_id,60);
        }
        $jihe=redis::sadd($luyou.$user_id1->user_id,uniqid());
        //——————————————————————————————————————————————————







        return $next($request);
    }
}
