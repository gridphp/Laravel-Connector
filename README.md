# Laravel-Connector

#### Using full version

In the "App package", add the full version file, with name jqgrid_dist_full.php
and add in the Env file the key-value
GRID_FULL_VERSION=true


### Few notes

#### Eloquent model
Conventions define automatically some values, as plural of class name as tablename



#### Random data
Each time running
~~~
php artisan db:seed
~~~
adds 30 superheroes

## Smoke tests
listing, changing rowAmmount, paginating, fitlering, ordering


## Considerations (Project tasks are too short to collaborate)

- Ordering by first column

    Didn't loose time checking if possible with query builder
    
    If we refactor core, when we get to define columns, from a collection, may we send the first element name?

- //TODO datetime fields with bug (datetime)

    searching for a datetime, with op=eq to solve
