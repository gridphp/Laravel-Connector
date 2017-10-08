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

        $query_builder = DB::table("superheroes");

        // Means to specify (to the paginator class) which page we want
        request()->merge(["page" => request("jqgrid_page")]);

        // Orderby if not integer //TODO check how to order by col nr in query builder
        if(request("sidx") != "1") $query_builder = $query_builder->orderBy(request("sidx"), request("sord"));

        // Perform the filtering
        if(request("_search")){

            $filters = json_decode(request("filters"));
            $terms = collect($filters->rules);

            $terms->each(function($item, $key) use (&$query_builder){
                if ($item->op == "cn") $query_builder->where($item->field,"LIKE", "%{$item->data}%");
            });
        }

        // Retrieve the paginator
        $list = $query_builder->paginate(request("rows"));

        $this->gridListResponseBuilder($list); // request Ending

    }


}