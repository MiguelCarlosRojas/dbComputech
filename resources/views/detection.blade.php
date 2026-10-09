<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>dbCOMPUTECH - Sistema de Visión Artificial, Objetos, Cabello y Gestos</title>

    <!-- System Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        cyber: {
                            50: '#f0fdfa',
                            500: '#14b8a6',
                            900: '#042f2e',
                            950: '#021817',
                        }
                    }
                }
            }
        }
    </script>

    <!-- TensorFlow.js 4.10.0 & COCO-SSD 2.2.3 -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.10.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd@2.2.3/dist/coco-ssd.min.js"></script>

    <!-- Pusher JS for Laravel Reverb WebSockets -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>

    <!-- WebM Seekable Duration Fixer (Permite adelantar y retroceder en reproductores) -->
    <script src="/js/fix-webm-duration.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #060911;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }

        #notificationsFeed::-webkit-scrollbar {
            width: 5px;
        }
        #notificationsFeed::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 4px;
        }
        #notificationsFeed::-webkit-scrollbar-thumb {
            background: rgba(6, 182, 212, 0.4);
            border-radius: 4px;
        }
        #notificationsFeed::-webkit-scrollbar-thumb:hover {
            background: rgba(6, 182, 212, 0.7);
        }

        .glow-accent {
            box-shadow: 0 0 35px -5px rgba(14, 165, 233, 0.25);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col selection:bg-cyan-500 selection:text-slate-950 w-full overflow-x-hidden">

    <!-- Header: Full Width Responsive Command Bar -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-50 w-full">
        <div class="w-full px-3 sm:px-6 lg:px-8 min-h-16 py-2.5 sm:py-0 flex flex-wrap md:flex-nowrap items-center justify-between gap-2.5 sm:gap-4">
            <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                <a href="/" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-cyan-500 via-emerald-500 to-indigo-500 p-0.5 flex items-center justify-center shadow-lg shadow-cyan-500/20 shrink-0">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <img src="/favicon.svg" alt="dbCOMPUTECH Logo" class="w-5 h-5 sm:w-6 sm:h-6 object-contain" />
                    </div>
                </a>
                <div>
                    <h1 class="text-sm sm:text-base md:text-lg font-bold tracking-tight text-white flex items-center gap-1.5 sm:gap-2">
                        dbCOMPUTECH <span class="text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-mono-code font-normal">IA Vision 360° Pro</span>
                    </h1>
                    <p class="text-[11px] sm:text-xs text-slate-400 hidden md:block">Detección de Objetos del Entorno, Multi-Persona, Gestos y Tono de Cabello con WebSockets</p>
                </div>
            </div>

            <!-- Controls: Guía Cromática, Historial BD, Audio: Activo y Toggle de Estado/Permisos -->
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2.5">
                <!-- Navigation: Guía Cromática (Left Drawer) -->
                <x-button variant="secondary" size="xs" id="btnOpenLeftDrawer" onclick="openLeftDrawer()" icon='<svg class="w-3.5 h-3.5 text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".75" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".75" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".75" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".75" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.563C22 6.5 17.5 2 12 2Z"/></svg>'>
                    <span class="hidden sm:inline">Guía Cromática</span>
                    <span class="sm:hidden">Guía</span>
                </x-button>

                <!-- Navigation: Registro Histórico (Right Drawer) -->
                <x-button variant="secondary" size="xs" id="btnOpenRightDrawer" onclick="openRightDrawer()" icon='<svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>'>
                    <span class="hidden sm:inline">Historial BD</span>
                    <span class="sm:hidden">Historial</span>
                </x-button>

                <!-- Sound toggle using Component with SVG Icon (Audio: Activo) -->
                <x-button variant="secondary" size="xs" id="toggleAudioBtn" onclick="toggleAudio()" title="Activar/Silenciar Audio">
                    <span id="audioIconHolder" class="w-3.5 h-3.5 inline-flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </span>
                    <span id="audioStatusText" class="font-medium">Audio: Activo</span>
                </x-button>

                <!-- Icon button to toggle System Status & Permissions Panel -->
                <x-button variant="secondary" size="xs" id="btnToggleStatusPanel" onclick="toggleSystemStatusPanel()" title="Ver Estado de Servicios y Centro de Permisos" icon='<svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>'></x-button>
            </div>
        </div>
    </header>

    <!-- Main Workspace (Full Width) -->
    <main class="w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-5 flex-1 flex flex-col gap-4 sm:gap-5">

        <!-- Panel Desplegable: Estado del Sistema & Centro de Permisos de Usuario -->
        <div id="systemStatusAndPermsPanel" class="hidden w-full bg-slate-900/95 border border-slate-800 rounded-2xl p-4 sm:p-5 shadow-2xl backdrop-blur transition-all duration-300">
            <div class="flex flex-col gap-4">
                <!-- Panel Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse shrink-0"></div>
                        <h3 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider">Centro de Estado del Sistema & Permisos de Usuario</h3>
                    </div>
                    <button type="button" onclick="toggleSystemStatusPanel()" class="text-slate-400 hover:text-white transition p-1 rounded-lg hover:bg-slate-800" title="Cerrar Panel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Las 3 Tarjetas de Estado (IA, WebSocket, Cámara) -->
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-2">Estado de Conexión y Servicios de Inteligencia Artificial:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <!-- AI Model Status Badge -->
                        <div id="aiModelBadge" class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-amber-500/30 bg-amber-500/10 text-amber-400 text-xs font-mono-code shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse shrink-0"></span>
                            <span id="aiModelText" class="truncate font-semibold">IA: Cargando...</span>
                        </div>

                        <!-- WebSocket Status Badge -->
                        <div id="wsStatusBadge" class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-amber-500/30 bg-amber-500/10 text-amber-400 text-xs font-mono-code shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse shrink-0"></span>
                            <span id="wsStatusText" class="truncate font-semibold">WS: Conectando...</span>
                        </div>

                        <!-- Camera Status Badge -->
                        <div id="cameraStatusBadge" class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-slate-800 bg-slate-800/80 text-slate-400 text-xs font-mono-code shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-500 shrink-0"></span>
                            <span id="cameraStatusText" class="truncate font-semibold">Cámara: Inactiva</span>
                        </div>
                    </div>
                </div>

                <!-- Centro de Permisos de Usuario: Captura y Grabación -->
                <div class="pt-3 border-t border-slate-800/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                    <div class="flex items-start gap-3 text-xs text-slate-300">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 shrink-0 mt-0.5 md:mt-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <strong class="text-white block sm:inline font-semibold">Centro de Permisos de Usuario:</strong>
                            <span class="text-slate-400">Para activar los botones de captura y grabación de video en tu carpeta de Windows, debes autorizar los permisos correspondientes.</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <!-- Permission: Capture -->
                        <div id="permSnapshotContainer">
                            <x-button variant="cyan" size="sm" id="btnRequestSnapshotPerm" onclick="requestSnapshotPermission()" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'>
                                Autorizar Permiso de Captura
                            </x-button>
                            <span id="permSnapshotGrantedBadge" class="hidden text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Permiso Captura Concedido
                            </span>
                        </div>

                        <!-- Permission: Recording -->
                        <div id="permRecordContainer">
                            <x-button variant="danger" size="sm" id="btnRequestRecordPerm" onclick="requestRecordPermission()" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>'>
                                Autorizar Permiso de Grabación
                            </x-button>
                            <span id="permRecordGrantedBadge" class="hidden text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Permiso Grabación Concedido
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Real-Time Metrics Cards (Responsive Grid) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3.5 w-full">
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-2.5 sm:p-3.5 flex items-center gap-2.5 sm:gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-[11px] text-slate-400 uppercase font-semibold">Personas en Escena</div>
                    <div id="kpiPersonsCount" class="text-xs sm:text-base font-bold text-white truncate">0 personas</div>
                </div>
            </div>

            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-2.5 sm:p-3.5 flex items-center gap-2.5 sm:gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18c2.5-2 4-5 4-9 0-3.5 2-6 5-6s5 2.5 5 6c0 4 1.5 7 4 9m-10-8c1 2 2 3.5 3 3.5s2-1.5 3-3.5"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-[11px] text-slate-400 uppercase font-semibold">Tono de Cabello</div>
                    <div id="kpiHairTone" class="text-xs sm:text-base font-bold text-amber-400 truncate">No analizado</div>
                </div>
            </div>

            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-2.5 sm:p-3.5 flex items-center gap-2.5 sm:gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-[11px] text-slate-400 uppercase font-semibold">Gesto / Actividad</div>
                    <div id="kpiBehavior" class="text-xs sm:text-base font-bold text-emerald-400 truncate">En espera</div>
                </div>
            </div>

            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-2.5 sm:p-3.5 flex items-center gap-2.5 sm:gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-[11px] text-slate-400 uppercase font-semibold">Objetos Entorno</div>
                    <div id="kpiObjectsCount" class="text-xs sm:text-base font-bold text-purple-400 truncate">0 detectados</div>
                </div>
            </div>

            <div class="col-span-2 sm:col-span-1 lg:col-span-1 bg-slate-900/80 border border-slate-800 rounded-xl p-2.5 sm:p-3.5 flex items-center gap-2.5 sm:gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-[11px] text-slate-400 uppercase font-semibold">Notificaciones WS</div>
                    <div id="kpiWsCount" class="text-xs sm:text-base font-bold text-cyan-400 truncate">{{ $stats['total'] }} eventos</div>
                </div>
            </div>
        </div>

        <!-- Camera Permission Banner (When blocked) -->
        <div id="permissionNotice" class="hidden bg-gradient-to-r from-amber-950/70 via-slate-900 to-amber-950/70 border border-amber-500/40 rounded-2xl p-5 shadow-xl w-full">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-amber-300">Permiso de Cámara Requerido en tu Navegador</h3>
                    <p class="text-sm text-slate-300 mt-1">
                        Para analizar los objetos del alrededor, cabello y gestos en tiempo real, haz clic en el botón y selecciona <strong class="text-white">"Permitir"</strong> cuando el navegador te lo solicite.
                    </p>
                    <div class="mt-3 flex gap-3">
                        <x-button variant="warning" size="md" onclick="requestCameraAccess()">
                            Habilitar Permiso de Cámara Ahora
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Workspace Grid: Video Area + Real-Time Live Feed (Equalized Heights) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 w-full items-stretch">

            <!-- Left / Center: Video Stream & Overlays -->
            <div class="lg:col-span-7 xl:col-span-8 flex flex-col h-full">
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl flex flex-col glow-accent relative h-full">
                    <!-- Video Card Header -->
                    <div class="px-3.5 sm:px-5 py-2.5 sm:py-3.5 bg-slate-900 border-b border-slate-800 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                            <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                            <span class="text-xs sm:text-sm font-semibold text-white truncate max-w-[180px] sm:max-w-none">Transmisión de Video & Detección Integral de Entorno</span>
                        </div>
                        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                            <span id="fpsCounter" class="text-[11px] sm:text-xs font-mono-code px-1.5 sm:px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">0 FPS</span>
                            <span id="inferenceCounter" class="text-[11px] sm:text-xs font-mono-code px-1.5 sm:px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">0 ms</span>
                            <x-button variant="secondary" size="xs" onclick="toggleFullscreenVideo()" icon='<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>'>
                                <span class="hidden sm:inline">Maximizar</span>
                            </x-button>
                        </div>
                    </div>

                    <!-- Video Viewport & Canvas Overlay -->
                    <div id="videoViewport" class="relative bg-black w-full flex-1 min-h-[280px] sm:min-h-[380px] md:min-h-[460px] lg:min-h-[560px] flex items-center justify-center overflow-hidden">
                        <!-- Video element -->
                        <video id="webcam" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover"></video>

                        <!-- Canvas for AI Bounding boxes and Overlays -->
                        <canvas id="canvasOverlay" class="absolute inset-0 w-full h-full object-cover z-10 pointer-events-none"></canvas>

                        <!-- Placeholder / Camera Off State -->
                        <div id="cameraPlaceholder" class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm flex flex-col items-center justify-center p-4 sm:p-6 text-center z-20 transition-opacity">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-cyan-400 shadow-inner">
                                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-white mt-2">Cámara en Espera</h3>
                            <p class="text-xs sm:text-sm text-slate-400 max-w-md mt-1 px-2">
                                Activa la cámara para reconocer personas, tono de cabello, gestos, celulares, laptops, sillas, mesas, botellas y todos los objetos a tu alrededor.
                            </p>
                            <div class="mt-4 sm:mt-5">
                                <x-button variant="primary" size="md" id="startCamBtn" onclick="toggleCamera()" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'>
                                    Activar Cámara y Reconocimiento
                                </x-button>
                            </div>
                        </div>

                        <!-- Live Top HUD Overlay -->
                        <div id="videoHud" class="hidden absolute top-3 sm:top-4 left-3 sm:left-4 z-20 flex flex-wrap gap-1.5 sm:gap-2 pointer-events-none max-w-[90%]">
                            <div class="px-2.5 sm:px-3 py-1 rounded-lg bg-slate-950/85 backdrop-blur border border-emerald-500/40 text-[11px] sm:text-xs font-mono-code text-emerald-400 flex items-center gap-1.5 sm:gap-2 shadow">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                EN VIVO
                            </div>
                            <!-- Live Recording Indicator -->
                            <div id="hudRecordingBadge" class="hidden px-2.5 sm:px-3 py-1 rounded-lg bg-rose-950/90 backdrop-blur border border-rose-500/60 text-[11px] sm:text-xs font-bold text-rose-300 flex items-center gap-1.5 sm:gap-2 shadow animate-pulse">
                                <span class="w-2 sm:w-2.5 h-2 sm:h-2.5 rounded-full bg-rose-500"></span>
                                <span id="hudRecordTimer">REC 00:00</span>
                            </div>
                            <div id="hudPersonsBadge" class="px-2.5 sm:px-3 py-1 rounded-lg bg-blue-950/85 backdrop-blur border border-blue-500/40 text-[11px] sm:text-xs font-bold text-blue-300 shadow">
                                Personas: 0
                            </div>
                            <div id="hudHairBadge" class="px-2.5 sm:px-3 py-1 rounded-lg bg-amber-950/85 backdrop-blur border border-amber-500/40 text-[11px] sm:text-xs font-bold text-amber-300 shadow">
                                Cabello: -
                            </div>
                            <div id="hudBehaviorBadge" class="px-2.5 sm:px-3 py-1 rounded-lg bg-purple-950/85 backdrop-blur border border-purple-500/40 text-[11px] sm:text-xs font-bold text-purple-300 shadow">
                                Gesto: Analizando...
                            </div>
                        </div>
                    </div>

                    <!-- Video Controls Toolbar (With Permissions Check on Buttons) -->
                    <div class="px-3.5 sm:px-5 py-3 bg-slate-900 border-t border-slate-800 flex flex-wrap items-center justify-between gap-2.5 sm:gap-3">
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <x-button variant="emerald" size="sm" id="btnTogglePlay" onclick="toggleCamera()">
                                <span id="btnPlayText">Iniciar Detección</span>
                            </x-button>
                            <!-- PERMISSION & HARDWARE PROTECTED BUTTON: CAMBIAR CÁMARA (Solo visible si hay 2 o más cámaras) -->
                            <div id="btnSwitchCamWrapper" class="hidden relative inline-block">
                                <x-button variant="secondary" size="sm" id="btnSwitchCam" onclick="toggleCameraPickerMenu()" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>'>
                                    <span class="hidden sm:inline" id="btnSwitchCamLabel">Cambiar Cámara</span>
                                    <span class="sm:hidden">Cámara</span>
                                    <svg class="w-3.5 h-3.5 ml-1 text-slate-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </x-button>

                                <!-- Menú desplegable con la lista de cámaras conectadas -->
                                <div id="cameraPickerMenu" class="hidden absolute bottom-full mb-2 left-0 z-40 w-64 sm:w-72 bg-slate-900/95 border border-slate-700/80 rounded-xl shadow-2xl backdrop-blur-md p-2 flex flex-col gap-1.5">
                                    <div class="px-2 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800 pb-1.5">
                                        <span>Cámaras Disponibles</span>
                                        <span id="cameraCountTag" class="text-cyan-400 font-mono-code text-[10px]">2 detectadas</span>
                                    </div>
                                    <div id="cameraDeviceList" class="flex flex-col gap-1 max-h-48 overflow-y-auto pr-0.5">
                                        <!-- Cámaras inyectadas dinámicamente -->
                                    </div>
                                </div>
                            </div>

                            <!-- PERMISSION-PROTECTED ACTION BUTTON: SNAPSHOT -->
                            <div id="btnSnapshotWrapper" class="hidden">
                                <x-button variant="cyan" size="sm" id="btnTakeSnapshot" onclick="takeSnapshot()" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'>
                                    <span class="hidden sm:inline">Tomar Captura</span>
                                    <span class="sm:hidden">Captura</span>
                                </x-button>
                            </div>

                            <!-- PERMISSION-PROTECTED ACTION BUTTON: VIDEO RECORDING -->
                            <div id="btnRecordWrapper" class="hidden">
                                <x-button variant="danger" size="sm" id="btnToggleRecord" onclick="toggleRecording()" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>'>
                                    <span id="btnRecordText">Grabar Video</span>
                                </x-button>
                            </div>
                        </div>

                        <!-- Sensitivity / Distance Precision Slider -->
                        <div class="flex items-center gap-2 text-xs text-slate-300 w-full sm:w-auto justify-between sm:justify-start pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-800">
                            <label class="flex items-center gap-2 cursor-pointer w-full sm:w-auto justify-between sm:justify-start">
                                <span class="text-slate-400 text-[11px] font-semibold uppercase">Sensibilidad / Distancia:</span>
                                <input id="confidenceThreshold" type="range" min="10" max="75" value="16" oninput="updateConfidence(this.value)" class="w-24 sm:w-32 accent-cyan-400 cursor-pointer">
                                <span id="confidenceValue" class="font-mono-code text-cyan-400 text-xs font-bold whitespace-nowrap">16% (Alta/Lejana)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Real-Time WebSocket Feed (Equal Height & Stretched) -->
            <div class="lg:col-span-5 xl:col-span-4 flex flex-col h-full">

                <!-- Real-Time WebSocket Notifications Feed -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl flex flex-col glow-accent h-full min-h-[520px] sm:min-h-[600px]">
                    <!-- Feed Header -->
                    <div class="px-5 py-3.5 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></div>
                            <h3 class="text-sm font-bold text-white">Notificaciones en Vivo</h3>
                        </div>
                        <x-button variant="ghost" size="xs" onclick="clearLiveFeed()" title="Limpiar Notificaciones" icon='<svg class="w-4 h-4 text-slate-400 group-hover:text-rose-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'></x-button>
                    </div>

                    <!-- Channel & Subtitle Bar -->
                    <div class="px-5 py-2 bg-slate-950/70 border-b border-slate-800/80 text-[11px] text-slate-400 flex items-center justify-between font-mono-code">
                        <span>Canal: <strong class="text-cyan-400">detections</strong></span>
                        <span id="feedCounter" class="text-slate-500">0 eventos en sesión</span>
                    </div>

                    <!-- Feed List with Infinite Scroll and Skeleton (Ajustado exactamente para 7 registros visibles) -->
                    <div id="notificationsFeed" class="p-3.5 flex-1 min-h-[520px] max-h-[552px] sm:max-h-[556px] overflow-y-auto flex flex-col gap-2 relative">
                        <div id="feedItemsContainer" class="flex flex-col gap-2">
                            <div id="emptyFeedMessage" class="h-[500px] flex flex-col items-center justify-center text-center text-slate-500 p-6">
                                <svg class="w-10 h-10 mb-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <p class="text-xs font-medium text-slate-400">Esperando eventos en vivo de WebSocket...</p>
                                <p class="text-[11px] text-slate-600 mt-1">Cualquier objeto, persona, cabello o gesto detectado generará una alerta con su color distintivo.</p>
                            </div>
                        </div>

                        <!-- Skeleton Loader for Infinite Scroll (Mismo tamaño de tarjeta para evitar saltos) -->
                        <div id="feedSkeletonLoader" class="hidden flex flex-col gap-2 pt-1">
                            <div class="h-[68px] p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 animate-pulse flex flex-col justify-between border-l-4 border-l-cyan-500/50 shrink-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-slate-800 shrink-0"></div>
                                        <div class="h-3 w-28 bg-slate-800 rounded"></div>
                                    </div>
                                    <div class="h-2.5 w-14 bg-slate-800 rounded"></div>
                                </div>
                                <div class="flex items-center justify-between pt-1 border-t border-slate-800/50">
                                    <div class="h-3 w-16 bg-slate-800 rounded"></div>
                                    <div class="h-3 w-20 bg-slate-800 rounded"></div>
                                </div>
                            </div>
                            <div class="h-[68px] p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 animate-pulse flex flex-col justify-between border-l-4 border-l-cyan-500/50 shrink-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-slate-800 shrink-0"></div>
                                        <div class="h-3 w-32 bg-slate-800 rounded"></div>
                                    </div>
                                    <div class="h-2.5 w-12 bg-slate-800 rounded"></div>
                                </div>
                                <div class="flex items-center justify-between pt-1 border-t border-slate-800/50">
                                    <div class="h-3 w-14 bg-slate-800 rounded"></div>
                                    <div class="h-3 w-18 bg-slate-800 rounded"></div>
                                </div>
                            </div>
                        </div>

                        <!-- End of feed notice -->
                        <div id="feedEndNotice" class="hidden text-center py-2 text-[11px] font-mono-code text-slate-500">
                            • Todos los registros previos cargados •
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Radar de Objetos del Entorno (Visual Surroundings Scanner - Ocupa todo el Ancho) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 w-full">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><circle cx="12" cy="12" r="5" stroke-width="2"/><circle cx="12" cy="12" r="1" stroke-width="2"/></svg>
                    Radar de Entorno en Vivo
                </h4>
                <span id="activeCountBadge" class="text-xs px-2.5 py-0.5 rounded-full bg-slate-800 text-cyan-400 font-mono-code font-bold">0 elementos</span>
            </div>

            <!-- Categorized Surroundings Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Column 1: Personas & Cabello -->
                <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 flex flex-col gap-2">
                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Personas & Cabello
                    </span>
                    <div id="radarPersonsContainer" class="flex flex-col gap-1.5 min-h-[50px] text-xs text-slate-500 italic">
                        Esperando personas...
                    </div>
                </div>

                <!-- Column 2: Gestos & Actividad -->
                <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 flex flex-col gap-2">
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Gestos & Postura
                    </span>
                    <div id="radarGesturesContainer" class="flex flex-col gap-1.5 min-h-[50px] text-xs text-slate-500 italic">
                        Esperando gestos...
                    </div>
                </div>

                <!-- Column 3: Dispositivos & Tecnología -->
                <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 flex flex-col gap-2">
                    <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Tecnología & Dispositivos
                    </span>
                    <div id="radarTechContainer" class="flex flex-col gap-1.5 min-h-[50px] text-xs text-slate-500 italic">
                        Esperando dispositivos...
                    </div>
                </div>

                <!-- Column 4: Mobiliario & Accesorios del Entorno -->
                <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 flex flex-col gap-2">
                    <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                        Mobiliario & Alrededor
                    </span>
                    <div id="radarFurnitureContainer" class="flex flex-col gap-1.5 min-h-[50px] text-xs text-slate-500 italic">
                        Esperando entorno...
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- ==================================================== -->
    <!-- BARRA LATERAL IZQUIERDA: Guía Cromática de Categorías -->
    <!-- ==================================================== -->
    <div id="leftDrawerBackdrop" onclick="closeLeftDrawer()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none"></div>

    <aside id="leftDrawerPanel" class="fixed inset-y-0 left-0 z-50 w-full max-w-md sm:max-w-lg bg-slate-900 border-r border-slate-800 shadow-2xl flex flex-col transform -translate-x-full transition-transform duration-300 ease-out backdrop-blur-xl">
        <!-- Drawer Header -->
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/60 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                    <svg class="w-5 h-5 text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".75" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".75" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".75" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".75" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.563C22 6.5 17.5 2 12 2Z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Guía Cromática de Identificación</h3>
                    <p class="text-[11px] text-slate-400">Colores únicos asignados por categoría</p>
                </div>
            </div>
            <button onclick="closeLeftDrawer()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Drawer Body -->
        <div class="flex-1 overflow-y-auto p-5 space-y-5 text-xs">
            <!-- 1. Hair Tones Colors -->
            <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4">
                <div class="text-xs font-semibold text-amber-400 uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span>Tonos de Cabello Humano</span>
                    <span class="text-[10px] text-slate-500 font-mono-code">Muestreo Pixel RGB</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($hairColors as $key => $h)
                    <div class="flex items-center gap-2.5 p-2 rounded-lg bg-slate-900 border border-slate-800/60 text-xs">
                        <span class="w-4 h-4 rounded-md shrink-0 shadow-sm border border-slate-700" style="background-color: {{ $h['color'] }};"></span>
                        <div class="min-w-0">
                            <span class="text-slate-200 font-semibold truncate block">{{ $h['name'] }}</span>
                            <span class="text-[10px] font-mono-code text-slate-500">{{ $h['color'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- 2. Behaviors & Gestures Colors -->
            <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4">
                <div class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span>Gestos y Comportamiento</span>
                    <span class="text-[10px] text-slate-500 font-mono-code">Heurística Espacial</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto pr-1">
                    @foreach($behaviorColors as $key => $beh)
                    <div class="flex items-center gap-2.5 p-2 rounded-lg bg-slate-900 border border-slate-800/60 text-xs">
                        <span class="w-4 h-4 rounded-md shrink-0 shadow-sm" style="background-color: {{ $beh['color'] }};"></span>
                        <div class="min-w-0">
                            <span class="text-slate-200 font-semibold truncate block">{{ $beh['name'] }}</span>
                            <span class="text-[10px] font-mono-code text-slate-500">{{ $beh['color'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. Objects Colors -->
            <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4">
                <div class="text-xs font-semibold text-cyan-400 uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span>Objetos del Entorno</span>
                    <span class="text-[10px] text-slate-500 font-mono-code">80 Clases COCO</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-72 overflow-y-auto pr-1">
                    @foreach($objectColors as $key => $obj)
                    <div class="flex items-center gap-2.5 p-2 rounded-lg bg-slate-900 border border-slate-800/60 text-xs">
                        <span class="w-4 h-4 rounded-md shrink-0 shadow-sm" style="background-color: {{ $obj['color'] }};"></span>
                        <div class="min-w-0">
                            <span class="text-slate-200 font-semibold truncate block">{{ $obj['name'] }}</span>
                            <span class="text-[10px] font-mono-code text-slate-500">{{ $obj['color'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </aside>

    <!-- ======================================================= -->
    <!-- BARRA LATERAL DERECHA: Registro Histórico en Base de Datos -->
    <!-- ======================================================= -->
    <div id="rightDrawerBackdrop" onclick="closeRightDrawer()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none"></div>

    <aside id="rightDrawerPanel" class="fixed inset-y-0 right-0 z-50 w-full max-w-xl md:max-w-2xl lg:max-w-3xl bg-slate-900 border-l border-slate-800 shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-out backdrop-blur-xl">
        <!-- Drawer Header -->
        <div class="px-3.5 sm:px-5 py-3 sm:py-4 border-b border-slate-800 flex flex-wrap sm:flex-nowrap items-center justify-between gap-2.5 bg-slate-950/60 shrink-0">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-xs sm:text-sm font-bold text-white truncate">Registro Histórico y Auditoría</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 truncate">Persistencia SQLite en tiempo real</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <x-button variant="secondary" size="xs" onclick="reloadDbLogs()" icon='<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>'>
                    <span class="hidden sm:inline">Actualizar</span>
                </x-button>
                <x-button variant="danger-subtle" size="xs" onclick="clearDbHistory()" icon='<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'>
                    <span class="hidden sm:inline">Limpiar BD</span>
                </x-button>
                <button onclick="closeRightDrawer()" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center transition" title="Cerrar Historial">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Drawer Body -->
        <div class="flex-1 overflow-y-auto p-5">
            <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-950/60 w-full shadow-inner">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/95 text-slate-400 border-b border-slate-800 uppercase tracking-wider font-mono-code text-[11px] sticky top-0">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Categoría</th>
                            <th class="px-4 py-3">Elemento / Gesto</th>
                            <th class="px-4 py-3">Color</th>
                            <th class="px-4 py-3">Confianza</th>
                            <th class="px-4 py-3">Hora</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody" class="divide-y divide-slate-800/60 font-sans">
                        @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-900/50 transition">
                            <td class="px-4 py-2.5 font-mono-code text-slate-500">#{{ $log->id }}</td>
                            <td class="px-4 py-2.5">
                                @if($log->category === 'object')
                                    <span class="px-2 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 font-semibold text-[10px]">OBJETO</span>
                                @elseif($log->category === 'hair')
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-semibold text-[10px]">CABELLO</span>
                                @elseif($log->category === 'gesture')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-semibold text-[10px]">GESTO</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 font-semibold text-[10px]">COMPORTAMIENTO</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 font-medium text-slate-200">{{ $log->display_name }}</td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow" style="background-color: {{ $log->color }};"></span>
                                    <span class="font-mono-code text-[11px] text-slate-400">{{ $log->color }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 font-mono-code">
                                <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300">
                                    {{ round($log->confidence * 100, 1) }}%
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-slate-400 font-mono-code">
                                {{ $log->created_at->format('H:i:s') }}
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyTableRow">
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                No hay registros previos. Comienza la detección para almacenar eventos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Drawer Pagination Footer (Paginación por cada 15 registros) -->
        <div class="px-4 sm:px-5 py-3 border-t border-slate-800 bg-slate-950/80 flex flex-wrap items-center justify-between gap-2.5 shrink-0">
            <div class="text-[11px] font-mono-code text-slate-400">
                <span id="dbRecordsCountText" class="text-slate-300 font-semibold">1 - {{ min(15, $stats['total']) }} de {{ $stats['total'] }} registros</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnDbPrevPage" onclick="goToPrevDbPage()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium border border-slate-700 transition flex items-center gap-1 hidden" disabled>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Anterior</span>
                </button>
                <span id="dbPageIndicator" class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-mono-code text-cyan-400 font-bold">
                    Página 1 de {{ max(1, (int) ceil($stats['total'] / 15)) }}
                </span>
                <button type="button" id="btnDbNextPage" onclick="goToNextDbPage()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium border border-slate-700 transition flex items-center gap-1 {{ $stats['total'] <= 15 ? 'hidden' : '' }}" {{ $stats['total'] <= 15 ? 'disabled' : '' }}>
                    <span>Siguiente</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

    </aside>

    <!-- Floating Toast Notification Container (Top Right) -->
    <div id="toastContainer" class="fixed top-20 right-4 z-50 flex flex-col gap-2 pointer-events-none max-w-sm w-full"></div>

    <!-- Snapshot Modal -->
    <div id="snapshotModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-3xl w-full p-5 shadow-2xl flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Captura Guardada en tu Equipo Windows
                </h4>
                <button onclick="closeSnapshotModal()" class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <img id="snapshotImg" class="rounded-xl border border-slate-800 w-full object-contain max-h-[65vh]" />
            <div class="flex justify-end gap-2">
                <a id="snapshotDownload" download="captura-dbcomputech.png" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Descargar de Nuevo
                </a>
                <x-button variant="secondary" size="sm" onclick="closeSnapshotModal()">Cerrar</x-button>
            </div>
        </div>
    </div>

    <!-- Hidden Offscreen Canvas for Hair Color & Pixel Processing -->
    <canvas id="offscreenCanvas" class="hidden"></canvas>

    <!-- Ultra-Fast Decoupled Vision, Recording & Windows Saving Logic -->
    <script>
        // Server Configuration Injection
        const REVERB_CONFIG = @json($reverbConfig);
        const OBJECT_COLOR_MAP = @json($objectColors);
        const BEHAVIOR_COLOR_MAP = @json($behaviorColors);
        const HAIR_COLOR_MAP = @json($hairColors);

        // Extended Spanish Dictionary for ALL 80 COCO Classes
        const COCO_SPANISH_MAP = {
            'person': 'Persona', 'bicycle': 'Bicicleta', 'car': 'Automóvil', 'motorcycle': 'Motocicleta',
            'airplane': 'Avión', 'bus': 'Autobús', 'train': 'Tren', 'truck': 'Camión', 'boat': 'Barco',
            'traffic light': 'Semáforo', 'fire hydrant': 'Hidrante', 'stop sign': 'Señal de Pare',
            'parking meter': 'Parquímetro', 'bench': 'Banca / Asiento', 'bird': 'Pájaro', 'cat': 'Gato',
            'dog': 'Perro', 'horse': 'Caballo', 'sheep': 'Oveja', 'cow': 'Vaca', 'elephant': 'Elefante',
            'bear': 'Oso', 'zebra': 'Cebra', 'giraffe': 'Jirafa', 'backpack': 'Mochila / Bolso',
            'umbrella': 'Paraguas', 'handbag': 'Cartera / Bolso de Mano', 'tie': 'Corbata',
            'suitcase': 'Maleta', 'frisbee': 'Frisbee', 'skis': 'Esquís', 'snowboard': 'Snowboard',
            'sports ball': 'Pelota de Deporte', 'kite': 'Cometa', 'baseball bat': 'Bate de Béisbol',
            'baseball glove': 'Guante de Béisbol', 'skateboard': 'Patineta', 'surfboard': 'Tabla de Surf',
            'tennis racket': 'Raqueta de Tenis', 'bottle': 'Botella', 'wine glass': 'Copa de Vino',
            'cup': 'Taza / Vaso', 'fork': 'Tenedor', 'knife': 'Cuchillo', 'spoon': 'Cuchara',
            'bowl': 'Tazón / Plato Hondo', 'banana': 'Plátano / Banana', 'apple': 'Manzana',
            'sandwich': 'Sándwich', 'orange': 'Naranja', 'broccoli': 'Brócoli', 'carrot': 'Zanahoria',
            'hot dog': 'Hot Dog', 'pizza': 'Pizza', 'donut': 'Dona', 'cake': 'Pastel',
            'chair': 'Silla / Asiento', 'couch': 'Sofá / Mueble', 'potted plant': 'Planta Decorativa',
            'bed': 'Cama', 'dining table': 'Mesa / Escritorio', 'toilet': 'Inodoro',
            'tv': 'Monitor / Televisor', 'laptop': 'Laptop / Computadora', 'mouse': 'Mouse / Ratón',
            'remote': 'Control Remoto', 'keyboard': 'Teclado', 'cell phone': 'Teléfono Celular',
            'microwave': 'Horno Microondas', 'oven': 'Horno', 'toaster': 'Tostadora',
            'sink': 'Lavabo / Fregadero', 'refrigerator': 'Refrigerador', 'book': 'Libro / Cuaderno',
            'clock': 'Reloj', 'vase': 'Florero', 'scissors': 'Tijeras', 'teddy bear': 'Oso de Peluche',
            'hair drier': 'Secador de Cabello', 'toothbrush': 'Cepillo de Dientes'
        };

        // State Management
        let isCameraActive = false;
        let currentFacingMode = 'user';
        let videoElement = document.getElementById('webcam');
        let canvasElement = document.getElementById('canvasOverlay');
        let ctx = canvasElement.getContext('2d');
        let offscreenCanvas = document.getElementById('offscreenCanvas');
        let offscreenCtx = offscreenCanvas.getContext('2d');
        let cocoModel = null;
        let isModelLoading = false;
        let animationFrameId = null;
        let audioEnabled = true;

        // Permissions State
        let hasSnapshotPermission = false;
        let hasRecordPermission = false;

        // Video Recording State (Composite Stream: Cámara Real + HUD Detecciones)
        let mediaRecorder = null;
        let recordedChunks = [];
        let isRecording = false;
        let recordStartTime = 0;
        let recordTimerInterval = null;
        let recordingMimeType = 'video/webm';
        let recordingExtension = 'webm';
        const recordingCanvas = document.createElement('canvas');
        const recordingCtx = recordingCanvas.getContext('2d', { alpha: false });

        // Multi-Target Real-Time Tracker (Seguimiento Continuo de Personas y Objetos)
        let activeTracks = [];
        let nextTrackId = 1;
        const TRACK_MAX_MISSED_CYCLES = 12; // Mantiene el seguimiento ~0.8s si la IA parpadea
        const TRACK_LERP_FACTOR = 0.38; // Desplazamiento fluido para seguir el movimiento a 60 FPS

        function computeIoU(boxA, boxB) {
            const xA = Math.max(boxA[0], boxB[0]);
            const yA = Math.max(boxA[1], boxB[1]);
            const xB = Math.min(boxA[0] + boxA[2], boxB[0] + boxB[2]);
            const yB = Math.min(boxA[1] + boxA[3], boxB[1] + boxB[3]);

            const interW = Math.max(0, xB - xA);
            const interH = Math.max(0, yB - yA);
            const interArea = interW * interH;

            const areaA = boxA[2] * boxA[3];
            const areaB = boxB[2] * boxB[3];
            const unionArea = areaA + areaB - interArea;

            return unionArea > 0 ? interArea / unionArea : 0;
        }

        function getCentroidDistance(boxA, boxB) {
            const cAx = boxA[0] + boxA[2] / 2;
            const cAy = boxA[1] + boxA[3] / 2;
            const cBx = boxB[0] + boxB[2] / 2;
            const cBy = boxB[1] + boxB[3] / 2;
            return Math.hypot(cAx - cBx, cAy - cBy);
        }

        function updateObjectTracks(newPredictions) {
            const matchedDetections = new Set();

            // 1. Asignar detecciones a tracks existentes por clase, IoU y proximidad
            for (const track of activeTracks) {
                let bestMatchIndex = -1;
                let bestMatchScore = 0;

                for (let i = 0; i < newPredictions.length; i++) {
                    if (matchedDetections.has(i)) continue;
                    const pred = newPredictions[i];
                    if (pred.class !== track.class) continue;

                    const iou = computeIoU(track.targetBbox, pred.bbox);
                    const dist = getCentroidDistance(track.targetBbox, pred.bbox);
                    const maxDim = Math.max(track.targetBbox[2], track.targetBbox[3], pred.bbox[2], pred.bbox[3], 50);
                    const normDist = dist / maxDim;

                    let score = iou;
                    if (normDist < 0.75) {
                        score = Math.max(score, 0.45 - (normDist * 0.35));
                    }

                    if (score > 0.12 && score > bestMatchScore) {
                        bestMatchScore = score;
                        bestMatchIndex = i;
                    }
                }

                if (bestMatchIndex !== -1) {
                    const pred = newPredictions[bestMatchIndex];
                    matchedDetections.add(bestMatchIndex);

                    // Calcular vector de velocidad del movimiento
                    const dx = pred.bbox[0] - track.targetBbox[0];
                    const dy = pred.bbox[1] - track.targetBbox[1];
                    const dw = pred.bbox[2] - track.targetBbox[2];
                    const dh = pred.bbox[3] - track.targetBbox[3];

                    track.velocity = [
                        track.velocity[0] * 0.3 + dx * 0.7,
                        track.velocity[1] * 0.3 + dy * 0.7,
                        track.velocity[2] * 0.3 + dw * 0.7,
                        track.velocity[3] * 0.3 + dh * 0.7
                    ];

                    track.targetBbox = [...pred.bbox];
                    track.score = Math.max(track.score * 0.15 + pred.score * 0.85, pred.score);
                    track.missedCycles = 0;
                    track.lastSeen = performance.now();
                } else {
                    // Predecir posición con velocidad para mantener seguimiento ininterrumpido
                    track.missedCycles = (track.missedCycles || 0) + 1;
                    track.targetBbox[0] += (track.velocity[0] || 0) * 0.5;
                    track.targetBbox[1] += (track.velocity[1] || 0) * 0.5;
                    track.targetBbox[2] += (track.velocity[2] || 0) * 0.2;
                    track.targetBbox[3] += (track.velocity[3] || 0) * 0.2;
                    track.targetBbox[0] = Math.max(0, Math.min(canvasElement.width - 20, track.targetBbox[0]));
                    track.targetBbox[1] = Math.max(0, Math.min(canvasElement.height - 20, track.targetBbox[1]));
                }
            }

            // 2. Registrar nuevas detecciones como nuevos tracks
            for (let i = 0; i < newPredictions.length; i++) {
                if (!matchedDetections.has(i)) {
                    const pred = newPredictions[i];
                    activeTracks.push({
                        id: nextTrackId++,
                        class: pred.class,
                        bbox: [...pred.bbox],
                        targetBbox: [...pred.bbox],
                        velocity: [0, 0, 0, 0],
                        score: pred.score,
                        missedCycles: 0,
                        hair: null,
                        firstSeen: performance.now(),
                        lastSeen: performance.now()
                    });
                }
            }

            // 3. Filtrar tracks que excedan el límite de tolerancia
            activeTracks = activeTracks.filter(t => (t.missedCycles || 0) <= TRACK_MAX_MISSED_CYCLES);
        }

        // High-Precision Fast Inference Canvas (640x360 maintains far-away and subtle object features)
        const inferCanvas = document.createElement('canvas');
        inferCanvas.width = 640;
        inferCanvas.height = 360;
        const inferCtx = inferCanvas.getContext('2d', { alpha: false, willReadFrequently: false });
        let inferenceTimer = null;

        // Decoupled AI Inference vs 60fps Rendering
        let isInferring = false;
        let cachedPredictions = [];
        let cachedPersonsData = [];
        let cachedBehavior = { key: 'absent', name: 'Persona Ausente', color: '#64748B', confidence: 0.95 };
        let lastInferenceTime = 0;
        let lastHairSampleTime = 0;
        let lastDomUpdateTime = 0;

        // Statistics & Throttling
        let lastFrameTime = performance.now();
        let frameCount = 0;
        let fps = 0;
        let wsEventsCount = {{ $stats['total'] }};
        let sessionEventsCount = 0;
        let minConfidence = 0.16;

        // Tracking state
        let previousPersons = [];
        let previousPersonTime = 0;
        let currentActiveBehavior = 'absent';
        let currentDetectedHair = null;
        let lastEventSentTimestamps = {};

        // ==========================================
        // LATERAL DRAWERS (LEFT & RIGHT) LOGIC
        // ==========================================
        function openLeftDrawer() {
            closeRightDrawer();
            const bd = document.getElementById('leftDrawerBackdrop');
            const panel = document.getElementById('leftDrawerPanel');
            bd.classList.remove('opacity-0', 'pointer-events-none');
            bd.classList.add('opacity-100');
            panel.classList.remove('-translate-x-full');
            panel.classList.add('translate-x-0');
        }

        function closeLeftDrawer() {
            const bd = document.getElementById('leftDrawerBackdrop');
            const panel = document.getElementById('leftDrawerPanel');
            bd.classList.add('opacity-0', 'pointer-events-none');
            bd.classList.remove('opacity-100');
            panel.classList.add('-translate-x-full');
            panel.classList.remove('translate-x-0');
        }

        function openRightDrawer() {
            closeLeftDrawer();
            const bd = document.getElementById('rightDrawerBackdrop');
            const panel = document.getElementById('rightDrawerPanel');
            bd.classList.remove('opacity-0', 'pointer-events-none');
            bd.classList.add('opacity-100');
            panel.classList.remove('translate-x-full');
            panel.classList.add('translate-x-0');
            reloadDbLogs();
        }

        function closeRightDrawer() {
            const bd = document.getElementById('rightDrawerBackdrop');
            const panel = document.getElementById('rightDrawerPanel');
            bd.classList.add('opacity-0', 'pointer-events-none');
            bd.classList.remove('opacity-100');
            panel.classList.add('translate-x-full');
            panel.classList.remove('translate-x-0');
        }

        // ==========================================
        // SYSTEM STATUS & PERMISSIONS PANEL TOGGLE
        // ==========================================
        function toggleSystemStatusPanel() {
            const panel = document.getElementById('systemStatusAndPermsPanel');
            const btn = document.getElementById('btnToggleStatusPanel');
            if (!panel) return;
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                panel.classList.remove('hidden');
                if (btn) btn.classList.add('ring-2', 'ring-cyan-400');
            } else {
                panel.classList.add('hidden');
                if (btn) btn.classList.remove('ring-2', 'ring-cyan-400');
            }
        }

        function closeSystemStatusPanel() {
            const panel = document.getElementById('systemStatusAndPermsPanel');
            const btn = document.getElementById('btnToggleStatusPanel');
            if (panel && !panel.classList.contains('hidden')) {
                panel.classList.add('hidden');
                if (btn) btn.classList.remove('ring-2', 'ring-cyan-400');
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeLeftDrawer();
                closeRightDrawer();
                closeSnapshotModal();
                closeSystemStatusPanel();
            }
        });

        // Audio Feedback
        let audioCtx = null;
        function playChime(category) {
            if (!audioEnabled) return;
            try {
                if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);

                if (category === 'behavior' || category === 'gesture') {
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(587.33, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15);
                } else if (category === 'hair') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(523.25, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(783.99, audioCtx.currentTime + 0.15);
                } else {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(440, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(659.25, audioCtx.currentTime + 0.12);
                }

                gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.25);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.25);
            } catch (e) {
                console.warn('Audio notice', e);
            }
        }

        function toggleAudio() {
            audioEnabled = !audioEnabled;
            const textEl = document.getElementById('audioStatusText');
            const iconHolder = document.getElementById('audioIconHolder');
            if (textEl) {
                textEl.innerText = audioEnabled ? 'Audio: Activo' : 'Audio: Silenciado';
            }
            if (iconHolder) {
                if (audioEnabled) {
                    iconHolder.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
                } else {
                    iconHolder.innerHTML = '<svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>';
                }
            }
        }

        // ==========================================
        // PERMISSIONS SYSTEM (SNAPSHOT & RECORDING)
        // ==========================================
        function requestSnapshotPermission() {
            const confirmed = confirm('¿Deseas autorizar al sistema a capturar imágenes y guardarlas directamente en la carpeta de tu equipo (Windows)?');
            if (confirmed) {
                hasSnapshotPermission = true;
                document.getElementById('btnRequestSnapshotPerm').classList.add('hidden');
                document.getElementById('permSnapshotGrantedBadge').classList.remove('hidden');
                document.getElementById('btnSnapshotWrapper').classList.remove('hidden');
                showToastNotification({
                    category: 'permission',
                    display_name: 'Permiso de Captura Concedido',
                    color: '#06B6D4',
                    confidence: 100
                });
            }
        }

        function requestRecordPermission() {
            const confirmed = confirm('¿Deseas autorizar al sistema a grabar videos en tiempo real y guardarlos directamente en la carpeta de tu equipo (Windows)?');
            if (confirmed) {
                hasRecordPermission = true;
                document.getElementById('btnRequestRecordPerm').classList.add('hidden');
                document.getElementById('permRecordGrantedBadge').classList.remove('hidden');
                document.getElementById('btnRecordWrapper').classList.remove('hidden');
                showToastNotification({
                    category: 'permission',
                    display_name: 'Permiso de Grabación de Video Concedido',
                    color: '#EF4444',
                    confidence: 100
                });
            }
        }

        // ==========================================
        // HIGH PERFORMANCE CAMERA & WEBSOCKET
        // ==========================================
        let pusherInstance = null;
        let detectionChannel = null;

        function initWebSocket() {
            const badge = document.getElementById('wsStatusBadge');
            const statusText = document.getElementById('wsStatusText');

            try {
                Pusher.logToConsole = false;
                pusherInstance = new Pusher(REVERB_CONFIG.key, {
                    wsHost: REVERB_CONFIG.host,
                    wsPort: REVERB_CONFIG.port,
                    wssPort: REVERB_CONFIG.port,
                    forceTLS: REVERB_CONFIG.scheme === 'https',
                    enabledTransports: ['ws', 'wss'],
                    disableStats: true,
                    cluster: 'mt1'
                });

                pusherInstance.connection.bind('connected', () => {
                    badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-xs font-mono-code';
                    badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-emerald-400';
                    statusText.innerText = 'WebSocket: Conectado (Reverb)';
                });

                pusherInstance.connection.bind('connecting', () => {
                    badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-amber-500/30 bg-amber-500/10 text-amber-400 text-xs font-mono-code';
                    badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-amber-400 animate-pulse';
                    statusText.innerText = 'WebSocket: Conectando...';
                });

                pusherInstance.connection.bind('disconnected', () => {
                    badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-rose-500/30 bg-rose-500/10 text-rose-400 text-xs font-mono-code';
                    badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-rose-400';
                    statusText.innerText = 'WebSocket: Desconectado';
                });

                detectionChannel = pusherInstance.subscribe('detections');
                detectionChannel.bind('DetectionDetected', (data) => {
                    handleWebSocketNotification(data);
                });

            } catch (err) {
                statusText.innerText = 'WebSocket: Local Fallback';
            }
        }

        function handleWebSocketNotification(data) {
            wsEventsCount++;
            sessionEventsCount++;
            document.getElementById('kpiWsCount').innerText = `${wsEventsCount} eventos`;
            document.getElementById('feedCounter').innerText = `${sessionEventsCount} eventos en sesión`;

            playChime(data.category);
            appendNotificationToFeed(data);
            showToastNotification(data);
            prependLogRow(data);
        }

        // Infinite scroll & WebSocket feed management
        let feedOffset = 0;
        const FEED_PAGE_SIZE = 7;
        let isFeedLoading = false;
        let feedHasMore = true;

        function buildFeedCardElement(data) {
            let categoryLabel = 'OBJETO';
            let catColorClass = 'bg-blue-500/20 text-blue-400 border-blue-500/30';
            if (data.category === 'hair') {
                categoryLabel = 'CABELLO';
                catColorClass = 'bg-amber-500/20 text-amber-400 border-amber-500/30';
            } else if (data.category === 'gesture') {
                categoryLabel = 'GESTO';
                catColorClass = 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30';
            } else if (data.category === 'behavior') {
                categoryLabel = 'COMPORTAMIENTO';
                catColorClass = 'bg-purple-500/20 text-purple-400 border-purple-500/30';
            }

            const item = document.createElement('div');
            item.className = 'p-3 rounded-xl bg-slate-950/85 border border-slate-800 transition transform hover:translate-x-1 shadow-md shrink-0 h-[68px] flex flex-col justify-between';
            item.style.borderLeft = `4px solid ${data.color}`;

            const formattedTime = data.timestamp || (data.created_at ? new Date(data.created_at).toLocaleTimeString() : new Date().toLocaleTimeString());

            item.innerHTML = `
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 truncate">
                        <span class="w-3 h-3 rounded-full shrink-0 shadow-sm" style="background-color: ${data.color}"></span>
                        <span class="text-xs font-bold text-white truncate">${data.display_name}</span>
                    </div>
                    <span class="text-[11px] font-mono-code text-slate-400 shrink-0">${formattedTime}</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[11px]">
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border ${catColorClass}">${categoryLabel}</span>
                        <span class="font-mono-code text-slate-400" style="color: ${data.color}">${data.color}</span>
                    </div>
                    <div class="font-mono-code text-slate-300">
                        Confianza: <span class="font-bold text-cyan-400">${data.confidence}%</span>
                    </div>
                </div>
            `;
            return item;
        }

        function appendNotificationToFeed(data) {
            const container = document.getElementById('feedItemsContainer');
            if (!container) return;
            const emptyMsg = document.getElementById('emptyFeedMessage');
            if (emptyMsg) emptyMsg.remove();

            const item = buildFeedCardElement(data);
            container.insertBefore(item, container.firstChild);
            feedOffset++;

            if (container.children.length > 100) {
                container.removeChild(container.lastChild);
            }
        }

        async function loadFeedBatch() {
            if (isFeedLoading || !feedHasMore) return;
            isFeedLoading = true;

            const loader = document.getElementById('feedSkeletonLoader');
            const emptyMsg = document.getElementById('emptyFeedMessage');

            // REGLA ESTRICTA: El esqueleto NUNCA debe aparecer si está visible el mensaje de "Esperando eventos en vivo..."
            // Solo debe aparecer si YA HAY registros previos en pantalla (feedOffset > 0) y NO hay mensaje de feed vacío.
            const shouldShowSkeleton = feedOffset > 0 && !emptyMsg;
            if (loader) {
                if (shouldShowSkeleton) {
                    loader.classList.remove('hidden');
                } else {
                    loader.classList.add('hidden');
                }
            }

            try {
                const fetchPromise = fetch(`/api/detections?limit=${FEED_PAGE_SIZE}&offset=${feedOffset}`);
                const [response] = shouldShowSkeleton
                    ? await Promise.all([fetchPromise, new Promise(resolve => setTimeout(resolve, 350))])
                    : [await fetchPromise];

                if (!response.ok) throw new Error('Error al consultar detecciones');
                const result = await response.json();

                const container = document.getElementById('feedItemsContainer');
                const endNotice = document.getElementById('feedEndNotice');

                if (result.logs && result.logs.length > 0) {
                    const currentEmpty = document.getElementById('emptyFeedMessage');
                    if (currentEmpty) currentEmpty.remove();

                    result.logs.forEach(log => {
                        const item = buildFeedCardElement(log);
                        container.appendChild(item);
                    });

                    feedOffset += result.logs.length;
                }

                if (!result.has_more || (result.logs && result.logs.length < FEED_PAGE_SIZE)) {
                    feedHasMore = false;
                    if (feedOffset > 0 && endNotice) {
                        endNotice.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.warn('Error cargando lote de feed:', err);
            } finally {
                if (loader) loader.classList.add('hidden');
                isFeedLoading = false;
            }
        }

        function initFeedInfiniteScroll() {
            const feed = document.getElementById('notificationsFeed');
            if (!feed) return;

            const checkAndTriggerLoad = () => {
                if (isFeedLoading || !feedHasMore) return;
                const emptyMsg = document.getElementById('emptyFeedMessage');
                if (emptyMsg) return;

                const distanceToBottom = feed.scrollHeight - feed.scrollTop - feed.clientHeight;
                if (distanceToBottom <= 160) {
                    loadFeedBatch();
                }
            };

            // 1. Escuchar evento de scroll regular
            feed.addEventListener('scroll', checkAndTriggerLoad, { passive: true });

            // 2. Escuchar giro de rueda del mouse (wheel) hacia abajo
            feed.addEventListener('wheel', (e) => {
                if (e.deltaY > 0 && !isFeedLoading && feedHasMore) {
                    const emptyMsg = document.getElementById('emptyFeedMessage');
                    if (emptyMsg) return;

                    const distanceToBottom = feed.scrollHeight - feed.scrollTop - feed.clientHeight;
                    if (distanceToBottom <= 160 || feed.scrollHeight <= feed.clientHeight + 20) {
                        loadFeedBatch();
                    }
                }
            }, { passive: true });

            // 3. Escuchar gestos táctiles en pantallas móviles y tablets
            let touchStartY = 0;
            feed.addEventListener('touchstart', (e) => {
                if (e.touches && e.touches[0]) {
                    touchStartY = e.touches[0].clientY;
                }
            }, { passive: true });

            feed.addEventListener('touchmove', (e) => {
                if (e.touches && e.touches[0]) {
                    const diffY = touchStartY - e.touches[0].clientY;
                    if (diffY > 15 && !isFeedLoading && feedHasMore) {
                        const emptyMsg = document.getElementById('emptyFeedMessage');
                        if (emptyMsg) return;

                        const distanceToBottom = feed.scrollHeight - feed.scrollTop - feed.clientHeight;
                        if (distanceToBottom <= 160 || feed.scrollHeight <= feed.clientHeight + 20) {
                            loadFeedBatch();
                        }
                    }
                }
            }, { passive: true });
        }

        function showToastNotification(data) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'p-3.5 rounded-xl bg-slate-900/95 border border-slate-700/80 shadow-2xl backdrop-blur-md flex items-center gap-3 transition-all transform translate-y-2 opacity-0 pointer-events-auto';
            toast.style.borderLeft = `5px solid ${data.color}`;
            toast.style.boxShadow = `0 10px 25px -5px ${data.color}33`;

            let svgIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>';
            if (data.category === 'hair') {
                svgIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18c2.5-2 4-5 4-9 0-3.5 2-6 5-6s5 2.5 5 6c0 4 1.5 7 4 9m-10-8c1 2 2 3.5 3 3.5s2-1.5 3-3.5"/></svg>';
            } else if (data.category === 'gesture') {
                svgIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>';
            } else if (data.category === 'behavior') {
                svgIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>';
            } else if (data.category === 'permission') {
                svgIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>';
            }

            toast.innerHTML = `
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background-color: ${data.color}22; color: ${data.color}; border: 1px solid ${data.color}55">
                    ${svgIcon}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold tracking-wider" style="color: ${data.color}">
                            ${data.category.toUpperCase()}
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono-code">${data.timestamp || 'Ahora'}</span>
                    </div>
                    <div class="text-xs font-bold text-white truncate mt-0.5">${data.display_name}</div>
                </div>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.remove('translate-y-2', 'opacity-0'));
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => toast.remove(), 400);
            }, 3500);
        }

        function clearLiveFeed() {
            const container = document.getElementById('feedItemsContainer');
            if (container) {
                container.innerHTML = `
                    <div id="emptyFeedMessage" class="h-64 flex flex-col items-center justify-center text-center text-slate-500 p-6">
                        <svg class="w-10 h-10 mb-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <p class="text-xs font-medium text-slate-400">Feed limpiado.</p>
                        <p class="text-[11px] text-slate-600 mt-1">Cualquier nuevo evento en vivo de WebSocket aparecerá aquí.</p>
                    </div>
                `;
            }
            const endNotice = document.getElementById('feedEndNotice');
            if (endNotice) endNotice.classList.add('hidden');
            const loader = document.getElementById('feedSkeletonLoader');
            if (loader) loader.classList.add('hidden');
            sessionEventsCount = 0;
            document.getElementById('feedCounter').innerText = '0 eventos en sesión';
            feedOffset = 0;
            feedHasMore = false;
        }

        function prependLogRow(data) {
            dbTotalRecords++;
            dbTotalPages = Math.max(1, Math.ceil(dbTotalRecords / DB_PAGE_SIZE));

            const recordsCountText = document.getElementById('dbRecordsCountText');
            if (recordsCountText && dbCurrentPage === 1) {
                recordsCountText.innerText = `1 - ${Math.min(DB_PAGE_SIZE, dbTotalRecords)} de ${dbTotalRecords} registros`;
            }
            const pageIndicator = document.getElementById('dbPageIndicator');
            if (pageIndicator) {
                pageIndicator.innerText = `Página ${dbCurrentPage} de ${dbTotalPages}`;
            }
            const nextBtn = document.getElementById('btnDbNextPage');
            if (nextBtn) {
                nextBtn.disabled = dbCurrentPage >= dbTotalPages;
                nextBtn.classList.toggle('hidden', dbCurrentPage >= dbTotalPages);
            }
            const prevBtn = document.getElementById('btnDbPrevPage');
            if (prevBtn) {
                prevBtn.disabled = dbCurrentPage <= 1;
                prevBtn.classList.toggle('hidden', dbCurrentPage <= 1);
            }

            // Solo insertar al tope si el usuario está en la página 1
            if (dbCurrentPage === 1) {
                const tbody = document.getElementById('logsTableBody');
                if (!tbody) return;
                const emptyRow = document.getElementById('emptyTableRow');
                if (emptyRow) emptyRow.remove();

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-900/50 transition bg-emerald-500/5';

                let catBadge = '<span class="px-2 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 font-semibold text-[10px]">OBJETO</span>';
                if (data.category === 'hair') {
                    catBadge = '<span class="px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-semibold text-[10px]">CABELLO</span>';
                } else if (data.category === 'gesture') {
                    catBadge = '<span class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-semibold text-[10px]">GESTO</span>';
                } else if (data.category === 'behavior') {
                    catBadge = '<span class="px-2 py-0.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 font-semibold text-[10px]">COMPORTAMIENTO</span>';
                }

                tr.innerHTML = `
                    <td class="px-4 py-2.5 font-mono-code text-slate-500">#${data.id || 'live'}</td>
                    <td class="px-4 py-2.5">${catBadge}</td>
                    <td class="px-4 py-2.5 font-medium text-slate-200">${data.display_name}</td>
                    <td class="px-4 py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow" style="background-color: ${data.color};"></span>
                            <span class="font-mono-code text-[11px] text-slate-400">${data.color}</span>
                        </div>
                    </td>
                    <td class="px-4 py-2.5 font-mono-code">
                        <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300">${data.confidence}%</span>
                    </td>
                    <td class="px-4 py-2.5 text-slate-400 font-mono-code">${data.timestamp || new Date().toLocaleTimeString()}</td>
                `;

                tbody.insertBefore(tr, tbody.firstChild);

                // Mantener exactamente 15 registros por página en la vista
                if (tbody.children.length > DB_PAGE_SIZE) {
                    tbody.removeChild(tbody.lastChild);
                }
            }
        }

        // ==========================================
        // SMART CAMERA SELECTION & HARDWARE DETECTION
        // ==========================================
        let availableVideoDevices = [];
        let activeCameraDeviceId = null;

        async function checkAvailableCameras() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                availableVideoDevices = devices.filter(d => d.kind === 'videoinput');
                renderCameraSwitcherUI();
            } catch (err) {
                console.warn('Error al enumerar cámaras:', err);
            }
        }

        function renderCameraSwitcherUI() {
            const wrapper = document.getElementById('btnSwitchCamWrapper');
            const tag = document.getElementById('cameraCountTag');
            const list = document.getElementById('cameraDeviceList');
            if (!wrapper || !list) return;

            // Si tengo solo 1 cámara (o 0), el botón NO debe aparecer.
            // Solo si tengo varias (2 o más) dependiendo de cuántas cámaras tengo vinculadas.
            if (availableVideoDevices.length <= 1) {
                wrapper.classList.add('hidden');
                closeCameraPickerMenu();
                return;
            }

            // Múltiples cámaras disponibles: mostrar botón y poblar el listado
            wrapper.classList.remove('hidden');
            if (tag) tag.innerText = `${availableVideoDevices.length} detectadas`;

            list.innerHTML = '';
            availableVideoDevices.forEach((device, index) => {
                const isSelected = activeCameraDeviceId ? (device.deviceId === activeCameraDeviceId) : (index === 0);
                const cameraName = device.label || `Cámara ${index + 1}`;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `w-full text-left px-3 py-2 rounded-lg text-xs flex items-center justify-between gap-2 transition ${
                    isSelected 
                        ? 'bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40 shadow' 
                        : 'hover:bg-slate-800 text-slate-300'
                }`;
                btn.innerHTML = `
                    <div class="flex items-center gap-2 truncate">
                        <svg class="w-3.5 h-3.5 shrink-0 ${isSelected ? 'text-cyan-400' : 'text-slate-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate">${cameraName}</span>
                    </div>
                    ${isSelected ? '<svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : ''}
                `;
                btn.onclick = () => {
                    selectCamera(device.deviceId);
                };
                list.appendChild(btn);
            });
        }

        function toggleCameraPickerMenu() {
            const menu = document.getElementById('cameraPickerMenu');
            if (menu) menu.classList.toggle('hidden');
        }

        function closeCameraPickerMenu() {
            const menu = document.getElementById('cameraPickerMenu');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        }

        async function selectCamera(deviceId) {
            closeCameraPickerMenu();
            if (activeCameraDeviceId === deviceId && isCameraActive) return;
            activeCameraDeviceId = deviceId;
            renderCameraSwitcherUI();
            if (isCameraActive) {
                stopCamera();
                await requestCameraAccess(deviceId);
            }
        }

        // ==========================================
        // CAMERA ACCESS & BROWSER PERMISSIONS
        // ==========================================
        async function requestCameraAccess(preferredDeviceId = null) {
            try {
                if (!cocoModel && !isModelLoading) loadDetectionModel();

                const targetDeviceId = preferredDeviceId || activeCameraDeviceId;
                const videoConstraints = {
                    width: { ideal: 1280, min: 640 },
                    height: { ideal: 720, min: 480 }
                };

                if (targetDeviceId) {
                    videoConstraints.deviceId = { exact: targetDeviceId };
                } else {
                    videoConstraints.facingMode = currentFacingMode;
                }

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: videoConstraints,
                    audio: false
                });

                const videoTrack = stream.getVideoTracks()[0];
                if (videoTrack) {
                    const settings = videoTrack.getSettings ? videoTrack.getSettings() : null;
                    if (settings && settings.deviceId) {
                        activeCameraDeviceId = settings.deviceId;
                    }
                }

                document.getElementById('permissionNotice').classList.add('hidden');
                videoElement.srcObject = stream;
                
                await new Promise((resolve) => {
                    videoElement.onloadedmetadata = () => {
                        videoElement.play();
                        canvasElement.width = videoElement.videoWidth;
                        canvasElement.height = videoElement.videoHeight;
                        resolve();
                    };
                });

                isCameraActive = true;
                updateCameraStatusUI(true);
                startDetectionEngine();

                // Re-verificar cámaras con permisos concedidos para obtener nombres reales de dispositivos
                await checkAvailableCameras();

            } catch (err) {
                console.error('Error cámara:', err);
                showCameraPermissionDeniedUI(err);
            }
        }

        function showCameraPermissionDeniedUI(error) {
            const notice = document.getElementById('permissionNotice');
            notice.classList.remove('hidden');
            const statusText = document.getElementById('cameraStatusText');
            statusText.innerText = 'Cámara: Permiso denegado';
            
            let message = 'El navegador ha bloqueado el acceso a la cámara. Por favor autoriza el permiso en el ícono de la barra de direcciones.';
            if (error.name === 'NotAllowedError') {
                message = 'Has denegado el permiso de cámara. Habilítalo en los ajustes del sitio en el navegador.';
            }
            notice.querySelector('p').innerText = message;
        }

        async function toggleCamera() {
            if (isCameraActive) {
                stopCamera();
            } else {
                await requestCameraAccess();
            }
        }

        function stopCamera() {
            if (isRecording) stopRecording();
            isCameraActive = false;
            activeTracks = [];
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
            if (inferenceTimer) clearTimeout(inferenceTimer);
            if (videoElement.srcObject) {
                videoElement.srcObject.getTracks().forEach(track => track.stop());
                videoElement.srcObject = null;
            }
            updateCameraStatusUI(false);
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
        }

        async function switchCamera() {
            if (availableVideoDevices.length > 1) {
                const currentIndex = availableVideoDevices.findIndex(d => d.deviceId === activeCameraDeviceId);
                const nextIndex = (currentIndex + 1) % availableVideoDevices.length;
                await selectCamera(availableVideoDevices[nextIndex].deviceId);
            } else {
                if (!isCameraActive) return;
                currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
                stopCamera();
                await requestCameraAccess();
            }
        }

        function toggleFullscreenVideo() {
            const viewport = document.getElementById('videoViewport');
            if (!document.fullscreenElement) {
                viewport.requestFullscreen().catch(err => alert(err.message));
            } else {
                document.exitFullscreen();
            }
        }

        function updateCameraStatusUI(active) {
            const badge = document.getElementById('cameraStatusBadge');
            const statusText = document.getElementById('cameraStatusText');
            const placeholder = document.getElementById('cameraPlaceholder');
            const btnPlayText = document.getElementById('btnPlayText');
            const videoHud = document.getElementById('videoHud');

            if (active) {
                badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-xs font-mono-code';
                badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-emerald-400 animate-pulse';
                statusText.innerText = 'Cámara: Activa';
                placeholder.classList.add('opacity-0', 'pointer-events-none');
                btnPlayText.innerText = 'Pausar Cámara';
                videoHud.classList.remove('hidden');
            } else {
                badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-slate-800 bg-slate-800/80 text-slate-400 text-xs font-mono-code';
                badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-slate-500';
                statusText.innerText = 'Cámara: Inactiva';
                placeholder.classList.remove('opacity-0', 'pointer-events-none');
                btnPlayText.innerText = 'Iniciar Detección';
                videoHud.classList.add('hidden');
                document.getElementById('kpiPersonsCount').innerText = '0 personas';
                document.getElementById('kpiHairTone').innerText = 'No analizado';
                document.getElementById('kpiBehavior').innerText = 'En espera';
                document.getElementById('kpiObjectsCount').innerText = '0 detectados';
                document.getElementById('activeCountBadge').innerText = '0 elementos';
                resetRadarContainers();
            }
        }

        function resetRadarContainers() {
            document.getElementById('radarPersonsContainer').innerHTML = '<span class="text-slate-500 italic">Cámara inactiva</span>';
            document.getElementById('radarGesturesContainer').innerHTML = '<span class="text-slate-500 italic">Cámara inactiva</span>';
            document.getElementById('radarTechContainer').innerHTML = '<span class="text-slate-500 italic">Cámara inactiva</span>';
            document.getElementById('radarFurnitureContainer').innerHTML = '<span class="text-slate-500 italic">Cámara inactiva</span>';
        }

        function updateConfidence(val) {
            minConfidence = val / 100;
            const label = val <= 18 ? `${val}% (Alta/Lejana)` : (val <= 30 ? `${val}% (Media)` : `${val}%`);
            document.getElementById('confidenceValue').innerText = label;
        }

        // ==========================================
        // ULTRA-PERFORMANCE FLASH AI ENGINE
        // ==========================================
        async function loadDetectionModel() {
            if (cocoModel || isModelLoading) return;
            isModelLoading = true;
            const badge = document.getElementById('aiModelBadge');
            const text = document.getElementById('aiModelText');

            try {
                if (window.tf) {
                    tf.env().set('WEBGL_PACK', true);
                    tf.env().set('WEBGL_FORCE_F16_TEXTURES', true);
                    await tf.setBackend('webgl').catch(() => tf.setBackend('cpu'));
                    await tf.ready();
                }

                text.innerText = 'IA: Descargando Red...';
                cocoModel = await cocoSsd.load({ base: 'lite_mobilenet_v2' });
                
                badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-xs font-mono-code';
                badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-emerald-400';
                text.innerText = 'IA: Activo Flash (GPU)';
            } catch (e) {
                badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-rose-500/30 bg-rose-500/10 text-rose-400 text-xs font-mono-code';
                badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-rose-400';
                text.innerText = 'IA: Error al cargar';
            } finally {
                isModelLoading = false;
            }
        }

        function startDetectionEngine() {
            if (!cocoModel) loadDetectionModel();
            if (videoElement.videoWidth > 0) {
                canvasElement.width = videoElement.videoWidth;
                canvasElement.height = videoElement.videoHeight;
            }
            renderLoop();
            inferenceScheduler();
        }

        // 1. RENDER LOOP: Runs at full 60 FPS purely rendering latest cached overlay
        function renderLoop() {
            if (!isCameraActive || !videoElement || videoElement.readyState < 2) {
                if (isCameraActive) animationFrameId = requestAnimationFrame(renderLoop);
                return;
            }

            if (canvasElement.width !== videoElement.videoWidth || canvasElement.height !== videoElement.videoHeight) {
                canvasElement.width = videoElement.videoWidth;
                canvasElement.height = videoElement.videoHeight;
            }

            // Interpolación de movimiento a 60 FPS (Seguimiento continuo de personas y objetos)
            for (const track of activeTracks) {
                track.bbox[0] += (track.targetBbox[0] - track.bbox[0]) * TRACK_LERP_FACTOR;
                track.bbox[1] += (track.targetBbox[1] - track.bbox[1]) * TRACK_LERP_FACTOR;
                track.bbox[2] += (track.targetBbox[2] - track.bbox[2]) * TRACK_LERP_FACTOR;
                track.bbox[3] += (track.targetBbox[3] - track.bbox[3]) * TRACK_LERP_FACTOR;
            }

            renderComprehensiveOverlay(cachedPredictions, cachedPersonsData, cachedBehavior);

            // Composición para grabación de video (Cámara Web Real + Bounding Boxes & HUD de IA)
            if (isRecording) {
                if (recordingCanvas.width !== videoElement.videoWidth || recordingCanvas.height !== videoElement.videoHeight) {
                    recordingCanvas.width = videoElement.videoWidth;
                    recordingCanvas.height = videoElement.videoHeight;
                }
                // 1. Dibuja la cámara real en alta definición
                recordingCtx.drawImage(videoElement, 0, 0, recordingCanvas.width, recordingCanvas.height);
                // 2. Dibuja las cajas delimitadoras, etiquetas y HUD en tiempo real
                recordingCtx.drawImage(canvasElement, 0, 0, recordingCanvas.width, recordingCanvas.height);
            }

            frameCount++;
            const now = performance.now();
            if (now - lastFrameTime >= 1000) {
                fps = frameCount;
                frameCount = 0;
                lastFrameTime = now;
                document.getElementById('fpsCounter').innerText = `${fps} FPS`;
            }

            if (isCameraActive) {
                animationFrameId = requestAnimationFrame(renderLoop);
            }
        }

        // 2. INFERENCE SCHEDULER: Autonomous zero-millisecond loop for instant detection
        function scheduleNextInference() {
            if (!isCameraActive) return;
            if (videoElement && 'requestVideoFrameCallback' in videoElement) {
                videoElement.requestVideoFrameCallback(() => {
                    inferenceScheduler();
                });
            } else {
                requestAnimationFrame(() => {
                    inferenceScheduler();
                });
            }
        }

        async function inferenceScheduler() {
            if (!isCameraActive) return;
            if (cocoModel && !isInferring && videoElement && videoElement.readyState >= 2) {
                await runFastInference();
            }
            if (isCameraActive) {
                scheduleNextInference();
            }
        }

        // 3. FAST HIGH-PRECISION INFERENCE: Detects far-away and subtle objects instantly
        async function runFastInference() {
            isInferring = true;
            const startTime = performance.now();

            try {
                // High-fidelity downscaled frame preserving distant object features
                inferCtx.drawImage(videoElement, 0, 0, inferCanvas.width, inferCanvas.height);

                // Run MobileNet inference with high box capacity and low confidence threshold for distant items
                const rawPredictions = await cocoModel.detect(inferCanvas, 40, minConfidence);

                // Re-project coordinates onto full screen canvas
                const scaleX = canvasElement.width / inferCanvas.width;
                const scaleY = canvasElement.height / inferCanvas.height;

                const scaled = rawPredictions.map(p => ({
                    class: p.class,
                    score: p.score,
                    bbox: [
                        p.bbox[0] * scaleX,
                        p.bbox[1] * scaleY,
                        p.bbox[2] * scaleX,
                        p.bbox[3] * scaleY
                    ]
                }));

                // Actualizar el motor de seguimiento multi-objetivo continuo
                updateObjectTracks(scaled);

                // Proyectar los tracks seguidos a la caché de predicciones y personas
                const trackedPredictions = activeTracks.map(t => ({
                    id: t.id,
                    class: t.class,
                    score: t.score,
                    bbox: t.bbox
                }));

                cachedPredictions = trackedPredictions;
                cachedPersonsData = analyzePersonsAndHair(activeTracks);
                cachedBehavior = analyzeGesturesAndBehavior(trackedPredictions, cachedPersonsData);

                const infDuration = Math.round(performance.now() - startTime);
                document.getElementById('inferenceCounter').innerText = `${infDuration} ms`;

                processAllDetectionsAndWebSocket(trackedPredictions, cachedPersonsData, cachedBehavior);

                // Instant UI feedback (every 80ms)
                const now = performance.now();
                if (now - lastDomUpdateTime > 80) {
                    lastDomUpdateTime = now;
                    updateKPIsAndRadar(trackedPredictions, cachedPersonsData, cachedBehavior);
                }

            } catch (err) {
                console.warn('Inference notice:', err);
            } finally {
                isInferring = false;
            }
        }

        // ==========================================
        // HAIR COLOR ANALYSIS (THROTTLED SAMPLING CON SEGUIMIENTO)
        // ==========================================
        function analyzePersonsAndHair(items) {
            const personTracks = activeTracks.filter(p => p.class === 'person');
            if (personTracks.length === 0) return [];

            const now = performance.now();
            const shouldSampleHair = (now - lastHairSampleTime > 1800);
            if (shouldSampleHair) lastHairSampleTime = now;

            return personTracks.map((track) => {
                const [x, y, w, h] = track.bbox;

                if (shouldSampleHair || !track.hair) {
                    const hairY = Math.max(0, y);
                    const hairH = Math.max(10, h * 0.18);
                    const hairW = Math.max(10, w * 0.55);
                    const hairX = Math.max(0, x + (w - hairW) / 2);
                    const hairAnalysis = sampleHairTone(hairX, hairY, hairW, hairH);
                    if (hairAnalysis) track.hair = hairAnalysis;
                }

                return {
                    id: track.id,
                    bbox: track.bbox,
                    score: track.score,
                    hair: track.hair
                };
            });
        }

        function sampleHairTone(x, y, w, h) {
            try {
                offscreenCanvas.width = w;
                offscreenCanvas.height = h;
                offscreenCtx.drawImage(videoElement, x, y, w, h, 0, 0, w, h);

                const imgData = offscreenCtx.getImageData(0, 0, w, h);
                const data = imgData.data;

                let rTotal = 0, gTotal = 0, bTotal = 0, count = 0;
                for (let i = 0; i < data.length; i += 24) {
                    const r = data[i];
                    const g = data[i+1];
                    const b = data[i+2];

                    if (r > 240 && g > 240 && b > 240) continue;
                    const isSkin = (r > 120 && g > 80 && b > 60 && (r - g > 15) && (g - b > 10));
                    if (isSkin && count > 10) continue;

                    rTotal += r; gTotal += g; bTotal += b; count++;
                }

                if (count === 0) count = 1;
                const rAvg = Math.round(rTotal / count);
                const gAvg = Math.round(gTotal / count);
                const bAvg = Math.round(bTotal / count);

                const rNorm = rAvg / 255, gNorm = gAvg / 255, bNorm = bAvg / 255;
                const max = Math.max(rNorm, gNorm, bNorm), min = Math.min(rNorm, gNorm, bNorm);
                let hVal, sVal, lVal = (max + min) / 2;

                if (max === min) {
                    hVal = sVal = 0;
                } else {
                    const d = max - min;
                    sVal = lVal > 0.5 ? d / (2 - max - min) : d / (max + min);
                    switch (max) {
                        case rNorm: hVal = (gNorm - bNorm) / d + (gNorm < bNorm ? 6 : 0); break;
                        case gNorm: hVal = (bNorm - rNorm) / d + 2; break;
                        case bNorm: hVal = (rNorm - gNorm) / d + 4; break;
                    }
                    hVal /= 6;
                }

                const hueDeg = Math.round(hVal * 360);
                const satPct = Math.round(sVal * 100);
                const lumPct = Math.round(lVal * 100);

                let toneKey = 'hair_dark_brown';
                let toneName = 'Cabello Castaño Oscuro';
                let toneColor = HAIR_COLOR_MAP['hair_dark_brown'].color;

                if (lumPct < 22) {
                    toneKey = 'hair_black';
                    toneName = 'Cabello Negro / Ébano';
                    toneColor = HAIR_COLOR_MAP['hair_black'].color;
                } else if (satPct < 15 && lumPct >= 42) {
                    toneKey = 'hair_gray';
                    toneName = 'Cabello Canoso / Plateado';
                    toneColor = HAIR_COLOR_MAP['hair_gray'].color;
                } else if (hueDeg >= 30 && hueDeg <= 60 && lumPct >= 40) {
                    toneKey = 'hair_blonde';
                    toneName = 'Cabello Rubio / Dorado';
                    toneColor = HAIR_COLOR_MAP['hair_blonde'].color;
                } else if ((hueDeg <= 18 || hueDeg >= 345) && satPct >= 30 && lumPct >= 20) {
                    toneKey = 'hair_red';
                    toneName = 'Cabello Rojizo / Cobrizo';
                    toneColor = HAIR_COLOR_MAP['hair_red'].color;
                } else if (lumPct >= 32) {
                    toneKey = 'hair_light_brown';
                    toneName = 'Cabello Castaño Claro';
                    toneColor = HAIR_COLOR_MAP['hair_light_brown'].color;
                }

                return {
                    key: toneKey,
                    name: toneName,
                    color: toneColor,
                    rgb: `rgb(${rAvg},${gAvg},${bAvg})`,
                    confidence: 0.92
                };
            } catch (err) {
                return { key: 'hair_dark_brown', name: 'Cabello Castaño', color: '#78350F', rgb: 'rgb(80,50,30)', confidence: 0.85 };
            }
        }

        // ==========================================
        // GESTURE & BEHAVIOR ANALYSIS HEURISTICS
        // ==========================================
        function analyzeGesturesAndBehavior(predictions, personsData) {
            const personCount = personsData.length;

            if (personCount === 0) {
                return {
                    key: 'absent',
                    name: 'Persona Ausente / Sin Detección',
                    color: BEHAVIOR_COLOR_MAP['absent'].color,
                    confidence: 0.95
                };
            }

            const phones = predictions.filter(p => p.class === 'cell phone');
            const laptops = predictions.filter(p => p.class === 'laptop' || p.class === 'tv');
            const drinks = predictions.filter(p => p.class === 'bottle' || p.class === 'cup' || p.class === 'wine glass');
            const books = predictions.filter(p => p.class === 'book');

            if (personCount >= 2) {
                const p1 = personsData[0].bbox;
                const p2 = personsData[1].bbox;
                const dist = Math.abs((p1[0] + p1[2]/2) - (p2[0] + p2[2]/2));

                if (dist < (p1[2] + p2[2]) * 1.5) {
                    return {
                        key: 'group_interaction',
                        name: 'Interacción / Conversación en Grupo',
                        color: BEHAVIOR_COLOR_MAP['group_interaction'].color,
                        confidence: 0.94
                    };
                }

                return {
                    key: 'multiple_people',
                    name: `Múltiples Personas Detectadas (${personCount} en escena)`,
                    color: BEHAVIOR_COLOR_MAP['multiple_people'].color,
                    confidence: 0.96
                };
            }

            const mainPerson = personsData[0];
            const [px, py, pw, ph] = mainPerson.bbox;

            // Gesture: Phone Call
            for (const phone of phones) {
                const [bx, by, bw, bh] = phone.bbox;
                const phoneCenterX = bx + bw / 2;
                const phoneCenterY = by + bh / 2;

                if (phoneCenterX >= px - pw * 0.25 && phoneCenterX <= px + pw * 1.25 &&
                    phoneCenterY >= py && phoneCenterY <= py + ph * 0.6) {
                    return {
                        key: 'talking_phone',
                        name: 'Hablando por Teléfono / Celular',
                        color: BEHAVIOR_COLOR_MAP['talking_phone'].color,
                        confidence: 0.95
                    };
                }
            }

            // Gesture: Drinking
            for (const drink of drinks) {
                const [dx, dy, dw, dh] = drink.bbox;
                const drinkCenterX = dx + dw / 2;
                const drinkCenterY = dy + dh / 2;

                if (drinkCenterX >= px - pw * 0.2 && drinkCenterX <= px + pw * 1.2 &&
                    drinkCenterY >= py && drinkCenterY <= py + ph * 0.55) {
                    return {
                        key: 'drinking',
                        name: 'Bebiendo / Consumiendo Líquido',
                        color: BEHAVIOR_COLOR_MAP['drinking'].color,
                        confidence: 0.92
                    };
                }
            }

            // Gesture: Hands up
            const nowTime = performance.now();
            let isRestless = false;
            if (previousPersons.length > 0 && (nowTime - previousPersonTime < 350)) {
                const prev = previousPersons[0].bbox;
                const deltaX = Math.abs(px - prev[0]);
                const deltaY = Math.abs(py - prev[1]);

                if (deltaY > canvasElement.height * 0.09 && py < prev[1]) {
                    return {
                        key: 'hands_up',
                        name: 'Gesto: Manos Arriba / Alerta',
                        color: BEHAVIOR_COLOR_MAP['hands_up'].color,
                        confidence: 0.91
                    };
                }

                if (deltaX > canvasElement.width * 0.08 || deltaY > canvasElement.height * 0.08) {
                    isRestless = true;
                }
            }

            previousPersons = personsData;
            previousPersonTime = nowTime;

            if (isRestless) {
                return {
                    key: 'restless',
                    name: 'Movimiento Rápido / Inquietud',
                    color: BEHAVIOR_COLOR_MAP['restless'].color,
                    confidence: 0.89
                };
            }

            // Working on Computer
            for (const laptop of laptops) {
                const [lx, ly, lw, lh] = laptop.bbox;
                const laptopCenterX = lx + lw / 2;
                const laptopCenterY = ly + lh / 2;

                if (laptopCenterX >= px - pw * 0.25 && laptopCenterX <= px + pw * 1.25 &&
                    laptopCenterY >= py + ph * 0.3) {
                    return {
                        key: 'working_laptop',
                        name: 'Trabajando en Computadora',
                        color: BEHAVIOR_COLOR_MAP['working_laptop'].color,
                        confidence: 0.96
                    };
                }
            }

            // Reading
            for (const book of books) {
                const [bkx, bky, bkw, bkh] = book.bbox;
                if (bkx + bkw/2 >= px && bkx + bkw/2 <= px + pw && bky >= py + ph * 0.2) {
                    return {
                        key: 'reading',
                        name: 'Leyendo Documento / Libro',
                        color: BEHAVIOR_COLOR_MAP['reading'].color,
                        confidence: 0.90
                    };
                }
            }

            return {
                key: 'attentive',
                name: 'Persona Atenta / Presente',
                color: BEHAVIOR_COLOR_MAP['attentive'].color,
                confidence: 0.94
            };
        }

        // ==========================================
        // VIBRANT COLOR RESOLVER FOR ALL OBJECTS
        // ==========================================
        function getObjectColor(label) {
            if (OBJECT_COLOR_MAP[label]) return OBJECT_COLOR_MAP[label].color;

            let hash = 0;
            for (let i = 0; i < label.length; i++) hash = label.charCodeAt(i) + ((hash << 5) - hash);
            const hue = Math.abs(hash % 360);
            return `hsl(${hue}, 85%, 55%)`;
        }

        function getObjectDisplayName(label) {
            if (COCO_SPANISH_MAP[label]) return COCO_SPANISH_MAP[label];
            if (OBJECT_COLOR_MAP[label]) return OBJECT_COLOR_MAP[label].name;
            return label.charAt(0).toUpperCase() + label.slice(1);
        }

        // ==========================================
        // CANVAS RENDERING WITH EXACT COORDINATES
        // ==========================================
        function renderComprehensiveOverlay(predictions, personsData, behavior) {
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            predictions.forEach(pred => {
                const isPerson = pred.class === 'person';
                const [x, y, width, height] = pred.bbox;
                const color = getObjectColor(pred.class);
                const displayName = getObjectDisplayName(pred.class);
                const scorePercent = Math.round(pred.score * 100);

                ctx.save();
                ctx.strokeStyle = color;
                ctx.lineWidth = isPerson ? 3 : 2;
                ctx.strokeRect(x, y, width, height);

                const bracketSize = Math.min(18, width / 4, height / 4);
                ctx.lineWidth = 4;
                ctx.beginPath(); ctx.moveTo(x, y + bracketSize); ctx.lineTo(x, y); ctx.lineTo(x + bracketSize, y); ctx.stroke();
                ctx.beginPath(); ctx.moveTo(x + width - bracketSize, y); ctx.lineTo(x + width, y); ctx.lineTo(x + width, y + bracketSize); ctx.stroke();
                ctx.beginPath(); ctx.moveTo(x, y + height - bracketSize); ctx.lineTo(x, y + height); ctx.lineTo(x + bracketSize, y + height); ctx.stroke();
                ctx.beginPath(); ctx.moveTo(x + width - bracketSize, y + height); ctx.lineTo(x + width, y + height); ctx.lineTo(x + width, y + height - bracketSize); ctx.stroke();
                ctx.restore();

                // Retícula central de seguimiento activo
                const cx = x + width / 2;
                const cy = y + height / 2;
                ctx.save();
                ctx.strokeStyle = color;
                ctx.lineWidth = 1.5;
                ctx.globalAlpha = 0.55;
                ctx.beginPath();
                ctx.moveTo(cx - 7, cy); ctx.lineTo(cx + 7, cy);
                ctx.moveTo(cx, cy - 7); ctx.lineTo(cx, cy + 7);
                ctx.stroke();
                ctx.restore();

                const labelText = isPerson ? `${displayName} #${pred.id || 1} • ${scorePercent}%` : `${displayName} ${scorePercent}%`;
                ctx.font = 'bold 12px "JetBrains Mono", monospace';
                const textWidth = ctx.measureText(labelText).width;
                const tagHeight = 22;
                const tagY = y > tagHeight + 5 ? y - tagHeight - 2 : y + 2;

                ctx.fillStyle = color;
                ctx.fillRect(x, tagY, textWidth + 14, tagHeight);

                ctx.fillStyle = '#0a0f1d';
                ctx.fillText(labelText, x + 7, tagY + 15);
            });

            personsData.forEach(p => {
                const [px, py, pw, ph] = p.bbox;
                if (p.hair) {
                    const hairTag = `[Persona #${p.id} • ${p.hair.name}]`;
                    ctx.font = 'bold 12px "JetBrains Mono", monospace';
                    const tagW = ctx.measureText(hairTag).width + 16;
                    const tagY = Math.max(26, py - 26);

                    ctx.save();
                    ctx.fillStyle = 'rgba(15, 23, 42, 0.92)';
                    ctx.strokeStyle = p.hair.color;
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.roundRect(px, tagY, tagW, 22, 6);
                    ctx.fill();
                    ctx.stroke();

                    ctx.fillStyle = p.hair.color;
                    ctx.beginPath();
                    ctx.arc(px + 10, tagY + 11, 4.5, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.fillStyle = '#f8fafc';
                    ctx.fillText(hairTag, px + 20, tagY + 15);
                    ctx.restore();
                }
            });

            if (behavior && behavior.key !== 'absent') {
                const bannerHeight = 36;
                const bannerY = canvasElement.height - bannerHeight - 12;
                const bannerText = `GESTO / COMPORTAMIENTO: ${behavior.name.toUpperCase()}`;

                ctx.font = 'bold 13px "JetBrains Mono", monospace';
                const bannerWidth = ctx.measureText(bannerText).width + 36;
                const bannerX = (canvasElement.width - bannerWidth) / 2;

                ctx.save();
                ctx.fillStyle = 'rgba(10, 15, 29, 0.92)';
                ctx.strokeStyle = behavior.color;
                ctx.lineWidth = 2.5;

                ctx.beginPath();
                ctx.roundRect(bannerX, bannerY, bannerWidth, bannerHeight, 10);
                ctx.fill();
                ctx.stroke();

                ctx.fillStyle = behavior.color;
                ctx.fillText(bannerText, bannerX + 18, bannerY + 23);
                ctx.restore();
            }
        }

        // ==========================================
        // SMART THROTTLED WEBSOCKET DISPATCHER
        // ==========================================
        function processAllDetectionsAndWebSocket(predictions, personsData, behavior) {
            const now = performance.now();
            const THROTTLE_MS = 5000;

            predictions.forEach(pred => {
                if (pred.class === 'person') return;
                const key = `obj_${pred.class}`;
                const lastSent = lastEventSentTimestamps[key] || 0;

                if (now - lastSent >= THROTTLE_MS) {
                    lastEventSentTimestamps[key] = now;
                    sendDetectionToServer({
                        category: 'object',
                        label: pred.class,
                        display_name: getObjectDisplayName(pred.class),
                        confidence: pred.score,
                        color: getObjectColor(pred.class),
                        details: { bbox: pred.bbox }
                    });
                }
            });

            if (personsData.length > 0) {
                const mainPerson = personsData[0];
                if (mainPerson.hair && currentDetectedHair !== mainPerson.hair.key) {
                    const hairKey = `hair_${mainPerson.hair.key}`;
                    const lastHairSent = lastEventSentTimestamps[hairKey] || 0;

                    if (now - lastHairSent >= 6000) {
                        currentDetectedHair = mainPerson.hair.key;
                        lastEventSentTimestamps[hairKey] = now;

                        sendDetectionToServer({
                            category: 'hair',
                            label: mainPerson.hair.key,
                            display_name: mainPerson.hair.name,
                            confidence: mainPerson.hair.confidence,
                            color: mainPerson.hair.color,
                            details: { rgb: mainPerson.hair.rgb }
                        });
                    }
                }
            }

            if (behavior && behavior.key !== 'absent') {
                const behaviorKey = `beh_${behavior.key}`;
                const lastBehaviorSent = lastEventSentTimestamps[behaviorKey] || 0;
                const changed = currentActiveBehavior !== behavior.key;
                const interval = changed ? 600 : 7000;

                if (now - lastBehaviorSent >= interval) {
                    currentActiveBehavior = behavior.key;
                    lastEventSentTimestamps[behaviorKey] = now;

                    sendDetectionToServer({
                        category: behavior.key.startsWith('hands_up') || behavior.key.startsWith('waving') ? 'gesture' : 'behavior',
                        label: behavior.key,
                        display_name: behavior.name,
                        confidence: behavior.confidence,
                        color: behavior.color,
                        details: { persons: personsData.length }
                    });
                }
            }
        }

        async function sendDetectionToServer(payload) {
            try {
                const response = await fetch('/api/detections', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                if (!pusherInstance || pusherInstance.connection.state !== 'connected') {
                    if (result.success && result.detection) {
                        handleWebSocketNotification(result.detection);
                    }
                }
            } catch (err) {
                console.warn('Sync notice:', err);
            }
        }

        // ==========================================
        // RADAR & KPIS UPDATE (THROTTLED)
        // ==========================================
        function updateKPIsAndRadar(predictions, personsData, behavior) {
            const countBadge = document.getElementById('activeCountBadge');
            const kpiPersons = document.getElementById('kpiPersonsCount');
            const kpiHair = document.getElementById('kpiHairTone');
            const kpiBehavior = document.getElementById('kpiBehavior');
            const kpiObjects = document.getElementById('kpiObjectsCount');

            const hudPersons = document.getElementById('hudPersonsBadge');
            const hudHair = document.getElementById('hudHairBadge');
            const hudBehavior = document.getElementById('hudBehaviorBadge');

            const personCount = personsData.length;
            kpiPersons.innerText = personCount === 1 ? '1 persona' : `${personCount} personas`;
            hudPersons.innerText = `Personas: ${personCount}`;

            if (personsData.length > 0 && personsData[0].hair) {
                const hName = personsData[0].hair.name.replace('Cabello ', '');
                kpiHair.innerText = hName;
                kpiHair.style.color = personsData[0].hair.color;
                hudHair.innerText = `Cabello: ${hName}`;
                hudHair.style.borderColor = personsData[0].hair.color;
            } else {
                kpiHair.innerText = 'No detectado';
                kpiHair.style.color = '#94a3b8';
                hudHair.innerText = 'Cabello: -';
            }

            if (behavior && behavior.key !== 'absent') {
                kpiBehavior.innerText = behavior.name;
                kpiBehavior.style.color = behavior.color;
                hudBehavior.innerText = `Gesto: ${behavior.name}`;
                hudBehavior.style.borderColor = behavior.color;
            } else {
                kpiBehavior.innerText = 'Ausente';
                kpiBehavior.style.color = '#64748b';
                hudBehavior.innerText = 'Gesto: Ausente';
            }

            const nonPersons = predictions.filter(p => p.class !== 'person');
            kpiObjects.innerText = `${nonPersons.length} detectados`;
            countBadge.innerText = `${predictions.length} elementos`;

            // Radar Personas
            const radarPersons = document.getElementById('radarPersonsContainer');
            if (personsData.length === 0) {
                radarPersons.innerHTML = '<span class="text-slate-500 italic">No hay personas en cámara</span>';
            } else {
                radarPersons.innerHTML = personsData.map(p => `
                    <div class="p-1.5 rounded-lg bg-slate-900 border border-blue-500/30 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="font-bold text-white">Persona #${p.id}</span>
                        </div>
                        <span class="text-[11px] font-semibold" style="color: ${p.hair ? p.hair.color : '#94a3b8'}">
                            ${p.hair ? p.hair.name : 'Cabello N/D'}
                        </span>
                    </div>
                `).join('');
            }

            // Radar Gestos
            const radarGestures = document.getElementById('radarGesturesContainer');
            if (behavior && behavior.key !== 'absent') {
                radarGestures.innerHTML = `
                    <div class="p-1.5 rounded-lg bg-slate-900 border text-xs flex items-center gap-2" style="border-color: ${behavior.color}55;">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${behavior.color};"></span>
                        <span class="font-bold text-white">${behavior.name}</span>
                    </div>
                `;
            } else {
                radarGestures.innerHTML = '<span class="text-slate-500 italic">Sin gestos detectados</span>';
            }

            // Radar Tecnología
            const techClasses = ['laptop', 'tv', 'cell phone', 'mouse', 'keyboard', 'remote', 'microwave'];
            const techItems = predictions.filter(p => techClasses.includes(p.class));
            const radarTech = document.getElementById('radarTechContainer');
            if (techItems.length === 0) {
                radarTech.innerHTML = '<span class="text-slate-500 italic">No hay dispositivos en vista</span>';
            } else {
                radarTech.innerHTML = techItems.map(t => {
                    const c = getObjectColor(t.class);
                    return `
                        <div class="p-1.5 rounded-lg bg-slate-900 border text-xs flex items-center justify-between" style="border-color: ${c}44;">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${c};"></span>
                                <span class="text-slate-200 font-medium">${getObjectDisplayName(t.class)}</span>
                            </div>
                            <span class="text-[11px] font-mono-code text-cyan-400">${Math.round(t.score*100)}%</span>
                        </div>
                    `;
                }).join('');
            }

            // Radar Mobiliario
            const furnitureItems = predictions.filter(p => !techClasses.includes(p.class) && p.class !== 'person');
            const radarFurniture = document.getElementById('radarFurnitureContainer');
            if (furnitureItems.length === 0) {
                radarFurniture.innerHTML = '<span class="text-slate-500 italic">Sin objetos de entorno detectados</span>';
            } else {
                radarFurniture.innerHTML = furnitureItems.map(f => {
                    const c = getObjectColor(f.class);
                    return `
                        <div class="p-1.5 rounded-lg bg-slate-900 border text-xs flex items-center justify-between" style="border-color: ${c}44;">
                            <div class="flex items-center gap-1.5 truncate">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${c};"></span>
                                <span class="text-slate-200 font-medium truncate">${getObjectDisplayName(f.class)}</span>
                            </div>
                            <span class="text-[11px] font-mono-code text-cyan-400 shrink-0 ml-1">${Math.round(f.score*100)}%</span>
                        </div>
                    `;
                }).join('');
            }
        }

        // ==========================================
        // SNAPSHOT CAPTURE & DIRECT WINDOWS SAVING
        // ==========================================
        async function takeSnapshot() {
            if (!hasSnapshotPermission) {
                alert('No tienes el permiso de captura activo. Por favor autorízalo primero en el Centro de Permisos.');
                return;
            }
            if (!isCameraActive) {
                alert('La cámara debe estar activa para tomar una captura.');
                return;
            }

            const snapCanvas = document.createElement('canvas');
            snapCanvas.width = canvasElement.width;
            snapCanvas.height = canvasElement.height;
            const sCtx = snapCanvas.getContext('2d');
            sCtx.drawImage(videoElement, 0, 0, snapCanvas.width, snapCanvas.height);
            sCtx.drawImage(canvasElement, 0, 0, snapCanvas.width, snapCanvas.height);

            const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
            const filename = `captura-dbcomputech-${timestamp}.png`;

            let savedLocally = false;
            try {
                if ('showSaveFilePicker' in window) {
                    const blob = await new Promise(r => snapCanvas.toBlob(r, 'image/png'));
                    const handle = await window.showSaveFilePicker({
                        suggestedName: filename,
                        types: [{
                            description: 'Imagen PNG (*.png)',
                            accept: { 'image/png': ['.png'] }
                        }]
                    });
                    const writable = await handle.createWritable();
                    await writable.write(blob);
                    await writable.close();
                    savedLocally = true;
                    showToastNotification({
                        category: 'permission',
                        display_name: 'Captura guardada en tu carpeta de Windows',
                        color: '#10B981',
                        confidence: 100
                    });
                }
            } catch (err) {
                if (err.name !== 'AbortError') console.warn('Picker notice:', err);
            }

            const dataUrl = snapCanvas.toDataURL('image/png');
            if (!savedLocally) {
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                showToastNotification({
                    category: 'permission',
                    display_name: 'Captura descargada a tu equipo Windows',
                    color: '#06B6D4',
                    confidence: 100
                });
            }

            document.getElementById('snapshotImg').src = dataUrl;
            document.getElementById('snapshotDownload').href = dataUrl;
            document.getElementById('snapshotDownload').download = filename;
            document.getElementById('snapshotModal').classList.remove('hidden');
        }

        function closeSnapshotModal() {
            document.getElementById('snapshotModal').classList.add('hidden');
        }

        // ==========================================
        // VIDEO RECORDING & DIRECT WINDOWS SAVING
        // ==========================================
        async function toggleRecording() {
            if (!hasRecordPermission) {
                alert('No tienes el permiso de grabación activo. Por favor autorízalo primero en el Centro de Permisos.');
                return;
            }
            if (!isCameraActive) {
                alert('La cámara debe estar activa para poder grabar.');
                return;
            }

            if (isRecording) {
                stopRecording();
            } else {
                startRecording();
            }
        }

        function startRecording() {
            try {
                if (!videoElement || videoElement.videoWidth === 0) {
                    alert('Espera a que el video de la cámara esté activo para grabar.');
                    return;
                }

                recordingCanvas.width = videoElement.videoWidth;
                recordingCanvas.height = videoElement.videoHeight;

                // Captura compuesta: cámara real + bounding boxes y HUD en tiempo real
                const stream = recordingCanvas.captureStream(30);

                // Agregar pista de audio del micrófono si existe en el stream de la cámara
                if (videoElement.srcObject) {
                    const audioTracks = videoElement.srcObject.getAudioTracks();
                    if (audioTracks && audioTracks.length > 0) {
                        stream.addTrack(audioTracks[0]);
                    }
                }

                // Detectar formato compatible preferente (MP4 para soporte nativo de Windows o WebM seekable)
                recordingMimeType = 'video/webm;codecs=vp9,opus';
                recordingExtension = 'webm';

                if (MediaRecorder.isTypeSupported('video/mp4;codecs=avc1,mp4a.40.2')) {
                    recordingMimeType = 'video/mp4;codecs=avc1,mp4a.40.2';
                    recordingExtension = 'mp4';
                } else if (MediaRecorder.isTypeSupported('video/mp4')) {
                    recordingMimeType = 'video/mp4';
                    recordingExtension = 'mp4';
                } else if (MediaRecorder.isTypeSupported('video/webm;codecs=vp9,opus')) {
                    recordingMimeType = 'video/webm;codecs=vp9,opus';
                    recordingExtension = 'webm';
                } else if (MediaRecorder.isTypeSupported('video/webm;codecs=vp8,opus')) {
                    recordingMimeType = 'video/webm;codecs=vp8,opus';
                    recordingExtension = 'webm';
                } else if (MediaRecorder.isTypeSupported('video/webm')) {
                    recordingMimeType = 'video/webm';
                    recordingExtension = 'webm';
                }

                recordedChunks = [];
                mediaRecorder = new MediaRecorder(stream, { mimeType: recordingMimeType });

                mediaRecorder.ondataavailable = (e) => {
                    if (e.data && e.data.size > 0) {
                        recordedChunks.push(e.data);
                    }
                };

                mediaRecorder.onstop = saveRecordedVideoToWindows;

                mediaRecorder.start(100);
                isRecording = true;
                recordStartTime = Date.now();

                document.getElementById('btnRecordText').innerText = 'Detener Grabación';
                document.getElementById('hudRecordingBadge').classList.remove('hidden');
                
                recordTimerInterval = setInterval(() => {
                    const elapsed = Math.floor((Date.now() - recordStartTime) / 1000);
                    const m = String(Math.floor(elapsed / 60)).padStart(2, '0');
                    const s = String(elapsed % 60).padStart(2, '0');
                    document.getElementById('hudRecordTimer').innerText = `REC ${m}:${s}`;
                }, 1000);

                showToastNotification({
                    category: 'permission',
                    display_name: 'Grabación Compuesta Iniciada (Cámara + IA)',
                    color: '#EF4444',
                    confidence: 100
                });

            } catch (err) {
                console.error('Error al iniciar grabación:', err);
                alert('No se pudo iniciar la grabación: ' + err.message);
            }
        }

        function stopRecording() {
            if (!isRecording || !mediaRecorder) return;
            mediaRecorder.stop();
            isRecording = false;
            clearInterval(recordTimerInterval);

            document.getElementById('btnRecordText').innerText = 'Grabar Video';
            document.getElementById('hudRecordingBadge').classList.add('hidden');
        }

        async function saveRecordedVideoToWindows() {
            let blob = new Blob(recordedChunks, { type: recordingMimeType });
            const durationMs = Math.max(1000, Date.now() - recordStartTime);

            // Inyectar metadatos de duración para permitir avance y retroceso (seeking) en reproductores
            if (recordingExtension === 'webm' && typeof window.ysFixWebmDuration === 'function') {
                try {
                    blob = await window.ysFixWebmDuration(blob, durationMs, { logger: false });
                } catch (e) {
                    console.warn('WebM duration fix notice:', e);
                }
            }

            const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
            const filename = `grabacion-dbcomputech-${timestamp}.${recordingExtension}`;

            let savedLocally = false;
            try {
                if ('showSaveFilePicker' in window) {
                    const pickerOptions = {
                        suggestedName: filename,
                        types: recordingExtension === 'mp4' ? [{
                            description: 'Video MP4 (*.mp4)',
                            accept: { 'video/mp4': ['.mp4'] }
                        }] : [{
                            description: 'Video WebM Seekable (*.webm)',
                            accept: { 'video/webm': ['.webm'] }
                        }]
                    };
                    const handle = await window.showSaveFilePicker(pickerOptions);
                    const writable = await handle.createWritable();
                    await writable.write(blob);
                    await writable.close();
                    savedLocally = true;
                    showToastNotification({
                        category: 'permission',
                        display_name: `Video guardado en tu carpeta (${filename})`,
                        color: '#10B981',
                        confidence: 100
                    });
                }
            } catch (err) {
                if (err.name !== 'AbortError') console.warn('Save notice:', err);
            }

            if (!savedLocally) {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(() => URL.revokeObjectURL(url), 5000);

                showToastNotification({
                    category: 'permission',
                    display_name: `Video descargado a Windows (${filename})`,
                    color: '#06B6D4',
                    confidence: 100
                });
            }
        }

        // ==========================================
        // DATABASE AUDIT LOG ACTIONS (15 REGISTROS POR PÁGINA)
        // ==========================================
        let dbCurrentPage = 1;
        const DB_PAGE_SIZE = 15;
        let dbTotalRecords = {{ $stats['total'] }};
        let dbTotalPages = {{ max(1, (int) ceil($stats['total'] / 15)) }};
        let isDbLoading = false;

        async function fetchDbLogsPage(page = 1) {
            if (isDbLoading) return;
            isDbLoading = true;
            dbCurrentPage = Math.max(1, page);

            const tbody = document.getElementById('logsTableBody');
            const prevBtn = document.getElementById('btnDbPrevPage');
            const nextBtn = document.getElementById('btnDbNextPage');
            const pageIndicator = document.getElementById('dbPageIndicator');
            const recordsCountText = document.getElementById('dbRecordsCountText');

            if (tbody) tbody.classList.add('opacity-50');

            try {
                const offset = (dbCurrentPage - 1) * DB_PAGE_SIZE;
                const res = await fetch(`/api/detections?limit=${DB_PAGE_SIZE}&offset=${offset}`);
                const data = await res.json();

                dbTotalRecords = data.total || 0;
                dbTotalPages = Math.max(1, Math.ceil(dbTotalRecords / DB_PAGE_SIZE));

                if (dbCurrentPage > dbTotalPages) {
                    dbCurrentPage = dbTotalPages;
                }

                if (tbody) {
                    tbody.innerHTML = '';
                    if (data.logs && data.logs.length > 0) {
                        data.logs.forEach(log => {
                            appendLogRowToTable(log);
                        });
                    } else {
                        tbody.innerHTML = '<tr id="emptyTableRow"><td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay registros en esta página.</td></tr>';
                    }
                }

                if (pageIndicator) {
                    pageIndicator.innerText = `Página ${dbCurrentPage} de ${dbTotalPages}`;
                }
                if (recordsCountText) {
                    const start = dbTotalRecords > 0 ? (dbCurrentPage - 1) * DB_PAGE_SIZE + 1 : 0;
                    const end = Math.min(dbCurrentPage * DB_PAGE_SIZE, dbTotalRecords);
                    recordsCountText.innerText = `${start} - ${end} de ${dbTotalRecords} registros`;
                }

                if (prevBtn) {
                    prevBtn.disabled = dbCurrentPage <= 1;
                    prevBtn.classList.toggle('hidden', dbCurrentPage <= 1);
                }
                if (nextBtn) {
                    nextBtn.disabled = dbCurrentPage >= dbTotalPages;
                    nextBtn.classList.toggle('hidden', dbCurrentPage >= dbTotalPages);
                }

            } catch (e) {
                console.error('Error al cargar historial BD:', e);
            } finally {
                if (tbody) tbody.classList.remove('opacity-50');
                isDbLoading = false;
            }
        }

        function goToPrevDbPage() {
            if (dbCurrentPage > 1 && !isDbLoading) {
                fetchDbLogsPage(dbCurrentPage - 1);
            }
        }

        function goToNextDbPage() {
            if (dbCurrentPage < dbTotalPages && !isDbLoading) {
                fetchDbLogsPage(dbCurrentPage + 1);
            }
        }

        function reloadDbLogs() {
            fetchDbLogsPage(dbCurrentPage);
        }

        function appendLogRowToTable(data) {
            const tbody = document.getElementById('logsTableBody');
            if (!tbody) return;
            const emptyRow = document.getElementById('emptyTableRow');
            if (emptyRow) emptyRow.remove();

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/50 transition';

            let catBadge = '<span class="px-2 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 font-semibold text-[10px]">OBJETO</span>';
            if (data.category === 'hair') {
                catBadge = '<span class="px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-semibold text-[10px]">CABELLO</span>';
            } else if (data.category === 'gesture') {
                catBadge = '<span class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-semibold text-[10px]">GESTO</span>';
            } else if (data.category === 'behavior') {
                catBadge = '<span class="px-2 py-0.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 font-semibold text-[10px]">COMPORTAMIENTO</span>';
            }

            const formattedTime = data.timestamp || (data.created_at ? new Date(data.created_at).toLocaleTimeString() : new Date().toLocaleTimeString());

            tr.innerHTML = `
                <td class="px-4 py-2.5 font-mono-code text-slate-500">#${data.id || '-'}</td>
                <td class="px-4 py-2.5">${catBadge}</td>
                <td class="px-4 py-2.5 font-medium text-slate-200">${data.display_name}</td>
                <td class="px-4 py-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow" style="background-color: ${data.color};"></span>
                        <span class="font-mono-code text-[11px] text-slate-400">${data.color}</span>
                    </div>
                </td>
                <td class="px-4 py-2.5 font-mono-code">
                    <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300">${data.confidence}%</span>
                </td>
                <td class="px-4 py-2.5 text-slate-400 font-mono-code">${formattedTime}</td>
            `;

            tbody.appendChild(tr);
        }

        async function clearDbHistory() {
            if (!confirm('¿Deseas vaciar todo el registro histórico de detecciones?')) return;
            try {
                await fetch('/api/detections', { method: 'DELETE' });
                document.getElementById('logsTableBody').innerHTML = '<tr id="emptyTableRow"><td colspan="6" class="px-4 py-8 text-center text-slate-500">Historial limpiado.</td></tr>';
                document.getElementById('kpiWsCount').innerText = '0 eventos';
                dbCurrentPage = 1;
                dbTotalRecords = 0;
                dbTotalPages = 1;
                const pageIndicator = document.getElementById('dbPageIndicator');
                if (pageIndicator) pageIndicator.innerText = 'Página 1 de 1';
                const recordsCountText = document.getElementById('dbRecordsCountText');
                if (recordsCountText) recordsCountText.innerText = '0 registros';
                const prevBtn = document.getElementById('btnDbPrevPage');
                const nextBtn = document.getElementById('btnDbNextPage');
                if (prevBtn) { prevBtn.disabled = true; prevBtn.classList.add('hidden'); }
                if (nextBtn) { nextBtn.disabled = true; nextBtn.classList.add('hidden'); }
            } catch (e) {
                console.error(e);
            }
        }

        function testNotificationEvent() {
            const demoItems = [
                { category: 'object', label: 'cell phone', display_name: 'Teléfono Celular', color: '#F59E0B', confidence: 0.95 },
                { category: 'object', label: 'laptop', display_name: 'Laptop / Computadora', color: '#10B981', confidence: 0.98 },
                { category: 'object', label: 'chair', display_name: 'Silla / Asiento', color: '#64748B', confidence: 0.89 },
                { category: 'object', label: 'potted plant', display_name: 'Planta Decorativa', color: '#22C55E', confidence: 0.91 },
                { category: 'hair', label: 'hair_dark_brown', display_name: 'Cabello Castaño Oscuro', color: '#78350F', confidence: 0.93 },
                { category: 'hair', label: 'hair_black', display_name: 'Cabello Negro / Ébano', color: '#1E293B', confidence: 0.96 },
                { category: 'hair', label: 'hair_blonde', display_name: 'Cabello Rubio / Dorado', color: '#EAB308', confidence: 0.92 },
                { category: 'gesture', label: 'hands_up', display_name: 'Gesto: Manos Arriba / Alerta', color: '#DC2626', confidence: 0.94 },
                { category: 'gesture', label: 'waving', display_name: 'Gesto: Saludando con la Mano', color: '#F59E0B', confidence: 0.90 },
                { category: 'behavior', label: 'multiple_people', display_name: 'Múltiples Personas Detectadas (2 en escena)', color: '#6366F1', confidence: 0.97 },
                { category: 'behavior', label: 'working_laptop', display_name: 'Trabajando en Computadora', color: '#059669', confidence: 0.96 }
            ];
            const randomItem = demoItems[Math.floor(Math.random() * demoItems.length)];
            sendDetectionToServer(randomItem);
        }

        // Close camera picker menu when clicking outside
        document.addEventListener('click', (e) => {
            const wrapper = document.getElementById('btnSwitchCamWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                closeCameraPickerMenu();
            }
        });

        // Listen for hardware camera connect / disconnect events
        if (navigator.mediaDevices && navigator.mediaDevices.addEventListener) {
            navigator.mediaDevices.addEventListener('devicechange', () => {
                checkAvailableCameras();
            });
        }

        // Auto load on start: WS, detection models, camera enumeration, and initial 6 feed records
        document.addEventListener('DOMContentLoaded', () => {
            initWebSocket();
            loadDetectionModel();
            checkAvailableCameras();
            initFeedInfiniteScroll();
            loadFeedBatch();
        });
    </script>
</body>
</html>
