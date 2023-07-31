<?php

namespace App\Repositories\Article;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Visitor;
use Cache;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Str;

class ArticleRepository implements ArticleInterface
{

    private $model;
    private $disk = 'public';

    public function __construct(Article $article)
    {
        $this->model = $article;
    }

    public function save(Request $request)
    {

        $image_url = $this->storeImage($request, $currentArticle = '');
        $data = $this->storeData($request, $image_url);
        $article = $this->model::create($data);
        // Category
        $categoryIDs = $this->getCategoryIDs($request->input('categories'));
        $article->categories()->sync($categoryIDs);
        // Tags
        $tagIDs = $this->getTagIDs($request);
        $article->tags()->sync($tagIDs);

        return $article;
    }

    public function update(Request $request, int $id): array
    {
        $article = Article::findOrFail($id);
        $isPublishedBefore = $article->status;

        $image_url = $this->storeImage($request, $currentArticle = $article);
        $data = $this->storeData($request, $image_url);
        // Category
        $article->categories()->detach();
        $categoryIDs = $this->getCategoryIDs($request->input('categories'));
        $article->categories()->sync($categoryIDs);
        // Tags
        $tagIDs = $this->getTagIDs($request);
        $article->tags()->detach();
        $article->tags()->sync($tagIDs);

        $article->update($data);

        return ['article' => $article, 'previouslyPublished' => $isPublishedBefore];
    }

    private function getCategoryIDs($request): array
    {
        $newCategories = explode(',', $request);
        $categoryIDs = [];
        foreach ($newCategories as $category) {
            $cat = Category::where('name', $category)->first();

            $categoryIDs[] = $cat->id;
        }

        return $categoryIDs;
    }

    private function getTagIDs($request): array
    {
        $newTags = explode(',', $request->input('tags'));
        $tagIDs = [];
        foreach ($newTags as $tag) {
            $tag = Tag::firstOrCreate(['name' => $tag]);
            $tagIDs[] = $tag->id;
        }
        return $tagIDs;
    }

    private function storeImage($request, $currentArticle): string
    {
        $image = $request->image;
        if ($request->hasFile('image')) {
            $image_ext = $image->getClientOriginalExtension();
            $image_full_name = 'cover_' . $request->input('title') . '.' . $image_ext;
            $upload_path = 'article/images/';
            $image_url = $upload_path . $image_full_name;
            $image->move($upload_path, $image_full_name);
        } else {
            $image_url = $currentArticle->image;
        }
        return $image_url;
    }

