<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
        use LogsActivity;
        protected $fillable = ['nama_kategori', 'kode_kategori'];

    public function events()
    {
        return $this->hasMany(Event::class, 'category_id');
    }

        public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('category');
    }
}
