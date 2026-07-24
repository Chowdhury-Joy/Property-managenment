<x-layouts.public title="Properties | Pillar Property Management" metaDescription="View our currently available active properties.">
    <section class="py-24 bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-6">Available Properties</h1>
            <p class="text-xl text-gray-500 leading-relaxed max-w-2xl mx-auto">
                Browse our current active portfolio. We manage single-family, multi-unit, and commercial properties.
            </p>
        </div>
    </section>

    <section class="py-24 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($properties as $property)
                    <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden hover:shadow-lg transition-shadow group">
                        <div class="h-56 bg-gray-200 w-full relative">
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $property->name }}</h3>
                                    <p class="text-gray-500 text-sm mt-1">{{ $property->city }}, {{ $property->state }}</p>
                                </div>
                                <span class="px-3 py-1 bg-gray-50 border border-gray-200 text-xs font-semibold rounded-full text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $property->type)) }}
                                </span>
                            </div>
                            <p class="text-gray-500 text-sm mb-6">{{ $property->address }} • {{ $property->zip }}</p>
                            
                            <a href="/contact" class="block text-center w-full py-3 border border-[var(--brand-primary)] text-[var(--brand-primary)] font-semibold rounded-full hover:bg-[var(--brand-primary)] hover:text-white transition-colors">
                                Inquire Now
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-20 bg-white border border-gray-100 rounded-3xl">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">No Properties Found</h3>
                        <p class="text-gray-500">There are no active properties at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.public>
