<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TogglableController extends Controller
{
    protected function toggle(Model $model, string $field)
    {
        if (!method_exists($model, 'toggle')) {
            return response()->json(['error' => 'Model does not support toggling'], 400);
        }

        $model->toggle($field);

        return response()->json([
            'message' => ucfirst($field) . ' status toggled successfully',
            $field => $model->$field
        ]);
    }

}
