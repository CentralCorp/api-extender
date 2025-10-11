@extends('admin.layouts.admin')

@section('title', trans('apiextender::admin.cron.title'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">{{ trans('apiextender::admin.cron.info') }}</p>
        </div>
        @if(\Azuriom\Plugin\ApiExtender\Models\ApiExtenderSetting::getValue('cron_enabled', false))
            <span class="badge bg-success fs-6 px-3 py-2">
                <i class="bi bi-check-circle me-1"></i>
                {{ trans('apiextender::admin.cron.enabled') }}
            </span>
        @else
            <span class="badge bg-secondary fs-6 px-3 py-2">
                <i class="bi bi-x-circle me-1"></i>
                {{ trans('apiextender::admin.cron.disabled') }}
            </span>
        @endif
    </div>

    <!-- Configuration Section -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        {{ trans('apiextender::admin.cron.api_status') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('apiextender.admin.cron.toggle') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input fs-5" type="checkbox" id="cronEnabled" name="cron_enabled" 
                                           value="1" {{ \Azuriom\Plugin\ApiExtender\Models\ApiExtenderSetting::getValue('cron_enabled', false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="cronEnabled">
                                        {{ trans('apiextender::admin.cron.enable_api') }}
                                    </label>
                                    <div class="form-text">
                                        {{ trans('apiextender::admin.cron.usage_info') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-save me-1"></i>
                                    {{ trans('messages.actions.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ trans('apiextender::admin.cron.test_execution') }}
                    </h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    @if(\Azuriom\Plugin\ApiExtender\Models\ApiExtenderSetting::getValue('cron_enabled', false))
                        <form method="POST" action="{{ route('apiextender.admin.cron.test') }}" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-play-circle me-1"></i>
                                {{ trans('apiextender::admin.cron.test_execution') }}
                            </button>
                        </form>
                        <small class="text-muted text-center">
                            {{ trans('apiextender::admin.cron.usage_info') }}
                        </small>
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-exclamation-triangle display-6"></i>
                            <p class="mt-2">{{ trans('apiextender::admin.cron.disabled') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- API Endpoints & Execution Info -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-link-45deg me-2"></i>
                        {{ trans('apiextender::admin.cron.endpoints') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="endpoint-item mb-3 p-3 border rounded bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success me-2">GET/POST</span>
                            <code class="fs-6">/api/cron/execute</code>
                        </div>
                        <small class="text-muted">{{ trans('apiextender::admin.cron.execute_endpoint') }}</small>
                    </div>
                    
                    <div class="endpoint-item mb-3 p-3 border rounded bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info me-2">GET</span>
                            <code class="fs-6">/api/cron/status</code>
                        </div>
                        <small class="text-muted">{{ trans('apiextender::admin.cron.status_endpoint') }}</small>
                    </div>
                    
                    <div class="alert alert-info d-flex align-items-center mt-3 mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <div>
                            <strong>API Key Options:</strong><br>
                            <small>• Header: <code>API-Key: YOUR_KEY</code> OR <br>
                                      •  php-auth-user: <code>cron</code> and php-auth-pw: <code>YOUR_KEY</code></small>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        <small>{{ trans('apiextender::admin.cron.api_key_required') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        {{ trans('apiextender::admin.cron.execution_info') }}
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $lastExecution = \Azuriom\Plugin\ApiExtender\Models\ApiExtenderSetting::getValue('last_cron_execution');
                        $executionCount = \Azuriom\Plugin\ApiExtender\Models\ApiExtenderSetting::getValue('cron_execution_count', 0);
                    @endphp
                    
                    @if($lastExecution)
                        <div class="stats-grid">
                            <div class="stat-item mb-3 p-3 border rounded">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-event text-primary fs-4 me-3"></i>
                                    <div>
                                        <div class="fw-semibold">{{ trans('apiextender::admin.cron.last_execution') }}</div>
                                        <div class="text-muted">{{ $lastExecution->format('d/m/Y H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="stat-item p-3 border rounded">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-graph-up text-success fs-4 me-3"></i>
                                    <div>
                                        <div class="fw-semibold">{{ trans('apiextender::admin.cron.execution_count') }}</div>
                                        <div class="h5 text-success mb-0">{{ number_format($executionCount) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-clock display-6"></i>
                            <p class="mt-3">{{ trans('apiextender::admin.cron.no_execution') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Usage Instructions & Examples -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="bi bi-book me-2"></i>
                {{ trans('apiextender::admin.cron.usage') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-4">
                    <p class="lead">{{ trans('apiextender::admin.cron.usage_info') }}</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <h6 class="text-primary mb-3">
                        <i class="bi bi-terminal me-1"></i>
                        Method 1: Header Authentication
                    </h6>
                    <div class="code-block bg-dark text-light p-3 rounded mb-3">
                        <pre class="mb-0"><code class="text-light">curl -X POST {{ url('/api/apiextender/cron/execute') }} \
  -H "API-Key: YOUR_API_KEY_HERE" \
  -H "Content-Type: application/json"</code></pre>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <h6 class="text-success mb-3">
                        <i class="bi bi-code-square me-1"></i>
                        {{ trans('apiextender::admin.cron.response_example') }}
                    </h6>
                    <div class="code-block bg-dark text-light p-3 rounded">
                        <pre class="mb-0"><code class="text-light">{
  "success": true,
  "message": "Cron tasks executed successfully",
  "output": "...",
  "executed_at": "2025-10-11T10:30:00.000Z"
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection