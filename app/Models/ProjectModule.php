<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectModule extends Model
{
    protected $guarded = [];

    public function clientProject()
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }
}
