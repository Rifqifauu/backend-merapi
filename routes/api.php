<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    EventsController,
    NewsController,
    GalleryController,
    AnnouncementsController,
    CommunityController,
    AuthController,
    ForumPostController,
    MessageController,
    TestimonialController
};
use App\Models\User;
use App\Models\News;
use App\Models\Events;
use App\Models\ForumPost;
use App\Models\Message;
use App\Models\Testimonial;
use App\Models\Gallery;
use App\Models\Announcements;
use App\Models\Community;

Route::get('/dashboard/stats', function () {
    return [
        'news'          => News::count(),
        'events'        => Events::count(),
        'forumPosts'    => ForumPost::count(),
        'testimonials'  => Testimonial::count(),
        'galleries'     => Gallery::count(),
        'announcements' => Announcements::count(),
        'communities'   => Community::count(),
    ];
});

Route::get('/dashboard/latest', function () {
    return [
        'news'         => News::latest()->take(5)->get(),
        'events'       => Events::latest()->take(5)->get(),
        'forumPosts'   => ForumPost::latest()->take(5)->get(),
        'testimonials' => Testimonial::latest()->take(5)->get(),
    ];
});


/*
|--------------------------------------------------------------------------
| Public GET Routes (tanpa auth)
|--------------------------------------------------------------------------
*/
Route::get('/news', [NewsController::class, 'index']);
Route::get('/announcements', [AnnouncementsController::class, 'index']);
Route::get('/events', [EventsController::class, 'index']);
Route::get('/gallery', [GalleryController::class, 'index']);
Route::get('/communities', [CommunityController::class, 'index']);
Route::get('/forum', [ForumPostController::class, 'index']);
Route::get('/testimonials', [TestimonialController::class, 'index']); // <-- baru

// GET users & messages juga publik
Route::get('/users', function () {
    $users = User::where('is_admin', false)->get(['id', 'name']);
    return response()->json($users);
});
Route::get('/messages', [MessageController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Messages Routes (auth untuk create/update)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages', [MessageController::class, 'store']);
    Route::patch('/messages/read/{id}', [MessageController::class, 'markAsRead']);
});

/*
|--------------------------------------------------------------------------
| Forum Routes (auth untuk posting/reply)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/forum', [ForumPostController::class, 'store']);
    Route::post('/forum/{post}/reply', [ForumPostController::class, 'reply']);
    Route::delete('/forum/{id}', [ForumPostController::class, 'destroy']);
    Route::put('/forum/{forum}', [ForumPostController::class, 'update']);

});

/*
|--------------------------------------------------------------------------
| API Resource Routes (auth untuk create/update/delete)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('news', NewsController::class)->except(['index']);
    Route::apiResource('announcements', AnnouncementsController::class)->except(['index']);
    Route::apiResource('events', EventsController::class)->except(['index']);
    Route::apiResource('gallery', GalleryController::class)->except(['index']);
    Route::apiResource('communities', CommunityController::class)->except(['index']);
    Route::apiResource('testimonials', TestimonialController::class)->except(['index']); // <-- baru
});

/*
|--------------------------------------------------------------------------
| OPTIONS preflight (CORS)
|--------------------------------------------------------------------------
*/
Route::options('{any}', function () {
    return response()->noContent();
})->where('any', '.*');
