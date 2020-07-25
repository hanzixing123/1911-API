<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JiaoHuController extends Controller
{
    //
    public function api(){

        $data=request()->get("data");
        $method=request()->get("method");

        $key=sha1($data.'LZY');
        if($method==$key){
            echo "成功";
        }else{
            echo "失败";
        }
//    echo "ok?";


    }



}
