<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $table = 'institutions';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function datasets()
    {
        return $this->hasMany('App\Dataset', 'institution_id', 'id');
    }
}
