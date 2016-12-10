<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    protected $table = 'datasets';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function institution()
    {
        return $this->belongsTo('App\Institution', 'institution_id', 'id');
    }
}
