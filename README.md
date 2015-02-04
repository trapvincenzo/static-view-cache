#Static View Cache for Laravel4 (Blade template engine)

Suppose to include a view (with dynamic data) inside another view:

```php
<ul>
	@include('car', [$car])
</ul>
``` 

For this statement, the blade engine creates a cached file for the subview like this:

```php
<ul>
	<?php echo $__env->make('car', $car, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>;
</ul>
```

This means that for every request of the page, the rendering code is executed.

##Loop
The problem comes when the inclusion is made inside a loop. Executing the code at every iteration, can cause a big delay in the rendering of the page.

##Cached subview
What the StaticViewCache does, is to save a cached version of the generated subview with the real data. Doing so, the code for the rendering will be executed just the first time (to create the cache).


##How to use
To install the StaticViewCache as a Composer package, add this line to your composer.json:

```php
"trapvincenzo/static-view-cache": "dev-master"
```
and update your vendor folder running the ```composer update ``` command.

Register the service provider (app/config/app.php): 

```php
'providers' => array(
	...
	'Trapvincenzo\StaticViewCache\StaticViewCacheServiceProvider'
	...
)
```

Replace the @include statement with:

```php
StaticViewCache::render($viewName, $id, $data)
```

string **$viewName** view name

string **$id** unique identifier for the view (different for each iteration)

array **$data** data to use inside the view


Enjoy!