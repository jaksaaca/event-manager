<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Event extends Model
{
    use LogsActivity;
    protected $fillable = ['nama_event', 'category_id', 'deskripsi', 'tanggal_event'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('event');
    }
}
