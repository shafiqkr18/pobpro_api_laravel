<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * Document path
 * https://documenter.getpostman.com/view/6265407/SWE29fxz?version=latest
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//user section


//Route::group(['middleware' => 'cors'], function() {
//    Route::options('{any}');
//    Route::post('user/login','UserController@login');
//    Route::post('user/save_new_user','UserController@save_new_user');
//});
Route::post('user/login','UserController@login');
Route::post('user/save_new_user','UserController@save_new_user');

Route::group(['middleware' => [ 'auth:api']], function() {
    Route::options('{any}');
    Route::post('user/profile','UserController@profile');
    Route::post('user/all','UserController@all_users');
    Route::post('user/logout','UserController@logout');



    //exective on board , eob
    Route::match(['post'],'eob/correlative_map','GeneralController@correlative_map');
    Route::post('eob/topics','TopicController@all_topics');
    Route::post('eob/topic/detail','TopicController@topic_detail');
    Route::post('eob/topic/save', 'TopicController@save_topic');



    Route::post('eob/tasks','TaskController@all_tasks');
    Route::post('eob/task/detail','TaskController@task_detail');
    Route::post('eob/task/save', 'TaskController@save_task');
    Route::post('eob/task/delete','TaskController@delete');
    Route::post('eob/tasks/filters','TaskController@filters');


    Route::post('eob/reports','ReportController@reports');
    Route::post('eob/report/detail','ReportController@report_detail');
    Route::post('eob/report/create', 'ReportController@save_report');
    Route::post('eob/report/save_remarks', 'ReportController@save_remarks');



    Route::post('eob/meetings','MeetingController@meetings');
    Route::post('eob/meeting/detail','MeetingController@meeting_detail');
    Route::post('eob/meeting/create', 'MeetingController@save_meeting');



    Route::post('eob/correspondence/addresses','CorrespondenceAddressController@addresses');
    Route::post('eob/correspondence/contact/create','CorrespondenceAddressController@save_address');
    Route::post('eob/correspondence/contact/detail','CorrespondenceAddressController@detail');


    Route::post('eob/correspondence/letters','CorrespondenceController@letters');
    Route::post('eob/correspondence/letter/create','CorrespondenceController@save_letter');
    Route::post('eob/correspondence/letter/detail','CorrespondenceController@letter_detail');


    //general apis
    Route::match(['post','get'],'source_types','SourceTypeController@source_types');

    //test api
    Route::post('test','GeneralController@test_api');


});
