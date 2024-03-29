<x-layouts.admin>
  @section('title', 'Service')
  <!-- Content Header (Service header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Admin Service Manager</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'Service Manager','route'=> 'admin.categories.index'],['label' => !empty($category) ? 'Edit Service' : 'Add Service' ]]]) }}
      </div>
    </div>
  </x-slot>
  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Service</h3>
            <div class="card-tools">
              <a href="{{ route('admin.categories.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            @if(isset($category))
            {{ Form::model($category, ['route' => ['admin.categories.update', $category->id], 'method' => 'patch', 'id' => 'submit-category', 'files'=>'true']) }}
            @else
            {{ Form::open(['route' => ['admin.categories.store'], 'id' => 'submit-category', 'files'=>'true']) }}
            @endif
            <div class="box-body">
              <div class="row">

                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Service Title</label>
                    {{ Form::text('name', old('name'), ['class' => 'form-control','placeholder' => 'Service Name']) }}
                    @if($errors->has('name'))
                    <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 kycImages">
                  <div class="form-group {{ $errors->has('icon') ? 'has-error' : '' }}">
                    <label for="iconFile">Service icon</label>
                    <div class="input-group">
                        <input type="file" class="form-control" name="icon" id="iconFile">
                    </div>
                    @if($errors->has('icon'))
                    <span class="help-block">{{ $errors->first('icon') }}</span>
                    @endif
                  </div>
				  @if(isset($category))
                  <div class="form-group">
                    <div class="form-control">
                    <img src="{{ $category->icon_url }}" alt="" style="width:100%">
                    </div>
                  </div>
				  @endif
                  <!-- <div class="container">
                    <div class="form form-group">
                      <label>Parent Services</label>
                      <div id="selected-categories"></div>
                      <div id="treeview"></div>
                      <div class="selected-container" style="display: none">
                        Selected Category
                        <div id="selected-categories"></div>
                      </div>
                      {!! Form::hidden('parent_id', old('parent_id'), ['id' => 'selectedParentId']) !!}
                    </div>
                  </div> -->
                </div>


              </div>


              <div class="row">
                <div class="col-md-12">
                  <label class="chk-container">Is Active
                    @if(isset($category))
                    @if($category->status == 1)
                    <input name="status" type="checkbox" checked="checked">
                    @else
                    <input name="status" type="checkbox">
                    @endif
                    @else
                    <input name="status" type="checkbox" checked="checked">
                    @endif
                    <span class="checkmark"></span>
                  </label>
                </div>
              </div>

            </div><!-- /.box-body -->
            <div class="box-footer">
              <button class="btn btn-primary btn-flat submit-form" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>
              <a href="{{ route('admin.categories.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
            {{ Form::close() }}
          </div>
          <!-- /.card-body -->

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->

    <!-- /.content -->
    @push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/21.1.6/css/dx.common.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/21.1.6/css/dx.light.css" />
    <style>
       .kycImages .form-group .form-control {
          height: auto;
        }
      /* .category-option ul { padding: 0px; margin: 0px 0px 0px 18px;}
      .category-option ul li{ padding:0px; margin: 0px; list-style: none;}
      .category-option .card-header{ padding: 0px 10px;}
      .category-option  .card-title { display: flex; align-items: center;}
      .category-option label { margin-left: 4px;}
      .selectedCategory{font-weight: 700; color:#28a745!important}
      .search-box{margin-bottom: 5px;}
      .extra-size {min-height: 200px;max-height: 200px;overflow-y: scroll;} */

      .form>div,
      #treeview {
        /*display: inline-block;*/
        vertical-align: top;
      }

      /*.form .dx-texteditor.dx-editor-outlined{width: 100% }*/
      .selected-container {
        padding: 20px;
        margin-left: 20px;
        font-size: 115%;
        font-weight: bold;
      }

      #selected-categories {
        margin-top: 0px;
        font-weight: 700;
      }

      /*#selected-categories .dx-list-item-content{ color:#28a745!important}*/
      .dx-list-item-content {
        padding-left: 0;
      }

      .options {
        padding: 20px;
        background-color: rgba(191, 191, 191, 0.15);
        margin-top: 20px;
        box-sizing: border-box;
      }

      .caption {
        font-size: 18px;
        font-weight: 500;
      }

      .option {
        width: 24%;
        display: inline-block;
        margin-top: 10px;
        margin-right: 5px;
        box-sizing: border-box;
      }

      .recursive-option {
        padding-left: 10px;
      }

      .option:last-of-type {
        margin-right: 0px;
      }

      .dx-treeview-with-search {
        width: 100% !important
      }
    </style>

    @endpush
    @push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn3.devexpress.com/jslib/21.1.6/js/dx.all.js"></script>
    <script>
      var categories = {!!json_encode($categories) !!};
    </script>
    <script type="text/javascript">
      $(function() {
        var selectedCategoriesList = $("#selected-categories").dxList({
          width: 400,
          height: 50,
          showScrollbar: "always",
          itemTemplate: function(item) {
            $('#selectedParentId').val(item.id);
            return "<div>Selected Category: " + item.text + "</div>";
          }
        }).dxList("instance");

        var treeView = $("#treeview").dxTreeView({
          items: categories,
          width: 350,
          height: 700,
          showCheckBoxesMode: "normal",
          selectionMode: "single",
          searchEnabled: true,
          searchMode: 'contains',
          onSelectionChanged: function(e) {
            syncSelection(e.component);
          },
          onContentReady: function(e) {
            syncSelection(e.component);
          },
          itemTemplate: function(item) {
            return "<div>" + item.text + "</div>";
          }
        }).dxTreeView('instance');

        function syncSelection(treeView) {
          var selectedCategories = treeView.getSelectedNodes()
            .map(function(node) {
              try {
                return node.itemData;
              } catch (e) {
                console.log(e);
              }

            });
          if (jQuery.isEmptyObject(selectedCategories)) {
            $('#selectedParentId').val('');
          }
          // console.log(selectedCategories);
          selectedCategoriesList.option("items", selectedCategories);
        }

        $("#showCheckBoxesMode").dxSelectBox({
          items: ["selectAll", "normal", "none"],
          value: "normal",
          onValueChanged: function(e) {
            treeView.option("showCheckBoxesMode", e.value);

            if (e.value === 'selectAll') {
              selectionModeSelectBox.option('value', 'multiple');
              recursiveCheckBox.option('disabled', false);
            }
            selectionModeSelectBox.option('disabled', e.value === 'selectAll');
          }
        });

        var selectionModeSelectBox = $("#selectionMode").dxSelectBox({
          items: ["multiple", "single"],
          value: "multiple",
          onValueChanged: function(e) {
            treeView.option("selectionMode", e.value);

            if (e.value === 'single') {
              recursiveCheckBox.option('value', false);
              treeView.unselectAll();
            }

            recursiveCheckBox.option('disabled', e.value === 'single');
          }
        }).dxSelectBox('instance');

        var recursiveCheckBox = $("#selectNodesRecursive").dxCheckBox({
          text: "Select Nodes Recursive",
          value: true,
          onValueChanged: function(e) {
            treeView.option("selectNodesRecursive", e.value);
          }
        }).dxCheckBox('instance');

        $("#selectByClick").dxCheckBox({
          text: "Select By Click",
          value: false,
          onValueChanged: function(e) {
            treeView.option("selectByClick", e.value);
          }
        });



      });
    </script>

    <script>
      $(document).on("click", ".browse", function() {
        var file = $(this).parents().find(".file");
        file.trigger("click");
      });
      $('input[type="file"]').change(function(e) {
        var fileName = e.target.files[0].name;
        $("#file").val(fileName);

        var reader = new FileReader();
        reader.onload = function(e) {
          // get loaded data and render thumbnail.
          document.getElementById("preview").src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
      });
    </script>
    @endpush

  </x-slot>
</x-layouts.admin>