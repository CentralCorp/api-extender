<?php

namespace Azuriom\Plugin\ApiExtender\Controllers\Admin;

use Azuriom\Models\Server;
use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\ApiExtender\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Show the home admin page of the plugin.
     */
    public function index()
    {
        $servers = Server::all();
        return view('apiextender::admin.index', compact('servers'));
    }

    public function apikeys()
    {
        $apiKeys = ApiKey::all();
        return view('apiextender::admin.apikeys', compact('apiKeys'));
    }

    public function generateApiKey(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $apiKey = ApiKey::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'api_key' => Str::random(32),
            'is_active' => true
        ]);

        return redirect()->route('apiextender.admin.api-keys.index')
            ->with('success', trans('apiextender::admin.api.key_generated'));
    }

    public function deleteApiKey(ApiKey $apiKey)
    {
        $apiKey->delete();
        
        return redirect()->route('apiextender.admin.api-keys.index')
            ->with('success', trans('apiextender::admin.api.key_deleted'));
    }

    public function toggleApiKey(ApiKey $apiKey)
    {
        $apiKey->update([
            'is_active' => !$apiKey->is_active
        ]);

        return redirect()->route('apiextender.admin.api-keys.index')
            ->with('success', trans('apiextender::admin.api.key_updated'));
    }

    public function images()
    {
        $requests = [
            'default' => ['full', 'bust', 'face'],
            'marching' => ['full', 'bust', 'face'],
            'walking' => ['full', 'bust', 'face'],
            'criss_cross' => ['full', 'bust', 'face'],
            'ultimate' => ['full', 'bust', 'face'],
            'isometric' => ['full', 'bust', 'face', 'head'],
            'head' => ['full'],
            'cheering' => ['full', 'bust', 'face'],
            'relaxing' => ['full', 'bust', 'face'],
            'trudging' => ['full', 'bust', 'face'],
            'cowering' => ['full', 'bust', 'face'],
            'pointing' => ['full', 'bust', 'face'],
            'lunging' => ['full', 'bust', 'face'],
            'dungeons' => ['full', 'bust', 'face'],
            'facepalm' => ['full', 'bust', 'face'],
            'sleeping' => ['full', 'bust'],
            'dead' => ['full', 'bust', 'face'],
            'archer' => ['full', 'bust', 'face'],
            'kicking' => ['full', 'bust', 'face'],
            'mojavatar' => ['full', 'bust'],
            'reading' => ['full', 'bust', 'face'],
            'high_ground' => ['full', 'bust', 'face'],
            'clown' => ['full', 'bust', 'face'],
        ];

        $perPage = 3; 
        $currentPage = request()->get('page', 1);
        
        $totalPages = ceil(count($requests) / $perPage);
        
        if ($currentPage < 1) {
            $currentPage = 1;
        } elseif ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedRequests = array_slice($requests, $offset, $perPage, true);

        return view('apiextender::admin.images', [
            'requests' => $paginatedRequests,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalImages' => count($requests)
        ]);
    }
}
