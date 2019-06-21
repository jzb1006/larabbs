<?php
/**
 * Created by PhpStorm.
 * User: JIANG
 * Date: 2019/6/21
 * Time: 14:30
 */

namespace App\Transformers;


use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Permission;

class PermissionsTransform extends TransformerAbstract
{
   public function transform(Permission $permission){
       return [
           'id'=>$permission->id,
           'name'=>$permission->name,
       ];
   }
}
