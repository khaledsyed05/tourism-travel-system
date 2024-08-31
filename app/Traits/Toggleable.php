<?php

namespace App\Traits;

trait Toggleable
{
    public function toggle($field)
    {
        $this->$field = !$this->$field;
        $this->save();
        return $this;
    }

    public function scopeWhereOn($query, $field)
    {
        return $query->where($field, true);
    }

    public function scopeWhereOff($query, $field)
    {
        return $query->where($field, false);
    }
}
