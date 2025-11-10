@extends('admin.layouts.admin')

@section('title', trans('apiextender::messages.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-code-square me-2"></i>{{ trans('apiextender::messages.title') }}
                    </h5>
                </div>
                <div class="col-auto">
                    <span class="badge bg-primary" id="endpoint-count"></span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <h4 class="alert-heading">
                    <i class="bi bi-key me-2"></i>{{ trans('apiextender::messages.auth.title') }}
                </h4>
                <p class="mb-2">{{ trans('apiextender::messages.auth.description') }}</p>
                <code class="d-block p-2 bg-white rounded">{{ trans('apiextender::messages.auth.header') }}</code>
            </div>

            <!-- Barre de recherche -->
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           id="endpoint-search" 
                           placeholder="{{ trans('apiextender::admin.endpoints.search_placeholder') }}">
                    <button class="btn btn-outline-secondary" type="button" id="clear-search">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Filtres -->
            <div class="mb-4">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active" data-filter="all">
                        {{ trans('apiextender::admin.endpoints.all') }} <span class="badge bg-primary ms-1" id="count-all"></span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" data-filter="GET">
                        GET <span class="badge bg-success ms-1" id="count-get"></span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" data-filter="POST">
                        POST <span class="badge bg-warning ms-1" id="count-post"></span>
                    </button>
                </div>
                <button class="btn btn-sm btn-outline-secondary ms-2" id="toggle-all">
                    <i class="bi bi-eye me-1"></i>{{ trans('apiextender::admin.endpoints.show_all') }}
                </button>
            </div>

            <div class="row">
                <div class="col-md-12" id="endpoints-container">
            <div class="row">
                <div class="col-md-12" id="endpoints-container">
                    <!--  endpoints  -->
                </div>
            </div>
        </div>
    </div>

    <!-- Template pour les endpoints -->
    <template id="endpoint-template">
        <div class="endpoint-card mb-3" data-endpoint>
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="mb-2">
                        <span class="method-badge me-2" data-method></span>
                        <h5 class="d-inline-block mb-0" data-title></h5>
                    </div>
                    <div class="endpoint-url-container mb-2">
                        <code class="endpoint-url" data-url></code>
                        <button class="btn btn-sm btn-link copy-btn" data-copy-url title="{{ trans('apiextender::admin.endpoints.copy_url') }}">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <p class="text-muted small mb-2" data-description></p>
                </div>
                <button class="btn btn-sm btn-outline-primary toggle-details" data-toggle>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="endpoint-details" style="display: none;">
                <hr>
                <h6 class="mb-2">
                    <i class="bi bi-terminal me-1"></i>{{ trans('apiextender::admin.endpoints.curl_example') }}
                </h6>
                <div class="code-container position-relative">
                    <pre class="bg-light p-3 rounded mb-0"><code data-example></code></pre>
                    <button class="btn btn-sm btn-outline-secondary copy-btn-curl" data-copy-curl>
                        <i class="bi bi-clipboard"></i> {{ trans('apiextender::admin.endpoints.copy') }}
                    </button>
                </div>
                <div data-params></div>
            </div>
        </div>
    </template>

    <script>
        const endpoints = [
            {
                title: '{{ trans('apiextender::messages.endpoints.root.title') }}',
                method: 'GET',
                url: 'api/apiextender/',
                fullUrl: '{{ url('/api/apiextender/') }}',
                description: '{{ trans('apiextender::messages.endpoints.root.description') }}',
                example: 'curl -X GET "{{ url('/api/apiextender/') }}" -H "API-Key: votre_cle_api"'
            },
            {
                title: '{{ trans('apiextender::messages.endpoints.money.title') }}',
                method: 'GET',
                url: 'api/apiextender/money',
                fullUrl: '{{ url('/api/apiextender/money') }}',
                description: '{{ trans('apiextender::messages.endpoints.money.description') }}',
                example: 'curl -X GET "{{ url('/api/apiextender/money') }}" -H "API-Key: votre_cle_api"'
            },
            {
                title: '{{ trans('apiextender::messages.endpoints.social.title') }}',
                method: 'GET',
                url: 'api/apiextender/social',
                fullUrl: '{{ url('/api/apiextender/social') }}',
                description: '{{ trans('apiextender::messages.endpoints.social.description') }}',
                example: 'curl -X GET "{{ url('/api/apiextender/social') }}" -H "API-Key: votre_cle_api"'
            },
            {
                title: '{{ trans('apiextender::messages.endpoints.servers.title') }}',
                method: 'GET',
                url: 'api/apiextender/servers',
                fullUrl: '{{ url('/api/apiextender/servers') }}',
                description: '{{ trans('apiextender::messages.endpoints.servers.description') }}',
                example: 'curl -X GET "{{ url('/api/apiextender/servers') }}" -H "API-Key: votre_cle_api"'
            },
            {
                title: '{{ trans('apiextender::messages.endpoints.roles.title') }}',
                method: 'GET',
                url: 'api/apiextender/roles',
                fullUrl: '{{ url('/api/apiextender/roles') }}',
                description: '{{ trans('apiextender::messages.endpoints.roles.description') }}',
                example: 'curl -X GET "{{ url('/api/apiextender/roles') }}" -H "API-Key: votre_cle_api"'
            },
            {
                title: '{{ trans('apiextender::messages.endpoints.users.title') }}',
                method: 'GET',
                url: 'api/apiextender/users',
                fullUrl: '{{ url('/api/apiextender/users') }}',
                description: '{{ trans('apiextender::messages.endpoints.users.description') }}',
                example: 'curl -X GET "{{ url('/api/apiextender/users') }}" -H "API-Key: votre_cle_api"'
            },
            @plugin('shop')
            {
                title: '{{ trans('apiextender::admin.endpoints.shop.categories') }}',
                method: 'GET',
                url: 'api/apiextender/shop/categories',
                fullUrl: '{{ url('/api/apiextender/shop/categories') }}',
                description: '{{ trans('apiextender::admin.endpoints.shop.categories_desc') }}',
                example: 'curl -X GET "{{ url('/api/apiextender/shop/categories') }}" -H "API-Key: votre_cle_api"'
            },
            {
                title: '{{ trans('apiextender::admin.endpoints.shop.payments') }}',
                method: 'GET',
                url: 'api/apiextender/shop/payments',
                fullUrl: '{{ url('/api/apiextender/shop/payments') }}',
                description: '{{ trans('apiextender::admin.endpoints.shop.payments_desc') }}',
                example: 'curl -X GET "{{ url('/api/apiextender/shop/payments') }}" -H "API-Key: votre_cle_api"'
            },
            {
                title: '{{ trans('apiextender::admin.endpoints.shop.giftcard') }}',
                method: 'POST',
                url: 'api/apiextender/shop/giftcard',
                fullUrl: '{{ url('/api/apiextender/shop/giftcard') }}',
                description: '{{ trans('apiextender::admin.endpoints.shop.giftcard_desc') }}',
                example: 'curl -X POST "{{ url('/api/apiextender/shop/giftcard') }}" -H "API-Key: votre_cle_api" -H "Content-Type: application/json" -d "{\\"balance\\": 50.00}"',
                params: '<div class="mt-3"><h6 class="small"><i class="bi bi-gear me-1"></i>{{ trans('apiextender::admin.endpoints.parameters') }}</h6><ul class="small mb-0"><li><code>balance</code> {{ trans('apiextender::admin.endpoints.shop.giftcard_param_balance') }}</li><li><code>code</code> {{ trans('apiextender::admin.endpoints.shop.giftcard_param_code') }}</li><li><code>start_at</code> {{ trans('apiextender::admin.endpoints.shop.giftcard_param_start') }}</li><li><code>expire_at</code> {{ trans('apiextender::admin.endpoints.shop.giftcard_param_expire') }}</li></ul></div>'
            },
            @endplugin
        ];

        let currentFilter = 'all';
        let showAll = false;

        function renderEndpoints(filter = 'all', searchTerm = '') {
            const container = document.getElementById('endpoints-container');
            const template = document.getElementById('endpoint-template');
            container.innerHTML = '';

            let filteredEndpoints = endpoints.filter(ep => {
                const matchesFilter = filter === 'all' || ep.method === filter;
                const matchesSearch = !searchTerm || 
                    ep.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    ep.url.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    ep.description.toLowerCase().includes(searchTerm.toLowerCase());
                return matchesFilter && matchesSearch;
            });

            const displayCount = showAll ? filteredEndpoints.length : Math.min(3, filteredEndpoints.length);
            
            filteredEndpoints.slice(0, displayCount).forEach(endpoint => {
                const clone = template.content.cloneNode(true);
                
                const methodBadge = clone.querySelector('[data-method]');
                methodBadge.textContent = endpoint.method;
                methodBadge.classList.add(endpoint.method === 'GET' ? 'bg-success' : 'bg-warning');
                
                clone.querySelector('[data-title]').textContent = endpoint.title;
                clone.querySelector('[data-url]').textContent = endpoint.url;
                clone.querySelector('[data-description]').textContent = endpoint.description;
                clone.querySelector('[data-example]').textContent = endpoint.example;
                
                if (endpoint.params) {
                    clone.querySelector('[data-params]').innerHTML = endpoint.params;
                }
                
                const copyUrlBtn = clone.querySelector('[data-copy-url]');
                copyUrlBtn.addEventListener('click', () => {
                    copyToClipboard(endpoint.fullUrl).then(() => {
                        showToast('{{ trans('apiextender::admin.endpoints.url_copied') }}');
                    }).catch(() => {
                        showToast('Erreur lors de la copie');
                    });
                });
                
                const copyCurlBtn = clone.querySelector('[data-copy-curl]');
                copyCurlBtn.addEventListener('click', () => {
                    copyToClipboard(endpoint.example).then(() => {
                        showToast('{{ trans('apiextender::admin.endpoints.curl_copied') }}');
                    }).catch(() => {
                        showToast('Erreur lors de la copie');
                    });
                });
                
                const toggleBtn = clone.querySelector('[data-toggle]');
                const details = clone.querySelector('.endpoint-details');
                toggleBtn.addEventListener('click', () => {
                    const isVisible = details.style.display !== 'none';
                    details.style.display = isVisible ? 'none' : 'block';
                    toggleBtn.querySelector('i').className = isVisible ? 'bi bi-chevron-down' : 'bi bi-chevron-up';
                });
                
                container.appendChild(clone);
            });

            if (!showAll && filteredEndpoints.length > 3) {
                const showMoreBtn = document.createElement('div');
                showMoreBtn.className = 'text-center mt-3';
                showMoreBtn.innerHTML = `<button class="btn btn-outline-primary" id="show-more-btn">
                    <i class="bi bi-chevron-down me-1"></i>{{ trans('apiextender::admin.endpoints.show_more', ['count' => '${filteredEndpoints.length - 3}']) }}
                </button>`.replace('${filteredEndpoints.length - 3}', filteredEndpoints.length - 3);
                container.appendChild(showMoreBtn);
                
                document.getElementById('show-more-btn').addEventListener('click', () => {
                    showAll = true;
                    renderEndpoints(currentFilter, document.getElementById('endpoint-search').value);
                });
            }

            updateCounts();
        }

        function updateCounts() {
            const getCount = endpoints.filter(ep => ep.method === 'GET').length;
            const postCount = endpoints.filter(ep => ep.method === 'POST').length;
            
            document.getElementById('endpoint-count').textContent = `${endpoints.length} endpoints`;
            document.getElementById('count-all').textContent = endpoints.length;
            document.getElementById('count-get').textContent = getCount;
            document.getElementById('count-post').textContent = postCount;
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                return new Promise((resolve, reject) => {
                    document.execCommand('copy') ? resolve() : reject();
                    textArea.remove();
                });
            }
        }

        document.getElementById('endpoint-search').addEventListener('input', (e) => {
            showAll = false;
            renderEndpoints(currentFilter, e.target.value);
        });

        document.getElementById('clear-search').addEventListener('click', () => {
            document.getElementById('endpoint-search').value = '';
            showAll = false;
            renderEndpoints(currentFilter, '');
        });

        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.dataset.filter;
                showAll = false;
                renderEndpoints(currentFilter, document.getElementById('endpoint-search').value);
            });
        });

        document.getElementById('toggle-all').addEventListener('click', function() {
            showAll = !showAll;
            this.innerHTML = showAll ? 
                '<i class="bi bi-eye-slash me-1"></i>{{ trans('apiextender::admin.endpoints.show_less') }}' : 
                '<i class="bi bi-eye me-1"></i>{{ trans('apiextender::admin.endpoints.show_all') }}';
            renderEndpoints(currentFilter, document.getElementById('endpoint-search').value);
        });

        renderEndpoints();
    </script>

    <style>
        .endpoint-card {
            background: var(--bs-body-bg);
            padding: 1.25rem;
            border-radius: 0.5rem;
            border: 1px solid var(--bs-border-color);
            transition: all 0.3s ease;
        }
        
        .method-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 0.35rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
        }
        
        .endpoint-url-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--bs-secondary-bg);
            padding: 0.75rem;
            border-radius: 0.35rem;
            border-left: 3px solid var(--bs-primary);
        }
        
        .endpoint-url {
            background: transparent;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: var(--bs-body-color);
            flex: 1;
        }
        
        .copy-btn {
            padding: 0.25rem 0.5rem;
            color: var(--bs-secondary);
            transition: color 0.2s;
            text-decoration: none;
        }
        
        .copy-btn:hover {
            color: var(--bs-primary);
        }
        
        .toggle-details {
            border: none;
            background: transparent;
            color: var(--bs-primary);
        }
        
        .endpoint-details {
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .code-container {
            position: relative;
        }
        
        .code-container pre {
            margin-bottom: 0;
            font-size: 0.85rem;
            background: var(--bs-secondary-bg) !important;
            color: var(--bs-body-color);
            border: 1px solid var(--bs-border-color);
        }

        .code-container pre code {
            color: var(--bs-body-color);
        }
        
        .copy-btn-curl {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        
        .copy-btn-curl:hover {
            opacity: 1;
        }
        
        .toast-notification {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--bs-success);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            z-index: 9999;
            opacity: 0;
            transform: translateY(1rem);
            transition: all 0.3s ease;
        }
        
        .toast-notification.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        #endpoint-search {
            border: 1px solid var(--bs-border-color);
            background: var(--bs-body-bg);
            color: var(--bs-body-color);
            transition: border-color 0.3s;
        }
        
        #endpoint-search:focus {
            border-color: var(--bs-primary);
            background: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        #endpoint-search::placeholder {
            color: var(--bs-secondary-color);
        }
        
        .input-group-text {
            border: 1px solid var(--bs-border-color);
            color: var(--bs-body-color);
        }

        .btn-group .btn.active {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            color: white;
        }
        
        hr {
            margin: 1rem 0;
            opacity: 0.1;
            border-color: var(--bs-border-color);
        }

        .alert-info {
            background-color: rgba(var(--bs-info-rgb), 0.1);
            border-color: rgba(var(--bs-info-rgb), 0.3);
            color: var(--bs-body-color);
        }

        .alert-info .alert-heading {
            color: var(--bs-body-color);
        }

        .alert-info code {
            background: var(--bs-body-bg);
            color: var(--bs-primary);
            border: 1px solid var(--bs-border-color);
        }

        .text-muted {
            color: var(--bs-secondary-color) !important;
        }

        .badge {
            font-weight: 600;
        }

        .btn-outline-primary, .btn-outline-secondary, .btn-outline-success, .btn-outline-warning {
            border-width: 1px;
        }
    </style>
@endsection
