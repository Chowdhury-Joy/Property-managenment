<x-layouts.public title="Blog | Pillar Property Management" metaDescription="Read our latest insights on property management, real estate investing, and landlord tips.">
    <section class="py-24 bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-6">Our Blog</h1>
            <p class="text-xl text-gray-500 leading-relaxed max-w-2xl mx-auto">
                Insights, tips, and news on modern property management.
            </p>
        </div>
    </section>

    <section class="py-24 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    <article class="bg-white border border-gray-100 rounded-3xl overflow-hidden hover:shadow-lg transition-shadow group flex flex-col">
                        <a href="{{ route('blog.show', $post->slug) }}" class="block h-56 bg-gray-200 relative overflow-hidden">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3L22 4" /></svg>
                                </div>
                            @endif
                        </a>
                        <div class="p-8 flex flex-col flex-grow">
                            <div class="flex items-center text-xs text-gray-400 mb-4 font-medium tracking-wide uppercase">
                                <time datetime="{{ $post->published_at->format('Y-m-d') }}">{{ $post->published_at->format('M j, Y') }}</time>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                                <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-[var(--brand-primary)] transition-colors">{{ $post->title }}</a>
                            </h2>
                            <p class="text-gray-500 mb-6 line-clamp-3 text-sm leading-relaxed flex-grow">
                                {{ $post->excerpt }}
                            </p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center text-[var(--brand-primary)] font-semibold hover:underline">
                                Read Article <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 text-center py-20 bg-white border border-gray-100 rounded-3xl">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3L22 4" /></svg>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">No Posts Yet</h3>
                        <p class="text-gray-500">Check back later for new articles and insights.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.public>
