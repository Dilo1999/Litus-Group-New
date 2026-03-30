<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Services\SeoService;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(SeoService $seo): View
    {
        $seo->applyForPage('blogs.index', [
            'meta_title' => 'Blog – Insights on Sourcing & Supply Chain',
            'meta_description' => 'Read insights on sourcing, supply chain, logistics, and procurement from Al Zaha. Industry updates and best practices.',
        ]);

        $posts = BlogPost::published()
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.blogs.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();

        $title = $post->meta_title ?: ($post->title.' – Al Zaha');
        $textParts = array_filter(array_column($post->content ?? [], 'text'));
        $description = $post->meta_description ?: ($post->intro ?? \Illuminate\Support\Str::limit(strip_tags(implode(' ', $textParts)), 155));
        $image = $post->og_image_url ?? asset('images/content/cta2.jpg');
        $url = route('blogs.show', $post->slug);

        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description ?: 'Read this article on the Al Zaha blog.');
        SEOMeta::setCanonical($url);
        if ($post->published_at) {
            SEOMeta::addMeta('article:published_time', $post->published_at->toIso8601String(), 'property');
        }

        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description ?: 'Read this article on the Al Zaha blog.');
        OpenGraph::setUrl($url);
        OpenGraph::addProperty('type', 'article');
        OpenGraph::addImage($image);

        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description ?: 'Read this article on the Al Zaha blog.');
        TwitterCard::setImage($image);

        JsonLd::setType('Article');
        JsonLd::setTitle($post->title);
        JsonLd::setDescription($description ?: $post->title);
        JsonLd::setUrl($url);
        JsonLd::addImage($image);
        if ($post->published_at) {
            JsonLd::addValue('datePublished', $post->published_at->toIso8601String());
        }
        if ($post->author) {
            JsonLd::addValue('author', ['@type' => 'Person', 'name' => $post->author]);
        }

        return view('pages.blogs.show', [
            'post' => $post,
        ]);
    }
}
