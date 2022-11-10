<?php
/**
 * Created by PhpStorm.
 * User: Apporio
 * Date: 9/28/2018
 * Time: 11:09 PM
 */

namespace App;


use Auth;
use Illuminate\Support\Facades\Session;

trait MerchantTrait
{
    public function checkPermission()
    {
        $permissions = Session::get('permissions');
        if (empty($permissions)) {
            $this->PermissionSet();
        }
    }

    public function PermissionSet()
    {
        $admin = Auth::user('merchant')->load('role.permission')->toArray();
        $permissions = array_pluck($admin['role'], 'permission');
        $permissions = array_pluck($permissions[0], 'slug');
        Session::put('permissions', $permissions);
    }
}