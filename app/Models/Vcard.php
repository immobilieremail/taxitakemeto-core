<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A Vcard is how a user chooses to present themselves to other
 * users. It contains a name.
 *
 * TODO: include picture
 */
class Vcard extends Model
{
    protected $fillable = ['name'];
}
