<?php

namespace Gridphp\Gridphp;

use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class RestService
{

    protected function gridListResponseBuilder(LengthAwarePaginator $paginator){

        $response = collect();

        $response->put("page",      $paginator->currentPage());
        $response->put("total",     $paginator->lastPage());
        $response->put("records",   $paginator->total());
        $response->put("rows",      $paginator->items());

        response()->make($response)->send();
    }


    public function index()
    {
        $list = DB::table("superheros")->paginate(20);

        $this->gridListResponseBuilder($list); // request Ending

    }


}