<?php

namespace Dcms\Pages\Models;
use Dcms\Core\Models\Languages\Language;

use Dcms\Core\Models\EloquentDefaults;

use DB;
use \Baum\Node as Node ;


	class Pages extends Node
	{
		protected $connection = 'project';
	  protected $table  = "pages_language";

		public function parentpage()
		{
	//		return $this->belongsTo('\Dcweb\Dcms\Models\Pages\Page', 'parent_id','id');
		}
/*
		public function article()
		{
				//BelongsToMany belongsToMany(string $related, string $table = null, string $foreignKey = null, string $otherKey = null, string $relation = null)
        //return $this->belongsToMany('\Dcweb\Dcms\Models\Articles\Detail', 'articles_language_to_pages', 'page_id', 'article_detail_id');
				return $this->belongsToMany('\Dcweb\Dcms\Models\Articles\Detail', 'articles_language_to_pages', 'id', 'article_detail_id');
		}
*/
		public function langauge()
		{
			return $this->belongsTo('Dcms\Core\Models\Languages\Language\Language','language_id','id');
		}


		//the columnMapper is an array with integer index values
		// 0 represesenting the id column
		// 1 		"		 "	value "
		public static function OptionValueArray($enableEmpty = false, $columns = array('*') , $columnMapper = array("id","title","language_id","depth")){

			$PageObj = parent::orderBy('language_id','asc')->orderBy('lft','asc')->get($columns);

			$OptionValueArray = array();
			if (count($PageObj)>0)
			{
				foreach($PageObj as $lang)
				{
						if (array_key_exists($lang->language_id, $OptionValueArray)== false ) $OptionValueArray[$lang->language_id] = array();

						if ($enableEmpty == true && array_key_exists(1, $OptionValueArray[$lang->language_id])== false )
						{
							$OptionValueArray[$lang->language_id][1] = "- ROOT -";
						}
						//we  make an array with array[languageid][maincategoryid] = translated category;
						$OptionValueArray[$lang->language_id][$lang->$columnMapper[0]]=str_repeat('-',$lang->level).' '.$lang->$columnMapper[1];
				}
			}
			elseif($enableEmpty === true)
			{
				$Languages = Language::all();

				foreach($Languages as $Lang)
				{
					$OptionValueArray[$Lang->id][1] = "- ROOT -";
				}
			}
			return $OptionValueArray;
		}
	}
/*
	class Pagetree extends EloquentDefaults
	{
		protected $connection = 'project';
	  protected $table  = "pages_language";

		//the columnMapper is an array with integer index values
		// 0 represesenting the id column
		// 1 		"		 "	value "
		public static function OptionValueArray($enableEmpty = false, $columns = array('*') , $columnMapper = array("id","title","language_id","depth")){

			$PageObj = parent::orderBy('language_id','asc')->orderBy('lft','asc')->get($columns);

			$OptionValueArray = array();
			if (count($PageObj)>0)
			{
				foreach($PageObj as $lang)
				{
						if (array_key_exists($lang->language_id, $OptionValueArray)== false ) $OptionValueArray[$lang->language_id] = array();

						if ($enableEmpty == true && array_key_exists(1, $OptionValueArray[$lang->language_id])== false )
						{
							$OptionValueArray[$lang->language_id][1] = "- ROOT -";
						}
						//we  make an array with array[languageid][maincategoryid] = translated category;
						$OptionValueArray[$lang->language_id][$lang->$columnMapper[0]]=str_repeat('-',$lang->level).' '.$lang->$columnMapper[1];
				}
			}
			elseif($enableEmpty === true)
			{
				$Languages = Language::all();

				foreach($Languages as $Lang)
				{
					$OptionValueArray[$Lang->id][1] = "- ROOT -";
				}
			}
			return $OptionValueArray;
		}
	}*/

	class Detail extends EloquentDefaults
	{
		protected $connection = 'project';
	  protected $table  = "pages_language";

		public function page()
		{
		//	return $this->belongsTo('\Dcweb\Dcms\Models\Pages\Page','page_id','id');
		}

		public function parentpage()
		{
		//	return $this->belongsTo('\Dcweb\Dcms\Models\Pages\Page', 'parent_id','id');
		}
	}
