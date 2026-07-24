<x-layouts.public 
    title="{{ $post->title }} | Pillar Property Management" 
    metaDescription="{{ $post->excerpt }}"
    ogImage="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : null }}"
    ogType="article">
    
    <article class="bg-white pb-24">
        <!-- Post Header -->
        <header class="pt-24 pb-16 max-w-4xl mx-auto px-6 text-center">
            <div class="flex items-center justify-center text-sm text-gray-400 mb-6 font-medium tracking-widest uppercase">
                <time datetime="{{ $post->published_at->format('Y-m-d') }}">{{ $post->published_at->format('F j, Y') }}</time>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">
                {{ $post->title }}
            </h1>
            @if($post->excerpt)
            <p class="text-xl text-gray-500 leading-relaxed max-w-2xl mx-auto">
                {{ $post->excerpt }}
            </p>
            @endif
        </header>

        <!-- Featured Image -->
        @if($post->featured_image)
        <div class="max-w-6xl mx-auto px-6 mb-16">
            <div class="rounded-3xl overflow-hidden shadow-sm border border-gray-100">
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-auto object-cover max-h-[600px]">
            </div>
        </div>
        @endif

        <!-- Post Content -->
        <div class="max-w-3xl mx-auto px-6">
            <div class="prose prose-lg prose-blue max-w-none prose-headings:font-bold prose-headings:tracking-tight prose-headings:text-gray-900 prose-p:text-gray-600 prose-p:leading-relaxed prose-a:text-[var(--brand-primary)] prose-img:rounded-xl">
                {!! $post->content !!}
            </div>
            
            <!-- Share & Back -->
            <div class="mt-16 pt-8 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('blog.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-900 font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to Blog
                </a>
            </div>
        </div>
    </article>
</x-layouts.public>
