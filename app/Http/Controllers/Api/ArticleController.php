<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Repositories\Article\ArticleRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function response;

class ArticleController extends ApiController
{
    public ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Returns All categories
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */

    public function index(Request $request)
    {

        $allArticles= $this->successResponse($this->articleRepository->paginate(10), true);
        $popular= $this->successResponse($this->articleRepository->mostReadArticles(7), true);
        $count= $this->successResponse($this->articleRepository->getArticleCount(), true);
        $hitsPerUser= $this->successResponse($this->articleRepository->getUniqueVisitorCount(), true);
        $hits= $this->successResponse($this->articleRepository->getTotalVisitCount(), true);
        $hitsLastDay= $this->successResponse($this->articleRepository->getLastDaysTotalVisitCount(), true);
        $hitsPerUserLastWeek= $this->successResponse($this->articleRepository->getLastWeeksUniqueVisitorCount(), true);
        $categoryCount= $this->successResponse($this->articleRepository->getCategoriesCount(), true);
        $hitsPerDayLastWeek= $this->successResponse($this->articleRepository->getLastWeeksVisitCountByDay(), true);
        $AllCount= $this->successResponse($this->articleRepository->getAllArticleCount(), true);

        $response = [
            'all' => $allArticles,
            'countInLastDay'=>$count,
            'allArticleCount'=>$AllCount,
            'allTimeUniqueVisitors'=>$hitsPerUser,
            'LastWeeksUniqueVisitors'=>$hitsPerUserLastWeek,
            'totalVisits'=>$hits,
            'totalVisitsLastDay'=>$hitsLastDay,
            'categoryCount'=>$categoryCount,
            'popular'=>$popular,
            'hitsPerDayLastWeek'=>$hitsPerDayLastWeek
        ];

        return response($response, 201);
    }

    /**
     * Creates Category & Returns created category
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|unique:articles,title',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        \DB::beginTransaction();

        try {
            $article = $this->articleRepository->save($request);

            // While creating new article, if it is directly published, then send notification
            if ($article->published) {
                $this->sendNotification($article);
            }

            \DB::commit();

            return $this->successResponse($article);

        } catch (\Throwable $throwable) {
            \DB::rollBack();
            $this->errorLog($throwable, 'api');

            return $this->failResponse($throwable->getMessage());
        }

    }

    public function edit($slug): JsonResponse
    {
        try {
            $article = Article::where('id', $slug)->with(['categories', 'tags'])->firstOrFail();

            return $this->successResponse($article);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }
    }

    public function show($slug)
    {
        try {
           return $this->articleRepository->getArticle($slug,true);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');
            return $this->failResponse($exception->getMessage());
        }
    }

    /**
     * Updates Category & Returns updated category
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'title' => 'required'
        ]);

        $articleInfo = $this->articleRepository->update($request, $id);

        // if the article is not published before send notification
        if (!$articleInfo['previouslyPublished']) {
            $this->sendNotification($articleInfo['article']);
        }

        \Artisan::call('cache:clear');

        return $this->successResponse($articleInfo['article']);
    }

    /**
     * Deletes Category & Returns boolean
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->articleRepository->delete($id);
        \Artisan::call('cache:clear');

        return $this->successResponse();
    }

    /**
     * @param $article
     */
    private function sendNotification($article): void
    {
        \Artisan::call('cache:clear');

        $data = [
            "article_id" => $article->id,
            "title" => $article->title,
            "body" => $article->excerpt,
            "image" => $article->image
        ];

        \Artisan::call("send:notification", [
            'notificationData' => $data
        ]);
    }
}
