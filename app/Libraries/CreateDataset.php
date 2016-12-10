<?php
namespace Libraries;

use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class CreateDataset
{
    protected $filePath;
    protected $tableName;
    protected $separator;
    protected $file;
    protected $columns;

    public function __construct($filePath, $tableName, $separator)
    {
        $this->filePath = $filePath;
        $this->tableName = $tableName;
        $this->separator = $separator;

        $this->file = Reader::createFromPath($this->filePath);
        $this->file->setDelimiter($this->separator);
    }

    private function getColumns()
    {
        $columns = $this->file->fetchOne(0);
        return $columns;
    }

    public function createTable()
    {
        $this->columns = $this->getColumns();
        if (count($this->columns)) {
            $query = '
                CREATE TABLE `'.$this->tableName.'` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,';
            foreach ($this->columns as $column) {
                $query .= '`'.str_slug($column).'` longtext,';
            }
            $query .= '
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ';
            DB::statement($query);
        }
    }

    public function populateTable()
    {
        $results = $this->file->setOffset(1)->fetchAll();
        foreach ($results as $row) {
            $insert = [];
            $i = 0;
            foreach ($this->columns as $column) {
                $insert[str_slug($column)] = $row[$i];
                $i++;
            }
            DB::table($this->tableName)->insert($insert);
        }
    }
}
