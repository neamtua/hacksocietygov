@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Institutii
                    <div class="pull-right">
                        @if (auth()->user()->can('create-institutions'))
                            <a href="{{ url('institutions/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Adauga</a>
                        @endif
                    </div>
                </div>

                <div class="panel-body">
                    <table id="dt_table" class="table display table-hover">
                        <thead>
                            <tr>
                                <th>Nume</th>
                                <th>Optiuni</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    @parent
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $('#dt_table').DataTable({
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "pageLength": 10,
                "ajax": {
                    "url": "{{ url('institutions/ajax') }}",
                    "type": "POST"
                },
                "columnDefs": [
                    { 'sortable': false, 'targets': [ 1 ] }
                ],
                "serverSide": true,
                "processing": true,
                "paging": true,
                "autoWidth": false,
                "stateSave": true,
                "info": true,
                "order": [
                    [0, "asc"]
                ]
            });
        });
    </script>
@stop
