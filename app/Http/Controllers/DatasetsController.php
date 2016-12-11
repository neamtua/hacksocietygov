<?php

namespace App\Http\Controllers;

use App\Dataset;
use App\Http\Requests\DatasetsEditRequest;
use App\Http\Requests\DatasetsRequest;
use App\Institution;
use Illuminate\Http\Request;
use Storage;
use Libraries;
use DB;

class DatasetsController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('read-datasets')) {
            abort('403');
        }

        return view('front.datasets.list');
    }

    public function create()
    {
        if (!auth()->user()->can('create-datasets')) {
            abort('403');
        }

        $institutions = Institution::orderBy('name', 'ASC')->get();

        return view('front.datasets.create')->with('institutions', $institutions);
    }

    public function add(DatasetsRequest $request)
    {
        if (!auth()->user()->can('create-datasets')) {
            abort('403');
        }

        $input = $request->all();

        # generare nume tabel
        $table = str_random(50).date('YmdHis');

        # upload fisier
        $file = $request->file('file');
        if ($file->isValid()) {
            $file->move(storage_path('app'), $table.'.csv');
        }

        $dataset = new Dataset();
        $dataset->name = $input['name'];
        $dataset->institution_id = $input['institution_id'];
        $dataset->description = $input['description'];
        $dataset->table_name = $table;
        $dataset->save();

        # parsam fisierul
        $data = new Libraries\CreateDataset(
            storage_path('app/'.$table.'.csv'),
            $table,
            $input['separator']
        );
        $data->createTable();
        $data->populateTable();

        unlink(storage_path('app/'.$table.'.csv'));

        return redirect()->back()->with('message', 'Setul de date a fost adaugat');
    }

    public function edit($id)
    {
        if (!auth()->user()->can('update-datasets')) {
            abort('403');
        }

        $dataset = Dataset::findOrFail($id);
        $institutions = Institution::orderBy('name', 'ASC')->get();

        return view('front.datasets.edit')
            ->with('dataset', $dataset)
            ->with('institutions', $institutions);
    }

    public function update(DatasetsEditRequest $request, $id)
    {
        if (!auth()->user()->can('update-datasets')) {
            abort('403');
        }

        $dataset = Dataset::findOrFail($id);
        $input = $request->all();

        $dataset->name = $input['name'];
        $dataset->institution_id = $input['institution_id'];
        $dataset->description = $input['description'];
        $dataset->save();

        return redirect()->back()->with('message', 'Setul de date a fost modificat');
    }

    public function view($id)
    {
        if (!auth()->user()->can('read-datasets')) {
            abort('403');
        }

        $dataset = Dataset::findOrFail($id);
        $columns = Libraries\DatasetHelper::getColumns($dataset->table_name);

        return view('front.datasets.view')
            ->with('dataset', $dataset)
            ->with('columns', $columns);
    }

    public function delete($id)
    {
        if (!auth()->user()->can('delete-datasets')) {
            abort('403');
        }

        $dataset = Dataset::findOrFail($id);
        if (!empty($dataset->table_name)) {
            DB::statement('DROP TABLE '.$dataset->table_name);
        }
        $dataset->delete();
    }

    public function ajax(Request $request)
    {
        if (!auth()->user()->can('read-datasets')) {
            abort('403');
        }

        $input = $request->all();

        $length = $input['length']?$input['length']:10;
        $draw = $input['draw']?$input['draw']:1;
        $start = $input['start']?$input['start']:0;
        $recordsTotal = Dataset::count();
        $search = $input['search']['value']?$input['search']['value']:'';

        $columns = [
            0 => 'name',
            1 => 'institution_id',
            2 => 'optiuni'
        ];

        $datasets = [];

        $datasetsSql = Dataset::whereRaw('1=1');

        if (!empty($search)) {
            $datasetsSql->where('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%');
        }

        $recordsFiltered = $datasetsSql->count();

        if ($length >= 0) {
            $datasetsSql = $datasetsSql->orderBy(
                $columns[$input['order'][0]['column']],
                $input['order'][0]['dir']
            )->take($length)->skip($start)->get();
        } else {
            $datasetsSql = $datasetsSql->orderBy(
                $columns[$input['order'][0]['column']],
                $input['order'][0]['dir']
            )->get();
        }

        foreach ($datasetsSql as $row) {
            $datasets[] = [
                $row->name,
                $row->institution->name,
                '<a href="'.url('datasets/'.$row->id.'/view').'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> Vizualizare</a>
                <a href="'.url('datasets/'.$row->id.'/edit').'" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o"></i> Modifica</a>
                <a href="'.url('datasets/'.$row->id).'" class="btn btn-xs btn-danger js-delete"><i class="fa fa-trash"></i> Sterge</a>'
            ];
        }

        return json_encode([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $datasets
        ]);
    }

    public function viewAjax(Request $request, $id)
    {
        if (!auth()->user()->can('read-datasets')) {
            abort('403');
        }

        $dataset = Dataset::findOrFail($id);
        $columns = Libraries\DatasetHelper::getColumns($dataset->table_name);
        $columnList = [];
        if (count($columns)) {
            foreach ($columns as $column) {
                $columnList[] = $column->Field;
            }
        }

        $input = $request->all();

        $length = $input['length']?$input['length']:10;
        $draw = $input['draw']?$input['draw']:1;
        $start = $input['start']?$input['start']:0;
        $recordsTotal = DB::table($dataset->table_name)->count();
        $search = $input['search']['value']?$input['search']['value']:'';

        $columns = $columnList;

        $datasets = [];

        $datasetsSql = DB::table($dataset->table_name)->whereRaw('1=1');

        if (!empty($search) && count($columnList)) {
            foreach ($columnList as $column) {
                if (isset($notFirst)) {
                    $datasetsSql->orWhere($column, 'LIKE', '%'.$search.'%');
                } else {
                    $datasetsSql->where($column, 'LIKE', '%'.$search.'%');
                    $notFirst = true;
                }
            }
        }

        $recordsFiltered = $datasetsSql->count();

        if ($length >= 0) {
            $datasetsSql = $datasetsSql->orderBy(
                $columns[$input['order'][0]['column']],
                $input['order'][0]['dir']
            )->take($length)->skip($start)->get();
        } else {
            $datasetsSql = $datasetsSql->orderBy(
                $columns[$input['order'][0]['column']],
                $input['order'][0]['dir']
            )->get();
        }

        foreach ($datasetsSql as $row) {
            $rowValues = [];
            if (count($columnList)) {
                foreach ($columnList as $column) {
                    $rowValues[] = $row->{$column};
                }
            }
            $datasets[] = $rowValues;
        }

        return json_encode([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $datasets
        ]);
    }
}
