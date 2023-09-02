<?php

use App\Http\Controllers\Api\AboutMeController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProjectsController;
use App\Http\Controllers\Api\ServicesController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SkillsController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\WorkplaceController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'v1/',
    'namespace' => 'Api'
], function () {
    Route::post('/send-mail', [WebsiteController::class, 'sendMail'])->name('send.mail');
});

Route::group([
    'prefix' => 'v1/auth',
    'namespace' => 'Api'
], function () {
    Route::post("/login", [AuthController::class, 'login']);
    Route::post("/forgot-password", [AuthController::class, 'forgotPassword']);
    Route::post("/token-verification", [AuthController::class, 'tokenVerification']);
    Route::post("/reset-password", [AuthController::class, 'resetPassword']);
    Route::get("/user", [AuthController::class, 'user']);
});

Route::group([
    'prefix' => 'v1',
], function () {
    Route::post("/auth/logout", [AuthController::class, "logout"]);
    Route::post("/auth/save-profile", [ProfileController::class, "update"]);
    Route::post("/email-update", [ProfileController::class, "emailUpdate"]);
    Route::post("/update-password", [ProfileController::class, "passwordUpdate"]);

    Route::post("categories/priority-update", [CategoryController::class, 'priorityUpdate']);
    Route::apiResource("categories", CategoryController::class);
    Route::post("categories/{slug}", [CategoryController::class, 'getWithArticles']);

    Route::get("articles/{slug}/edit", [ArticleController::class, 'edit']);
    Route::post("articles/{id}", [ArticleController::class, 'update']);
    Route::get("articles/show/{id}", [ArticleController::class, 'show']);
    Route::get("articles/all", [ArticleController::class, 'allArticles']);
    Route::apiResource("articles", ArticleController::class);

    Route::get("workplaces/{id}/edit", [WorkplaceController::class, 'show']);
    Route::post("workplaces/{id}", [WorkplaceController::class, 'update']);
    Route::apiResource("workplaces", WorkplaceController::class);

    Route::get("educations/{id}/edit", [EducationController::class, 'show']);
    Route::post("educations/{id}", [EducationController::class, 'update']);
    Route::apiResource("educations", EducationController::class);

    Route::get("projects/{id}/edit", [ProjectsController::class, 'show']);
    Route::post("projects/{id}", [ProjectsController::class, 'update']);
    Route::apiResource("projects", ProjectsController::class);

    Route::get("services/{id}/edit", [ServicesController::class, 'show']);
    Route::post("services/{id}", [ServicesController::class, 'update']);
    Route::apiResource("services", ServicesController::class);

    Route::get("skills/{id}/edit", [SkillsController::class, 'show']);
    Route::post("skills/{id}", [SkillsController::class, 'update']);
    Route::apiResource("skills", SkillsController::class);

    Route::get("about-me/{id}/edit", [AboutMeController::class, 'show']);
    Route::get("allTags", [AboutMeController::class, 'getAllTags']);
    Route::post("about-me/{id}", [AboutMeController::class, 'update']);
    Route::get("about-me/etc", [AboutMeController::class, 'etc']);
    Route::apiResource("about-me", AboutMeController::class);

    Route::get("settings/{id}/edit", [SettingsController::class, 'show']);
    Route::post("settings/{id}", [SettingsController::class, 'update']);
    Route::apiResource("settings", SettingsController::class);

    Route::post("sendEmail", [WebsiteController::class, 'sendEmail']);
    Route::get("hit", [WebsiteController::class, 'SetVisitor']);
    Route::get("visitCount", [WebsiteController::class, 'getTotalVisitCount']);
    Route::get("visitorInfo", [WebsiteController::class, 'getVisitorsInfo']);

    Route::apiResource("news", NewsController::class);
    Route::post("save-news-status", [NewsController::class, 'saveNewsStatus']);
    Route::delete("delete-news/{id}", [NewsController::class, 'deleteNews']);
    Route::post("news/{id}", [NewsController::class, 'update']);
    Route::get("news/{slug}/edit", [NewsController::class, 'edit']);
    Route::get("fetch-all-published-news", [NewsController::class, 'get']);
});
