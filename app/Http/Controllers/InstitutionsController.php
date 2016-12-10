<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionsRequest;
use App\Institution;
use Illuminate\Http\Request;
use DB;

class InstitutionsController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('read-institutions')) {
            abort('403');
        }

        return view('front.institutions.list');
    }

    public function create()
    {
        if (!auth()->user()->can('create-institutions')) {
            abort('403');
        }

        return view('front.institutions.create');
    }

    public function add(InstitutionsRequest $request)
    {
        if (!auth()->user()->can('create-institutions')) {
            abort('403');
        }

        $input = $request->all();

        $institution = new Institution();
        $institution->name = $input['name'];
        $institution->description = $input['description'];
        $institution->save();

        return redirect()->back()->with('message', 'Institutia a fost adaugata');
    }

    public function edit($id)
    {
        if (!auth()->user()->can('update-institutions')) {
            abort('403');
        }

        $institution = Institution::findOrFail($id);

        return view('front.institutions.edit')->with('institution', $institution);
    }

    public function update(InstitutionsRequest $request, $id)
    {
        if (!auth()->user()->can('update-institutions')) {
            abort('403');
        }

        $institution = Institution::findOrFail($id);
        $input = $request->all();

        $institution->name = $input['name'];
        $institution->description = $input['description'];
        $institution->save();

        return redirect()->back()->with('message', 'Institutia a fost modificata');
    }

    public function delete($id)
    {
        if (!auth()->user()->can('delete-institutions')) {
            abort('403');
        }

        $institution = Institution::findOrFail($id);
        $datasets = $institution->datasets()->get();
        if (count($datasets)) {
            foreach ($datasets as $dataset) {
                if (!empty($dataset->table_name)) {
                    DB::statement('DROP TABLE '.$dataset->table_name);
                }
                $dataset->delete();
            }
        }
        $institution->delete();
    }

    public function ajax(Request $request)
    {
        if (!auth()->user()->can('read-institutions')) {
            abort('403');
        }

        $input = $request->all();

        $length = $input['length']?$input['length']:10;
        $draw = $input['draw']?$input['draw']:1;
        $start = $input['start']?$input['start']:0;
        $recordsTotal = Institution::count();
        $search = $input['search']['value']?$input['search']['value']:'';

        $columns = [
            0 => 'name',
            1 => 'optiuni'
        ];

        $institutions = [];

        $institutionsSql = Institution::whereRaw('1=1');

        if (!empty($search)) {
            $institutionsSql->where('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%');
        }

        $recordsFiltered = $institutionsSql->count();

        if ($length >= 0) {
            $institutionsSql = $institutionsSql->orderBy(
                $columns[$input['order'][0]['column']],
                $input['order'][0]['dir']
            )->take($length)->skip($start)->get();
        } else {
            $institutionsSql = $institutionsSql->orderBy(
                $columns[$input['order'][0]['column']],
                $input['order'][0]['dir']
            )->get();
        }

        foreach ($institutionsSql as $row) {
            $institutions[] = [
                $row->name,
                '<a href="'.url('institutions/'.$row->id.'/edit').'" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o"></i> Modifica</a>
                <a href="'.url('institutions/'.$row->id).'" class="btn btn-xs btn-danger js-delete"><i class="fa fa-trash"></i> Sterge</a>'
            ];
        }

        return json_encode([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $institutions
        ]);
    }
}