    private function storeData($request, $image_url): array
    {
        return [
            'user_id' => auth()->user()->id,
            'title' => $request->input('title'),
            'meta_title' => $request->input('meta_title'),
            'slug' => $this->slugify($request->input('title')),
            'meta_description' => $request->input('meta_description'),
            'slider_status' => filter_var($request->input('slider_status'), FILTER_VALIDATE_BOOLEAN),
            'description' => $request->input('description'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'image' => $image_url,
        ];
    }

    private function slugify($input){
        return Str::slug($input);
    }

    public function delete(int $id)
    {
        $article = Article::findOrFail($id);
        if (File::exists($article->image)) {
            File::delete($article->image);
        }
        $article->categories()->detach();
        $article->tags()->detach();

        return $article->delete();
    }

    public function all(array $columns = [])
    {
        return count($columns) ? Article::select($columns)->orderBy('id')->get() : Article::orderBy('hit_count')->get();
    }

    public function paginate($perPage = 12)
    {
        return Article::latest()
            ->with(['categories'])
            ->with(['author'])
            ->when(request()->has('category'), function ($q) {
                $q->whereHas('categories', function ($sq) {
                    $sq->where('categories.name', \request('category'));
                });
            })
            ->when(request()->has('is_published'), function ($q) {
                $q->where('status', (bool)request('is_published'));
            })
            ->when(\request()->has('search'), function ($q) {
                $q->where('title', 'LIKE', '%' . \request('search') . '%');
            })
            ->paginate($perPage);
    }

    public function paginateWithFilter(int $limit)
    {
        // TODO: Implement paginateWithFilter() method.
    }

    public function paginateByCategoryWithFilter(int $perPage, int $categoryId)
    {
        return $this->baseQuery($categoryId)
            ->select('id', 'title', 'slug', 'featured', 'published', 'image', 'viewed', 'description')
            ->latest()
            ->paginate($perPage);
    }

    public function getArticleCount()
    {
        return Article::where('created_at', '>', Carbon::now()->subDays(1))
            ->groupBy(\DB::raw('HOUR(created_at)'))
            ->count();
    }


    public function getCategoriesCount(): int
    {
        return Category::all()->count();
    }

    private function baseQuery(int $categoryId = 1)
    {
        return $this->model->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('status', '=', 0);
            $q->when($categoryId !== 1, function ($sq) use ($categoryId) {
                $sq->where('category_id', $categoryId);
            });
        });
    }

    public function mostReadArticles(int $limit)
    {
        return $this->model
            ->select('id', 'title', 'status', 'image', 'hit_count', 'meta_description')
            ->limit($limit)
            ->where('status', 1)
            ->orderBy('hit_count', 'desc')
            ->get();
    }

    public function featured()
    {
        return $this->model
            ->select('id', 'title', 'status', 'image', 'slug', 'hit_count', 'description', 'created_at')
            ->limit(5)
            ->where('status', 1)
            ->where('slider_status', 1)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function categoryArticles($ids)
    {
//        return Category::with(['articles' => function ($query) use ($ids) {
//            $query->take(5)->whereNotIn('articles.id', $ids);
//        }])->get();
//
        return Category::where('status', 1)
            ->with('articles', function ($q) use ($ids) {
                $q->where('status', 1)
                    ->orderBy('articles.id', 'desc')
                    ->whereNotIn('articles.id', $ids)
                    ->take(10);
            })
            ->get();
    }

    public function getArticle($condition, $isSlug = true)
    {
        $this->model->where('title', $condition)->increment('hit_count');

        return $this->model->with(['categories' => function ($q) use ($condition, $isSlug) {
            $q->with(['articles' => function ($sq) use ($condition, $isSlug) {
                $sq->select('article_id', 'title', 'slug', 'status', 'hit_count', 'image', 'slider_status', 'description', 'created_at')
                    ->when($isSlug, function ($s) use ($condition, $isSlug) {
                        $s->where('title', '!=', $condition);
                    })
                    ->when(!$isSlug, function ($s) use ($condition, $isSlug) {
                        $s->where('article_id', '!=', $condition);
                    })
                    ->inRandomOrder()
                    ->limit(4);
            }]);
        }])
            ->with(['tags'])
            ->with(['author'])
            ->where('status', 1)
            ->when($isSlug, function ($q) use ($condition) {
                $q->where('title', $condition);
            })
            ->when(!$isSlug, function ($q) use ($condition) {
                $q->where('id', $condition);
            })
            ->first();
    }

    public function getSimilarArticles($categoryId, $limit)
    {
        return $this->baseQuery($categoryId)
            ->select('id', 'title', 'status', 'hit_count', 'image', 'slider_status', 'description')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function searchArticles($query, $perPage)
    {
        return $this->baseQuery(1)
            ->select('id', 'title', 'slug', 'published', 'viewed', 'image', 'featured', 'description')
            ->where('title', 'LIKE', '%' . $query . '%')
            ->latest()
            ->limit(5)
            ->paginate($perPage);
    }

    public function getAllTags()
    {
        return Tag::all()->unique('name');
    }

    public function getTagInfoWithArticles($tag, $perPage, $includeFavorites = false): array
    {
        $string = Str::title(str_replace('-', ' ', trim($tag)));
        $tag = Tag::where('title', 'LIKE', '%' . $string . '%')->get();
        $tags = Tag::all()->unique('name');

        return [
            'tagInfo' => count($tag) ? $tag[0] : null,
            'tags' => count($tags) ? $tags : null,
            'articles' => count($tag) ? $this->getArticlesByTag($perPage, $tag->pluck('id')->toArray(), $includeFavorites) : []
        ];
    }

    public function getArticlesByTag($perPage, array $tagIDs, $includeFavorites = false)
    {
        $q = $this->model->whereHas('tags', function ($q) use ($tagIDs) {
            $q->whereIn('tag_id', $tagIDs);
        })
            ->with('categories:id,name')
            ->with('tags:id,name')
            ->where('published', true)
            ->when($includeFavorites, function ($q) {
                $q->with(['favorites']);
            })
            ->select('id', 'title', 'slider_status', 'status', 'image', 'hit_count', 'description')
            ->latest();

        return $perPage === 4 ? $q->limit($perPage)->get() : $q->paginate($perPage);
    }

}
