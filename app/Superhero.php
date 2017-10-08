<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Superhero extends Model
{

    //use one or the other
//    protected $guarded = ['id'];
    protected $fillable = ['name','superpower'];

    //TODO i prefer using guarded, but post params are mixed with grid_id

}
