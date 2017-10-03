<?php

namespace	Dcms\Pages;
/**
*
* @author web <web@groupdc.be>
*/
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class PagesServiceprovider extends ServiceProvider{
 /**
  * Indicates if loading of the provider is deferred.
  *
  * @var bool
  */
 protected $defer = false;

 public function boot()
 {

   $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'dcms');
   $this->setupRoutes($this->app->router);
   // this  for conig
   $this->publishes([
      //// __DIR__.'/config/contact.php' => config_path('contact.php'),
      //__DIR__.'/resources/views' => resource_path('views/vendor/dcms/core'),
      __DIR__.'/public/assets' => public_path('packages/dcms/pages'),
    //  __DIR__.'/config/auth.php' => config_path('dcms/pages/auth.php'),
      __DIR__.'/config/dcms_sidebar.php' => config_path('dcms/pages/dcms_sidebar.php'),
   ]);
    
    $this->app['config']['dcms_sidebar'] =  array_replace_recursive($this->app["config"]["dcms_sidebar"], config('dcms.pages.dcms_sidebar'));
    //$this->app['config']['auth'] = array_replace_recursive($this->app["config"]["auth"], config('dcms.pages.auth'));
 }
 /**
  * Define the routes for the application.
  *
  * @param  \Illuminate\Routing\Router  $router
  * @return void
  */
 public function setupRoutes(Router $router)
 {
   $router->group(['namespace' => 'Dcms\Pages\Http\Controllers'], function($router)
   {
     require __DIR__.'/Http/routes.php';
   });

 }

 public function register()
 {

   $this->registerPages();
   //b// config([
   //b//    'config/contact.php',
   //b// ]);

 }

 private function registerPages()
 {

    $this->app->bind('pages',function($app){
     return new Pages($app);
   });

 }

}

 ?>
