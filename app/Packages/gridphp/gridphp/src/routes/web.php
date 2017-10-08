<?php

/**
 * Every route you register here will be already
 * prefixed with Ijpatricio\Phpgrid namespace
 * for you, right out of the box! Awesome!
 */


//For
//Route::get('/phpgrid','Ijpatricio\Phpgrid\Http\Controllers\WelcomeController@index');

//Just do
Route::any('/phpgrid','WelcomeController@index');





