<?php


namespace Modules\Auth\Services;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Entities\AuthPlugin;

class AuthLogic extends AuthService
{
      public function register($allRequest,$item,$type){

          $auth = AuthPlugin::create([
              'model_id'=>$item->id,
              'model_type'=>$type,
              'email'=>$allRequest['email'],
              'phone'=>$allRequest['phone'],
              'password'=> bcrypt($allRequest['password'])
          ]);

      }


      public function updateProfile($allRequest,$item){

          $auth = $item->update([
              'email'=>$allRequest['email'] ??$item->email ,
              'phone'=>$allRequest['phone'] ??$item->phone
          ]);

      }

}
