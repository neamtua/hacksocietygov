<?php
namespace Libraries;

use Illuminate\Support\Facades\DB;

class DatasetHelper
{
    public static function getColumns($tableName)
    {
        $columns = DB::select(DB::raw('DESCRIBE '.$tableName));
        return $columns;
    }
}
