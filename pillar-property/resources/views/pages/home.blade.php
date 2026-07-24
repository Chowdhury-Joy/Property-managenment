<x-layouts.public title="Home | Pillar Property Management" metaDescription="Modern, transparent, and minimal property management designed for the future of real estate investing.">
    
    <!-- Hero Section -->
    <section class="relative bg-white overflow-hidden pt-24 pb-32">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">
                Property Management, <br class="hidden md:block"/> Simplified.
            </h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                Protecting your investment and providing a quality home for your tenants with modern, transparent, and minimal property management.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/properties" class="px-8 py-4 bg-[var(--brand-primary)] text-white font-semibold rounded-full hover:opacity-90 transition-opacity w-full sm:w-auto text-lg">
                    View Properties
                </a>
                <a href="/contact" class="px-8 py-4 bg-white text-gray-900 border border-gray-200 font-semibold rounded-full hover:border-gray-900 hover:bg-gray-50 transition-all w-full sm:w-auto text-lg">
                    Get in Touch
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Why Choose Pillar?</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-blue-50 text-[var(--brand-primary)] rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Fast Maintenance</h3>
                    <p class="text-gray-500 leading-relaxed">24/7 maintenance request handling to keep your tenants happy and your property in top condition.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Transparent Pricing</h3>
                    <p class="text-gray-500 leading-relaxed">No hidden fees. Just simple, flat-rate pricing so you always know what to expect.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Secure Portals</h3>
                    <p class="text-gray-500 leading-relaxed">Dedicated, secure portals for both owners and tenants to manage payments, requests, and documents.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Properties Snippet -->
    <section class="py-24 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Featured Properties</h2>
                <a href="/properties" class="text-[var(--brand-primary)] font-semibold hover:underline">View All &rarr;</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($featuredProperties as $property)
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition-shadow bg-gray-50 group cursor-pointer">
                        <div class="h-48 bg-gray-200 w-full relative">
                            <!-- Placeholder for Property Image -->
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400 group-hover:scale-105 transition-transform">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $property->name }}</h3>
                            <p class="text-gray-500 mb-4">{{ $property->city }}, {{ $property->state }}</p>
                            <span class="inline-block px-3 py-1 bg-white border border-gray-200 text-xs font-semibold rounded-full text-gray-600">
                                {{ ucfirst(str_replace('_', ' ', $property->type)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-gray-500">
                        No properties available at the moment.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-gray-900 text-white">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <svg class="w-12 h-12 text-gray-700 mx-auto mb-8" fill="currentColor" viewBox="0 0 32 32"><path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"/></svg>
            <h2 class="text-3xl md:text-4xl font-semibold leading-tight mb-8">
                "Pillar Property Management completely transformed how I handle my real estate portfolio. I finally have peace of mind knowing my investments are in good hands."
            </h2>
            <p class="text-gray-400 font-medium">— Sarah Jenkins, Property Owner</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-24 bg-white border-t border-gray-100">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Our Services</h2>
                <a href="/services" class="text-[var(--brand-primary)] font-semibold hover:underline">Learn More &rarr;</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-10 rounded-3xl border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">For Owners</h3>
                    <p class="text-gray-600 mb-6">Comprehensive management from tenant screening to financial reporting.</p>
                    <a href="/services" class="text-[var(--brand-primary)] font-semibold hover:underline">View Owner Services &rarr;</a>
                </div>
                <div class="bg-gray-50 p-10 rounded-3xl border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">For Tenants</h3>
                    <p class="text-gray-600 mb-6">Easy online payments, 24/7 maintenance requests, and a dedicated portal.</p>
                    <a href="/services" class="text-[var(--brand-primary)] font-semibold hover:underline">View Tenant Services &rarr;</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="py-24 bg-gray-50 border-t border-gray-100">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Latest News</h2>
                <a href="/blog" class="text-[var(--brand-primary)] font-semibold hover:underline">Read Blog &rarr;</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($recentPosts as $post)
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-md transition-shadow">
                        <time class="text-xs text-gray-400 font-medium tracking-wide uppercase mb-2 block">{{ $post->published_at->format('M j, Y') }}</time>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2"><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                        <p class="text-gray-500 text-sm line-clamp-3 mb-4">{{ $post->excerpt }}</p>
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-[var(--brand-primary)] text-sm font-semibold hover:underline">Read More &rarr;</a>
                    </div>
                @empty
                    <div class="col-span-3 text-gray-500 text-center py-8 bg-white rounded-2xl border border-gray-100">No recent posts.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-24 bg-white border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight mb-6">Got Questions?</h2>
            <p class="text-gray-500 mb-8 text-lg">We've compiled answers to the most common questions from our owners and tenants.</p>
            <a href="/faq" class="inline-block px-8 py-4 bg-gray-900 text-white font-semibold rounded-full hover:bg-gray-800 transition-colors">View FAQ</a>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24 bg-[var(--brand-primary)] text-white text-center">
        <div class="max-w-4xl mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-6">Ready to get started?</h2>
            <p class="text-white/80 text-lg mb-10 max-w-2xl mx-auto">Whether you're looking for a new home or need professional management for your investments, we're here to help.</p>
            <a href="/contact" class="inline-block px-8 py-4 bg-white text-[var(--brand-primary)] font-bold rounded-full hover:bg-gray-50 transition-colors shadow-sm">Contact Us Today</a>
        </div>
    </section>
</x-layouts.public>
