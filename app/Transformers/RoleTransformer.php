<?php
/**
 * Created by PhpStorm.
 * User: JIANG
 * Date: 2019/6/21
 * Time: 14:49
 */

namespace App\Transformers;


use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

class RoleTransformer extends TransformerAbstract
{
   public function transform(Role $role){
       return [
           'id'=>$role->id,
           'name'=>$role->name,
       ];
   }
}
