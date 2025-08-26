<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskMessage extends Model
{ 
    protected $guarded = [];
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function views() 
    {
        return $this->hasMany(TaskMessageView::class, 'message_id');
    }
}
