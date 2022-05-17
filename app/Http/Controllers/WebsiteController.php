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



}
