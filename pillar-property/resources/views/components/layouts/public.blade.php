<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('company_name', 'Pillar Property Management') }}</title>
    
    <!-- Dynamic Favicon -->
    @php $favicon = \App\Models\Setting::get('favicon'); @endphp
    <link rel="icon" type="image/png" href="{{ $favicon ? asset('storage/' . $favicon) : asset('favicon.png') }}">

    <!-- Tailwind CSS CDN Fallback + Vite Assets -->
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Dynamic Brand Color CSS Variable -->
    <style>
        :root {
            --brand-primary: {{ \App\Models\Setting::get('primary_color', '#1e3a8a') }};
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                @php $logo = \App\Models\Setting::get('logo'); @endphp
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="h-10 w-auto">
                @else
                    <span class="text-2xl font-bold text-[var(--brand-primary)]">
                        {{ \App\Models\Setting::get('company_name', 'Pillar Property Management') }}
                    </span>
                @endif
            </a>
            <div class="hidden md:flex space-x-8 font-medium text-gray-600">
                <a href="/" class="hover:text-[var(--brand-primary)]">Home</a>
                <a href="/about" class="hover:text-[var(--brand-primary)]">About</a>
                <a href="/services" class="hover:text-[var(--brand-primary)]">Services</a>
                <a href="/contact" class="hover:text-[var(--brand-primary)]">Contact</a>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/owner/login" class="text-xs font-semibold text-[var(--brand-primary)] border border-[var(--brand-primary)] px-3 py-1.5 rounded-md hover:bg-blue-50">
                    Owner Login
                </a>
                <a href="/tenant/login" class="text-xs font-semibold text-emerald-700 border border-emerald-700 px-3 py-1.5 rounded-md hover:bg-emerald-50">
                    Tenant Portal
                </a>
                <a href="/admin" class="hidden md:inline-block bg-[var(--brand-primary)] text-white px-4 py-2 rounded-md text-sm font-semibold hover:opacity-90 transition">
                    Staff Portal
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-white text-lg font-bold mb-4">{{ \App\Models\Setting::get('company_name', 'Pillar Property Management') }}</h3>
                <p class="text-sm">Protecting your investment and providing a quality home for your tenants.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/services" class="hover:text-white">Our Services</a></li>
                    <li><a href="/contact" class="hover:text-white">Contact Us</a></li>
                    <li><a href="/owner/login" class="hover:text-white">Owner Portal</a></li>
                    <li><a href="/tenant/login" class="hover:text-white">Tenant Portal</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Contact</h4>
                <p class="text-sm">{{ \App\Models\Setting::get('contact_phone', '(555) 123-4567') }}</p>
                <p class="text-sm">{{ \App\Models\Setting::get('contact_email', 'hello@pillarpm.demo') }}</p>
            </div>
        </div>
        <div class="border-t border-gray-800 py-6 text-center text-xs">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('company_name', 'Pillar Property Management') }}. All rights reserved.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
