# Laravel-Connector

## Using full version

In the "App package", add the full version file, with name jqgrid_dist_full.php
and add in the Env file the key-value
GRID_FULL_VERSION=true


## REST-ish
I call this, because for real RESTapi with laravel, he have either to build the request with the correct verb


or we can leverage using method spoofing (like when from an html form)
passing attribute _method="delete"

and more considerations, let's speak



### Eloquent model
Conventions define automatically some values, as plural of class name as tablename



### Random data
Each time running
~~~
php artisan db:seed
~~~
adds 30 superheroes

# Smoke tests performed
listing, changing rowAmmount, paginating, fitlering, ordering

Please do your tests

## Considerations (Project tasks are too short to collaborate)

- Model softdeletes
    Lets speak about this (models that use deleted_at attribute)
    
- Ordering by first column

    Didn't loose time checking if possible with query builder
    
    If we refactor core, when we get to define columns, from a collection, may we send the first element name?

- //TODO datetime fields with bug (datetime)

    searching for a datetime, with op=eq to solve

- //TODO i prefer using guarded, but post params are mixed with grid_id

    the POST oper=add sends flat array mixing grid data with, example = oper: add
    this for with $guarded attribute makes something to solve
    if using $fillable, all ok (just fills the interection)


## Nextsteps

configuring grid columns from model $fillable attributes?
it makes sense, if is "rest-ish", we should setup a connection right?