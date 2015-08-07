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
Route::any("admin", array('as' => 'admin', 'uses' => "AdminController@index"));
Route::get('/', 'SocialMediaController@welcome');
Route::get('twitter', 'TwitterController@index');
Route::get('twitter/addfriends', 'TwitterController@addfriends');
Route::get('twitter/addstatus', 'TwitterController@addstatus');

/*

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
*/

// Provide controller methods with object instead of ID
Route::model('tasks', 'Task');
Route::model('projects', 'Project');
Route::model('categories', 'Category');
//Route::model('socialmedia', 'Category');
//Route::model('member', 'Member');

/*
Route::bind('socialmedia', function($value, $route) {
	$obj = App\Category::whereSlug($value)->first();
    //printR($obj);
    return $obj;
});
*/
Route::bind('members', function($value, $route) {
	return App\Member::where('id', $value)->first();
});

Route::bind('categories', function($slug, $route) {
    // pass category to social media controller
    // members in the category and their social media can subsequently 
    // be retrieved via social media model or social media controller
	return App\Category::whereSlug($slug)->first();
});
Route::get('members/{slug}', 'MembersController@index');
Route::resource('members', 'MembersController');

Route::resource('categories', 'CategoriesController');
//Route::resource('socialmedia', 'SocialMediaController');
Route::resource('twitter', 'TwitterController');

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