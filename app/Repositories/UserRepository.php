<?php

namespace App\Repositories;

use App\User;
use Carbon\Carbon;

class UserRepository
{
	public function browse()
	{
		# code...
	}

	public function read()
	{
		# code...
	}

	public function edit()
	{
		# code...
	}

	public function add(array $data)
	{
		try {
			$data['password'] = bcrypt($data['password']);

			$user = User::create($data);
		} catch (\Illuminate\Database\QueryException $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}

        return $user;
	}

	public function delete()
	{
		# code...
	}
}