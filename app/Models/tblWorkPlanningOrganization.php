<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UuidForKey;
use App, DB, Config, Helper;
class tblWorkPlanningOrganization extends Model
{
    protected $table = 'tblWork_planning_organization';
}