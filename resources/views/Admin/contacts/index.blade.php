<x-layouts.admin>
    @section('title', 'Help Center')
      <!-- Content Header (contact header) -->
      <x-slot name="breadcrumb">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1>Help Center</h1>
          </div>
          <div class="col-sm-12">
            {{ Breadcrumbs::render('common',['append' => [['label'=> 'Enquiry List', 'route'=> \Request::route()->getName()]]]) }}
          </div>
        </div>
      </x-slot>
    <?php
  $response = Gate::inspect('check-user', "enquries-create");
  $canCreate = true;
  if (!$response->allowed()) {
      $canCreate = false;
  }
  ?>
      <x-slot name="content">
        <!-- Main content -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Help Center</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0"> 
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th style="width:7%">#</th>
                      <th scope="col">@sortablelink('name', 'Name', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                      <th scope="col">Email</th>
                      <th scope="col">Message</th>
                      <th scope="col">@sortablelink('created_at', 'Created', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                      <th scope="col" class="actions" style="width:12%">Action</th>
                    </tr>                   
                  </thead>
                  @if($contacts->count() > 0)
                  <tbody>
                    @php
                    $i = (($contacts->currentPage() - 1) * ($contacts->perPage()) + 1)
                    @endphp
                    @foreach($contacts as $contact)
                  
                    <tr class="row-{{ $contact->id }}">
                      <td> {{$i}}. </td>
                      <td>{{$contact->name}}</td>
                      <td>{{$contact->email}}</td>
                      <td>{{$contact->message}}</td>
                      <td>{{ $contact->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                    <td class="actions action-btn-tab">
                        <a href="{{ route('admin.contacts.show',['contact' => $contact->id]) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" alt="View Enquiry" title="View Enquiry" data-original-title="View"><i class="fa fa-fw fa-eye"></i></a>
                        &nbsp;
                        @if($canCreate)
                        <!-- <a href="{{ route('admin.contacts.contactEdit',[$contact->id]) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Reply" title="Reply Enquiry" data-original-title="Reply"><i class="fa fa-reply"></i></a>
                        &nbsp; -->
                        <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete Enquiry" data-url="{{ route('admin.contacts.delete', $contact->id) }}" title="Delete Enquiry"  data-action="delete" data-message="Are you sure want to delete this enquiry?"><i class="fa fa-trash"></i></a>
                        @endif
                      </td>
                    </tr>
                    @php
                    $i++;
                    @endphp
                    @endforeach
                  </tbody>
                  @else
                  <tfoot>
                    <tr>
                      <td colspan='7' align='center'> <strong>Record Not Available</strong> </td>
                    </tr>
                  </tfoot>
                  @endif
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
              {{ $contacts->appends(Request::query())->links() }}
              </div>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.content -->
      </x-slot>
    </x-layouts.admin>