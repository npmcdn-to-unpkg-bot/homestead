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

Route::get('/', 'SocialMediaController@welcome');
Route::get('twitter', 'TwitterController@index');
Route::get('twitter/getfriends', 'TwitterController@getfriends');
Route::get('twitter/getfeed', 'TwitterController@getfeed');
Route::get('instagram/getfriends', 'InstagramController@getfriends');
Route::get('instagram/getfeed', 'InstagramController@getfeed');

Route::get('login/{provider?}', 'Auth\AuthController@login');


Route::group(['middleware' => 'auth'], function () {
    
    Route::any("admin", array('as' => 'admin', 'uses' => "AdminController@index"));
    Route::get('members/create', 'MembersController@create');
    Route::get('members/{slug}', 'MembersController@index');
    Route::resource('members', 'MembersController');

    Route::bind('members', function($value, $route) {
        $ent = new App\MemberEntity();
        return $ent->getMemberDB($value);
    });
    
    Route::bind('categories', function($slug, $route) {
        // pass category to social media controller
        // members in the category and their social media can subsequently 
        // be retrieved via social media model or social media controller
        return App\Category::whereSlug($slug)->first();
    });

          Route::get('categories/sort', 'CategoriesController@sort');
    //Route::model('categories', 'Category');
     Route::resource('categories', 'CategoriesController');  

});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//Route::model('tasks', 'Task');
//Route::model('projects', 'Project');




//Route::resource('socialmedia', 'SocialMediaController');
Route::resource('twitter', 'TwitterController');
Route::resource('instagram', 'InstagramController');

Route::get('socialmedia/getmembersocialmedia', 'SocialMediaController@getmembersocialmedia');
Route::get('socialmedia/{slug}', 'SocialMediaController@index');
Route::get('socialmedia', 'SocialMediaController@categorylist');
/*
Route::group(array('domain' => '{subdomain}.nowarena.dev'), function()
{


    Route::get('/', function($subdomain)
    {
        dd(Route::input('subdomain'));
        Route::get('/', 'SocialMediaController@categorylist');

    });

});
 
 */