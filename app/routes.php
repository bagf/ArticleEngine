<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

Route::get('/', function() {
  
  return View::make('hello');
});

Route::get('article/limit/{start}/{limit?}', 'ArticlesController@index')
        ->where(array('start' => '[0-9]+', 'limit' => '[0-9]+'));
Route::get('article/{article}/view_revisions', 'ArticlesController@viewRevisions')
        ->where(array('article' => '[0-9]+'));
Route::match(array('PUT', 'PATCH'), 'article/{article}/apply_revision/{revision}', 'ArticlesController@applyRevision');
//Route::post('article/{article}', 'ArticlesController@update')
//        ->where(array('article' => '[0-9]+'));
Route::resource('article', 'ArticlesController',
        array('except' => array('create', 'edit')));
