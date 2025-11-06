<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['nama_event', 'category_id', 'deskripsi', 'tanggal_event'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
