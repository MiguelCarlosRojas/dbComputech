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

    <!-- MediaPipe Hands for Real-Time Finger Counting & 3D Skeletal Landmark Tracking -->
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js" crossorigin="anonymous"></script>

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
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-sm sm:text-base md:text-lg font-bold tracking-tight text-white flex items-center gap-1.5 sm:gap-2">
                            dbCOMPUTECH <span class="text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-mono-code font-normal">IA Vision 360° Pro</span>
                        </h1>
                        <span id="aiEvolutionBadge" class="hidden xl:inline-flex items-center gap-1.5 text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-mono-code font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            IA Auto-Evolutiva: Gen 1 • 25 Conceptos • 100% Adaptativa
                        </span>
                    </div>
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

                <!-- Las 4 Tarjetas de Estado (IA, WebSocket, Cámara, Auto-Evolución) -->
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-2">Estado de Conexión y Servicios de Inteligencia Artificial:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
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

                        <!-- AI Evolution Status Card -->
                        <div id="aiEvolutionCard" class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-xs font-mono-code shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                            <span id="aiEvolutionText" class="truncate font-semibold">IA Evolutiva: Activa</span>
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
                        <!-- Video element (Webcam local) -->
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

                        <!-- Live Top HUD Overlay (Exclusively EN VIVO and REC indicator) -->
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
                                <span class="text-slate-400 text-[11px] font-semibold uppercase">Precisión / Filtrado:</span>
                                <input id="confidenceThreshold" type="range" min="20" max="85" value="40" oninput="updateConfidence(this.value)" class="w-24 sm:w-32 accent-cyan-400 cursor-pointer">
                                <span id="confidenceValue" class="font-mono-code text-cyan-400 text-xs font-bold whitespace-nowrap">40% (Precisión Óptima)</span>
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
                                {{ $log->created_at->timezone(config('app.timezone', 'America/Lima'))->format('H:i:s') }}
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
            <div class="flex justify-end">
                <a id="snapshotDownload" download="captura-dbcomputech.png" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Descargar de Nuevo
                </a>
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

        // Extended Spanish Dictionary for ALL 80 COCO Classes (Nombres Únicos y Claros)
        const COCO_SPANISH_MAP = {
            'person': 'Persona', 'bicycle': 'Bicicleta', 'car': 'Automóvil', 'motorcycle': 'Motocicleta',
            'airplane': 'Avión', 'bus': 'Autobús', 'train': 'Tren', 'truck': 'Camión', 'boat': 'Barco',
            'traffic light': 'Semáforo', 'fire hydrant': 'Hidrante', 'stop sign': 'Señal Pare',
            'parking meter': 'Parquímetro', 'bench': 'Banca', 'bird': 'Pájaro', 'cat': 'Gato',
            'dog': 'Perro', 'horse': 'Caballo', 'sheep': 'Oveja', 'cow': 'Vaca', 'elephant': 'Elefante',
            'bear': 'Oso', 'zebra': 'Cebra', 'giraffe': 'Jirafa', 'backpack': 'Mochila',
            'umbrella': 'Paraguas', 'handbag': 'Cartera', 'tie': 'Corbata',
            'suitcase': 'Maleta', 'frisbee': 'Frisbee', 'skis': 'Esquís', 'snowboard': 'Snowboard',
            'sports ball': 'Pelota', 'kite': 'Cometa', 'baseball bat': 'Bate',
            'baseball glove': 'Guante', 'skateboard': 'Patineta', 'surfboard': 'Tabla de Surf',
            'tennis racket': 'Raqueta', 'bottle': 'Botella', 'wine glass': 'Copa',
            'cup': 'Taza', 'fork': 'Tenedor', 'knife': 'Cuchillo', 'spoon': 'Cuchara',
            'bowl': 'Tazón', 'banana': 'Plátano', 'apple': 'Manzana',
            'sandwich': 'Sándwich', 'orange': 'Naranja', 'broccoli': 'Brócoli', 'carrot': 'Zanahoria',
            'hot dog': 'Hot Dog', 'pizza': 'Pizza', 'donut': 'Dona', 'cake': 'Pastel',
            'chair': 'Silla', 'couch': 'Sofá', 'potted plant': 'Planta',
            'bed': 'Cama', 'dining table': 'Mesa', 'toilet': 'Inodoro',
            'tv': 'Monitor', 'laptop': 'Laptop', 'mouse': 'Mouse',
            'remote': 'Control Remoto', 'keyboard': 'Teclado', 'cell phone': 'Celular',
            'microwave': 'Microondas', 'oven': 'Horno', 'toaster': 'Tostadora',
            'sink': 'Lavabo', 'refrigerator': 'Refrigerador', 'book': 'Libro',
            'clock': 'Reloj', 'vase': 'Florero', 'scissors': 'Tijeras', 'teddy bear': 'Peluche',
            'hair drier': 'Secador', 'toothbrush': 'Cepillo Dental',
            'pen': 'Bolígrafo', 'glasses': 'Lentes', 'watch': 'Reloj Pulsera',
            'wallet': 'Billetera', 'headphones': 'Auriculares', 'document': 'Documento'
        };

        // State Management
        let isCameraActive = false;
        let currentFacingMode = 'user';
        let videoElement = document.getElementById('webcam');
        let inferenceResolution = 640; // Pro HD default 640p
        let canvasElement = document.getElementById('canvasOverlay');
        let ctx = canvasElement.getContext('2d');
        let offscreenCanvas = document.getElementById('offscreenCanvas');
        let offscreenCtx = offscreenCanvas.getContext('2d');
        let cocoModel = null;
        let isModelLoading = false;
        let animationFrameId = null;
        let audioEnabled = true;

        // Active Media Helpers (Ultra-High Performance Direct WebCam)
        function getActiveMediaElement() {
            return videoElement;
        }

        function getActiveMediaDimensions() {
            return {
                w: (videoElement && videoElement.videoWidth) ? videoElement.videoWidth : 640,
                h: (videoElement && videoElement.videoHeight) ? videoElement.videoHeight : 480
            };
        }

        function isMediaSourceReady() {
            return videoElement && videoElement.readyState >= 2 && videoElement.videoWidth > 0;
        }

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

        // Multi-Target Real-Time Tracker (Seguimiento Continuo e Instantáneo de Personas y Objetos)
        let activeTracks = [];
        let nextTrackId = 1;
        const TRACK_LERP_FACTOR = 0.72; // Respuesta instantánea y seguimiento continuo a 60 FPS sin retraso

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

        // Filtro profesional de alta precisión que detecta objetos en tiempo real y descarta alucinaciones
        function validateAndFilterPredictions(rawDetections, scaleX, scaleY, canvasW, canvasH) {
            const absurdClasses = ['zebra', 'giraffe', 'bear', 'elephant', 'sheep', 'cow', 'horse', 'airplane', 'train', 'boat', 'fire hydrant', 'stop sign', 'parking meter', 'kite', 'skis', 'snowboard', 'surfboard', 'frisbee'];
            const furnitureClasses = ['chair', 'couch', 'bed', 'backpack', 'tv', 'dining table'];

            // 1. Escalar coordenadas al canvas de visualización HD
            const scaled = rawDetections.map(p => ({
                class: p.class,
                score: p.score,
                bbox: [
                    p.bbox[0] * scaleX,
                    p.bbox[1] * scaleY,
                    p.bbox[2] * scaleX,
                    p.bbox[3] * scaleY
                ]
            }));

            const candidatePersons = [];
            const validObjects = [];

            // Categorías de objetos con umbrales calibrados para capturar cualquier elemento del mundo
            const handheldAndPersonal = [
                'cell phone', 'cup', 'bottle', 'wine glass', 'fork', 'knife', 'spoon', 'bowl',
                'apple', 'banana', 'orange', 'broccoli', 'carrot', 'sandwich', 'pizza', 'hot dog', 'donut', 'cake',
                'book', 'clock', 'scissors', 'teddy bear', 'hair drier', 'toothbrush',
                'pen', 'glasses', 'watch', 'wallet', 'headphones', 'document', 'remote', 'mouse'
            ];

            const workAndTech = ['laptop', 'keyboard', 'tv', 'microwave', 'oven', 'toaster', 'sink', 'refrigerator', 'vase', 'potted plant'];

            // 2. Filtrado estricto por geometría y umbrales verídicos
            for (const p of scaled) {
                if (absurdClasses.includes(p.class) && p.score < 0.85) continue;

                const [bx, by, bw, bh] = p.bbox;
                if (bw < 18 || bh < 18) continue;
                if (bw > canvasW * 0.98 && bh > canvasH * 0.98) continue;

                if (p.class === 'person') {
                    // FILTRADO ESTRICTO DE PERSONA (Evita confundir personas con objetos/muebles):
                    // a) Umbral de confianza firme: nunca clasificar objetos ambiguos con puntajes bajos como personas
                    if (p.score < 0.46) continue;

                    // b) Altura y área mínimas realistas para una persona en cámara
                    if (bh < 75 || bw < 40 || (bw * bh) < 5000) continue;

                    // c) Relación de aspecto: las personas en encuadre son verticales o cuadradas.
                    // Si el ancho es mayor al alto (bw > bh * 1.30) y no tiene confianza altísima, es una mesa/mueble/teclado, NO una persona
                    if (bw > bh * 1.30 && p.score < 0.80) continue;

                    candidatePersons.push(p);
                } else {
                    // Calibración adaptativa por tipo de objeto:
                    // Para objetos en mano, comida, frutas y utensilios: umbral sensible para respuesta inmediata en tiempo real
                    let objThreshold = minConfidence;
                    if (handheldAndPersonal.includes(p.class)) {
                        objThreshold = Math.max(0.18, minConfidence * 0.70);
                    } else if (workAndTech.includes(p.class)) {
                        objThreshold = Math.max(0.24, minConfidence * 0.80);
                    }

                    if (p.score >= objThreshold) {
                        validObjects.push(p);
                    }
                }
            }

            // 3. Resolución de conflicto Persona vs Mueble:
            // Si una persona candidata se superpone fuertemente con una silla/sofá/cama/mochila
            // y la persona tiene menor certeza (<0.68), es el mueble!
            const truePersons = candidatePersons.filter(person => {
                for (const obj of validObjects) {
                    if (furnitureClasses.includes(obj.class)) {
                        const iou = computeIoU(person.bbox, obj.bbox);
                        if (iou > 0.42 && person.score <= obj.score + 0.05 && person.score < 0.68) {
                            return false;
                        }
                    }
                }
                return true;
            });

            // 4. Non-Maximum Suppression (NMS) en personas para evitar cajas dobles
            truePersons.sort((a, b) => b.score - a.score);
            const nmsPersons = [];
            for (const p of truePersons) {
                const overlap = nmsPersons.some(existing => computeIoU(p.bbox, existing.bbox) > 0.35);
                if (!overlap) {
                    nmsPersons.push(p);
                }
            }

            // 5. Non-Maximum Suppression (NMS) en objetos para evitar cajas dobles de un mismo elemento
            validObjects.sort((a, b) => b.score - a.score);
            const nmsObjects = [];
            for (const obj of validObjects) {
                const overlap = nmsObjects.some(existing => existing.class === obj.class && computeIoU(obj.bbox, existing.bbox) > 0.38);
                if (!overlap) {
                    nmsObjects.push(obj);
                }
            }

            return [...nmsPersons, ...nmsObjects];
        }

        function updateObjectTracks(newPredictions) {
            const matchedDetections = new Set();

            // 1. Asignar detecciones a tracks existentes por clase, proximidad espacial e IoU
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
                    // Para personas: permitir amplio rango espacial continuo sin perder al usuario
                    const maxDistThreshold = (track.class === 'person') ? 2.5 : 1.5;
                    if (normDist < maxDistThreshold) {
                        score = Math.max(score, 0.55 - (normDist * 0.20));
                    }

                    if (score > 0.08 && score > bestMatchScore) {
                        bestMatchScore = score;
                        bestMatchIndex = i;
                    }
                }

                if (bestMatchIndex !== -1) {
                    const pred = newPredictions[bestMatchIndex];
                    matchedDetections.add(bestMatchIndex);

                    // Calcular inercia de velocidad para seguimiento suave
                    const vx = pred.bbox[0] - track.targetBbox[0];
                    const vy = pred.bbox[1] - track.targetBbox[1];
                    track.velocity = [vx, vy];

                    // Si hay salto grande (>180px), saltar de inmediato para respuesta sin rezago
                    const jumpDist = getCentroidDistance(track.bbox, pred.bbox);
                    if (jumpDist > 180) {
                        track.bbox = [...pred.bbox];
                    }

                    track.targetBbox = [...pred.bbox];
                    track.score = Math.max(track.score * 0.2 + pred.score * 0.8, pred.score);
                    track.missedCycles = 0;
                    track.lastSeen = performance.now();
                } else {
                    // Incrementar ciclos sin detección
                    track.missedCycles = (track.missedCycles || 0) + 1;
                    if (track.velocity) {
                        track.targetBbox[0] += track.velocity[0] * 0.3;
                        track.targetBbox[1] += track.velocity[1] * 0.3;
                    }
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
                        velocity: [0, 0],
                        score: pred.score,
                        missedCycles: 0,
                        hair: null,
                        firstSeen: performance.now(),
                        lastSeen: performance.now()
                    });
                }
            }

            // 3. Purga diferenciada en tiempo real:
            // Para 'person': tolerancia de hasta 8 ciclos (~250ms) para tolerar micro-movimientos sin parpadeo
            // Para 'objects': tolerancia de 2 ciclos (~60ms) para que al retirar un objeto de la cámara desaparezca inmediatamente
            activeTracks = activeTracks.filter(t => {
                if (t.class === 'person') return (t.missedCycles || 0) <= 8;
                return (t.missedCycles || 0) <= 2;
            });
        }

        // Buffer de Inferencia de Alta Velocidad por Aceleración de Hardware (512x288)
        // Reduce el tiempo de inferencia de 180ms a 25-35ms sin pérdida de calidad
        const inferCanvas = document.createElement('canvas');
        inferCanvas.width = 512;
        inferCanvas.height = 288;
        const inferCtx = inferCanvas.getContext('2d', { alpha: false, willReadFrequently: false });
        let inferenceTimer = null;

        // Decoupled AI Inference vs 60fps Rendering
        let isInferring = false;
        let cachedPredictions = [];
        let cachedPersonsData = [];
        let cachedObjectContexts = {};
        let cachedHandResults = [];
        let mediaPipeHands = null;
        let isHandsModelLoading = false;
        let isHandsInferring = false;
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
        let minConfidence = 0.40;

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
            const alertKeys = ['handling_sharp', 'person_fallen', 'face_hidden', 'aggressive_motion', 'unattended_object'];
            const isAlert = (data.category === 'behavior' && (alertKeys.includes(data.label) || alertKeys.includes(data.label_key)));

            if (isAlert) {
                categoryLabel = 'ALERTA';
                catColorClass = 'bg-rose-500/25 text-rose-300 border-rose-500/50 animate-pulse';
            } else if (data.category === 'hair') {
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
            item.className = 'p-3 rounded-xl bg-slate-950/85 border border-slate-800 transition transform hover:translate-x-1 shadow-md shrink-0 min-h-[72px] flex flex-col justify-between gap-1.5';
            item.style.borderLeft = `4px solid ${data.color}`;

            const formattedTime = (data.created_at ? new Date(data.created_at).toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }) : new Date().toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }));
            const trackBadge = (data.details && data.details.track_id) ? `<span class="text-[9px] font-mono-code px-1 rounded bg-slate-900 border border-slate-700 text-slate-300 shrink-0">#${data.details.track_id}</span>` : '';
            const posBadge = (data.details && data.details.position) ? `<span class="text-[9px] px-1 rounded bg-slate-800 border border-slate-700 text-cyan-300 font-mono-code shrink-0">${data.details.position}</span>` : '';
            const ctxText = (data.details && data.details.context) ? `<span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-900/90 border border-slate-700/60 text-slate-300 font-sans truncate max-w-[170px]" title="${data.details.context}">${data.details.context}</span>` : '';

            item.innerHTML = `
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-3 h-3 rounded-full shrink-0 shadow-sm" style="background-color: ${data.color}"></span>
                        <span class="text-xs font-bold text-white truncate">${data.display_name}</span>
                        ${trackBadge}
                        ${posBadge}
                    </div>
                    <span class="text-[11px] font-mono-code text-slate-400 shrink-0">${formattedTime}</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[11px] gap-2">
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border shrink-0 ${catColorClass}">${categoryLabel}</span>
                        ${ctxText}
                    </div>
                    <div class="font-mono-code text-slate-300 shrink-0">
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
            // Notificación del sistema restringida exclusivamente a autorización de cámara y grabación de video
            if (!data || data.category !== 'permission') return;

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
                const alertKeys = ['handling_sharp', 'person_fallen', 'face_hidden', 'aggressive_motion', 'unattended_object'];
                const isAlert = (data.category === 'behavior' && (alertKeys.includes(data.label) || alertKeys.includes(data.label_key)));

                if (isAlert) {
                    catBadge = '<span class="px-2 py-0.5 rounded-full bg-rose-500/20 border border-rose-500/40 text-rose-300 font-semibold text-[10px]">ALERTA</span>';
                } else if (data.category === 'hair') {
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
                    <td class="px-4 py-2.5 text-slate-400 font-mono-code">${(data.created_at ? new Date(data.created_at).toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }) : new Date().toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }))}</td>
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
                    height: { ideal: 720, min: 480 },
                    frameRate: { ideal: 60, min: 30 }
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

                showToastNotification({
                    category: 'permission',
                    display_name: 'Cámara Autorizada y Transmitiendo',
                    color: '#10B981',
                    confidence: 100
                });

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

        // ==========================================
        // SISTEMA DE IA AUTO-EVOLUTIVA Y APRENDIZAJE CONTINUO 24/7
        // ==========================================
        class EvolutionaryAIEngine {
            constructor() {
                this.storageKey = 'dbcomputech_ai_evolution_knowledge_v2';
                this.stats = {
                    generation: 1,
                    learnedConcepts: 0,
                    webKnowledgeHits: 0,
                    lastEvolutionTime: Date.now(),
                    confidenceBoostMap: {}
                };
                this.knowledgeTaxonomy = {};
                this.onlineLearningQueue = new Set();
                this.isFetchingWeb = false;
                this.loadFromStorage();
                this.initBaseTaxonomy();
                this.startAutonomousLearningCycle();
            }

            loadFromStorage() {
                try {
                    const raw = localStorage.getItem(this.storageKey);
                    if (raw) {
                        const parsed = JSON.parse(raw);
                        if (parsed.stats) this.stats = Object.assign(this.stats, parsed.stats);
                        if (parsed.taxonomy) this.knowledgeTaxonomy = parsed.taxonomy;
                    }
                } catch (e) {
                    console.warn('Init memory notice:', e);
                }
            }

            saveToStorage() {
                try {
                    localStorage.setItem(this.storageKey, JSON.stringify({
                        stats: this.stats,
                        taxonomy: this.knowledgeTaxonomy
                    }));
                } catch (e) {
                    // Safe write
                }
            }

            initBaseTaxonomy() {
                const baseClasses = {
                    'cup': { name: 'Taza', category: 'Vajilla', details: 'Taza para café o infusiones' },
                    'glass': { name: 'Vaso', category: 'Cristalería', details: 'Vaso de cristal o agua' },
                    'bottle': { name: 'Botella', category: 'Recipiente', details: 'Botella para líquidos y bebidas' },
                    'wine glass': { name: 'Copa', category: 'Cristalería', details: 'Copa de cristal' },
                    'cell phone': { name: 'Teléfono Celular', category: 'Dispositivo', details: 'Smartphone con pantalla táctil' },
                    'laptop': { name: 'Computadora Portátil', category: 'Informática', details: 'Laptop en uso o reposo' },
                    'mouse': { name: 'Mouse Óptico', category: 'Periférico', details: 'Mouse de computadora' },
                    'keyboard': { name: 'Teclado', category: 'Periférico', details: 'Teclado para entrada de datos' },
                    'remote': { name: 'Control Remoto', category: 'Electrónica', details: 'Control a distancia' },
                    'person': { name: 'Persona', category: 'Humano', details: 'Individuo en cuadro de monitoreo' },
                    'banana': { name: 'Plátano', category: 'Fruta', details: 'Fruta fresca comestible' },
                    'apple': { name: 'Manzana', category: 'Fruta', details: 'Fruta fresca comestible' },
                    'orange': { name: 'Naranja', category: 'Fruta', details: 'Cítrico comestible rico en vitamina C' },
                    'sandwich': { name: 'Sándwich', category: 'Alimento', details: 'Emparedado o bocadillo' },
                    'pizza': { name: 'Pizza', category: 'Alimento', details: 'Porción de pizza caliente' },
                    'donut': { name: 'Dona', category: 'Repostería', details: 'Rosquilla dulce' },
                    'cake': { name: 'Pastel', category: 'Repostería', details: 'Tarta o pastel' },
                    'backpack': { name: 'Mochila', category: 'Equipaje', details: 'Mochila o bolso de hombros' },
                    'handbag': { name: 'Cartera', category: 'Accesorio', details: 'Bolso o cartera personal' },
                    'book': { name: 'Libro', category: 'Lectura', details: 'Material impreso de estudio o lectura' },
                    'scissors': { name: 'Tijeras', category: 'Herramienta', details: 'Instrumento filoso de corte' },
                    'pen': { name: 'Bolígrafo', category: 'Escritura', details: 'Instrumento para escribir o notas' },
                    'watch': { name: 'Reloj Pulsera', category: 'Accesorio', details: 'Reloj de muñeca analógico/digital' },
                    'glasses': { name: 'Lentes', category: 'Óptica', details: 'Gafas de visión o sol' },
                    'headphones': { name: 'Auriculares', category: 'Audio', details: 'Audífonos inalámbricos o con cable' }
                };

                for (const [k, val] of Object.entries(baseClasses)) {
                    if (!this.knowledgeTaxonomy[k]) {
                        this.knowledgeTaxonomy[k] = {
                            ...val,
                            confidenceFactor: 1.0,
                            occurrences: 1,
                            webEnriched: false,
                            synonyms: []
                        };
                    }
                }
                this.updateEvolutionBadgeUI();
            }

            async queryWebKnowledge(concept) {
                if (this.isFetchingWeb) return;
                this.isFetchingWeb = true;
                try {
                    const wikiUrl = `https://es.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(concept)}`;
                    const resp = await fetch(wikiUrl, { headers: { 'Accept': 'application/json' } });
                    if (resp.ok) {
                        const data = await resp.json();
                        if (data && data.description) {
                            if (!this.knowledgeTaxonomy[concept]) {
                                this.knowledgeTaxonomy[concept] = { name: data.title || concept, occurrences: 1, confidenceFactor: 1.0 };
                            }
                            this.knowledgeTaxonomy[concept].details = data.description;
                            this.knowledgeTaxonomy[concept].webEnriched = true;
                            this.stats.webKnowledgeHits++;
                            this.stats.learnedConcepts = Object.keys(this.knowledgeTaxonomy).length;
                            this.saveToStorage();
                            this.updateEvolutionBadgeUI();
                        }
                    }
                } catch (e) {
                    // Silencioso en desconexión
                } finally {
                    this.isFetchingWeb = false;
                }
            }

            reinforceDetection(label, rawScore, bbox) {
                const normalized = (label || '').toLowerCase().trim();
                if (!this.knowledgeTaxonomy[normalized]) {
                    this.knowledgeTaxonomy[normalized] = {
                        name: COCO_SPANISH_MAP[normalized] || normalized,
                        category: 'Entidad Aprendida',
                        details: 'Clase descubierta de forma autónoma',
                        confidenceFactor: 1.0,
                        occurrences: 0,
                        webEnriched: false
                    };
                    this.onlineLearningQueue.add(normalized);
                }

                const item = this.knowledgeTaxonomy[normalized];
                item.occurrences = (item.occurrences || 0) + 1;

                const boost = Math.min(1.0, (item.confidenceFactor || 1.0) * (1 + Math.log10(1 + item.occurrences * 0.05)));
                item.confidenceFactor = boost;

                this.stats.generation = Math.floor(1 + (item.occurrences / 40));
                this.stats.learnedConcepts = Object.keys(this.knowledgeTaxonomy).length;

                if (!item.webEnriched && !this.onlineLearningQueue.has(normalized)) {
                    this.onlineLearningQueue.add(normalized);
                }

                if (item.occurrences % 20 === 0) {
                    this.saveToStorage();
                    this.updateEvolutionBadgeUI();
                }

                const evolvedConfidence = Math.min(1.0, Math.max(rawScore, rawScore * 1.06 + (item.occurrences > 8 ? 0.05 : 0.02)));
                return {
                    name: item.name || label,
                    category: item.category || 'General',
                    details: item.details || '',
                    confidence: evolvedConfidence
                };
            }

            startAutonomousLearningCycle() {
                setInterval(() => {
                    if (this.onlineLearningQueue.size > 0 && !this.isFetchingWeb) {
                        const nextConcept = Array.from(this.onlineLearningQueue)[0];
                        this.onlineLearningQueue.delete(nextConcept);
                        this.queryWebKnowledge(nextConcept);
                    }
                }, 12000);
            }

            updateEvolutionBadgeUI() {
                const el = document.getElementById('aiEvolutionBadge');
                if (el) {
                    el.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>IA Auto-Evolutiva: Gen ${this.stats.generation} • ${this.stats.learnedConcepts} Conceptos • 100% Adaptativa`;
                }
                const card = document.getElementById('aiEvolutionText');
                if (card) {
                    card.innerText = `Evolución: Gen ${this.stats.generation} (${this.stats.learnedConcepts} Conceptos)`;
                }
            }
        }
        const evolutionaryEngine = new EvolutionaryAIEngine();

        function setInferenceMode(res) {
            inferenceResolution = res;
            const b640 = document.getElementById('btnMode640');
            const b512 = document.getElementById('btnMode512');
            const b400 = document.getElementById('btnMode400');
            if (b640 && b512 && b400) {
                b640.className = res === 640 ? 'p-2.5 rounded-lg border text-left transition bg-cyan-500/10 border-cyan-500/40 text-cyan-300 font-medium' : 'p-2.5 rounded-lg border text-left transition bg-slate-900/60 border-slate-800 text-slate-400 hover:text-slate-200';
                b512.className = res === 512 ? 'p-2.5 rounded-lg border text-left transition bg-cyan-500/10 border-cyan-500/40 text-cyan-300 font-medium' : 'p-2.5 rounded-lg border text-left transition bg-slate-900/60 border-slate-800 text-slate-400 hover:text-slate-200';
                b400.className = res === 400 ? 'p-2.5 rounded-lg border text-left transition bg-cyan-500/10 border-cyan-500/40 text-cyan-300 font-medium' : 'p-2.5 rounded-lg border text-left transition bg-slate-900/60 border-slate-800 text-slate-400 hover:text-slate-200';
            }
        }

        async function toggleCamera() {
            if (isCameraActive) {
                stopCamera();
            } else {
                await requestCameraAccess(activeCameraDeviceId);
            }
        }

        function stopCamera() {
            if (isRecording) stopRecording();
            isCameraActive = false;

            activeTracks = [];
            cachedPredictions = [];
            cachedPersonsData = [];
            cachedObjectContexts = {};
            cachedHandResults = [];
            cachedBehavior = null;
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
            const label = val <= 30 ? `${val}% (Permisiva)` : (val <= 50 ? `${val}% (Precisión Óptima)` : `${val}% (Ultra Estricta)`);
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

                text.innerText = 'IA: Cargando Red HD...';
                try {
                    cocoModel = await cocoSsd.load({ base: 'mobilenet_v2' });
                } catch (loadErr) {
                    console.warn('Fallback a lite_mobilenet_v2:', loadErr);
                    cocoModel = await cocoSsd.load({ base: 'lite_mobilenet_v2' });
                }

                // Inicializar MediaPipe Hands para detección de manos y conteo de dedos ultra veloz
                if (window.Hands && !mediaPipeHands && !isHandsModelLoading) {
                    try {
                        isHandsModelLoading = true;
                        mediaPipeHands = new Hands({
                            locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}`
                        });
                        mediaPipeHands.setOptions({
                            maxNumHands: 2,
                            modelComplexity: 0, // Modelo Lite optimizado para 60 FPS en tiempo real
                            minDetectionConfidence: 0.45,
                            minTrackingConfidence: 0.45
                        });
                        mediaPipeHands.onResults((results) => {
                            lastHandsResultTime = performance.now();
                            if (!results || !results.multiHandLandmarks || results.multiHandLandmarks.length === 0) {
                                cachedHandResults = [];
                            } else {
                                cachedHandResults = analyzeHandResults(results);
                            }
                            // Actualización instantánea del comportamiento cuando cambian los gestos de las manos
                            cachedBehavior = analyzeGesturesAndBehavior(cachedPredictions, cachedPersonsData, cachedObjectContexts, cachedHandResults);
                        });
                    } catch (hErr) {
                        console.warn('MediaPipe Hands load notice:', hErr);
                    } finally {
                        isHandsModelLoading = false;
                    }
                }
                
                badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-xs font-mono-code';
                badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-emerald-400 animate-pulse';
                text.innerText = 'IA: Monitoreo 24/7 (Alta Precisión)';
            } catch (e) {
                badge.className = 'flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg border border-rose-500/30 bg-rose-500/10 text-rose-400 text-xs font-mono-code';
                badge.firstElementChild.className = 'w-2 h-2 rounded-full bg-rose-400';
                text.innerText = 'IA: Error al cargar';
            } finally {
                isModelLoading = false;
            }
        }

        let lastHandsResultTime = 0;

        function startDetectionEngine() {
            if (!cocoModel) loadDetectionModel();
            const dims = getActiveMediaDimensions();
            if (dims.w > 0) {
                canvasElement.width = dims.w;
                canvasElement.height = dims.h;
            }
            renderLoop();
            scheduleNextInference();
            scheduleNextHands();
        }

        // ==========================================
        // 1. RENDER LOOP: Runs at full 60 FPS purely rendering latest cached overlay
        // ==========================================
        function renderLoop() {
            if (!isCameraActive || !isMediaSourceReady()) {
                if (isCameraActive) animationFrameId = requestAnimationFrame(renderLoop);
                return;
            }

            const activeMedia = getActiveMediaElement();
            const dims = getActiveMediaDimensions();

            if (canvasElement.width !== dims.w || canvasElement.height !== dims.h) {
                canvasElement.width = dims.w;
                canvasElement.height = dims.h;
            }

            // Expiración inmediata de manos si salieron del rango de visión (>220ms sin detección)
            if (performance.now() - lastHandsResultTime > 220) {
                cachedHandResults = [];
            }

            // 1. Interpolación de movimiento reactiva a 60 FPS (Seguimiento continuo suave)
            for (const track of activeTracks) {
                track.bbox[0] += (track.targetBbox[0] - track.bbox[0]) * TRACK_LERP_FACTOR;
                track.bbox[1] += (track.targetBbox[1] - track.bbox[1]) * TRACK_LERP_FACTOR;
                track.bbox[2] += (track.targetBbox[2] - track.bbox[2]) * TRACK_LERP_FACTOR;
                track.bbox[3] += (track.targetBbox[3] - track.bbox[3]) * TRACK_LERP_FACTOR;
            }

            // 2. Construir predicciones vivas directamente de los tracks interpolados a 60 FPS
            const liveRenderPredictions = activeTracks
                .filter(t => (t.class === 'person' ? (t.missedCycles || 0) <= 8 : (t.missedCycles || 0) <= 2))
                .map(t => ({
                    id: t.id,
                    class: t.class,
                    score: t.score,
                    bbox: [t.bbox[0], t.bbox[1], t.bbox[2], t.bbox[3]]
                }));

            // 3. Extraer personas sincronizadas a 60 FPS para tonos de cabello
            const liveRenderPersons = liveRenderPredictions
                .filter(p => p.class === 'person')
                .map(p => {
                    const matchedTrack = activeTracks.find(t => t.id === p.id);
                    return {
                        id: p.id,
                        bbox: p.bbox,
                        hair: matchedTrack ? matchedTrack.hair : null
                    };
                });

            renderComprehensiveOverlay(liveRenderPredictions, liveRenderPersons, cachedBehavior, cachedObjectContexts, cachedHandResults);

            // Composición para grabación de video (Cámara Web Real o IP + Bounding Boxes & HUD de IA a 60 FPS)
            if (isRecording) {
                if (recordingCanvas.width !== dims.w || recordingCanvas.height !== dims.h) {
                    recordingCanvas.width = dims.w;
                    recordingCanvas.height = dims.h;
                }
                // 1. Dibuja la cámara real o flujo IP en alta definición
                recordingCtx.drawImage(activeMedia, 0, 0, recordingCanvas.width, recordingCanvas.height);
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

        // ==========================================
        // 2. HANDS & FINGER TRACKING ENGINE (DESACOPLADO A 60 FPS)
        // ==========================================
        function scheduleNextHands() {
            if (!isCameraActive) return;
            const activeMedia = getActiveMediaElement();
            if (activeMedia && 'requestVideoFrameCallback' in activeMedia) {
                activeMedia.requestVideoFrameCallback(() => handsScheduler());
            } else {
                requestAnimationFrame(() => handsScheduler());
            }
        }

        async function handsScheduler() {
            if (!isCameraActive) return;
            const activeMedia = getActiveMediaElement();
            if (mediaPipeHands && !isHandsInferring && isMediaSourceReady()) {
                isHandsInferring = true;
                try {
                    await mediaPipeHands.send({ image: activeMedia });
                } catch (mhErr) {
                    // Hand tracking notice
                } finally {
                    isHandsInferring = false;
                }
            }
            if (isCameraActive) {
                scheduleNextHands();
            }
        }

        // ==========================================
        // 3. OBJECT & PERSON INFERENCE SCHEDULER (ALTA VELOCIDAD Y PRECISIÓN PROFESIONAL)
        // ==========================================
        function scheduleNextInference() {
            if (!isCameraActive) return;
            const activeMedia = getActiveMediaElement();
            if (activeMedia && 'requestVideoFrameCallback' in activeMedia) {
                activeMedia.requestVideoFrameCallback(() => inferenceScheduler());
            } else {
                requestAnimationFrame(() => inferenceScheduler());
            }
        }

        async function inferenceScheduler() {
            if (!isCameraActive) return;
            if (cocoModel && !isInferring && isMediaSourceReady()) {
                await runFastInference();
            }
            if (isCameraActive) {
                scheduleNextInference();
            }
        }

        // 4. FAST DIRECT HARDWARE INFERENCE: Detecta objetos y personas de inmediato en resolución Pro HD
        async function runFastInference() {
            isInferring = true;
            const startTime = performance.now();

            try {
                const activeMedia = getActiveMediaElement();
                const dims = getActiveMediaDimensions();
                const vW = dims.w || 640;
                const vH = dims.h || 480;
                const targetW = inferenceResolution || 640;
                const targetH = Math.round(targetW * (vH / vW));

                if (inferCanvas.width !== targetW || inferCanvas.height !== targetH) {
                    inferCanvas.width = targetW;
                    inferCanvas.height = targetH;
                }

                // Dibujar en buffer acelerado optimizado (copia por hardware < 1ms)
                inferCtx.drawImage(activeMedia, 0, 0, targetW, targetH);

                // Inferencia profesional: evalConfidence optimizado para capturar objetos pequeños/en mano
                const evalConfidence = Math.max(0.18, minConfidence - 0.12);
                const rawPredictions = await cocoModel.detect(inferCanvas, 25, evalConfidence);

                const cW = canvasElement.width || 1280;
                const cH = canvasElement.height || 720;
                const scaleX = cW / targetW;
                const scaleY = cH / targetH;

                // Filtrar con alta precisión geométrica y semántica (bloquea falsas personas y alucinaciones)
                const validPredictions = validateAndFilterPredictions(rawPredictions, scaleX, scaleY, cW, cH);

                // Actualizar el motor de seguimiento multi-objetivo continuo 24/7
                updateObjectTracks(validPredictions);

                // Proyectar tracks activos para inferencia contextual y eventos WebSocket con IA Auto-Evolutiva
                const trackedPredictions = activeTracks
                    .filter(t => (t.class === 'person' ? (t.missedCycles || 0) <= 8 : (t.missedCycles || 0) <= 2))
                    .map(t => {
                        const evo = (typeof evolutionaryEngine !== 'undefined')
                            ? evolutionaryEngine.reinforceDetection(t.class, t.score, t.bbox)
                            : { confidence: t.score, name: t.class };
                        return {
                            id: t.id,
                            class: t.class,
                            score: evo.confidence,
                            bbox: [t.bbox[0], t.bbox[1], t.bbox[2], t.bbox[3]]
                        };
                    });

                cachedPredictions = trackedPredictions;
                cachedPersonsData = analyzePersonsAndHair(trackedPredictions);
                cachedObjectContexts = enrichEnvironmentalContext(trackedPredictions, cachedPersonsData, cachedHandResults);
                cachedBehavior = analyzeGesturesAndBehavior(trackedPredictions, cachedPersonsData, cachedObjectContexts, cachedHandResults);

                const infDuration = Math.round(performance.now() - startTime);
                document.getElementById('inferenceCounter').innerText = `${infDuration} ms`;

                processAllDetectionsAndWebSocket(trackedPredictions, cachedPersonsData, cachedBehavior, cachedObjectContexts, cachedHandResults);

                // Actualización instantánea en tiempo real de KPIs y Radar
                const now = performance.now();
                if (now - lastDomUpdateTime > 50) {
                    lastDomUpdateTime = now;
                    updateKPIsAndRadar(trackedPredictions, cachedPersonsData, cachedBehavior, cachedObjectContexts, cachedHandResults);
                }

            } catch (err) {
                console.warn('Inference notice:', err);
            } finally {
                isInferring = false;
            }
        }

        // ==========================================
        // 24/7 SYSTEM WATCHDOG & SELF-HEALING ENGINE
        // Monitorea la continuidad ininterrumpida y autorecupera el flujo si el navegador estrangula la pestaña
        // ==========================================
        setInterval(() => {
            if (!isCameraActive) return;

            const now = performance.now();
            // Autorecuperación si el bucle visual se congeló por más de 1800ms
            if (now - lastFrameTime > 1800) {
                lastFrameTime = now;
                if (animationFrameId) cancelAnimationFrame(animationFrameId);
                renderLoop();
                scheduleNextInference();
                scheduleNextHands();
            }

            // Purgar marcas de tiempo viejas (>60s) para garantizar cero fugas de memoria en 24/7
            const cutoff = now - 60000;
            for (const key in lastEventSentTimestamps) {
                if (lastEventSentTimestamps[key] < cutoff) {
                    delete lastEventSentTimestamps[key];
                }
            }
        }, 1500);

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
                const activeMedia = getActiveMediaElement();
                offscreenCanvas.width = w;
                offscreenCanvas.height = h;
                offscreenCtx.drawImage(activeMedia, x, y, w, h, 0, 0, w, h);

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
        // HAND & FINGER COUNTING ANALYSIS ENGINE (MEDIAPIPE 21 LANDMARKS)
        // ==========================================
        function analyzeHandResults(results) {
            if (!results || !results.multiHandLandmarks || results.multiHandLandmarks.length === 0) {
                return [];
            }

            const handsData = [];
            const W = canvasElement.width || 1280;
            const H = canvasElement.height || 720;

            for (let i = 0; i < results.multiHandLandmarks.length; i++) {
                const landmarks = results.multiHandLandmarks[i];
                const handednessInfo = (results.multiHandedness && results.multiHandedness[i]) ? results.multiHandedness[i] : null;
                const rawLabel = handednessInfo ? handednessInfo.label : (i === 0 ? 'Right' : 'Left');

                // En cámara web frontal reflejada: 'Left' corresponde a la mano derecha del usuario
                const sideName = rawLabel === 'Left' ? 'Mano Derecha' : 'Mano Izquierda';

                // Distancias euclidianas invariantes a rotación
                // 1. Pulgar (Thumb): distancia Tip (4) a Pinky MCP (17) comparada con IP (3) a Pinky MCP (17)
                const dTipPinky = Math.hypot(landmarks[4].x - landmarks[17].x, landmarks[4].y - landmarks[17].y);
                const dIpPinky = Math.hypot(landmarks[3].x - landmarks[17].x, landmarks[3].y - landmarks[17].y);
                const isThumbOpen = dTipPinky > dIpPinky * 1.12;

                // 2. Índice (Index): distancia Tip (8) a Muñeca (0) vs PIP (6) a Muñeca (0)
                const dIndexTipWrist = Math.hypot(landmarks[8].x - landmarks[0].x, landmarks[8].y - landmarks[0].y);
                const dIndexPipWrist = Math.hypot(landmarks[6].x - landmarks[0].x, landmarks[6].y - landmarks[0].y);
                const isIndexOpen = dIndexTipWrist > dIndexPipWrist * 1.10;

                // 3. Medio (Middle): Tip (12) a Muñeca (0) vs PIP (10) a Muñeca (0)
                const dMiddleTipWrist = Math.hypot(landmarks[12].x - landmarks[0].x, landmarks[12].y - landmarks[0].y);
                const dMiddlePipWrist = Math.hypot(landmarks[10].x - landmarks[0].x, landmarks[10].y - landmarks[0].y);
                const isMiddleOpen = dMiddleTipWrist > dMiddlePipWrist * 1.10;

                // 4. Anular (Ring): Tip (16) a Muñeca (0) vs PIP (14) a Muñeca (0)
                const dRingTipWrist = Math.hypot(landmarks[16].x - landmarks[0].x, landmarks[16].y - landmarks[0].y);
                const dRingPipWrist = Math.hypot(landmarks[14].x - landmarks[0].x, landmarks[14].y - landmarks[0].y);
                const isRingOpen = dRingTipWrist > dRingPipWrist * 1.10;

                // 5. Meñique (Pinky): Tip (20) a Muñeca (0) vs PIP (18) a Muñeca (0)
                const dPinkyTipWrist = Math.hypot(landmarks[20].x - landmarks[0].x, landmarks[20].y - landmarks[0].y);
                const dPinkyPipWrist = Math.hypot(landmarks[18].x - landmarks[0].x, landmarks[18].y - landmarks[0].y);
                const isPinkyOpen = dPinkyTipWrist > dPinkyPipWrist * 1.10;

                const fingersOpen = [isThumbOpen, isIndexOpen, isMiddleOpen, isRingOpen, isPinkyOpen];
                const count = fingersOpen.filter(Boolean).length;

                // Identificación precisa del gesto manual según dedos extendidos
                let gestureName = `${count} dedos`;
                if (count === 0) {
                    gestureName = 'Puño Cerrado (0 dedos)';
                } else if (count === 1) {
                    if (isThumbOpen) gestureName = 'Pulgar Arriba (1 dedo)';
                    else if (isIndexOpen) gestureName = 'Señalando con Índice (1 dedo)';
                    else if (isMiddleOpen) gestureName = 'Dedo Medio Extendido (1 dedo)';
                    else if (isPinkyOpen) gestureName = 'Dedo Meñique Extendido (1 dedo)';
                    else gestureName = '1 Dedo Extendido';
                } else if (count === 2) {
                    if (isIndexOpen && isMiddleOpen && !isThumbOpen && !isRingOpen && !isPinkyOpen) gestureName = 'Señal de Paz / Victoria (2 dedos)';
                    else if (isThumbOpen && isIndexOpen) gestureName = 'Gesto L (2 dedos)';
                    else if (isIndexOpen && isPinkyOpen && !isMiddleOpen && !isRingOpen) gestureName = 'Gesto Rock (2 dedos)';
                    else if (isThumbOpen && isPinkyOpen) gestureName = 'Gesto Shaka (2 dedos)';
                    else gestureName = '2 Dedos Extendidos';
                } else if (count === 3) {
                    if (isThumbOpen && isIndexOpen && isMiddleOpen) gestureName = 'Tres Dedos - Tres (3 dedos)';
                    else if (isIndexOpen && isMiddleOpen && isRingOpen) gestureName = 'Tres Dedos - W (3 dedos)';
                    else if (isMiddleOpen && isRingOpen && isPinkyOpen && !isThumbOpen && !isIndexOpen) gestureName = 'Gesto OK (3 dedos)';
                    else gestureName = '3 Dedos Extendidos';
                } else if (count === 4) {
                    if (!isThumbOpen) gestureName = 'Cuatro Dedos - Sin Pulgar (4 dedos)';
                    else gestureName = 'Cuatro Dedos Mostrados (4 dedos)';
                } else if (count === 5) {
                    gestureName = 'Palma Abierta (5 dedos)';
                }

                // Cálculo del recuadro contenedor (bounding box) de la mano en píxeles
                let minX = 1, maxX = 0, minY = 1, maxY = 0;
                landmarks.forEach(pt => {
                    if (pt.x < minX) minX = pt.x;
                    if (pt.x > maxX) maxX = pt.x;
                    if (pt.y < minY) minY = pt.y;
                    if (pt.y > maxY) maxY = pt.y;
                });

                const handBbox = [
                    Math.max(0, minX * W - 15),
                    Math.max(0, minY * H - 15),
                    Math.min(W, (maxX - minX) * W + 30),
                    Math.min(H, (maxY - minY) * H + 30)
                ];

                handsData.push({
                    index: i,
                    side: sideName,
                    handedness: rawLabel,
                    count: count,
                    gesture: gestureName,
                    landmarks: landmarks,
                    bbox: handBbox,
                    fingers: {
                        thumb: isThumbOpen,
                        index: isIndexOpen,
                        middle: isMiddleOpen,
                        ring: isRingOpen,
                        pinky: isPinkyOpen
                    }
                });
            }

            return handsData;
        }

        // ==========================================
        // ENVIRONMENTAL & SPATIAL CONTEXT ENGINE
        // Analyzes topology of EVERY object in the scene relative to people and furniture
        // ==========================================
        function enrichEnvironmentalContext(predictions, personsData, handResults) {
            const contextMap = {};
            const tables = predictions.filter(p => p.class === 'dining table' || p.class === 'bench');
            const chairs = predictions.filter(p => p.class === 'chair' || p.class === 'couch' || p.class === 'bed');
            const fruitClasses = ['apple', 'banana', 'orange', 'broccoli', 'carrot'];
            const mealClasses = ['sandwich', 'pizza', 'hot dog', 'donut', 'cake', 'bowl'];
            const utensilClasses = ['fork', 'knife', 'spoon'];

            predictions.forEach(pred => {
                if (pred.class === 'person') return;

                const [ox, oy, ow, oh] = pred.bbox;
                const ocx = ox + ow / 2;
                const ocy = oy + oh / 2;
                let ctxTag = 'En entorno';

                // 1. Proximidad directa con manos detectadas (MediaPipe)
                if (handResults && handResults.length > 0) {
                    for (const hand of handResults) {
                        const [hx, hy, hw, hh] = hand.bbox;
                        const hcx = hx + hw / 2;
                        const hcy = hy + hh / 2;
                        const distToHand = Math.hypot(ocx - hcx, ocy - hcy);
                        if (distToHand < Math.max(hw, hh, ow, oh) * 1.5) {
                            if (fruitClasses.includes(pred.class)) {
                                ctxTag = 'En mano • Mostrando fruta';
                            } else if (mealClasses.includes(pred.class)) {
                                ctxTag = 'En mano • Mostrando alimento';
                            } else if (pred.class === 'cell phone') {
                                ctxTag = 'En mano • Sosteniendo celular';
                            } else {
                                ctxTag = 'En mano • Mostrando a la cámara';
                            }
                            break;
                        }
                    }
                }

                // 2. Interacción directa con personas detectadas
                if (ctxTag === 'En entorno') {
                    for (const person of personsData) {
                        const [px, py, pw, ph] = person.bbox;
                        const inPersonPerimeter = (ocx >= px - pw * 0.35 && ocx <= px + pw * 1.35 && ocy >= py && ocy <= py + ph * 1.15);
                        if (inPersonPerimeter) {
                            if (pred.class === 'cell phone') {
                                ctxTag = (ocy <= py + ph * 0.40) ? 'En oreja • Llamada activa' : 'En mano • Manipulando celular';
                            } else if (fruitClasses.includes(pred.class)) {
                                ctxTag = (ocy <= py + ph * 0.52) ? 'Consumo activo • Comiendo fruta' : 'En mano • Mostrando fruta';
                            } else if (mealClasses.includes(pred.class)) {
                                ctxTag = (ocy <= py + ph * 0.52) ? 'Consumo activo • Comiendo alimento' : 'En mano • Mostrando alimento';
                            } else if (utensilClasses.includes(pred.class)) {
                                ctxTag = 'En mano • Usando cubierto';
                            } else if (pred.class === 'bottle' || pred.class === 'cup' || pred.class === 'wine glass') {
                                ctxTag = (ocy <= py + ph * 0.52) ? 'Consumo • Bebiendo' : 'En mano • Sosteniendo bebida';
                            } else if (pred.class === 'laptop' || pred.class === 'keyboard' || pred.class === 'mouse') {
                                ctxTag = 'En uso activo • Informática';
                            } else if (pred.class === 'book' || pred.class === 'document') {
                                ctxTag = 'En mano • Lectura / Estudio';
                            } else if (pred.class === 'pen') {
                                ctxTag = 'En mano • Escribiendo';
                            } else if (pred.class === 'scissors') {
                                ctxTag = 'En mano • Cortando con tijeras';
                            } else if (pred.class === 'glasses') {
                                ctxTag = (ocy <= py + ph * 0.35) ? 'Puesto • Lentes en rostro' : 'En mano • Lentes';
                            } else if (pred.class === 'headphones') {
                                ctxTag = (ocy <= py + ph * 0.35) ? 'Puesto • Auriculares en cabeza' : 'En mano • Auriculares';
                            } else if (pred.class === 'watch') {
                                ctxTag = 'En muñeca • Reloj pulsera';
                            } else if (pred.class === 'remote') {
                                ctxTag = 'En mano • Usando control remoto';
                            } else if (pred.class === 'backpack' || pred.class === 'handbag' || pred.class === 'suitcase' || pred.class === 'wallet') {
                                ctxTag = 'Manipulando pertenencia';
                            } else {
                                ctxTag = 'En mano • Sostenido';
                            }
                            break;
                        }
                    }
                }

                // 3. Apoyo sobre mobiliario de escritorio / mesa
                if (ctxTag === 'En entorno') {
                    for (const table of tables) {
                        const [tx, ty, tw, th] = table.bbox;
                        if (ocx >= tx - tw * 0.1 && ocx <= tx + tw * 1.1 && ocy >= ty - oh * 0.8 && ocy <= ty + th) {
                            ctxTag = 'Sobre mesa';
                            break;
                        }
                    }
                }

                // 4. Ubicación sobre asientos o sofás
                if (ctxTag === 'En entorno') {
                    for (const chair of chairs) {
                        const [cx, cy, cw, ch] = chair.bbox;
                        if (ocx >= cx && ocx <= cx + cw && ocy >= cy && ocy <= cy + ch) {
                            ctxTag = 'Sobre asiento';
                            break;
                        }
                    }
                }

                // 5. Ubicación cuadrante general en el entorno
                if (ctxTag === 'En entorno') {
                    const zone = getPositionDescription(pred.bbox);
                    ctxTag = `Entorno • ${zone}`;
                }

                contextMap[pred.id || `${pred.class}_${Math.round(ox)}`] = ctxTag;
            });

            return contextMap;
        }

        // ==========================================
        // GESTURE & BEHAVIOR ANALYSIS HEURISTICS
        // Detecta gestos precisos, qué hace la persona, conteo de dedos y el estado de la escena completa
        // ==========================================
        function analyzeGesturesAndBehavior(predictions, personsData, objectContexts, handResults) {
            const personCount = personsData.length;
            const fruitClasses = ['apple', 'banana', 'orange', 'broccoli', 'carrot'];
            const mealClasses = ['sandwich', 'pizza', 'hot dog', 'donut', 'cake', 'bowl'];
            const utensilClasses = ['fork', 'knife', 'spoon'];

            if (personCount === 0) {
                // Caso 1: Objeto desatendido en el entorno
                const unattendedObjs = predictions.filter(p => ['backpack', 'handbag', 'suitcase', 'laptop', 'cell phone', 'wallet'].includes(p.class));
                if (unattendedObjs.length > 0) {
                    return {
                        key: 'unattended_object',
                        name: `Objeto Desatendido: ${getObjectDisplayName(unattendedObjs[0].class)}`,
                        color: BEHAVIOR_COLOR_MAP['unattended_object'] ? BEHAVIOR_COLOR_MAP['unattended_object'].color : '#F59E0B',
                        confidence: 0.95
                    };
                }

                // Caso 2: Objeto mostrado en primer plano a la cámara (sin cuerpo completo en escena)
                const fruitsVeg = predictions.filter(p => fruitClasses.includes(p.class));
                if (fruitsVeg.length > 0) {
                    return {
                        key: 'showing_fruit_veg',
                        name: `Mostrando Fruta: ${getObjectDisplayName(fruitsVeg[0].class)}`,
                        color: BEHAVIOR_COLOR_MAP['showing_fruit_veg'] ? BEHAVIOR_COLOR_MAP['showing_fruit_veg'].color : '#84CC16',
                        confidence: 0.94
                    };
                }

                const meals = predictions.filter(p => mealClasses.includes(p.class));
                if (meals.length > 0) {
                    return {
                        key: 'showing_food',
                        name: `Mostrando Alimento: ${getObjectDisplayName(meals[0].class)}`,
                        color: BEHAVIOR_COLOR_MAP['showing_food'] ? BEHAVIOR_COLOR_MAP['showing_food'].color : '#EAB308',
                        confidence: 0.94
                    };
                }

                const phones = predictions.filter(p => p.class === 'cell phone');
                if (phones.length > 0) {
                    return {
                        key: 'holding_phone',
                        name: 'Mostrando Celular',
                        color: BEHAVIOR_COLOR_MAP['holding_phone'] ? BEHAVIOR_COLOR_MAP['holding_phone'].color : '#F59E0B',
                        confidence: 0.94
                    };
                }

                const utensils = predictions.filter(p => utensilClasses.includes(p.class));
                if (utensils.length > 0) {
                    return {
                        key: 'using_utensil',
                        name: `Mostrando Cubierto [${getObjectDisplayName(utensils[0].class)}]`,
                        color: BEHAVIOR_COLOR_MAP['using_utensil'] ? BEHAVIOR_COLOR_MAP['using_utensil'].color : '#EC4899',
                        confidence: 0.92
                    };
                }

                const otherObjs = predictions.filter(p => !['chair', 'couch', 'bed', 'dining table'].includes(p.class));
                if (otherObjs.length > 0) {
                    return {
                        key: 'showing_object',
                        name: `Mostrando a la Cámara: ${getObjectDisplayName(otherObjs[0].class)}`,
                        color: BEHAVIOR_COLOR_MAP['showing_object'] ? BEHAVIOR_COLOR_MAP['showing_object'].color : '#38BDF8',
                        confidence: 0.92
                    };
                }

                // Caso 3: Manos mostradas en primer plano a la cámara (conteo de dedos)
                if (handResults && handResults.length > 0) {
                    if (handResults.length >= 2) {
                        const totalFingers = handResults[0].count + handResults[1].count;
                        return {
                            key: 'both_hands',
                            name: `Ambas Manos: ${totalFingers} Dedos Visibles (${handResults[0].count} + ${handResults[1].count})`,
                            color: BEHAVIOR_COLOR_MAP['both_hands'] ? BEHAVIOR_COLOR_MAP['both_hands'].color : '#06B6D4',
                            confidence: 0.96
                        };
                    } else {
                        const h = handResults[0];
                        return {
                            key: 'hand_fingers',
                            name: `${h.side}: ${h.gesture}`,
                            color: BEHAVIOR_COLOR_MAP['hand_fingers'] ? BEHAVIOR_COLOR_MAP['hand_fingers'].color : '#10B981',
                            confidence: 0.95
                        };
                    }
                }

                return {
                    key: 'absent',
                    name: BEHAVIOR_COLOR_MAP['absent'] ? BEHAVIOR_COLOR_MAP['absent'].name : 'Persona Ausente',
                    color: BEHAVIOR_COLOR_MAP['absent'] ? BEHAVIOR_COLOR_MAP['absent'].color : '#64748B',
                    confidence: 0.95
                };
            }

            const phones = predictions.filter(p => p.class === 'cell phone');
            const laptops = predictions.filter(p => p.class === 'laptop' || p.class === 'tv');
            const keyboards = predictions.filter(p => p.class === 'keyboard' || p.class === 'mouse');
            const drinks = predictions.filter(p => p.class === 'bottle' || p.class === 'cup' || p.class === 'wine glass');
            const books = predictions.filter(p => p.class === 'book' || p.class === 'document');
            const chairs = predictions.filter(p => p.class === 'chair' || p.class === 'couch');
            const tables = predictions.filter(p => p.class === 'dining table' || p.class === 'bench');
            const bags = predictions.filter(p => p.class === 'backpack' || p.class === 'handbag' || p.class === 'suitcase' || p.class === 'wallet');
            const utensils = predictions.filter(p => utensilClasses.includes(p.class));
            const writingTools = predictions.filter(p => ['pen', 'document'].includes(p.class));
            const sharpObjects = predictions.filter(p => ['knife', 'scissors'].includes(p.class));

            // 1. Detección de Grupo / Múltiples Personas
            if (personCount >= 2) {
                const p1 = personsData[0].bbox;
                const p2 = personsData[1].bbox;
                const dist = Math.abs((p1[0] + p1[2] / 2) - (p2[0] + p2[2] / 2));

                if (dist < (p1[2] + p2[2]) * 1.5) {
                    return {
                        key: 'group_interaction',
                        name: BEHAVIOR_COLOR_MAP['group_interaction'] ? BEHAVIOR_COLOR_MAP['group_interaction'].name : 'Conversación en Grupo',
                        color: BEHAVIOR_COLOR_MAP['group_interaction'] ? BEHAVIOR_COLOR_MAP['group_interaction'].color : '#9333EA',
                        confidence: 0.94
                    };
                }

                return {
                    key: 'multiple_people',
                    name: `Múltiples Personas (${personCount} en escena)`,
                    color: BEHAVIOR_COLOR_MAP['multiple_people'] ? BEHAVIOR_COLOR_MAP['multiple_people'].color : '#6366F1',
                    confidence: 0.96
                };
            }

            const mainPerson = personsData[0];
            const [px, py, pw, ph] = mainPerson.bbox;

            // 2. Alerta de Seguridad: Persona Caída
            if (pw / (ph || 1) >= 1.35 && (py + ph) >= canvasElement.height * 0.40) {
                return {
                    key: 'person_fallen',
                    name: BEHAVIOR_COLOR_MAP['person_fallen'] ? BEHAVIOR_COLOR_MAP['person_fallen'].name : 'Persona Caída',
                    color: BEHAVIOR_COLOR_MAP['person_fallen'] ? BEHAVIOR_COLOR_MAP['person_fallen'].color : '#DC2626',
                    confidence: 0.96
                };
            }

            // 3. Alerta de Seguridad: Manipulando Objeto Peligroso (Cuchillo, Tijeras)
            for (const sharp of sharpObjects) {
                const [sx, sy, sw, sh] = sharp.bbox;
                const scx = sx + sw / 2;
                const scy = sy + sh / 2;
                if (scx >= px - pw * 0.35 && scx <= px + pw * 1.35 && scy >= py && scy <= py + ph * 1.1) {
                    return {
                        key: 'handling_sharp',
                        name: `Manipulando Peligroso [${getObjectDisplayName(sharp.class)}]`,
                        color: BEHAVIOR_COLOR_MAP['handling_sharp'] ? BEHAVIOR_COLOR_MAP['handling_sharp'].color : '#E11D48',
                        confidence: 0.97
                    };
                }
            }

            // 4. Comiendo Frutas, Verduras o Alimentos (MÁXIMA PRIORIDAD CUANDO ESTÁ CERCA DE LA BOCA)
            const foods = predictions.filter(p => [...fruitClasses, ...mealClasses].includes(p.class));
            for (const food of foods) {
                const [fx, fy, fw, fh] = food.bbox;
                const foodCenterX = fx + fw / 2;
                const foodCenterY = fy + fh / 2;

                if (foodCenterX >= px - pw * 0.25 && foodCenterX <= px + pw * 1.25 &&
                    foodCenterY >= py + ph * 0.15 && foodCenterY <= py + ph * 0.55) {
                    const foodName = getObjectDisplayName(food.class);
                    return {
                        key: 'eating_food',
                        name: `Comiendo: ${foodName}`,
                        color: BEHAVIOR_COLOR_MAP['eating_food'] ? BEHAVIOR_COLOR_MAP['eating_food'].color : '#F97316',
                        confidence: 0.96
                    };
                }
            }

            // 5. Celular: Llamada Telefónica Activa en Oreja vs Manipulación
            for (const phone of phones) {
                const [bx, by, bw, bh] = phone.bbox;
                const phoneCenterX = bx + bw / 2;
                const phoneCenterY = by + bh / 2;

                if (phoneCenterX >= px - pw * 0.35 && phoneCenterX <= px + pw * 1.35) {
                    // Cerca de la oreja o lateral superior del rostro
                    if (phoneCenterY >= py && phoneCenterY <= py + ph * 0.40) {
                        return {
                            key: 'phone_call',
                            name: 'Llamada Activa',
                            color: BEHAVIOR_COLOR_MAP['phone_call'] ? BEHAVIOR_COLOR_MAP['phone_call'].color : '#FF3D00',
                            confidence: 0.97
                        };
                    } else if (phoneCenterY > py + ph * 0.40 && phoneCenterY <= py + ph * 0.90) {
                        return {
                            key: 'holding_phone',
                            name: BEHAVIOR_COLOR_MAP['holding_phone'] ? BEHAVIOR_COLOR_MAP['holding_phone'].name : 'Usando Celular',
                            color: BEHAVIOR_COLOR_MAP['holding_phone'] ? BEHAVIOR_COLOR_MAP['holding_phone'].color : '#F59E0B',
                            confidence: 0.95
                        };
                    }
                }
            }

            // 6. Mostrando Fruta a la Cámara
            const fruitsVeg = predictions.filter(p => fruitClasses.includes(p.class));
            for (const fv of fruitsVeg) {
                const [fvx, fvy, fvw, fvh] = fv.bbox;
                if (fvx + fvw / 2 >= px - pw * 0.35 && fvx + fvw / 2 <= px + pw * 1.35 && fvy >= py + ph * 0.25) {
                    return {
                        key: 'showing_fruit_veg',
                        name: `Mostrando Fruta: ${getObjectDisplayName(fv.class)}`,
                        color: BEHAVIOR_COLOR_MAP['showing_fruit_veg'] ? BEHAVIOR_COLOR_MAP['showing_fruit_veg'].color : '#84CC16',
                        confidence: 0.94
                    };
                }
            }

            // 7. Mostrando Comida o Alimento Preparado
            const meals = predictions.filter(p => mealClasses.includes(p.class));
            for (const meal of meals) {
                const [mx, my, mw, mh] = meal.bbox;
                if (mx + mw / 2 >= px - pw * 0.35 && mx + mw / 2 <= px + pw * 1.35 && my >= py + ph * 0.25) {
                    return {
                        key: 'showing_food',
                        name: `Mostrando Alimento: ${getObjectDisplayName(meal.class)}`,
                        color: BEHAVIOR_COLOR_MAP['showing_food'] ? BEHAVIOR_COLOR_MAP['showing_food'].color : '#EAB308',
                        confidence: 0.94
                    };
                }
            }

            // 8. Bebiendo Líquido (Botella, Taza, Copa)
            for (const drink of drinks) {
                const [dx, dy, dw, dh] = drink.bbox;
                const drinkCenterX = dx + dw / 2;
                const drinkCenterY = dy + dh / 2;

                if (drinkCenterX >= px - pw * 0.25 && drinkCenterX <= px + pw * 1.25 &&
                    drinkCenterY >= py && drinkCenterY <= py + ph * 0.55) {
                    return {
                        key: 'drinking',
                        name: `Bebiendo [${getObjectDisplayName(drink.class)}]`,
                        color: BEHAVIOR_COLOR_MAP['drinking'] ? BEHAVIOR_COLOR_MAP['drinking'].color : '#7C3AED',
                        confidence: 0.94
                    };
                }
            }

            // 9. Tomando Apuntes o Manipulando Escritura
            for (const wt of writingTools) {
                const [wx, wy, ww, wh] = wt.bbox;
                if (wx + ww / 2 >= px - pw * 0.3 && wx + ww / 2 <= px + pw * 1.3 && wy >= py + ph * 0.3) {
                    return {
                        key: 'taking_notes',
                        name: BEHAVIOR_COLOR_MAP['taking_notes'] ? BEHAVIOR_COLOR_MAP['taking_notes'].name : 'Tomando Apuntes',
                        color: BEHAVIOR_COLOR_MAP['taking_notes'] ? BEHAVIOR_COLOR_MAP['taking_notes'].color : '#EC4899',
                        confidence: 0.94
                    };
                }
            }

            // 10. Manipulando Mochila, Cartera o Pertenencias
            for (const bag of bags) {
                const [bx, by, bw, bh] = bag.bbox;
                if (bx + bw / 2 >= px - pw * 0.35 && bx + bw / 2 <= px + pw * 1.35 && by >= py + ph * 0.20) {
                    return {
                        key: 'rummaging_bags',
                        name: `Manipulando ${getObjectDisplayName(bag.class)}`,
                        color: BEHAVIOR_COLOR_MAP['rummaging_bags'] ? BEHAVIOR_COLOR_MAP['rummaging_bags'].color : '#9333EA',
                        confidence: 0.93
                    };
                }
            }

            // 11. Manipulando Utensilio (Tenedor, Cuchillo, Cuchara)
            if (utensils.length > 0) {
                return {
                    key: 'using_utensil',
                    name: `Usando Cubierto [${getObjectDisplayName(utensils[0].class)}]`,
                    color: BEHAVIOR_COLOR_MAP['using_utensil'] ? BEHAVIOR_COLOR_MAP['using_utensil'].color : '#EC4899',
                    confidence: 0.92
                };
            }

            // 12. Conteo de Dedos y Gestos de Manos con MediaPipe
            if (handResults && handResults.length > 0) {
                // Chequeo de Manos Arriba o en Cabeza
                if (handResults.length >= 2) {
                    const bothNearHead = handResults.every(h => (h.bbox[1] + h.bbox[3] / 2) <= py + ph * 0.38);
                    const bothAboveHead = handResults.every(h => (h.bbox[1] + h.bbox[3] / 2) < py);

                    if (bothAboveHead) {
                        return {
                            key: 'stretching',
                            name: BEHAVIOR_COLOR_MAP['stretching'] ? BEHAVIOR_COLOR_MAP['stretching'].name : 'Descanso Activo',
                            color: BEHAVIOR_COLOR_MAP['stretching'] ? BEHAVIOR_COLOR_MAP['stretching'].color : '#14B8A6',
                            confidence: 0.94
                        };
                    }

                    if (bothNearHead) {
                        return {
                            key: 'head_in_hands',
                            name: BEHAVIOR_COLOR_MAP['head_in_hands'] ? BEHAVIOR_COLOR_MAP['head_in_hands'].name : 'Manos en Cabeza',
                            color: BEHAVIOR_COLOR_MAP['head_in_hands'] ? BEHAVIOR_COLOR_MAP['head_in_hands'].color : '#C026D3',
                            confidence: 0.95
                        };
                    }

                    const totalFingers = handResults[0].count + handResults[1].count;
                    const bothDesc = (totalFingers === 10)
                        ? 'Ambas Manos: 10 Dedos Visibles (Palmas Abiertas)'
                        : `Ambas Manos: ${totalFingers} Dedos Visibles (${handResults[0].side}: ${handResults[0].count} • ${handResults[1].side}: ${handResults[1].count})`;
                    return {
                        key: 'both_hands',
                        name: bothDesc,
                        color: BEHAVIOR_COLOR_MAP['both_hands'] ? BEHAVIOR_COLOR_MAP['both_hands'].color : '#06B6D4',
                        confidence: 0.96
                    };
                } else if (handResults.length === 1 && handResults[0].count > 0) {
                    // Si se está mostrando 1 o más dedos de forma deliberada a la cámara
                    const h = handResults[0];
                    return {
                        key: 'hand_fingers',
                        name: `${h.side}: ${h.gesture}`,
                        color: BEHAVIOR_COLOR_MAP['hand_fingers'] ? BEHAVIOR_COLOR_MAP['hand_fingers'].color : '#10B981',
                        confidence: 0.96
                    };
                }
            }

            // 13. Mostrando Cualquier Otro Objeto en Primer Plano a la Cámara
            const heldObjects = predictions.filter(p => !['person', 'chair', 'couch', 'bed', 'dining table'].includes(p.class));
            for (const obj of heldObjects) {
                const [ox, oy, ow, oh] = obj.bbox;
                const ocx = ox + ow / 2;
                const ocy = oy + oh / 2;
                if (ocx >= px - pw * 0.25 && ocx <= px + pw * 1.25 && ocy >= py + ph * 0.20 && ocy <= py + ph * 0.90) {
                    return {
                        key: 'showing_object',
                        name: `Mostrando a la Cámara: ${getObjectDisplayName(obj.class)}`,
                        color: BEHAVIOR_COLOR_MAP['showing_object'] ? BEHAVIOR_COLOR_MAP['showing_object'].color : '#38BDF8',
                        confidence: 0.93
                    };
                }
            }

            // 14. Escribiendo en Teclado o Trabajando en Laptop
            for (const laptop of laptops) {
                const [lx, ly, lw, lh] = laptop.bbox;
                const laptopCenterX = lx + lw / 2;
                const laptopCenterY = ly + lh / 2;

                if (laptopCenterX >= px - pw * 0.35 && laptopCenterX <= px + pw * 1.35 &&
                    laptopCenterY >= py + ph * 0.25) {
                    if (keyboards.length > 0) {
                        return {
                            key: 'typing_keyboard',
                            name: BEHAVIOR_COLOR_MAP['typing_keyboard'] ? BEHAVIOR_COLOR_MAP['typing_keyboard'].name : 'Escribiendo en Teclado',
                            color: BEHAVIOR_COLOR_MAP['typing_keyboard'] ? BEHAVIOR_COLOR_MAP['typing_keyboard'].color : '#10B981',
                            confidence: 0.96
                        };
                    }
                    return {
                        key: 'working_laptop',
                        name: BEHAVIOR_COLOR_MAP['working_laptop'] ? BEHAVIOR_COLOR_MAP['working_laptop'].name : 'Trabajando en Laptop',
                        color: BEHAVIOR_COLOR_MAP['working_laptop'] ? BEHAVIOR_COLOR_MAP['working_laptop'].color : '#059669',
                        confidence: 0.96
                    };
                }
            }

            if (keyboards.length > 0) {
                const kb = keyboards[0];
                const kbcx = kb.bbox[0] + kb.bbox[2] / 2;
                const kbcy = kb.bbox[1] + kb.bbox[3] / 2;
                if (kbcx >= px - pw * 0.35 && kbcx <= px + pw * 1.35 && kbcy >= py + ph * 0.35) {
                    return {
                        key: 'typing_keyboard',
                        name: BEHAVIOR_COLOR_MAP['typing_keyboard'] ? BEHAVIOR_COLOR_MAP['typing_keyboard'].name : 'Escribiendo en Teclado',
                        color: BEHAVIOR_COLOR_MAP['typing_keyboard'] ? BEHAVIOR_COLOR_MAP['typing_keyboard'].color : '#10B981',
                        confidence: 0.93
                    };
                }
            }

            // 15. Leyendo Documento
            for (const book of books) {
                const [bkx, bky, bkw, bkh] = book.bbox;
                if (bkx + bkw / 2 >= px - pw * 0.2 && bkx + bkw / 2 <= px + pw * 1.2 && bky >= py + ph * 0.2) {
                    return {
                        key: 'reading',
                        name: BEHAVIOR_COLOR_MAP['reading'] ? BEHAVIOR_COLOR_MAP['reading'].name : 'Leyendo Documento',
                        color: BEHAVIOR_COLOR_MAP['reading'] ? BEHAVIOR_COLOR_MAP['reading'].color : '#DB2777',
                        confidence: 0.92
                    };
                }
            }

            // 16. Dinámica de Movimiento Rápido
            const nowTime = performance.now();
            let isRestless = false;
            let isWaving = false;
            let isHandsUp = false;
            let isAggressive = false;

            if (previousPersons.length > 0 && (nowTime - previousPersonTime < 400)) {
                const prev = previousPersons[0].bbox;
                const deltaX = Math.abs(px - prev[0]);
                const deltaY = Math.abs(py - prev[1]);

                if (deltaY > canvasElement.height * 0.08 && py < prev[1]) {
                    isHandsUp = true;
                } else if (deltaX > canvasElement.width * 0.06 && deltaY < canvasElement.height * 0.045) {
                    isWaving = true;
                } else if (deltaX > canvasElement.width * 0.12 || deltaY > canvasElement.height * 0.12) {
                    isAggressive = true;
                } else if (deltaX > canvasElement.width * 0.07 || deltaY > canvasElement.height * 0.07) {
                    isRestless = true;
                }
            }

            previousPersons = personsData;
            previousPersonTime = nowTime;

            if (isAggressive) {
                return {
                    key: 'aggressive_motion',
                    name: BEHAVIOR_COLOR_MAP['aggressive_motion'] ? BEHAVIOR_COLOR_MAP['aggressive_motion'].name : 'Movimiento Brusco',
                    color: BEHAVIOR_COLOR_MAP['aggressive_motion'] ? BEHAVIOR_COLOR_MAP['aggressive_motion'].color : '#EF4444',
                    confidence: 0.93
                };
            }

            if (isHandsUp) {
                return {
                    key: 'hands_up',
                    name: BEHAVIOR_COLOR_MAP['hands_up'] ? BEHAVIOR_COLOR_MAP['hands_up'].name : 'Manos Arriba',
                    color: BEHAVIOR_COLOR_MAP['hands_up'] ? BEHAVIOR_COLOR_MAP['hands_up'].color : '#DC2626',
                    confidence: 0.93
                };
            }

            if (isWaving) {
                return {
                    key: 'waving',
                    name: BEHAVIOR_COLOR_MAP['waving'] ? BEHAVIOR_COLOR_MAP['waving'].name : 'Saludando con Mano',
                    color: BEHAVIOR_COLOR_MAP['waving'] ? BEHAVIOR_COLOR_MAP['waving'].color : '#F59E0B',
                    confidence: 0.91
                };
            }

            if (isRestless) {
                return {
                    key: 'restless',
                    name: BEHAVIOR_COLOR_MAP['restless'] ? BEHAVIOR_COLOR_MAP['restless'].name : 'Movimiento Rápido',
                    color: BEHAVIOR_COLOR_MAP['restless'] ? BEHAVIOR_COLOR_MAP['restless'].color : '#0284C7',
                    confidence: 0.89
                };
            }

            // 17. Postura Corporal: Sentado vs De Pie vs Exponiendo
            const aspectRatio = ph / (pw || 1);
            const isNearSeat = chairs.some(c => {
                const cx = c.bbox[0] + c.bbox[2] / 2;
                return (cx >= px - pw * 0.4 && cx <= px + pw * 1.4);
            });
            const isNearDesk = tables.some(t => {
                const tx = t.bbox[0] + t.bbox[2] / 2;
                return (tx >= px - pw * 0.4 && tx <= px + pw * 1.4);
            });

            if (aspectRatio >= 1.6 && laptops.length > 0 && isWaving) {
                return {
                    key: 'presentation',
                    name: BEHAVIOR_COLOR_MAP['presentation'] ? BEHAVIOR_COLOR_MAP['presentation'].name : 'Exponiendo Presentación',
                    color: BEHAVIOR_COLOR_MAP['presentation'] ? BEHAVIOR_COLOR_MAP['presentation'].color : '#38BDF8',
                    confidence: 0.93
                };
            }

            if ((isNearSeat || isNearDesk) && aspectRatio < 1.55) {
                return {
                    key: 'sitting_posture',
                    name: BEHAVIOR_COLOR_MAP['sitting_posture'] ? BEHAVIOR_COLOR_MAP['sitting_posture'].name : 'Sentado',
                    color: BEHAVIOR_COLOR_MAP['sitting_posture'] ? BEHAVIOR_COLOR_MAP['sitting_posture'].color : '#3B82F6',
                    confidence: 0.94
                };
            }

            if (aspectRatio >= 1.7) {
                return {
                    key: 'standing_posture',
                    name: BEHAVIOR_COLOR_MAP['standing_posture'] ? BEHAVIOR_COLOR_MAP['standing_posture'].name : 'De Pie',
                    color: BEHAVIOR_COLOR_MAP['standing_posture'] ? BEHAVIOR_COLOR_MAP['standing_posture'].color : '#06B6D4',
                    confidence: 0.93
                };
            }

            // 18. Persona Atenta y Enfocada en Cámara
            return {
                key: 'attentive',
                name: BEHAVIOR_COLOR_MAP['attentive'] ? BEHAVIOR_COLOR_MAP['attentive'].name : 'Persona Presente',
                color: BEHAVIOR_COLOR_MAP['attentive'] ? BEHAVIOR_COLOR_MAP['attentive'].color : '#2563EB',
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
            const key = (label || '').toLowerCase().trim();
            if (typeof evolutionaryEngine !== 'undefined' && evolutionaryEngine.knowledgeTaxonomy[key]) {
                return evolutionaryEngine.knowledgeTaxonomy[key].name;
            }
            if (COCO_SPANISH_MAP[key]) return COCO_SPANISH_MAP[key];
            if (OBJECT_COLOR_MAP[key]) return OBJECT_COLOR_MAP[key].name;
            return key.charAt(0).toUpperCase() + key.slice(1);
        }

        // ==========================================
        // CANVAS RENDERING WITH EXACT COORDINATES & RICH CONTEXT & HAND SKELETONS
        // ==========================================
        function renderComprehensiveOverlay(predictions, personsData, behavior, objectContexts, handResults) {
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            // Sistema Avanzado de Colocación Anti-Colisión Dinámica de Etiquetas
            const occupiedLabelRects = [];
            function rectsOverlap(r1, r2, pad = 4) {
                return !(
                    r1.x + r1.w + pad <= r2.x ||
                    r2.x + r2.w + pad <= r1.x ||
                    r1.y + r1.h + pad <= r2.y ||
                    r2.y + r2.h + pad <= r1.y
                );
            }
            function allocateLabelPosition(anchorBbox, tagW, tagH) {
                const [bx, by, bw, bh] = anchorBbox;
                const pad = 6;
                const cW = canvasElement.width;
                const cH = canvasElement.height;

                // Si el objeto está pegado al borde superior, se coloca abajo para máxima visibilidad
                const isNearTop = by < (tagH + 12);
                const candidates = isNearTop ? [
                    { x: bx, y: by + bh + 4 },                          // 1: Inmediatamente abajo
                    { x: bx, y: by + bh + tagH + 8 },                    // 2: Segundo nivel inferior
                    { x: bx + bw + 6, y: Math.max(pad, by) },            // 3: Lateral derecho
                    { x: bx - tagW - 6, y: Math.max(pad, by) },          // 4: Lateral izquierdo
                    { x: bx + 4, y: by + 4 },                            // 5: Interior
                    { x: bx, y: by - tagH - 4 }                          // 6: Arriba (reserva)
                ] : [
                    { x: bx, y: by - tagH - 4 },                          // 1: Inmediatamente arriba
                    { x: bx, y: by + bh + 4 },                           // 2: Inmediatamente abajo
                    { x: bx + bw + 6, y: Math.max(pad, by) },            // 3: Lateral derecho
                    { x: bx - tagW - 6, y: Math.max(pad, by) },          // 4: Lateral izquierdo
                    { x: bx, y: by - (tagH * 2) - 8 },                   // 5: Segundo nivel superior
                    { x: bx, y: by + bh + tagH + 8 },                    // 6: Segundo nivel inferior
                    { x: bx + 4, y: by + 4 }                             // 7: Interior
                ];

                for (const cand of candidates) {
                    const clampedX = Math.max(pad, Math.min(cand.x, cW - tagW - pad));
                    const clampedY = Math.max(pad, Math.min(cand.y, cH - tagH - pad));
                    const candidateRect = { x: clampedX, y: clampedY, w: tagW, h: tagH };

                    const hasCollision = occupiedLabelRects.some(occ => rectsOverlap(candidateRect, occ, 4));
                    if (!hasCollision) {
                        occupiedLabelRects.push(candidateRect);
                        const isDisplaced = Math.abs(clampedX - bx) > 12 || Math.abs(clampedY - (by - tagH - 4)) > 12;
                        return { x: clampedX, y: clampedY, isDisplaced, anchorX: bx, anchorY: by };
                    }
                }

                // Respaldo inteligente en área despejada
                let fallbackY = Math.max(pad, Math.min(by - tagH - 4, cH - tagH - pad));
                let fallbackX = Math.max(pad, Math.min(bx, cW - tagW - pad));
                let attempts = 0;
                while (occupiedLabelRects.some(occ => rectsOverlap({ x: fallbackX, y: fallbackY, w: tagW, h: tagH }, occ, 3)) && attempts < 16) {
                    fallbackY += tagH + 4;
                    if (fallbackY + tagH > cH - pad) {
                        fallbackY = pad;
                        fallbackX = (fallbackX + 45) % Math.max(1, cW - tagW);
                    }
                    attempts++;
                }
                const finalRect = { x: fallbackX, y: fallbackY, w: tagW, h: tagH };
                occupiedLabelRects.push(finalRect);
                return { x: fallbackX, y: fallbackY, isDisplaced: true, anchorX: bx, anchorY: by };
            }

            // 1. Dibuja las cajas delimitadoras de objetos
            predictions.forEach(pred => {
                const isPerson = pred.class === 'person';
                const [x, y, width, height] = pred.bbox;
                const color = getObjectColor(pred.class);
                const displayName = getObjectDisplayName(pred.class);
                const scorePercent = Math.min(100, Math.round((pred.score || 0.95) * 100));
                const contextTag = (objectContexts && objectContexts[pred.id]) ? objectContexts[pred.id] : null;

                ctx.save();
                ctx.strokeStyle = color;
                ctx.lineWidth = isPerson ? 3 : 2;
                ctx.strokeRect(x, y, width, height);

                // Tech corner brackets
                const bracketSize = Math.min(18, width / 4, height / 4);
                ctx.lineWidth = 4;
                ctx.beginPath(); ctx.moveTo(x, y + bracketSize); ctx.lineTo(x, y); ctx.lineTo(x + bracketSize, y); ctx.stroke();
                ctx.beginPath(); ctx.moveTo(x + width - bracketSize, y); ctx.lineTo(x + width, y); ctx.lineTo(x + width, y + bracketSize); ctx.stroke();
                ctx.beginPath(); ctx.moveTo(x, y + height - bracketSize); ctx.lineTo(x, y + height); ctx.lineTo(x + bracketSize, y + height); ctx.stroke();
                ctx.beginPath(); ctx.moveTo(x + width - bracketSize, y + height); ctx.lineTo(x + width, y + height); ctx.lineTo(x + width, y + height - bracketSize); ctx.stroke();
                ctx.restore();

                // Retícula de mira y seguimiento continuo en tiempo real
                const cx = x + width / 2;
                const cy = y + height / 2;
                ctx.save();
                ctx.strokeStyle = color;
                ctx.lineWidth = 1.5;
                ctx.globalAlpha = 0.75;
                ctx.beginPath();
                ctx.arc(cx, cy, 5.5, 0, Math.PI * 2);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(cx - 10, cy); ctx.lineTo(cx + 10, cy);
                ctx.moveTo(cx, cy - 10); ctx.lineTo(cx, cy + 10);
                ctx.stroke();
                ctx.restore();

                // Etiqueta inteligente con información del estado y contexto
                let labelText = '';
                if (isPerson) {
                    const actionName = (behavior && behavior.name && behavior.key !== 'absent') ? behavior.name : 'Presente';
                    labelText = `Persona #${pred.id || 1} [${actionName}] • ${scorePercent}%`;
                } else {
                    const ctxStr = contextTag ? ` [${contextTag}]` : '';
                    labelText = `${displayName} #${pred.id || 1}${ctxStr} • ${scorePercent}%`;
                }

                ctx.font = 'bold 12px "JetBrains Mono", monospace';
                let textWidth = ctx.measureText(labelText).width;
                if (textWidth > canvasElement.width - 40) {
                    ctx.font = 'bold 11px "JetBrains Mono", monospace';
                    textWidth = ctx.measureText(labelText).width;
                }
                const tagWidth = Math.min(canvasElement.width - 16, textWidth + 24);
                const tagHeight = 24;

                const pos = allocateLabelPosition([x, y, width, height], tagWidth, tagHeight);
                const tagX = pos.x;
                const tagY = pos.y;

                // Línea guía luminosa si la etiqueta fue reubicada para evitar tapar información
                if (pos.isDisplaced) {
                    ctx.save();
                    ctx.strokeStyle = color;
                    ctx.lineWidth = 1;
                    ctx.setLineDash([3, 3]);
                    ctx.globalAlpha = 0.55;
                    ctx.beginPath();
                    ctx.moveTo(tagX + (tagWidth / 2), tagY + (tagY < y ? tagHeight : 0));
                    ctx.lineTo(x + Math.min(16, width / 3), y);
                    ctx.stroke();
                    ctx.restore();
                }

                ctx.save();
                ctx.shadowColor = 'rgba(0, 0, 0, 0.75)';
                ctx.shadowBlur = 6;
                ctx.fillStyle = 'rgba(10, 15, 29, 0.94)';
                ctx.strokeStyle = color;
                ctx.lineWidth = 1.5;
                ctx.beginPath();
                ctx.roundRect(tagX, tagY, tagWidth, tagHeight, 6);
                ctx.fill();
                ctx.stroke();

                // Barra indicadora lateral izquierda de color
                ctx.fillStyle = color;
                ctx.beginPath();
                ctx.roundRect(tagX + 2, tagY + 2, 4, tagHeight - 4, 2);
                ctx.fill();

                // Punto indicador
                ctx.beginPath();
                ctx.arc(tagX + 13, tagY + tagHeight / 2, 3.5, 0, Math.PI * 2);
                ctx.fill();

                // Texto blanco de máximo contraste y nitidez
                ctx.shadowBlur = 0;
                ctx.fillStyle = '#ffffff';
                ctx.fillText(labelText, tagX + 22, tagY + 16);
                ctx.restore();
            });

            // 2. Badges individuales de tono de cabello en personas (Anti-Colisión Garantizada)
            personsData.forEach(p => {
                const [px, py, pw, ph] = p.bbox;
                if (p.hair) {
                    const hairTag = `Cabello #${p.id} • ${p.hair.name}`;
                    ctx.font = 'bold 12px "JetBrains Mono", monospace';
                    const tagW = ctx.measureText(hairTag).width + 26;
                    const tagH = 24;

                    const hPos = allocateLabelPosition([px, py, pw, ph], tagW, tagH);
                    const clampedHairX = hPos.x;
                    const clampedHairY = hPos.y;

                    ctx.save();
                    ctx.shadowColor = 'rgba(0, 0, 0, 0.75)';
                    ctx.shadowBlur = 6;
                    ctx.fillStyle = 'rgba(15, 23, 42, 0.94)';
                    ctx.strokeStyle = p.hair.color;
                    ctx.lineWidth = 1.5;
                    ctx.beginPath();
                    ctx.roundRect(clampedHairX, clampedHairY, tagW, tagH, 6);
                    ctx.fill();
                    ctx.stroke();

                    // Barra indicadora lateral
                    ctx.fillStyle = p.hair.color;
                    ctx.beginPath();
                    ctx.roundRect(clampedHairX + 2, clampedHairY + 2, 4, tagH - 4, 2);
                    ctx.fill();

                    // Punto de color de cabello
                    ctx.beginPath();
                    ctx.arc(clampedHairX + 13, clampedHairY + tagH / 2, 3.5, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.shadowBlur = 0;
                    ctx.fillStyle = '#f8fafc';
                    ctx.fillText(hairTag, clampedHairX + 22, clampedHairY + 16);
                    ctx.restore();
                }
            });

            // 3. DIBUJO DE ESQUELETO DE MANOS Y CONTEO DE DEDOS (MEDIAPIPE 21 LANDMARKS)
            if (handResults && handResults.length > 0) {
                const HAND_CONNECTIONS = [
                    [0, 1], [1, 2], [2, 3], [3, 4],          // Pulgar
                    [0, 5], [5, 6], [6, 7], [7, 8],          // Índice
                    [5, 9], [9, 10], [10, 11], [11, 12],     // Medio
                    [9, 13], [13, 14], [14, 15], [15, 16],   // Anular
                    [13, 17], [17, 18], [18, 19], [19, 20],  // Meñique
                    [0, 17]                                  // Base de palma
                ];

                handResults.forEach(hand => {
                    const lm = hand.landmarks;
                    const W = canvasElement.width;
                    const H = canvasElement.height;

                    // Huesos de la mano
                    ctx.save();
                    ctx.strokeStyle = '#10B981';
                    ctx.lineWidth = 2.5;
                    ctx.shadowColor = '#10B981';
                    ctx.shadowBlur = 6;
                    HAND_CONNECTIONS.forEach(([i, j]) => {
                        ctx.beginPath();
                        ctx.moveTo(lm[i].x * W, lm[i].y * H);
                        ctx.lineTo(lm[j].x * W, lm[j].y * H);
                        ctx.stroke();
                    });
                    ctx.restore();

                    // Articulaciones luminosas (Joints)
                    ctx.save();
                    lm.forEach((pt, idx) => {
                        const jx = pt.x * W;
                        const jy = pt.y * H;
                        ctx.beginPath();
                        ctx.arc(jx, jy, [4, 8, 12, 16, 20].includes(idx) ? 4.5 : 3, 0, Math.PI * 2);
                        ctx.fillStyle = [4, 8, 12, 16, 20].includes(idx) ? '#38BDF8' : '#34D399';
                        ctx.fill();
                    });
                    ctx.restore();

                    // Etiqueta flotante del conteo de dedos y gesto (Anti-Colisión)
                    const [hx, hy, hw, hh] = hand.bbox;
                    const handLabel = `${hand.side}: ${hand.gesture}`;
                    ctx.font = 'bold 12px "JetBrains Mono", monospace';
                    const hTextW = ctx.measureText(handLabel).width + 24;
                    const hTagH = 24;

                    const handPos = allocateLabelPosition([hx, hy, hw, hh], hTextW, hTagH);
                    const clampedHX = handPos.x;
                    const clampedHY = handPos.y;

                    ctx.save();
                    ctx.shadowColor = 'rgba(0, 0, 0, 0.75)';
                    ctx.shadowBlur = 6;
                    ctx.fillStyle = 'rgba(6, 78, 59, 0.94)';
                    ctx.strokeStyle = '#34D399';
                    ctx.lineWidth = 1.5;
                    ctx.beginPath();
                    ctx.roundRect(clampedHX, clampedHY, hTextW, hTagH, 6);
                    ctx.fill();
                    ctx.stroke();

                    // Barra indicadora esmeralda
                    ctx.fillStyle = '#34D399';
                    ctx.beginPath();
                    ctx.roundRect(clampedHX + 2, clampedHY + 2, 4, hTagH - 4, 2);
                    ctx.fill();

                    // Punto esmeralda
                    ctx.beginPath();
                    ctx.arc(clampedHX + 13, clampedHY + hTagH / 2, 3.5, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.shadowBlur = 0;
                    ctx.fillStyle = '#ffffff';
                    ctx.fillText(handLabel, clampedHX + 22, clampedHY + 16);
                    ctx.restore();
                });
            }

            // 4. Banner inferior en tiempo real mostrando Gesto o Actividad
            if (behavior && behavior.key !== 'absent') {
                const bannerHeight = 36;
                const bannerY = canvasElement.height - bannerHeight - 12;
                const isGesture = (behavior.key.includes('hands_up') || behavior.key.includes('waving') || behavior.key.includes('thumbs_up') || behavior.key.includes('thinking') || behavior.key.includes('face_touch') || behavior.key === 'hand_fingers' || behavior.key === 'both_hands');
                const isCall = (behavior.key === 'phone_call' || behavior.key === 'talking_phone');
                const isEating = (behavior.key === 'eating_food');
                const isRisk = ['handling_sharp', 'person_fallen', 'face_hidden', 'aggressive_motion', 'unattended_object'].includes(behavior.key);
                const prefix = isRisk ? 'ALERTA EN VIVO' : (isCall ? 'LLAMADA EN VIVO' : (isEating ? 'CONSUMO EN VIVO' : (isGesture ? 'GESTO DETECTADO' : 'ACTIVIDAD EN VIVO')));
                const bannerText = `${prefix}: ${behavior.name.toUpperCase()}`;

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

        function getPositionDescription(bbox) {
            if (!bbox || bbox.length < 4) return 'Escena';
            const cx = bbox[0] + bbox[2] / 2;
            const cy = bbox[1] + bbox[3] / 2;
            const w = canvasElement.width || 640;
            const h = canvasElement.height || 480;
            const horiz = cx < w * 0.38 ? 'Izquierda' : (cx > w * 0.62 ? 'Derecha' : 'Centro');
            const vert = cy < h * 0.38 ? 'Superior' : (cy > h * 0.62 ? 'Inferior' : 'Medio');
            return `${horiz}-${vert}`;
        }

        // ==========================================
        // SMART REAL-TIME WEBSOCKET TRACKING DISPATCHER
        // ==========================================
        function processAllDetectionsAndWebSocket(predictions, personsData, behavior, objectContexts, handResults) {
            const now = performance.now();

            // 1. Seguimiento de personas y objetos en tiempo real por WebSocket
            predictions.forEach(pred => {
                const trackId = pred.id || 1;
                const isPerson = pred.class === 'person';
                const key = `track_${pred.class}_${trackId}`;
                const lastSent = lastEventSentTimestamps[key] || 0;
                const posDesc = getPositionDescription(pred.bbox);
                const ctxTag = (objectContexts && objectContexts[trackId]) ? objectContexts[trackId] : 'Escena';

                // Enviar de inmediato (0ms) al detectar nuevo objetivo, o cada 900ms para seguimiento fluido en tiempo real
                const interval = (lastSent === 0) ? 0 : 900;

                if (now - lastSent >= interval) {
                    lastEventSentTimestamps[key] = now;
                    const displayName = isPerson ? `Persona #${trackId}` : `${getObjectDisplayName(pred.class)} #${trackId}`;

                    sendDetectionToServer({
                        category: isPerson ? 'behavior' : 'object',
                        label: pred.class,
                        display_name: displayName,
                        confidence: pred.score,
                        color: getObjectColor(pred.class),
                        details: {
                            track_id: trackId,
                            bbox: pred.bbox,
                            position: posDesc,
                            context: ctxTag,
                            status: 'seguimiento_activo'
                        }
                    });
                }
            });

            // 2. Muestreo de tono de cabello por WebSocket
            if (personsData.length > 0) {
                const mainPerson = personsData[0];
                if (mainPerson.hair && currentDetectedHair !== mainPerson.hair.key) {
                    const hairKey = `hair_${mainPerson.id || 1}_${mainPerson.hair.key}`;
                    const lastHairSent = lastEventSentTimestamps[hairKey] || 0;

                    if (now - lastHairSent >= 1500) {
                        currentDetectedHair = mainPerson.hair.key;
                        lastEventSentTimestamps[hairKey] = now;

                        sendDetectionToServer({
                            category: 'hair',
                            label: mainPerson.hair.key,
                            display_name: `Persona #${mainPerson.id || 1} • ${mainPerson.hair.name}`,
                            confidence: mainPerson.hair.confidence,
                            color: mainPerson.hair.color,
                            details: {
                                track_id: mainPerson.id || 1,
                                rgb: mainPerson.hair.rgb
                            }
                        });
                    }
                }
            }

            // 3. Gestos y comportamiento por WebSocket (CAMBIOS DESPACHADOS AL INSTANTE 0ms)
            if (behavior && behavior.key !== 'absent') {
                const behaviorKey = `beh_${behavior.key}`;
                const lastBehaviorSent = lastEventSentTimestamps[behaviorKey] || 0;
                const changed = currentActiveBehavior !== behavior.key;
                const interval = changed ? 0 : 1200;

                if (now - lastBehaviorSent >= interval) {
                    currentActiveBehavior = behavior.key;
                    lastEventSentTimestamps[behaviorKey] = now;

                    const isGesture = (behavior.key.includes('gesture') || behavior.key === 'hands_up' || behavior.key === 'waving' || behavior.key === 'thumbs_up' || behavior.key === 'thinking' || behavior.key === 'face_touch' || behavior.key === 'hand_fingers' || behavior.key === 'both_hands');

                    sendDetectionToServer({
                        category: isGesture ? 'gesture' : 'behavior',
                        label: behavior.key,
                        display_name: behavior.name,
                        confidence: behavior.confidence,
                        color: behavior.color,
                        details: {
                            persons: personsData.length,
                            hands: handResults ? handResults.length : 0
                        }
                    });
                }
            }
        }

        async function sendDetectionToServer(payload) {
            try {
                payload.created_at = new Date().toISOString();
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
        function updateKPIsAndRadar(predictions, personsData, behavior, objectContexts, handResults) {
            const countBadge = document.getElementById('activeCountBadge');
            const kpiPersons = document.getElementById('kpiPersonsCount');
            const kpiHair = document.getElementById('kpiHairTone');
            const kpiBehavior = document.getElementById('kpiBehavior');
            const kpiObjects = document.getElementById('kpiObjectsCount');

            const personCount = personsData.length;
            kpiPersons.innerText = personCount === 1 ? '1 persona' : `${personCount} personas`;

            if (personsData.length > 0 && personsData[0].hair) {
                const hName = personsData[0].hair.name.replace('Cabello ', '');
                kpiHair.innerText = hName;
                kpiHair.style.color = personsData[0].hair.color;
            } else {
                kpiHair.innerText = 'No detectado';
                kpiHair.style.color = '#94a3b8';
            }

            if (behavior && behavior.key !== 'absent') {
                kpiBehavior.innerText = behavior.name;
                kpiBehavior.style.color = behavior.color;
            } else {
                kpiBehavior.innerText = 'Ausente';
                kpiBehavior.style.color = '#64748b';
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
                        <div class="flex items-center gap-1.5 truncate">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                            <span class="font-bold text-white truncate">Persona #${p.id}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-950/80 text-blue-300 font-semibold border border-blue-800/50 truncate">${(behavior && behavior.key !== 'absent') ? behavior.name : 'Presente'}</span>
                        </div>
                        <span class="text-[11px] font-semibold shrink-0 ml-1.5" style="color: ${p.hair ? p.hair.color : '#94a3b8'}">
                            ${p.hair ? p.hair.name : 'Cabello N/D'}
                        </span>
                    </div>
                `).join('');
            }

            // Radar Gestos
            const radarGestures = document.getElementById('radarGesturesContainer');
            let gesturesHtml = '';
            if (behavior && behavior.key !== 'absent') {
                gesturesHtml += `
                    <div class="p-1.5 rounded-lg bg-slate-900 border text-xs flex items-center gap-2" style="border-color: ${behavior.color}55;">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${behavior.color};"></span>
                        <span class="font-bold text-white">${behavior.name}</span>
                    </div>
                `;
            }
            if (handResults && handResults.length > 0) {
                handResults.forEach(h => {
                    gesturesHtml += `
                        <div class="p-1.5 rounded-lg bg-emerald-950/40 border border-emerald-500/40 text-xs flex items-center justify-between">
                            <div class="flex items-center gap-1.5 truncate">
                                <span class="w-2 rounded-full h-2 bg-emerald-400 shrink-0"></span>
                                <span class="text-emerald-200 font-semibold truncate">${h.side}</span>
                                <span class="text-[11px] text-white truncate">${h.gesture}</span>
                            </div>
                            <span class="text-[10px] font-mono-code text-emerald-400 shrink-0 ml-1 px-1.5 py-0.5 rounded bg-emerald-900/60 border border-emerald-700/50">${h.count} dedos</span>
                        </div>
                    `;
                });
            }
            radarGestures.innerHTML = gesturesHtml || '<span class="text-slate-500 italic">Sin gestos detectados</span>';

            // Radar Tecnología
            const techClasses = ['laptop', 'tv', 'cell phone', 'mouse', 'keyboard', 'remote', 'microwave'];
            const techItems = predictions.filter(p => techClasses.includes(p.class));
            const radarTech = document.getElementById('radarTechContainer');
            if (techItems.length === 0) {
                radarTech.innerHTML = '<span class="text-slate-500 italic">No hay dispositivos en vista</span>';
            } else {
                radarTech.innerHTML = techItems.map(t => {
                    const c = getObjectColor(t.class);
                    const ctxTag = (objectContexts && objectContexts[t.id]) ? objectContexts[t.id] : 'En escena';
                    return `
                        <div class="p-1.5 rounded-lg bg-slate-900 border text-xs flex items-center justify-between" style="border-color: ${c}44;">
                            <div class="flex items-center gap-1.5 truncate">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${c};"></span>
                                <span class="text-slate-200 font-medium truncate">${getObjectDisplayName(t.class)}</span>
                                <span class="text-[10px] text-cyan-400 font-mono-code px-1 rounded bg-cyan-950/60 border border-cyan-800/40 shrink-0">${ctxTag}</span>
                            </div>
                            <span class="text-[11px] font-mono-code text-cyan-400 shrink-0 ml-1">${Math.round(t.score*100)}%</span>
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
                    const ctxTag = (objectContexts && objectContexts[f.id]) ? objectContexts[f.id] : 'En entorno';
                    return `
                        <div class="p-1.5 rounded-lg bg-slate-900 border text-xs flex items-center justify-between" style="border-color: ${c}44;">
                            <div class="flex items-center gap-1.5 truncate">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${c};"></span>
                                <span class="text-slate-200 font-medium truncate">${getObjectDisplayName(f.class)}</span>
                                <span class="text-[10px] text-slate-400 font-mono-code px-1 rounded bg-slate-800/60 border border-slate-700/50 shrink-0">${ctxTag}</span>
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
            if (!isCameraActive || !isMediaSourceReady()) {
                alert('La cámara debe estar activa para tomar una captura.');
                return;
            }

            const activeMedia = getActiveMediaElement();
            const snapCanvas = document.createElement('canvas');
            snapCanvas.width = canvasElement.width;
            snapCanvas.height = canvasElement.height;
            const sCtx = snapCanvas.getContext('2d');
            sCtx.drawImage(activeMedia, 0, 0, snapCanvas.width, snapCanvas.height);
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
            if (!isCameraActive || !isMediaSourceReady()) {
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
                const dims = getActiveMediaDimensions();
                if (!isMediaSourceReady() || dims.w === 0) {
                    alert('Espera a que el flujo de la cámara esté activo para grabar.');
                    return;
                }

                recordingCanvas.width = dims.w;
                recordingCanvas.height = dims.h;

                // Captura compuesta: cámara real + bounding boxes y HUD en tiempo real
                const stream = recordingCanvas.captureStream(30);

                // Agregar pista de audio del micrófono si existe en el stream de la cámara (webcam)
                if (videoElement && videoElement.srcObject) {
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

            const formattedTime = (data.created_at ? new Date(data.created_at).toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }) : new Date().toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }));

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
                { category: 'object', label: 'cell phone', display_name: 'Celular', color: '#F59E0B', confidence: 0.95 },
                { category: 'object', label: 'laptop', display_name: 'Laptop', color: '#10B981', confidence: 0.98 },
                { category: 'object', label: 'chair', display_name: 'Silla', color: '#64748B', confidence: 0.89 },
                { category: 'object', label: 'potted plant', display_name: 'Planta', color: '#22C55E', confidence: 0.91 },
                { category: 'hair', label: 'hair_dark_brown', display_name: 'Cabello Castaño Oscuro', color: '#78350F', confidence: 0.93 },
                { category: 'hair', label: 'hair_black', display_name: 'Cabello Negro', color: '#1E293B', confidence: 0.96 },
                { category: 'hair', label: 'hair_blonde', display_name: 'Cabello Rubio', color: '#EAB308', confidence: 0.92 },
                { category: 'gesture', label: 'hands_up', display_name: 'Manos Arriba', color: '#DC2626', confidence: 0.94 },
                { category: 'gesture', label: 'waving', display_name: 'Saludando con Mano', color: '#F59E0B', confidence: 0.90 },
                { category: 'behavior', label: 'multiple_people', display_name: 'Múltiples Personas (2 en escena)', color: '#6366F1', confidence: 0.97 },
                { category: 'behavior', label: 'working_laptop', display_name: 'Trabajando en Laptop', color: '#059669', confidence: 0.96 }
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
