<?php

namespace App\Http\Controllers;
;

use App\Models\Article;
use App\Models\Category;
use App\Models\NewsLetter;
use App\Models\Page;
use App\Models\PageLink;
use App\Models\Visitor;
use App\Repositories\Article\ArticleRepository;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
    }

    public function index()
    {
        $this->articleRepository->SetVisitor();
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

    public function sendEMail(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'subject' => 'required',
            'message' => 'required',
        ]);
        $data = [
            'recipent' => 'tanvirrezaanik@gmail.com',
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'body' => $request->message
        ];

        \Mail::send('email.contact-template',$data, function ($message) use ($data) {
            $message->to($data['recipent'])
                ->from($data['email'], $data['name'])
                ->subject($data['subject']);
        });

        return response('successful', 201);
    }

    public function getArticleCount()
    {
        return Article::where('created_at', '>', Carbon::now()->subDays(1))
            ->groupBy(\DB::raw('HOUR(created_at)'))
            ->count();
    }

    public function getAllArticleCount(): int
    {
        return Article::all()->count();
    }

    public function SetVisitor()
    {
        $ip = request()->ip();
        $visited_date = Carbon::now();
        $visitor = Visitor::firstOrCreate(['ip' => $ip], ['visit_date' => $visited_date]);
        $visitor->increment('hits');
        $visitor->increment('lastDayRecord');

        Visitor::where('visit_date', '<', Carbon::now()->subDays(1))
            ->update(['lastDayRecord' => 0]);

        Visitor::where('created_at', '<', Carbon::now()->subDays(1))
            ->update(['visit_date' => Carbon::now(), 'created_at' => Carbon::now()]);
    }

    public function getTotalVisitCount(): array
    {
        $total= Visitor::sum('hits');
        $lastDay=Visitor::where('updated_at', '>', Carbon::now()->subDays(1))
            ->sum('lastDayRecord');

        return ['total'=>$total,'lastDay'=>$lastDay];
    }

    public function getVisitorsInfo(): array
    {
        $allVisitor= Visitor::all()->count();
        $visitorsLastWeek=Visitor::where('updated_at', '>', Carbon::now()->subDays(7))
            ->count('id');

        return ['allVisitor'=>$allVisitor,'visitorsLastWeek'=> $visitorsLastWeek];
    }

}
