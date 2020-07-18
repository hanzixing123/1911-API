<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    //
    protected $table="user";
    protected $primaryKey ="user_id";
    //关闭时间戳
    public $timestamps=false;

// 黑名单属性
    protected $guarded=[];


}
