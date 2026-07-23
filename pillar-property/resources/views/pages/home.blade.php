<x-layouts.public title="Home">
    <!-- Hero Section -->
    <section class="bg-[var(--brand-primary)] text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Property Management You Can Trust.</h1>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">We protect your investment, maximize your returns, and treat your tenants with respect.</p>
            <a href="#rental-analysis" class="inline-block bg-white text-[var(--brand-primary)] px-8 py-3 rounded-md font-bold hover:bg-gray-100 transition">
                Get Your Free Rental Analysis
            </a>
        </div>
    </section>

    <!-- Value Prop Section -->
    <section class="py-16 max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="w-12 h-12 bg-blue-100 text-[var(--brand-primary)] rounded-full flex items-center justify-center mb-4 font-bold text-xl">1</div>
                <h3 class="text-xl font-bold mb-2">Maximize Your ROI</h3>
                <p class="text-gray-600">Strategic pricing and rigorous tenant screening ensure your property generates consistent, reliable income.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="w-12 h-12 bg-blue-100 text-[var(--brand-primary)] rounded-full flex items-center justify-center mb-4 font-bold text-xl">2</div>
                <h3 class="text-xl font-bold mb-2">24/7 Maintenance</h3>
                <p class="text-gray-600">We handle the midnight calls. Our vetted vendor network keeps your property in pristine condition without the headache.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="w-12 h-12 bg-blue-100 text-[var(--brand-primary)] rounded-full flex items-center justify-center mb-4 font-bold text-xl">3</div>
                <h3 class="text-xl font-bold mb-2">Transparent Reporting</h3>
                <p class="text-gray-600">Access your owner portal anytime to see real-time financials, occupancy rates, and property updates.</p>
            </div>
        </div>
    </section>

    <!-- Lead Capture Form Section -->
    <section id="rental-analysis" class="bg-gray-100 py-16">
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Request a Free Rental Analysis</h2>
                <p class="text-gray-600 mt-2">Thinking about switching managers or buying an investment property? Let us tell you what it's really worth.</p>
            </div>
            
            @livewire('request-rental-analysis')
            
        </div>
    </section>
</x-layouts.public>
