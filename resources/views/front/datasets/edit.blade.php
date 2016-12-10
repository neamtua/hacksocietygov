@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Seturi de date - Modificare
                </div>

                <div class="panel-body">
                    @if (Session::has('message'))
                        <div class="alert alert-success"><button class="close" data-close="alert"></button>{{ Session::get('message') }}</div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/datasets/'.$dataset->id.'/edit') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nume</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $dataset->name }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('institution_id') ? ' has-error' : '' }}">
                            <label for="institution_id" class="col-md-4 control-label">Institutie</label>

                            <div class="col-md-6">
                                <select name="institution_id" id="institution_id" class="form-control">
                                    @if(count($institutions))
                                        @foreach ($institutions as $institution)
                                            <option value="{{ $institution->id }}" {{ $dataset->institution->id == $institution->id?'selected':'' }}>{{ $institution->name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                @if ($errors->has('institution_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('institution_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="col-md-4 control-label">Descriere</label>

                            <div class="col-md-6">
                                <textarea id="description" class="form-control" name="description">{{ $dataset->description }}</textarea>

                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-success">
                                    Salveaza
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
