<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/





Route::resource('landingpage', LandingPageController::class)->middleware(['auth', 'XSS']);
// Route::get('landingpage/', 'LandingPageController@index')->name('landingpage.index');


Route::resource('custom_page', CustomPageController::class);
Route::post('custom_store/', 'CustomPageController@customStore')->name('custom_store')->middleware(['auth', 'XSS']);
Route::get('pages/{slug}', 'CustomPageController@customPage')->name('custom.page');



Route::resource('homesection', HomeController::class)->middleware(['auth', 'XSS']);
// Route::get('homesection/', 'HomeController@index')->name('homesection.index');



Route::get('termspage', 'HomeController@termspage')->name('termspage');


Route::resource('features', FeaturesController::class)->middleware(['auth', 'XSS']);

Route::get('feature/create/', 'FeaturesController@feature_create')->name('feature_create')->middleware(['auth', 'XSS']);
Route::post('feature/store/', 'FeaturesController@feature_store')->name('feature_store')->middleware(['auth', 'XSS']);
Route::get('feature/edit/{key}', 'FeaturesController@feature_edit')->name('feature_edit')->middleware(['auth', 'XSS']);
Route::post('feature/update/{key}', 'FeaturesController@feature_update')->name('feature_update')->middleware(['auth', 'XSS']);
Route::get('feature/delete/{key}', 'FeaturesController@feature_delete')->name('feature_delete')->middleware(['auth', 'XSS']);

Route::post('feature_highlight_create/', 'FeaturesController@feature_highlight_create')->name('feature_highlight_create')->middleware(['auth', 'XSS']);

Route::get('features/create/', 'FeaturesController@features_create')->name('features_create')->middleware(['auth', 'XSS']);
Route::post('features/store/', 'FeaturesController@features_store')->name('features_store')->middleware(['auth', 'XSS']);
Route::get('features/edit/{key}', 'FeaturesController@features_edit')->name('features_edit')->middleware(['auth', 'XSS']);
Route::post('features/update/{key}', 'FeaturesController@features_update')->name('features_update')->middleware(['auth', 'XSS']);
Route::get('features/delete/{key}', 'FeaturesController@features_delete')->name('features_delete')->middleware(['auth', 'XSS']);



Route::resource('discover', DiscoverController::class)->middleware(['auth', 'XSS']);
Route::get('discover/create/', 'DiscoverController@discover_create')->name('discover_create')->middleware(['auth', 'XSS']);
Route::post('discover/store/', 'DiscoverController@discover_store')->name('discover_store')->middleware(['auth', 'XSS']);
Route::get('discover/edit/{key}', 'DiscoverController@discover_edit')->name('discover_edit')->middleware(['auth', 'XSS']);
Route::post('discover/update/{key}', 'DiscoverController@discover_update')->name('discover_update')->middleware(['auth', 'XSS']);
Route::get('discover/delete/{key}', 'DiscoverController@discover_delete')->name('discover_delete')->middleware(['auth', 'XSS']);



Route::resource('screenshots', ScreenshotsController::class)->middleware(['auth', 'XSS']);
Route::get('screenshots/create/', 'ScreenshotsController@screenshots_create')->name('screenshots_create')->middleware(['auth', 'XSS']);
Route::post('screenshots/store/', 'ScreenshotsController@screenshots_store')->name('screenshots_store')->middleware(['auth', 'XSS']);
Route::get('screenshots/edit/{key}', 'ScreenshotsController@screenshots_edit')->name('screenshots_edit')->middleware(['auth', 'XSS']);
Route::post('screenshots/update/{key}', 'ScreenshotsController@screenshots_update')->name('screenshots_update')->middleware(['auth', 'XSS']);
Route::get('screenshots/delete/{key}', 'ScreenshotsController@screenshots_delete')->name('screenshots_delete')->middleware(['auth', 'XSS']);


Route::resource('pricing_plan', PricingPlanController::class)->middleware(['auth', 'XSS']);



Route::resource('faq', FaqController::class)->middleware(['auth', 'XSS']);
Route::get('faq/create/', 'FaqController@faq_create')->name('faq_create')->middleware(['auth', 'XSS']);
Route::post('faq/store/', 'FaqController@faq_store')->name('faq_store')->middleware(['auth', 'XSS']);
Route::get('faq/edit/{key}', 'FaqController@faq_edit')->name('faq_edit')->middleware(['auth', 'XSS']);
Route::post('faq/update/{key}', 'FaqController@faq_update')->name('faq_update')->middleware(['auth', 'XSS']);
Route::get('faq/delete/{key}', 'FaqController@faq_delete')->name('faq_delete')->middleware(['auth', 'XSS']);


Route::resource('testimonials', TestimonialsController::class)->middleware(['auth', 'XSS']);
Route::get('testimonials/create/', 'TestimonialsController@testimonials_create')->name('testimonials_create')->middleware(['auth', 'XSS']);
Route::post('testimonials/store/', 'TestimonialsController@testimonials_store')->name('testimonials_store')->middleware(['auth', 'XSS']);
Route::get('testimonials/edit/{key}', 'TestimonialsController@testimonials_edit')->name('testimonials_edit')->middleware(['auth', 'XSS']);
Route::post('testimonials/update/{key}', 'TestimonialsController@testimonials_update')->name('testimonials_update')->middleware(['auth', 'XSS']);
Route::get('testimonials/delete/{key}', 'TestimonialsController@testimonials_delete')->name('testimonials_delete')->middleware(['auth', 'XSS']);


Route::resource('join_us', JoinUsController::class);
Route::get('join_us/delete//{key}', 'JoinUsController@destroy')->name('join_us_destroy')->middleware(['auth', 'XSS']);
Route::post('join_us/store/', 'JoinUsController@joinUsUserStore')->name('join_us_store');

// Route::get('footer/', 'FooterController@index')->name('footer.index');




