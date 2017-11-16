<?php

namespace Gridphp\Gridphp\Http\Controllers;

use DB;
use Phpgrid;
use App\Superhero;
use Gridphp\Gridphp\RestService;
use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{

    public function __construct()
    {
//        $this->middleware('auth');
    }


    public function spikeMssqlnative()
    {

//        if (!request()->ajax())
//        {
//            $v = DB::connection('sqlsrvubuntu')->select("select @@version");
//            $test = DB::connection('sqlsrvubuntu')->table('users')->get();
//            var_dump($v);
//            var_dump($test);
//        }


        $db_conf = [];

//        $db_conf["type"] = "mssqlnative";
//        $db_conf["server"] = config('database.connections.phcyudoeu.host');
//        $db_conf["user"] = config('database.connections.phcyudoeu.username');
//        $db_conf["password"] = config('database.connections.phcyudoeu.password');
//        $db_conf["database"] = config('database.connections.phcyudoeu.database');

        $db_conf["type"] = "mssqlnative"; // not mssql
        $db_conf["server"] = "127.0.0.1";
        $db_conf["user"] = "sa"; // username
        $db_conf["password"] = "Secret123"; // password
        $db_conf["database"] = "teste"; // database
        $g3 = new \jqgrid($db_conf);
        $g3->table = "users";
        $out = $g3->render("users");



        /*
         * this is another connection
         */
//        $g2 = Phpgrid::start('mysql');
//        $grid["caption"] = "YSS 3D Attachments";
//        $g2->set_options($grid);
//        $g2->table = "yss_reference_file3d";
//        $out2 = $g2->render("users");




    }


    public function index()
    {
        $restRepository = app()->make(RestService::class,[ "model_fqn" => Superhero::class]);


        $db_conf = array();
        $db_conf["type"] = "mysqli"; // not mssql
        $db_conf["server"] = "127.0.0.1";
//        $db_conf["server"] = "192.168.10.1";
        $db_conf["user"] = "root"; // username
        $db_conf["password"] = "root"; // password
        $db_conf["database"] = "grid-laravel-connector"; // database
        $g3 = new \jqgrid($db_conf);
        $g3->table = "superheroes";

        $grid["caption"] = "Superheros";
        $g3->set_options($grid);

        $e["on_select"] = array("index",        $restRepository, false);
        $e["on_insert"] = array("store",        $restRepository, false);
        $e["on_update"] = array("update",       $restRepository, false);
        $e["on_delete"] = array("destroy",      $restRepository, false);
        $e["on_export"] = array("on_export",    null, true); //TODO define, preferably with PHPoffice, i've seen it already, great! =)
        if (config("phpgrid.full_version")) $g3->set_events($e); //only in full version

        $out = $g3->render("gUsers");

        $out2 = "<br><br><br> Also subgrid is possible =)";

        return view("phpgrid::welcome")
            ->withOut($out)
            ->withOut2($out2);
    }




}
