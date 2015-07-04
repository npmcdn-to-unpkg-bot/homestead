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
Route::get('/', 'WelcomeController@index');
/*
Route::get('home', 'HomeController@index');
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
*/
// Provide controller methods with object instead of ID
Route::model('tasks', 'Task');
Route::model('projects', 'Project');
Route::model('categories', 'Category');
Route::model('socialmedia', 'SocialMedia');
Route::model('member', 'Member');
// Use slugs rather than IDs in URLs
Route::bind('tasks', function($value, $route) {
	return App\Task::whereSlug($value)->first();
});
Route::bind('projects', function($value, $route) {
	return App\Project::whereSlug($value)->first();
});
Route::bind('categories', function($value, $route) {
	return App\Category::whereSlug($value)->first();
});
Route::bind('socialmedia', function($slug, $route) {
    // pass category to social media controller
    // members in the category and their social media can subsequently 
    // be retrieved via social media model or social media controller
	return App\Category::whereSlug($slug)->first();
});
Route::bind('members', function($value, $route) {
	return App\Member::where('id', $value)->first();
});
Route::resource('members', 'MembersController');
Route::resource('projects', 'ProjectsController');
Route::resource('projects.tasks', 'TasksController');
Route::resource('categories', 'CategoriesController');
Route::resource('socialmedia', 'SocialMediaController');
