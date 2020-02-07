## Dependent on this package is quill/history. (already included in composer.json)
## How to make it work: https://github.com/centralization/quill-history/blob/master/README.md


## Service Provider
Register this provider on your `config/app.php`.
```php
'providers' => [
    ...,
    Quill\Login\LoginServiceProvider::class,
]
```


## Force publish User.php and LogInController.php
```php
php artisan vendor:publish --tag=login.user --force
php artisan vendor:publish --tag=login.controller --force
```


## In root's folder config/site.php (publish vellum.config if site.php does not exist)
update main_module_slug field to set where uam login will redirect (e.g gallery).
Make sure that your site_id has value as well
```php
 'main_module_slug'	=> 'gallery',
 'site_id' => 3
```


## Remove or comment out Auth::routes(); in routes/web.php



## Add these to routes/web.php
```php
Route::get('/', 'Auth\LoginController@index');
Route::get('/login', 'Auth\LoginController@login');
Route::post('/login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/auth', 'Auth\LoginController@authenticate');
```


## Update config/database.php
```php
'uam' => [
	'driver'    => 'mysql',
	'host'      => env('UAM_DB_HOST', 'localhost'),
	'database'  => env('UAM_DB_DATABASE', 'forge'),
	'username'  => env('UAM_DB_USERNAME', 'forge'),
	'password'  => env('UAM_DB_PASSWORD', ''),
	'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix'    => env('UAM_PREFIX', 'uam_'),
	'strict'    => false,
],
```


## Make sure UAM is installed as well


## In UAM, under config/vellum_site.php, add sites that you will convert to vellum
```php
return['cosmo'];
```


## Adding Module & Module's Permission
Under Register{Module}Module.php add permissions (name & description) and module's description
See example below
```php
'permissions' => [
	[
		'name' => 'add',
		'description' => 'gallery adding'
	]
],
'description' => 'package description'
```

## Run this artisan command to push module and permissions to UAM
```php
   php artisan make:modulePermission
```
