@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Seturi de date - Vizualizare {{ $dataset->name }}
                </div>

                <div class="panel-body">
                    @if (count($columns))
                        <table id="dt_table" class="table display table-hover">
                            <thead>
                                <tr>
                                    @foreach ($columns as $column)
                                        <th>{{ $column->Field }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    @endif
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
                    "url": "{{ url('datasets/'.$dataset->id.'/ajax') }}",
                    "type": "POST"
                },
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
