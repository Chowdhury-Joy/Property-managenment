<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Property;

class PageController extends Controller
{
    public function home()
    {
        $featuredProperties = Property::where('status', 'active')->limit(3)->get();
        $recentPosts = Post::where('is_published', true)->latest('published_at')->limit(3)->get();

        return view('pages.home', compact('featuredProperties', 'recentPosts'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        return view('pages.services');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function properties()
    {
        $properties = Property::where('status', 'active')->get();

        return view('pages.properties', compact('properties'));
    }

    public function blog()
    {
        $posts = Post::where('is_published', true)->latest('published_at')->get();

        return view('pages.blog.index', compact('posts'));
    }

    public function blogPost(string $slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();

        return view('pages.blog.show', compact('post'));
    }

    public function faq()
    {
        return view('pages.faq');
    }
}
