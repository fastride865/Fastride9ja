<?php

namespace App\Listeners;
use Laravel\Passport\Events\AccessTokenCreated;
use DB;
class PassportTokennewCreated
{
    public function handle(AccessTokenCreated $event)
    {

		$get_current_driver = \Config::get('auth.guards.api.provider');

			switch($get_current_driver){
				case 'users':
  				DB::table('oauth_access_tokens')
  				->where('id', '=', $event->tokenId)
  				->where('user_id', $event->userId)
  				->where('client_id', $event->clientId)
  				->update(['name' => $get_current_driver]);
  				break;
				case 'drivers':
  				DB::table('oauth_access_tokens')
  				->where('id', '=', $event->tokenId)
  				->where('user_id', $event->userId)
  				->where('client_id', $event->clientId)
  				->update(['name' => $get_current_driver]);
  				break;
				case 'restaurants':
				    $login = 'restaurant/admin/merchant/restaurant/login';
				    break;
				default:
				    $login = '/login';
				    break;
			}

		// DB::table('oauth_access_tokens')
    //         ->where('id', '<>', $event->tokenId)
    //         ->where('user_id', $event->userId)
    //         ->where('client_id', $event->clientId)
    //         ->update(['revoked' => true]);
    }
}
