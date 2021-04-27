<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SvgFile extends Model
{
     // use soft delete instead of permanent delete
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'svg_files';

    protected $fillable = ['path', 'avata_path', 'name', 'width', 'height', 'created_at', 'updated_at'];
}
