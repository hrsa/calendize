<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property DateTime $date
 * @property string $page
 * @property int $views
 */
class PageView extends Model
{
    public $timestamps = false;

    public $fillable = ['date', 'page', 'views'];
}
