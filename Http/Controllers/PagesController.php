<?php

namespace Dcms\Pages\Http\Controllers;

use Dcms\Pages\Models\Pages;
//use Dcweb\Dcms\Models\Pages\Pagetree;
use Dcms\Pages\Models\Detail;
use Dcms\Core\Models\Languages\Language;

use App\Http\Controllers\Controller;

use View;
use Input;
use Session;
use Validator;
use Redirect;
use DB;
use Datatable;
use Auth;
use DateTime;
use Config;


class PagesController extends Controller {

		public static function QueryTree()
		{
			$tree = DB::connection('project')
																->table('pages_language as node')
																->select(
																					(DB::connection("project")->raw("CONCAT( REPEAT( '-', node.depth ), node.title) AS page")),
																					"node.id",
																					"node.parent_id",
																					"node.language_id",
																					"node.depth",
																					(DB::connection("project")->raw('Concat("<img src=\'/packages/dcms/core/images/flag-",country,".png\' >") as regio'))
																				)
																->leftJoin('languages','node.language_id','=','languages.id')
																->orderBy('node.lft')
															->get();
			return $tree;
		}

		public static function CategoryDropdown($models = null,$selected_id = null, $enableNull = true, $name="parent_id", $key = "id",$value="page")
		{
			$dropdown = "empty set";
			if(!is_null($models) && count($models)>0)
			{
				$dropdown = '<select name="'.$name.'" class="form-control" id="parent_id">'."\r\n";

				if($enableNull == true)	$dropdown .= '<option value="">None</option>'; //epty value will result in NULL database value;

				foreach($models as $model)
				{
					$selected = "";
					if(!is_null($selected_id) && $selected_id == $model->$key) $selected = "selected";

					//altering these tag properties can affect the form (jQuery)
					$dropdown .= '<option '.$selected.' value="'.$model->$key.'" class="'.$name.' language_id'.$model->language_id.' parent-'.(is_null($model->parent_id)?0:$model->parent_id).' depth-'.$model->depth.'">'.$model->$value.'</option>'."\r\n";
				}
				$dropdown .= '</select>'."\r\n"."\r\n";
			}
			return $dropdown;
		}



	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// load the view
		return View::make('dcms::pages/index');
	}


	public function getDatatable()
	{
		return Datatable::query(DB::connection('project')
																		->table('pages_language as node')
																		->select(
																							(DB::connection("project")->raw("CONCAT( REPEAT( '-', node.depth ), node.title) AS page")),
																							"node.id",
																							(DB::connection("project")->raw('Concat("<img src=\'/packages/dcms/core/images/flag-",country,".png\' >") as regio'))
																						)
																		->leftJoin('languages','node.language_id','=','languages.id')
																		->orderBy('node.lft')
																		)

																	->showColumns('page','regio')
																	->addColumn('edit',function($model){return '<form method="POST" action="/admin/pages/'.$model->id.'" accept-charset="UTF-8" class="pull-right"> <input name="_token" type="hidden" value="'.csrf_token().'"> <input name="_method" type="hidden" value="DELETE">
																			<a class="btn btn-xs btn-default" href="/admin/pages/'.$model->id.'/edit"><i class="fa fa-pencil"></i></a>
																			<button class="btn btn-xs btn-default" type="submit" value="Delete this article" onclick="if(!confirm(\'Are you sure to delete this item?\')){return false;};"><i class="fa fa-trash-o"></i></button>
																		</form>';})
																	->searchColumns('node.title')
																	->make();
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$languages =  DB::connection("project")->table("languages")->select((DB::connection("project")->raw("'' as title, '' as parent_id, '' as body")), "id","id as thelanguage_id",  "language","country","language_name")->get();

		// load the create form (app/views/articles/create.blade.php)
		return View::make('dcms::pages/form')
					->with('languages',$languages)
					->with('pageOptionValues',$this->CategoryDropdown($this->QueryTree()));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//$Languages = Language::all();
		$rules = array('language_id'=>'required|integer','title'=>'required');
/*		foreach($Languages as $Lang)
		{
			$rules['title.'.$Lang->id] = 'required';
		}
*/
		$validator = Validator::make(Input::all(), $rules);

		// process the validator
		if ($validator->fails()) {
			return Redirect::to('admin/pages/create')
				->withErrors($validator)
				->withInput();
				//->withInput(Input::except());
		} else {
			// store

			$theParent = null;
			//find root ellement
			if(Input::has("parent_id") && intval(Input::get("parent_id"))>0 ){
				$theParent = Page::find(Input::get("parent_id"));
			}

			if(is_null($theParent)){
				//$theParent = Category::where('language_id','=',Input::get("language_id"))->where('depth','=','0')->first();
			}

			$Page = new Pages;

			$Page->language_id 		= Input::get('language_id');
			$Page->title 					= Input::get("title");
			$Page->body 					= Input::get("body");

			$Page->url_slug 			= str_slug(Input::get("title"));
			$Page->url_path 			= str_slug(Input::get("title"));

			$Page->admin 				= Auth::guard('dcms')->user()->username;
			$Page->save();

			if(is_null($theParent)){
				$Page->makeRoot();
			} else {
				$Page->makeChildOf($theParent);
			}

			if(Input::has('nexttosiblingid') && intval(Input::get('nexttosiblingid'))>0)	$Page->moveToLeftOf(intval(Input::get('nexttosiblingid')));

			// redirect
			Session::flash('message', 'Successfully created page!');
			return Redirect::to('admin/pages');
		}
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
			// get the Page
			$page = Pages::find($id);

		 	$languages = DB::connection("project")->select('
													SELECT language_id as thelanguage_id, languages.language, languages.country, languages.language_name, pages_language.parent_id, pages_language.id,  title,  body
													FROM pages_language
													LEFT JOIN languages on languages.id = pages_language.language_id
													WHERE  languages.id is not null AND  pages_language.id = ?
													UNION
													SELECT languages.id , language, country, language_name, \'\' , \'\' ,  \'\' , \'\'
													FROM languages
													WHERE id NOT IN (SELECT language_id FROM pages_language WHERE id = ?) ORDER BY 1
													', array($id,$id));

			// show the edit form and pass the nerd
			return View::make('dcms::pages/form')
				->with('page', $page)
				->with('languages', $languages)
				->with('pageOptionValues',$this->CategoryDropdown($this->QueryTree(),$page->parent_id));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// validate
		// read more on validation at http://laravel.com/docs/validation
		$rules = array('language_id'=>'required|integer','title'=>'required');
	/*	foreach($Languages as $Lang)
		{
			$rules['title.'.$Lang->id] = 'required';
		}
*/
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::to('admin/pages/' . $id . '/edit')
				->withErrors($validator)
				->withInput();
		} else {
			// store
			$Page = Pages::find($id);

			$Page->language_id 		= Input::get('language_id');
			$Page->title 					= Input::get("title");
			$Page->body 					= Input::get("body");

			$Page->url_slug 			= str_slug(Input::get("title"));
			$Page->url_path 			= str_slug(Input::get("title"));

			$Page->admin 				= Auth::guard('dcms')->user()->username;
			$Page->save();


			$setRoot = false;
			$theParent = null;
			$moveParent = true;

			if( intval(Input::get('parent_id')) > 0 &&  Input::get('parent_id') <> $Page->parent_id) {
				//move to a new parentid
				$moveParent = true;
				$theParent = Pages::find(Input::get("parent_id"));
			}elseif(intval(Input::get('parent_id')) <= 0  &&  Input::get('parent_id') <> $Page->parent_id) {
				//move to a ROOT of the same, or other language
				$moveParent = true;
				//$theParent = Category::where('language_id','=',Input::get("language_id"))->where('depth','=','0')->first();
				if(is_null($theParent))$setRoot = true;
			}else{
				//we stay in the same parent
				$moveParent = false;
			}
			if($setRoot == true) $Page->makeRoot();
			elseif(!is_null($theParent)) $Page->makeChildOf($theParent);

			if($moveParent == false && Input::has('nexttosiblingid') && intval(Input::get('nexttosiblingid'))>0 && Input::get("oldsort") < Input::get("sort")) $Page->moveToRightOf(intval(Input::get('nexttosiblingid')));
			elseif($moveParent == false && Input::has('nexttosiblingid') && intval(Input::get('nexttosiblingid'))>0  && Input::get("oldsort") > Input::get("sort"))	$Page->moveToLeftOf(intval(Input::get('nexttosiblingid')));
			elseif($moveParent == false && Input::get("oldsort") < Input::get("sort") ) $Page->makeLastChildOf($Page->parent_id);
			elseif($moveParent == true && Input::has('nexttosiblingid') && intval(Input::get('nexttosiblingid'))>0 ) $Page->moveToRightOf(intval(Input::get('nexttosiblingid')));

			// redirect
			Session::flash('message', 'Successfully updated page!');
			return Redirect::to('admin/pages');
		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// delete
		$Page = Pages::find($id);
		$Page->delete();

		// redirect
		Session::flash('message', 'Successfully deleted the page!');
		return Redirect::to('admin/pages');
	}
}
