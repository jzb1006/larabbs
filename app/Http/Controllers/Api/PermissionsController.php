<?php

namespace App\Http\Controllers\Api;

use App\Transformers\PermissionsTransform;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    //
    public function index(){
        $permissions = $this->user()->getAllPermissions();
        return $this->response->collection($permissions,new PermissionsTransform());
    }
}
