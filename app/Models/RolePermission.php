<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Yajra\DataTables\Html\Editor\Fields\BelongsTo;

class RolePermission extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'role_id',
        'permission_id',
    ];


    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

}
