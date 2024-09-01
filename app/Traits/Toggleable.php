<?php

namespace App\Traits;

trait Toggleable
{
    public function toggle(string $field)
    {
        $this->$field = !$this->$field;
        return $this->save();
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
