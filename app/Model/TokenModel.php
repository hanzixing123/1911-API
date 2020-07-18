<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TokenModel extends Model
{
    //
    protected $table="token";
    protected $primaryKey ="id";
    //关闭时间戳
    public $timestamps=false;

// 黑名单属性
    protected $guarded=[];

}
