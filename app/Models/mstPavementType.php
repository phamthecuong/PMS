<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class mstPavementType extends Model
{
    protected $table = 'mstPavement_type';

    protected $appends = ["name", 'creater','updater' ];

    public function mstPavementLayer() 
    {
        return $this->belongsTo('App\Models\mstPavementLayer', 'pavement_layer_id', 'id');
    }
    public function mstSurface() 
    {
        return $this->belongsTo('App\Models\mstSurface', 'surface_id', 'id');
    }
    public function getCreaterAttribute() {
        return  @$this->userCreate->name ? @$this->userCreate->name :'';
    }
    public function getUpdaterAttribute() {
        return @$this->userUpdate->name ? @$this->userUpdate->name : '';
    }
    public function userCreate()
    {
        return $this->belongsTo('App\Models\user', 'created_by' , 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo('App\Models\user', 'updated_by' , 'id');
    }

    function getNameAttribute()
    {
        $lang = (\App::isLocale('en')) ? 'en' : 'vn';
        return $this->{"name_{$lang}"};
    }

    static public function getData()
    {
        $pavement = mstPavementType::get();
        foreach ($pavement as $p)
        {
            $select[] = [
              'name' => (Config::get('app.locale') == 'en') ? $p->name_en : $p->name_vn,
                'value'=> $p->id
            ];
        }
        return $select;
    }
    static public function toOption()
    {
        $pavement = mstPavementType::where('pavement_layer_id',2)->get();
        foreach ($pavement as $p)
        {
            $select[] = [
              'name' => (Config::get('app.locale') == 'en') ? $p->name_en : $p->name_vn,
                'value'=> $p->id
            ];
        }
        return $select;
    }
}
