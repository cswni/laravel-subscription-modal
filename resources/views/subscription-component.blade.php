<div>
    @if($isConfigured)
        <div class="subscription-component-container">
            <!-- Badge flotante con efectos modernos -->
            <div class="subscription-badge subscription-badge-{{ $badgeColor }}" wire:click="openModal" title="Ver detalles de suscripción">
                <div class="badge-glow"></div>
                <div class="badge-content">
                    <span class="subscription-days">{{ $remainingDays }}</span>
                    <span class="subscription-label">días</span>
                </div>
                <div class="badge-pulse"></div>
            </div>

            <!-- Modal de suscripción con diseño moderno -->
            @if($showModal)
            <div class="subscription-modal-overlay" wire:click="closeModal">
                <div class="subscription-modal" wire:click.stop>
                    <!-- Header del modal con gradiente -->
                    <div class="modal-header">
                        <div class="header-content">
                            <div class="header-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="modal-title">Estado de Suscripción</h3>
                        </div>
                        @if($remainingDays > 0)
                        <button wire:click="closeModal" class="close-btn">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        @endif
                    </div>

                    <div class="modal-content">
                        @if($subscriptionData)
                            <!-- Información del usuario con diseño moderno -->
                            @if($userInfo)
                            <div class="user-info-card">
                                <div class="user-avatar">
                                    <div class="avatar-ring"></div>
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="user-details">
                                    <h4 class="user-name">{{ $userInfo }}</h4>
                                    <div class="user-status">
                                        <span class="status-dot"></span>
                                        <span class="status-text">Cliente activo</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Estado general con diseño moderno -->
                            <div class="status-overview">
                                <div class="status-card status-{{ $badgeColor }}">
                                    <div class="status-icon">
                                        @if($badgeColor === 'green')
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @elseif($badgeColor === 'orange')
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="status-info">
                                        <h5 class="status-title">
                                            @if($badgeColor === 'green')
                                                Suscripción activa
                                            @elseif($badgeColor === 'orange')
                                                Suscripción por vencer
                                            @else
                                                Suscripción vencida
                                            @endif
                                        </h5>
                                        <p class="status-days">{{ $remainingDays }} días restantes</p>
                                    </div>
                                    <div class="status-glow"></div>
                                </div>
                            </div>

                            <!-- Licencias por base de datos con diseño moderno -->
                            @if($licenseStatus && is_array($licenseStatus))
                            <div class="licenses-section">
                                <div class="section-header">
                                    <h4 class="section-title">Licencia adquirida</h4>
                                    <div class="section-line"></div>
                                </div>
                                <div class="licenses-grid">
                                    @foreach($licenseStatus as $index => $dbLicense)
                                        <div class="license-card">
                                            <div class="license-header">
                                                <h5 class="database-name">{{ $dbLicense['database'] ?? 'Base de datos ' . ($index + 1) }}</h5>
                                                @if(isset($dbLicense['license_status']) && is_array($dbLicense['license_status']))
                                                    @if(isset($dbLicense['license_status']['overdue']) && $dbLicense['license_status']['overdue'])
                                                        <span class="status-badge overdue">
                                                            <span class="badge-dot"></span>
                                                            Vencida
                                                        </span>
                                                    @else
                                                        <span class="status-badge active">
                                                            <span class="badge-dot"></span>
                                                            Activa
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="status-badge inactive">
                                                        <span class="badge-dot"></span>
                                                        Inactiva
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if(isset($dbLicense['license_status']) && is_array($dbLicense['license_status']))
                                                <div class="license-details">
                                                    <div class="detail-item">
                                                        <span class="detail-label">Días restantes:</span>
                                                        <span class="detail-value">{{ $dbLicense['license_status']['dias'] ?? 0 }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Tipo de licencia:</span>
                                                        <span class="detail-value">{{ $dbLicense['license_status']['license_type'] ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Último pago registrado:</span>
                                                        <span class="detail-value">
                                                            @if(isset($dbLicense['license_status']['last_payment_date']) && $dbLicense['license_status']['last_payment_date'] !== 'N/A')
                                                                {{ \Carbon\Carbon::parse($dbLicense['license_status']['last_payment_date'])->format('d/m/Y') }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Último dia con acceso:</span>
                                                        <span class="detail-value">
                                                            @if(isset($dbLicense['highest_valid_date']) && $dbLicense['highest_valid_date'])
                                                                {{ \Carbon\Carbon::parse($dbLicense['highest_valid_date'])->format('d/m/Y') }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="license-details">
                                                    <p class="no-license">{{ $dbLicense['license_status'] ?? 'Sin información de licencia' }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @else
                            <!-- Estado de error con diseño moderno -->
                            <div class="error-state">
                                <div class="error-icon">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <h4 class="error-title">Error al cargar datos</h4>
                                <p class="error-message">
                                    @if(!$subscriptionService->isConfigured())
                                        El servicio no está configurado. Verifica las variables de entorno SUBSCRIPTION_API_URL y SUBSCRIPTION_API_TOKEN.
                                    @else
                                        No se pudo obtener la información de suscripción. Verifica la conexión con la API.
                                    @endif
                                </p>
                                @if(config('app.debug'))
                                <div class="debug-info">
                                    <strong>Debug Info:</strong><br>
                                    Configurado: {{ $subscriptionService->isConfigured() ? 'Sí' : 'No' }}<br>
                                    API URL: {{ $subscriptionService->getDebugInfo()['api_url'] ?? 'No configurada' }}<br>
                                    Token: {{ $subscriptionService->getDebugInfo()['has_token'] ? 'Presente' : 'Faltante' }}
                                </div>
                                @endif
                                <button wire:click="refreshData" class="retry-btn">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Reintentar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Overlay bloqueante con diseño moderno y empático -->
            @if($showOverlay)
            <div class="subscription-overlay">
                <div class="overlay-content">
                    <!-- Header con gradiente suave -->
                    <div class="overlay-header">
                        <div class="overlay-icon-container">
                            <div class="icon-glow"></div>
                            <div class="icon-ring"></div>
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="overlay-title">
                            Suscripción Vencida
                        </h2>
                        <div class="title-underline"></div>
                    </div>

                    <!-- Mensaje principal con tono empático -->
                    <div class="overlay-message-section">
                        <p class="overlay-message">
                            Entendemos que esto puede ser frustrante. Tu suscripción ha expirado, 
                            pero estamos aquí para ayudarte a resolverlo rápidamente.
                        </p>
                        <div class="message-highlight">
                            <span class="highlight-icon">💡</span>
                            <span>No te preocupes, es muy fácil reactivar tu cuenta</span>
                        </div>
                    </div>

                    <!-- Información de pago con diseño destacado -->
                    <div class="payment-info-section">
                        <div class="payment-card">
                            <div class="payment-header">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <h3>Información de Pago</h3>
                            </div>
                            <div class="payment-details">
                                <div class="bank-info">
                                    <span class="bank-label">Banco Lafise Nicaragua: <span class="account-number">{{ config('subscription-modal.accountNumber1') }}</span></span>

                                </div>
                                <div class="payment-note">
                                    <span class="note-icon">📋</span>
                                    <span>Envía el comprobante de pago para activar tu suscripción</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción con diseño moderno -->
                    <div class="overlay-actions">
                        <button class="verify-payment-btn" wire:click="refreshData">
                            <div class="btn-content">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>Verificar Pago</span>
                            </div>
                            <div class="btn-glow"></div>
                        </button>
                        <button class="contact-support-btn">
                            <div class="btn-content">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Contactar Soporte</span>
                            </div>
                            <div class="btn-glow"></div>
                        </button>
                    </div>

                    <!-- Footer con mensaje de apoyo -->
                    <div class="overlay-footer">
                        <p class="footer-message">
                            <span class="heart-icon">💙</span>
                            Estamos aquí para ayudarte en todo momento
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endif

    <style>
        /* Badge Styles con efectos modernos */
        .subscription-badge {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 9999;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .subscription-badge:hover {
            transform: scale(1.1) translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }

        .subscription-badge-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .subscription-badge-orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .subscription-badge-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .badge-glow {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255,255,255,0.3) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .subscription-badge:hover .badge-glow {
            opacity: 1;
        }

        .badge-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .subscription-days {
            font-size: 20px;
            line-height: 1;
            margin-bottom: 2px;
            font-weight: 700;
        }

        .subscription-label {
            font-size: 10px;
            line-height: 1;
            opacity: 0.9;
            font-weight: 500;
        }

        .badge-pulse {
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 50%;
            background: inherit;
            opacity: 0.3;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.1;
            }
            100% {
                transform: scale(1);
                opacity: 0.3;
            }
        }
        
        /* Modal Styles con diseño moderno */
        .subscription-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .subscription-modal {
            background: white;
            border-radius: 20px;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px 32px;
            border-radius: 20px 20px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.1) 100%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
            z-index: 1;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .modal-content {
            padding: 32px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        /* User Info Card */
        .user-info-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .user-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .user-avatar {
            position: relative;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .avatar-ring {
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border: 2px solid transparent;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
        }

        .user-details h4 {
            margin: 0 0 4px 0;
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
        }

        .user-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .status-text {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        /* Status Overview */
        .status-overview {
            margin-bottom: 32px;
        }

        .status-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .status-card:hover::before {
            opacity: 1;
        }

        .status-green {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-left: 4px solid #10b981;
        }

        .status-orange {
            background: linear-gradient(135deg, #fffbeb 0%, #fed7aa 100%);
            border-left: 4px solid #f59e0b;
        }

        .status-red {
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
            border-left: 4px solid #ef4444;
        }

        .status-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .status-green .status-icon { color: #10b981; }
        .status-orange .status-icon { color: #f59e0b; }
        .status-red .status-icon { color: #ef4444; }

        .status-info h5 {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .status-info p {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            opacity: 0.8;
        }

        .status-green .status-info p { color: #065f46; }
        .status-orange .status-info p { color: #92400e; }
        .status-red .status-info p { color: #991b1b; }

        .status-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .status-card:hover .status-glow {
            opacity: 1;
        }

        /* Licenses Section */
        .licenses-section {
            margin-top: 32px;
        }

        .section-header {
            margin-bottom: 24px;
            text-align: center;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 12px 0;
        }

        .section-line {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            margin: 0 auto;
            border-radius: 2px;
        }

        .licenses-grid {
            display: grid;
            gap: 20px;
        }

        .license-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .license-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .license-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .license-card:hover::before {
            transform: scaleX(1);
        }

        .license-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .database-name {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.active {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #065f46;
        }

        .status-badge.overdue {
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
            color: #991b1b;
        }

        .status-badge.inactive {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #6b7280;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .license-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
        }

        .no-license {
            color: #6b7280;
            font-style: italic;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        /* Error State */
        .error-state {
            text-align: center;
            padding: 60px 20px;
        }

        .error-icon {
            color: #ef4444;
            margin-bottom: 24px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 12px 0;
        }

        .error-message {
            color: #6b7280;
            margin: 0 0 32px 0;
            line-height: 1.6;
            font-size: 16px;
        }

        .debug-info {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            padding: 16px;
            border-radius: 12px;
            margin: 24px 0;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .retry-btn {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        .retry-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        }
        
        /* Overlay Styles - Diseño Empático y Moderno */
        .subscription-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.95) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            backdrop-filter: blur(20px);
            animation: overlayFadeIn 0.6s ease-out;
        }

        @keyframes overlayFadeIn {
            from { 
                opacity: 0;
                backdrop-filter: blur(0px);
            }
            to { 
                opacity: 1;
                backdrop-filter: blur(20px);
            }
        }

        .overlay-content {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 24px;
            padding: 0;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            animation: overlaySlideIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .overlay-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.03) 0%, rgba(220, 38, 38, 0.03) 100%);
            border-radius: 24px;
        }

        @keyframes overlaySlideIn {
            from {
                opacity: 0;
                transform: scale(0.85) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Header Section */
        .overlay-header {
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
            padding: 24px 20px 20px;
            position: relative;
            overflow: hidden;
        }

        .overlay-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.4) 0%, transparent 50%, rgba(255,255,255,0.4) 100%);
            animation: shimmer 4s infinite;
        }

        .overlay-icon-container {
            position: relative;
            display: inline-block;
            margin-bottom: 16px;
            z-index: 1;
        }

        .icon-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: glowPulse 3s ease-in-out infinite;
        }

        @keyframes glowPulse {
            0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.6; transform: translate(-50%, -50%) scale(1.1); }
        }

        .icon-ring {
            position: absolute;
            top: -16px;
            left: -16px;
            right: -16px;
            bottom: -16px;
            border: 2px solid rgba(239, 68, 68, 0.2);
            border-radius: 50%;
            animation: ringPulse 2.5s infinite;
        }

        @keyframes ringPulse {
            0% {
                transform: scale(1);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.15);
                opacity: 0.3;
            }
            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }

        .overlay-icon-container svg {
            color: #ef4444;
            position: relative;
            z-index: 2;
        }

        .overlay-title {
            font-size: 22px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 6px 0;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .title-underline {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
            margin: 0 auto;
            border-radius: 2px;
            position: relative;
            z-index: 1;
        }

        /* Message Section */
        .overlay-message-section {
            padding: 20px;
            background: white;
        }

        .overlay-message {
            color: #4b5563;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 16px 0;
            font-weight: 400;
        }

        .message-highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 12px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .highlight-icon {
            font-size: 20px;
        }

        .message-highlight span:last-child {
            color: #92400e;
            font-weight: 600;
            font-size: 16px;
        }

        /* Contact Info Section */
        .contact-info-section {
            padding: 0 20px 16px;
        }

        .contact-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 16px;
            padding: 16px;
            border: 1px solid rgba(59, 130, 246, 0.1);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.1);
        }

        .contact-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #1e40af;
        }

        .contact-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .contact-details p {
            margin: 8px 0;
            color: #374151;
        }

        .contact-details p:first-child {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .contact-phone {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #059669;
            font-weight: 600;
        }

        /* Payment Info Section */
        .payment-info-section {
            padding: 0 20px 20px;
        }

        .payment-card {
            background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
            border-radius: 16px;
            padding: 16px;
            border: 1px solid rgba(147, 51, 234, 0.1);
            box-shadow: 0 4px 20px rgba(147, 51, 234, 0.1);
        }

        .payment-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #7c3aed;
        }

        .payment-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .bank-info {
            background: white;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .bank-label {
            display: block;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .account-number {
            display: inline-block;
            color: #1f2937;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Monaco', 'Menlo', monospace;
            letter-spacing: 1px;
        }

        .payment-note {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #7c3aed;
            font-size: 14px;
            font-weight: 500;
        }

        .note-icon {
            font-size: 16px;
        }

        /* Actions Section */
        .overlay-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            padding: 0 20px 20px;
            position: relative;
            z-index: 1;
        }

        .verify-payment-btn, .contact-support-btn {
            position: relative;
            padding: 0;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            min-width: 160px;
        }

        .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 12px 20px;
            position: relative;
            z-index: 2;
        }

        .btn-glow {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255,255,255,0.3) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .verify-payment-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3);
        }

        .verify-payment-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(239, 68, 68, 0.4);
        }

        .verify-payment-btn:hover .btn-glow {
            opacity: 1;
        }

        .contact-support-btn {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            box-shadow: 0 8px 32px rgba(107, 114, 128, 0.3);
        }

        .contact-support-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(107, 114, 128, 0.4);
        }

        .contact-support-btn:hover .btn-glow {
            opacity: 1;
        }

        /* Footer Section */
        .overlay-footer {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 16px 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .footer-message {
            color: #6b7280;
            font-size: 13px;
            font-weight: 500;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .heart-icon {
            font-size: 16px;
            animation: heartBeat 2s ease-in-out infinite;
        }

        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .subscription-modal {
                width: 95%;
                margin: 20px;
                border-radius: 16px;
            }

            .modal-header {
                padding: 20px;
                border-radius: 16px 16px 0 0;
            }

            .modal-content {
                padding: 24px;
            }

            .overlay-content {
                width: 95%;
                border-radius: 24px;
            }

            .overlay-header {
                padding: 20px 16px 16px;
            }

            .overlay-title {
                font-size: 24px;
            }

            .overlay-message-section {
                padding: 16px;
            }

            .overlay-message {
                font-size: 15px;
            }

            .contact-info-section,
            .payment-info-section {
                padding: 0 16px 16px;
            }

            .overlay-actions {
                flex-direction: column;
                padding: 0 16px 16px;
            }

            .verify-payment-btn, .contact-support-btn {
                min-width: auto;
                width: 100%;
            }

            .overlay-footer {
                padding: 20px 24px;
            }

            .licenses-grid {
                grid-template-columns: 1fr;
            }

            .status-card {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }

            .user-info-card {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }
        }

        @media (max-width: 480px) {
            .subscription-badge {
                width: 60px;
                height: 60px;
            }

            .subscription-days {
                font-size: 18px;
            }

            .subscription-label {
                font-size: 9px;
            }

            .modal-title {
                font-size: 20px;
            }

            .overlay-title {
                font-size: 20px;
            }

            .overlay-message {
                font-size: 14px;
            }
        }

        /* Scrollbar styling */
        .subscription-modal::-webkit-scrollbar {
            width: 8px;
        }

        .subscription-modal::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        .subscription-modal::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        .subscription-modal::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
    </style>
</div> 