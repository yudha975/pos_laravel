<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php
        $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $appName = $appSettings['company_name'] ?? config('app.name', 'POS');
        $favicon = $appSettings['company_favicon'] ?? null;
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $appName }}</title>
        @if($favicon)
            <link rel="icon" type="image/png" href="{{ $favicon }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <!-- Using Inter for a more modern look -->
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Print Styles -->
        <style>
            @media print {
                body {
                    background-color: white !important;
                }
                .print-hidden {
                    display: none !important;
                }
                .print-block {
                    display: block !important;
                }
                main {
                    padding: 0 !important;
                    margin: 0 !important;
                    overflow: visible !important;
                }
                .shadow-sm, .shadow-lg, .shadow-xl {
                    box-shadow: none !important;
                }
                .border, .border-gray-100, .border-gray-200 {
                    border-color: #e5e7eb !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 flex h-screen overflow-hidden print:h-auto print:overflow-visible" x-data="{ sidebarOpen: false }">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-black bg-opacity-50 sm:hidden transition-opacity print-hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar Layout -->
        <div class="print-hidden">
            @include('layouts.sidebar')
        </div>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden print:h-auto print:overflow-visible">
            
            <!-- Top Navigation Bar -->
            <div class="print-hidden">
                @include('layouts.topbar')
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8 print:bg-white print:p-0 print:overflow-visible">
                <!-- Page Heading -->
                @isset($header)
                    <header class="mb-6 print-hidden">
                        {{ $header }}
                    </header>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
