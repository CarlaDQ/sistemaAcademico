<?php

namespace App\Extensions;

use App\Models\Usuario;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class EmailHashUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return Usuario::find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        return Usuario::where('id_usuario', $identifier)
            ->where('remember_token', $token)
            ->first();
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (!isset($credentials['email'])) {
            return null;
        }

        $emailHash = hash('sha256', strtolower(trim($credentials['email'])));

        return Usuario::where('email_hash', $emailHash)->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (!isset($credentials['password'])) {
            return false;
        }

        return password_verify($credentials['password'], $user->password_hash);
    }
}
