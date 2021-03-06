<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::resource('game/react', 'GameController@react');
Route::resource('game/searchfilter', 'GameController@searchfilter');
Route::resource('game/jquerymatch', 'GameController@jquerymatch');
Route::resource('game/reactmatch', 'GameController@reactmatch');
Route::get("/index", "IndexController@index");
Route::get('/', 'SocialMediaController@welcome');
Route::get('twitter', 'TwitterController@index');
Route::get('twitter/getfriends', 'TwitterController@getfriends');
Route::get('twitter/getfeed', 'TwitterController@getfeed');
Route::get('instagram/getfriends', 'InstagramController@getfriends');
Route::get('instagram/getfeed', 'InstagramController@getfeed');
Route::get('yelp/getfeed', 'YelpController@getfeed');

Route::get('login/{provider?}', 'Auth\AuthController@login');

Route::group(['middleware' => 'auth'], function () {
    
    Route::any("admin", array('as' => 'admin', 'uses' => "AdminController@index"));
    Route::get('members/create', 'MembersController@create');
    Route::get('members/{slug}', 'MembersController@index');
    Route::resource('members', 'MembersController');
    Route::resource('links', 'LinksController');
    Route::post('links/sort', 'LinksController@sort');       

    Route::bind('members', function($value, $route) {
        $ent = new App\MemberEntity();
        return $ent->getMemberDB($value);
    });
    Route::post('categories/sort', 'CategoriesController@sort');    
    Route::bind('categories', function($slug, $route) {
        // pass category to social media controller
        // members in the category and their social media can subsequently 
        // be retrieved via social media model or social media controller
        return App\Category::whereSlug($slug)->first();
    });

    Route::resource('categories', 'CategoriesController');  
     

});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::resource('twitter', 'TwitterController');
Route::resource('instagram', 'InstagramController');

Route::get('socialmedia/getmembersocialmedia', 'SocialMediaController@getmembersocialmedia');
Route::get('socialmedia/{slug}', 'SocialMediaController@index');
Route::get('socialmedia', 'SocialMediaController@categorylist');