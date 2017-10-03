<?php



Route::group(['middleware' => ['web']], function () {

	Route::group( array("prefix" => "admin", "as"=>"admin."), function() {

    	Route::group(['middleware' => 'auth:dcms'], function() {

    		//PAGES
    		Route::group( array("prefix" => "pages", "as"=>"pages."), function() {
    			Route::any('api/table', array('as'=>'api.table', 'uses' => 'PagesController@getDatatable'));
    		});
    		Route::resource('pages','PagesController');
    });
  });
});



 ?>
