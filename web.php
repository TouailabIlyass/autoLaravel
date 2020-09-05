<?php


Route::get('/achats', 'AchatController@index');
Route::get('/achats/create', 'AchatController@create');
Route::post('/achats', 'AchatController@store');
Route::get('/achats/{achat}', 'AchatController@show');
Route::get('/achats/{achat}/edit', 'AchatController@edit');
Route::patch('/achats/{achat}', 'AchatController@update');
Route::delete('/achats/{achat}', 'AchatController@destroy');
Route::get('/api/achats/{limit}', 'AchatController@restIndex');
Route::get('/api/achats/{achat}', 'AchatController@restShow');
Route::post('/api/achats', 'AchatController@restStore');
Route::patch('/api/achats/{achat}', 'AchatController@restUpdate');
Route::delete('/api/achats/{achat}', 'AchatController@restDestroy');
