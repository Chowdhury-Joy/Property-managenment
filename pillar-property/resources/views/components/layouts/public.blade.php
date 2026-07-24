<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? \App\Models\Setting::get('company_name', 'Pillar Property Management') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Pillar Property Management provides top-tier property management services for owners and tenants.' }}">
    
    <!-- SEO & Open Graph -->
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? \App\Models\Setting::get('company_name', 'Pillar Property Management') }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Top-tier property management services.' }}">
    @php $logo = \App\Models\Setting::get('logo'); @endphp
    {{-- No bundled default-og.png ships with the app, so only render an image tag when one is actually available. --}}
    @if($ogImage ?? ($logo ? asset('storage/' . $logo) : null))
        <meta property="og:image" content="{{ $ogImage ?? asset('storage/' . $logo) }}">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? \App\Models\Setting::get('company_name', 'Pillar Property Management') }}">
    <meta property="twitter:description" content="{{ $metaDescription ?? 'Top-tier property management services.' }}">
    @if($ogImage ?? ($logo ? asset('storage/' . $logo) : null))
        <meta property="twitter:image" content="{{ $ogImage ?? asset('storage/' . $logo) }}">
    @endif

    <!-- Favicon: public/favicon.ico ships with the app; favicon.png does not exist, so fall back to the .ico rather than a 404. -->
    @php $favicon = \App\Models\Setting::get('favicon'); @endphp
    <link rel="icon" href="{{ $favicon ? asset('storage/' . $favicon) : asset('favicon.ico') }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --brand-primary: {{ \App\Models\Setting::get('primary_color', '#1e3a8a') }};
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased flex flex-col min-h-screen selection:bg-[var(--brand-primary)] selection:text-white">

    <!-- Global Header (Section 13) -->
    <header class="border-b border-gray-100 bg-white/80 backdrop-blur-md sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <nav class="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="h-8 w-auto">
                @else
                    <span class="text-xl font-bold tracking-tight text-gray-900">
                        {{ \App\Models\Setting::get('company_name', 'Pillar') }}
                    </span>
                @endif
            </a>
            
            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-500">
                <a href="/" class="hover:text-gray-900 transition-colors">Home</a>
                <a href="/about" class="hover:text-gray-900 transition-colors">About</a>
                <a href="/services" class="hover:text-gray-900 transition-colors">Services</a>
                <a href="/properties" class="hover:text-gray-900 transition-colors">Properties</a>
                <a href="/blog" class="hover:text-gray-900 transition-colors">Blog</a>
                <a href="/faq" class="hover:text-gray-900 transition-colors">FAQ</a>
                <a href="/contact" class="hover:text-gray-900 transition-colors">Contact</a>
            </div>

            <div class="hidden md:flex items-center space-x-4 text-sm font-medium">
                <a href="/tenant/login" class="text-gray-500 hover:text-gray-900 transition-colors">Tenants</a>
                <a href="/owner/login" class="text-gray-500 hover:text-gray-900 transition-colors">Owners</a>
                <a href="/admin" class="bg-[var(--brand-primary)] text-white px-5 py-2.5 rounded-full hover:opacity-90 transition-opacity">
                    Portal
                </a>
            </div>
            
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-400 hover:text-gray-900">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="mobileMenuOpen" style="display:none;" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </nav>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" style="display:none;" class="md:hidden border-t border-gray-100 bg-white">
            <div class="flex flex-col py-4 px-6 space-y-4 text-base font-medium text-gray-600">
                <a href="/" class="hover:text-gray-900">Home</a>
                <a href="/about" class="hover:text-gray-900">About</a>
                <a href="/services" class="hover:text-gray-900">Services</a>
                <a href="/properties" class="hover:text-gray-900">Properties</a>
                <a href="/blog" class="hover:text-gray-900">Blog</a>
                <a href="/faq" class="hover:text-gray-900">FAQ</a>
                <a href="/contact" class="hover:text-gray-900">Contact</a>
                <hr class="border-gray-100">
                <a href="/tenant/login" class="hover:text-gray-900">Tenant Portal</a>
                <a href="/owner/login" class="hover:text-gray-900">Owner Portal</a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        {{ $slot ?? '' }}
    </main>

    <!-- Global Footer (Section 14) -->
    <footer class="bg-gray-50 border-t border-gray-100 mt-24">
        <div class="max-w-6xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-4 gap-12 text-sm text-gray-500">
            <div class="md:col-span-2">
                <span class="text-lg font-bold text-gray-900 tracking-tight block mb-4">{{ \App\Models\Setting::get('company_name', 'Pillar') }}</span>
                <p class="max-w-sm leading-relaxed">Modern, transparent, and minimal property management designed for the future of real estate investing.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4">Company</h4>
                <ul class="space-y-3">
                    <li><a href="/about" class="hover:text-gray-900 transition-colors">About Us</a></li>
                    <li><a href="/services" class="hover:text-gray-900 transition-colors">Services</a></li>
                    <li><a href="/properties" class="hover:text-gray-900 transition-colors">Properties</a></li>
                    <li><a href="/blog" class="hover:text-gray-900 transition-colors">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4">Support</h4>
                <ul class="space-y-3">
                    <li><a href="/faq" class="hover:text-gray-900 transition-colors">FAQ</a></li>
                    <li><a href="/contact" class="hover:text-gray-900 transition-colors">Contact</a></li>
                    <li><a href="/tenant/login" class="hover:text-gray-900 transition-colors">Tenant Login</a></li>
                    <li><a href="/owner/login" class="hover:text-gray-900 transition-colors">Owner Login</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-6xl mx-auto px-6 py-6 border-t border-gray-200 text-xs text-gray-400 flex flex-col md:flex-row justify-between items-center">
            <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('company_name', 'Pillar Property Management') }}. All rights reserved.</p>
            <div class="flex space-x-4 mt-4 md:mt-0">
                <a href="#" class="hover:text-gray-900 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
