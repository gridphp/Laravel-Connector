<?php

namespace Gridphp\Gridphp;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class RestService
{
    protected $modelmodel_fqn;

    public function __construct($model_fqn)
    {
        $this->modelmodel_fqn = $model_fqn;
    }

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

                //TODO datetime fields with bug (datetime)
                if ($item->op == "eq") $query_builder->where($item->field,"=", $item->data);
            });
        }

        // Retrieve the paginator
        $list = $query_builder->paginate(request("rows"));

        $this->gridListResponseBuilder($list); // request Ending

    }


    public function store()
    {
        if(!request()->isMethod("POST") || request("oper") != "add")
            app()->abort(406,"Incongruous method");

        $new_model = app()->make($this->modelmodel_fqn);

        $new_model->fill(request()->all());

        $new_model->save();

        response()->make($new_model)->send();
    }

    public function update()
    {
        if(!request()->isMethod("POST") || request("oper") != "edit")
            app()->abort(406,"Incongruous method");

        $model = app()->make($this->modelmodel_fqn);

        $updating_model = $model->where($model->getKeyName(),request($model->getKeyName()))->firstOrFail();

        $updating_model->fill(request()->all());

        $updating_model->save();

        response()->make($updating_model)->send();
    }


    public function destroy()
    {
        if(!request()->isMethod("POST") || request("oper") != "del")
            app()->abort(406,"Incongruous method");

        $model = app()->make($this->modelmodel_fqn);

        $updating_model = $model->where($model->getKeyName(),request($model->getKeyName()))->firstOrFail();

        $updating_model->fill(request()->all());

        $updating_model->delete();

        response()->make($updating_model)->send();
    }

}