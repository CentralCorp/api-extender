<?php

namespace Azuriom\Plugin\ApiExtender\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\ApiExtender\Models\ApiExtenderSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ApiCronController extends Controller
{
    /**
     * Execute cron tasks via API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function executeCron(Request $request): JsonResponse
    {
        try {
            if (!ApiExtenderSetting::getValue('cron_enabled', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cron API route is disabled'
                ], 403);
            }

            Log::info('API Cron execution started via API', [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'api_key' => $request->header('API-Key')
            ]);

            Artisan::call('schedule:run');
            
            $output = Artisan::output();
            
            ApiExtenderSetting::setValue('last_cron_execution', now(), 'datetime');
            $currentCount = ApiExtenderSetting::getValue('cron_execution_count', 0);
            ApiExtenderSetting::setValue('cron_execution_count', $currentCount + 1, 'integer');
            
            Log::info('API Cron execution completed successfully', [
                'output' => $output,
                'execution_count' => $currentCount + 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cron tasks executed successfully',
                'output' => $output,
                'executed_at' => now()->toISOString(),
                'execution_count' => $currentCount + 1
            ]);

        } catch (\Exception $e) {
            Log::error('API Cron execution failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cron execution failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cron status
     *
     * @return JsonResponse
     */
    public function status(): JsonResponse
    {
        $lastExecution = ApiExtenderSetting::getValue('last_cron_execution');
        
        return response()->json([
            'enabled' => ApiExtenderSetting::getValue('cron_enabled', false),
            'last_execution' => $lastExecution ? $lastExecution->toISOString() : null,
            'execution_count' => ApiExtenderSetting::getValue('cron_execution_count', 0),
            'server_time' => now()->toISOString()
        ]);
    }
}
