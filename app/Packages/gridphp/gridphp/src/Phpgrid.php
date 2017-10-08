<?php

namespace Gridphp\Gridphp;


class Phpgrid
{

    public static function wantsTokenCheck()
    {

        if ( request()->ajax() && request()->has('_tokenRefresh') && request('_tokenRefresh') == "true" )
        {
            $data = [
                'data' => [
                    'token' => csrf_token(),
                ]
            ];

            response()->json($data)->send();
            die();
        }


    }

    public function start($connection = '')
    {
        static::wantsTokenCheck();

        $connection = ($connection == '') ? config('phpgrid.default_db_connection') : $connection ;

        $db_conf = [];
        $db_conf["type"]        = 'mysqli';
        $db_conf["server"]      = \Config::get( "database.connections.{$connection}.host" );
        $db_conf["user"]        = \Config::get( "database.connections.{$connection}.username" );
        $db_conf["password"]    = \Config::get( "database.connections.{$connection}.password" );
        $db_conf["database"]    = \Config::get( "database.connections.{$connection}.database" );

        return new \jqgrid($db_conf);
    }


}