<?php

namespace Azuriom\Plugin\ApiExtender\Controllers\Api;
use Azuriom\Extensions\Plugin\PluginManager;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Server;
use Azuriom\Models\Role;
use Azuriom\Models\User;
use Azuriom\Models\SocialLink;
use Azuriom\Plugin\ApiExtender\Middleware\VerifyApiKey;


class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(VerifyApiKey::class);
    }

    /**
     * Show the plugin API default page.
     */
    public function index()
    {
        $maintenance = setting('maintenance.enabled', "0");
        $maintenanceMessage = setting('maintenance.message', 'The server is currently under maintenance. Please check back later.');
        return response()->json(['maintenance' => $maintenance, 'message' => $maintenanceMessage]);
    }

    public function money()
    {
        $moneyName = setting('money', 'points');
        return response()->json(['money' => $moneyName]);
    }

    public function servers()
    {
        $servers = Server::all();
        return response()->json($servers);
    }

    public function roles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function users()
    {
        $users = User::select('id', 'name', 'role_id', 'is_banned')->get();
        return response()->json($users);
    }
    public function social()
    {
        $social = SocialLink::select('type', 'value', 'position')->get();
        return response()->json($social);
    }
    

}
