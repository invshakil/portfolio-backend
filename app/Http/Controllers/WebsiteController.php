<?php

namespace App\Http\Controllers;
;

use App\Models\Category;
use App\Models\NewsLetter;
use App\Models\Page;
use App\Models\PageLink;
use App\Repositories\Article\ArticleRepository;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Share;
use Str;
use function abort;
use function asset;
use function back;
use function config;
use function env;
use function request;
use function route;
use function url;
use function view;


class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    private $articleRepository;
    private $baseSeoData;
    private $homePageSeoData;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->homePageSeoData = json_decode(setting()->get('general'), true);
        $this->baseSeoData = [
            'title' => $this->homePageSeoData['home_page_title'],
            'description' => $this->homePageSeoData['home_page_description'],
            'keywords' => $this->homePageSeoData['home_page_keywords'],
            'image' => $this->homePageSeoData['home_page_image_url'] ?
                Storage::disk('public')->url('settings/' . basename($this->homePageSeoData['home_page_image_url']))
                :
                asset('asset/logo.png'),
            'type' => 'website',
            'site' => env('APP_URL'),
            'app_name' => $this->homePageSeoData['app_name'],
            'robots' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'
        ];

        $this->articleRepository = $articleRepository;
        $tags = $this->articleRepository->getAllTags();

        $categories = Category::select('name', 'slug')->where('is_published', 0)->orderBy('position', 'asc')->pluck('name', 'slug');
        $featuredArticles = $this->articleRepository->publishedArticles(1, 3);
        $footerPages = \Cache::remember('footer_pages', config('cache.default_ttl'), function () {
            return PageLink::where('key', 'footer_pages')->with('page:id,title,slug')->get()->toArray();
        });
        view()->share('footerPages', $footerPages);
        view()->share('categories', $categories);
        view()->share('tags', $tags);
        view()->share('featuredPosts', $featuredArticles);
    }

    public function index()
    {

        $this->articleRepository->SetVisitor();
        $publishedArticles = $this->articleRepository->publishedArticles(1, 4);
        $featuredArticles = $this->articleRepository->publishedFeaturedArticles(1, 3);
        $mostReadArticles = $this->articleRepository->mostReadArticles(1, 3);

        $this->seo($this->baseSeoData);

        return view('pages.landingPage.index',
            compact(
                'publishedArticles',
                'featuredArticles',
                'mostReadArticles'
            )
        );
    }

    public function home()
    {
        $this->baseSeoData['title'] = " Tanventure | Let me tell you a story";
        $this->seo($this->baseSeoData);

        return view('pages.homePage.index'
//            ,
//            compact(
//
//            )
        );
    }

    public function about()
    {
        $this->baseSeoData['title'] = " Tanvir Reza Anik | tanventure";
        $this->baseSeoData['keywords'] = "bikepacking";
        $this->seo($this->baseSeoData);

        return view('pages.about.index');

    }

    public function novel()
    {
        $novels=$this->articleRepository->getNovels();

        $this->baseSeoData['title'] = " The Novel | tanventure";
        $this->seo($this->baseSeoData);

        return view('pages.novel.index',
            compact(
                'novels'
            )
        );

    }

    public function newsLetters(Request $request)
    {
        $request->validate([
            'email' => 'email|required|unique:news_letters,email',
        ]);

        NewsLetter::create([
            'email' => $request->input('email')
        ]);

        return back()->with("success", "Thanks! We Got You!!");
    }

    public function sendNewsLetters(Request $request): \Illuminate\Http\RedirectResponse
    {
        $subscribers = NewsLetter::all();
        $data = [];
        $subject = "Hey Man";
        $body = "This Is The Body";
        $name = 'Name';
        $email = env('MAIL_USERNAME');
        for ($i = 0; $i < $subscribers->count(); $i++) {
            \Mail::send('email.mail', $data, function ($message) use ($subscribers, $i) {
                $message->to($subscribers[$i]->email)
                    ->from('Anikreza22@gmail.com', 'Anik Reza')
                    ->subject('Subject Line');
            });
        }

        return back()->with("success", "Thank You, We've Got You");
    }


    public function articleDetails($slug)
    {
        $article = $this->articleRepository->getArticle($slug, true);
        if (!$article) {
            return $this->renderPage($slug);
        }
        $category = $article['categories'][0];
        $similarArticles = $this->articleRepository->getSimilarArticles($category['id'], 2);
        $tags = $article->keywords;
        $tagTitles = [];
        foreach ($tags as $tag)
            array_push($tagTitles, $tag->title);
        $segments = [
            [
                'name' => $article['categories'][0]['name'],
                'url' => route('category', [
                    'slug' => $category['slug']
                ])
            ],
            ['name' => $article['title'], 'url' => url($slug)]
        ];
        $cacheKey = request()->ip() . $slug;
        \Cache::remember($cacheKey, 60, function () use ($article) {
            $article->viewed = $article->viewed + 1;
            $article->save();
            return true;
        });

        $appName = env('APP_NAME');
        $this->baseSeoData['title'] = " $article->title - $appName";
        $this->baseSeoData['keywords'] = $tagTitles;
        $this->seo($this->baseSeoData);
        $shareLinks = $this->getSeoLinksForDetailsPage($article);

        return view('pages.articleDetail.index', compact('article', 'shareLinks', 'similarArticles', 'category', 'segments'));
    }

    public function categoryDetails($slug)
    {
        $category = Category::where('slug', $slug)->first();
        $segments = [
            [
                'name' => "{$category->name}",
                'url' => route('category', ['slug' => $category->slug])
            ],
        ];
        $categoryArticles = $this->articleRepository->paginateByCategoryWithFilter(5, $category->id);

        // SEO META INFO
//        $name = empty($category->meta_title) ? $category->name : $category->meta_title;
//        $title = request()->has('page') ? $name . " (Page " . request('page') . ')' : $name;
        $appName = env('APP_NAME');
        $this->baseSeoData['title'] = "{$appName} | {$category->name} | {$category->keywords}";
        $this->baseSeoData['description'] = "{$category->excerpt}";
        $this->baseSeoData['keywords'] = "{$category->keywords}";
        $this->seo($this->baseSeoData);

        return view('pages.category.index', compact('segments', 'category', 'categoryArticles'));
    }

    public function tagDetails($slug)
    {
        $tagDetails = $this->articleRepository->getTagInfoWithArticles($slug, 10);
        $tag = $tagDetails['tagInfo'];
        $tags = $tagDetails['tags'];
        $tagArticles = $tagDetails['articles'];

        if (!isset($tag->title)) {
            \Log::error("tag not found: " . $slug);
            abort(404);
        }

        $segments = [
            ['name' => $tag->title, 'url' => route('tag', ['slug' => Str::slug($tag->title)])],
        ];

        // SEO META INFO
        if ($tag->title == 'XYZs column') {
            $this->baseSeoData['title'] = "Demo blogsite Travel blog | {$this->baseSeoData['app_name']}";
            $this->baseSeoData['description'] = "here, you will find blogs describing cultures, ethnicity and politics around the world";
        } else {
            $this->baseSeoData['title'] = "{$tag->title} | {$this->baseSeoData['app_name']}";
        }

        $this->seo($this->baseSeoData);

        view()->share('tags', $tags);
        return view('pages.tag.index', compact('segments', 'tag', 'tagArticles'));
    }

    public function searchArticle(Request $request)
    {
        $searchTerm = $request->input('query');
        $searchedArticles = $this->articleRepository->searchArticles($searchTerm, 3);

        $segments = [
            ['name' => $searchTerm],
        ];

        // SEO META INFO
        $appName = env('APP_NAME');
        $this->baseSeoData['title'] = "$searchTerm - $appName";
        $this->seo($this->baseSeoData);

        return view('pages.search.index', compact('segments', 'searchTerm', 'searchedArticles'));
    }

    public function renderPage($slug)
    {
        $page = Page::where('slug', $slug)->with('keywords')->first();

        if (!$page) {
            abort(404);
        }

        //visitor count
        $cacheKey = request()->ip() . $slug;
        \Cache::remember($cacheKey, 60, function () use ($page) {
            $page->viewed = $page->viewed + 1;
            $page->save();
            return true;
        });

        $segments = [
            ['name' => $page['title'], 'url' => url($slug)]
        ];
        $shareLinks = $this->getSeoLinksForDetailsPage($page);

        return view('pages.page-details.index', compact('page', 'segments', 'shareLinks'));
    }

    private function generatePageClass($title): \stdClass
    {
        $page = new \stdClass();
        $page->title = $title;
        $page->excerpt = null;
        $page->keywords = [];
        $page->image_url = null;
        return $page;
    }

    public function getColumnistPage()
    {
        $page = $this->generatePageClass('Columnist');
        $segments = [
            ['name' => $page->title, 'url' => url('Columnist')]
        ];
        $shareLinks = $this->getSeoLinksForDetailsPage($page);

        return view('pages.columnist.index', compact('page', 'segments', 'shareLinks'));
    }

    private function getSeoLinksForDetailsPage($data)
    {
        $this->baseSeoData = [
            'title' => $data->title . " | {$this->baseSeoData['app_name']}",
            'description' => $data->excerpt,
            'keywords' => count($data->keywords) ? implode(", ", $data->keywords->pluck('title')->toArray()) : $this->baseSeoData['keywords'],
            'image' => $data->image_url,
            'type' => 'article',
            'site' => env('APP_URL'),
            'app_name' => $this->baseSeoData['app_name'],
            'robots' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'
        ];
        $this->seo($this->baseSeoData);

        return Share::page(url()->current(), $data->title)
            ->facebook()
            ->twitter()
            ->linkedin($data->excerpt)
            ->whatsapp()
            ->telegram()
            ->getRawLinks();
    }

    private function seo($data)
    {
        SEOMeta::setTitle($data['title'], false);
        SEOMeta::setDescription($data['description']);
//        SEOMeta::addMeta('name', $value = null, $name = 'name');
        SEOMeta::setKeywords($data['keywords']);
        SEOMeta::setRobots($data['robots']);
        SEOMeta::setCanonical(url()->full());

//        OpenGraph::addProperty('keywords', '$value'); // value can be string or array
        OpenGraph::setTitle($data['title']); // define title
        OpenGraph::setDescription($data['description']);  // define description

        if ($data['image']) {
            OpenGraph::addImage($data['image']); // add image url
        } else {
            OpenGraph::addImage($this->homePageSeoData['home_page_image_url']); // add image url
        }

        OpenGraph::setUrl(url()->current()); // define url
        OpenGraph::setSiteName($data['app_name']); //define site_name

        TwitterCard::setType('summary'); // type of twitter card tag
        TwitterCard::setTitle($data['title']); // title of twitter card tag
        TwitterCard::setDescription($data['description']); // description of twitter card tag

        if ($data['image']) {
            TwitterCard::setImage($data['image']); // add image url
        } else {
            TwitterCard::setImage($this->homePageSeoData['home_page_image_url']); // add image url
        }

        TwitterCard::setSite($data['site']); // site of twitter card tag
        TwitterCard::setUrl(url()->current()); // url of twitter card tag

        if (isset($data['read_time'])) {
            TwitterCard::addValue('label1', 'Est. reading time'); // value can be string or array
            TwitterCard::addValue('data1', $data['read_time']); // value can be string or array
        }

//        JsonLd::addValue($key, $value); // value can be string or array
        JsonLd::setType($data['type']); // type of twitter card tag
        JsonLd::setTitle($data['title']); // title of twitter card tag
        JsonLd::setDescription($data['description']); // description of twitter card tag

        if ($data['image']) {

            JsonLd::setImage($data['image']); // add image url
        } else {
            JsonLd::setImage($this->homePageSeoData['home_page_image_url']); // add image url
        }
        JsonLd::setSite('@DemoBlog'); // site of twitter card tag
        JsonLd::setUrl(url()->current()); // url of twitter card tag
    }
}
