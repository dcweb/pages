<?php



Route::group(['middleware' => ['web']], function () {

	Route::group( array("prefix" => "admin"), function() {

    	Route::group(['middleware' => 'auth:dcms'], function() {

    		//PAGES
    		Route::group( array("prefix" => "pages"), function() {
    			Route::any('api/table', array('as'=>'admin/pages/api/table', 'uses' => 'PagesController@getDatatable'));
    		});
    		Route::resource('pages','PagesController');
    });
  });
});



 ?>
