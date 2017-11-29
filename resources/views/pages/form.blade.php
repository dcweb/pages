@extends("dcms::template/layout")

@section("content")

    <div class="main-header">
      <h1>Pages</h1>
      <ol class="breadcrumb">
        <li><a href="{!! URL::to('admin/dashboard') !!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{!! URL::to('admin/pages') !!}"><i class="fa fa-file"></i> Pages</a></li>
        @if(isset($page))
					 	<li class="active">Edit</li>
        @else
			  		<li class="active">Create</li>
        @endif
      </ol>
    </div>


    <div class="main-content">
    	<div class="row">
				<div class="col-md-12">
					<div class="main-content-block">

              @if(isset($page))
                <h2>Edit page</h2>
                {!! Form::model($page, array('route' => array('admin.pages.update', $page->id), 'method' => 'PUT')) !!}
              @else
                <h2>Create page</h2>

                  {!! Form::open(array('url' => 'admin/pages')) !!}
              @endif

              @if($errors->any())
                <div class="alert alert-danger">{!! Html::ul($errors->all()) !!}</div>
              @endif

              <div class="form-group">
              	{!! Form::label('Language', 'Language') !!}
                <div class="clearfix"></div>
                @foreach($languages as $O)
                <fieldset class="float-left">
                	{!! Form::radio('language_id', $O->thelanguage_id , (isset($page) && $page->language_id == $O->thelanguage_id ? true: false), array('class' => 'radiolanguage','id'=>'language_id'.$O->thelanguage_id)) !!}
                  {!! Html::decode(Form::label( 'language_id'.$O->thelanguage_id, ' <img src="/packages/dcms/core/images/flag-'.strtolower($O->country). '.png"> - ' .$O->language, array('class' => ''))) !!}
                </fieldset>
                @endforeach

                <div class="clearfix"></div>
              </div>

              <div class="form-group">
                {!! Form::label('parent_id', 'Parent page') !!}
                {!! $pageOptionValues !!}
              </div>

              <div class="form-group">
	              {!! Form::label('sort', 'Sort ') !!} <!-- Sort has some jQuery magic behind it.. since sorting in  controller is a bit different -->
              	{!! Form::text('sort', '', array('class' => 'form-control')) !!}
                {!! Form::hidden('nexttosiblingid', '', array('id'=>'nexttosiblingid', 'class' => 'form-control')) !!}
  	            {!! Form::hidden('oldsort', '', array('id'=>'oldsort', 'class' => 'form-control')) !!}
              </div>

              <div class="form-group">
	              {!! Form::label('title', 'Title ') !!}
              	{!! Form::text('title', null, array('class' => 'form-control')) !!}
              </div>

              <div class="form-group">
               {!! Form::label('thumbnail', 'Thumbnail') !!}

               <div class="input-group">
                   {!! Form::text('thumbnail', Input::old('thumbnail'), array('class' => 'form-control')) !!}
                 <span class="input-group-btn">
                   {!! Form::button('Browse Server', array('class' => 'btn btn-primary browse-server', 'id'=>'browse_thumbnail')) !!}
                 </span>
               </div>
             </div>

              <div class="form-group">
                {!! Form::label('body', 'Body') !!}
                {!! Form::textarea('body', null, array('class' => 'form-control')) !!}
              </div>

							{!! Form::submit('Save', array('class' => 'btn btn-primary')) !!}
              <a href="{!! URL::previous() !!}" class="btn btn-default">Cancel</a>
            	{!! Form::close() !!}

	      	</div>
      	</div>
      </div>
    </div>

<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckeditor/ckeditor.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckeditor/adapters/jquery.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckfinder/ckfinder.js') !!}"></script>
<script type="text/javascript" src="{!! asset('/packages/dcms/core/ckfinder/ckbrowser.js') !!}"></script>

<script type="text/javascript">
$(document).ready(function() {

	//CKFinder for CKEditor
	CKFinder.setupCKEditor( null, '/packages/dcms/core/ckfinder/' );

	//CKFinder
	$(".browse-server").click(function() {
		BrowseServer( 'Images:/articles/', 'thumbnail' );
	})

	//CKEditor
	$("textarea[id='description']").ckeditor();
	$("textarea[id='body']").ckeditor();



  function setParentIDDropdown()
  {
    $("#nexttosiblingid").val('');
    $(".parent_id").hide();
    language_id = ($("input:checked").attr("id").replace("language_id",""));
    $(".parent_id.language_id"+language_id).show();

    @if(isset($page->id))
      $('#parent_id option[value="{{$page->id}}"]').hide();
    @endif
  }

  //HELP SETTING THE SIBLING SORT
  var a = [];
  $(".parent_id").each(function(index){
    before  =  $(this).attr('class').indexOf('parent-');
    after   =  $(this).attr('class').indexOf(' depth-');
    a.push($(this).attr('class').substr(before,(after-before)));
  })
  //console.log(a);

  u = jQuery.unique(a);
  u = jQuery.unique(u); //same again, since it seems to have got some issues
  u = jQuery.unique(u); //same again, since it seems to have got some issues
  u = jQuery.unique(u); //same again, since it seems to have got some issues
  u = jQuery.unique(u); //same again, since it seems to have got some issues
  u = jQuery.unique(u); //same again, since it seems to have got some issues
  //console.log(u);

  jQuery.each(u,function(theindex){
    sort = 0;
    $("."+u[theindex]).each(function(){
      sort = sort + 1;
      //help setting the current sort and the oldsort
      // the oldsort is hidden it is needed, for sorting
      @if(isset($page))
          if($(this).val() == {{$page->id}})
          {
            $("#oldsort").val(sort);
            $("#sort").val(sort);
          }
      @endif

      $(this).attr('class', $(this).attr('class') + " sort-"+sort);
    })
  })

  //every change happens in the sort value, we have to find the current "sibbling" of new/editid object - being on the given sort -
  // this way we know if we want the new on the left or right of that sibbling
  $("#sort").keyup(function(){
    if($("#parent_id option:selected").val() == "") parent = "parent-0"
    else parent = "parent-"+$("#parent_id option:selected").val()

    $("#nexttosiblingid").val($("."+parent +".sort-"+$(this).val()).val());
  })


  if(typeof $("input:checked").attr("id") == "undefined"){
    $(".parent_id").hide(); //hide everything when create a new, and no language_id has been checked
  } else {
    setParentIDDropdown(); //make the dropdown with the correct language_id vissible basicly hides some options in the dropdown
  }

  $("#parent_id").change(function(){$("#sort").val('');}) // remove the sort when changing the parent_id (value in the dropdown)

	$(".radiolanguage").change(function(){
      $("#parent_id option:selected").removeAttr("selected"); //remove any selection
      setParentIDDropdown(); //make the dropdown with the correct language_id vissible
    }
  );
  //END OF SETTING THE SIBLING SORT

	//Bootstrap Tabs
	$(".tab-container .nav-tabs a").click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
});

</script>

<script type="text/javascript" src="{!! asset('packages/dcms/core/assets/js/bootstrap.min.js') !!}"></script>
@stop
