<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function education() {
        return $this->hasMany(Education::class, 'person_id', 'id');
    }
    public function career() {
        return $this->hasMany(Career::class, 'person_id', 'id');
    }
    public function project() {
        return $this->hasMany(ProjectDone::class, 'person_id', 'id');
    }
    public function referee() {
        return $this->hasMany(Referee::class, 'person_id', 'id');
    }
}
