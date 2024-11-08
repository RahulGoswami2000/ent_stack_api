<?php

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
 * Welcome route - link to any public API documentation here
 */

use App\Http\Controllers\Api\Admin\ApiIntegrationController;
use App\Http\Controllers\Api\Admin\ClientManagementController;
use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\Admin\MetricCategoryController;
use App\Http\Controllers\Api\Admin\MetricController;
use App\Http\Controllers\Api\Admin\MetricGroupController;
use App\Http\Controllers\Api\Admin\MyTeamMemberController;
use App\Http\Controllers\Api\Admin\ReferralsController;
use App\Http\Controllers\Api\Admin\RolesController;
use App\Http\Controllers\Api\Admin\SubscriptionController;
use App\Http\Controllers\Api\Admin\TemplateController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\Web\CompanyProjectController;
use App\Http\Controllers\Api\Web\CompanyStackCategoryController;
use App\Http\Controllers\Api\Web\CompanyStackModuleController;
use App\Http\Controllers\Api\Web\GoalStackController;
use App\Http\Controllers\Api\Web\ReferClientController;
use App\Http\Controllers\Api\Web\ScorecardStackController;
use App\Http\Controllers\Api\Web\LoginController as WebLoginController;
use App\Http\Controllers\Api\Web\MetricController as WebMetricController;
use App\Http\Controllers\Api\Web\OrganizationController;
use App\Http\Controllers\Api\Web\ScorecardStackArchiveController;
use App\Http\Controllers\Api\Web\TeamMemberController;
use App\Http\Controllers\Api\Web\TeamStackController;
use App\Http\Controllers\Api\Web\UserController;

Route::get('/', function () {
    echo 'Welcome to our API';

    // $matches = array();
    //     $regex = "/\|\|\|([a-zA-Z0-9_:]*)\|\|\|/";

    //     preg_match_all($regex, '(###(###5###+###5###)###+###|||62:abc|||###)', $matches);
    //     // foreach(explode(':',$matches[1][0]) as $data) {
    //     //     print_r($data);
    //     // }
    //     $expresData = explode(':',$matches[1][0]);
    //     // print_r($expresData);
    // //
    // //
    // $arr = ['63','avc'];
    // $newExpression = str_replace($matches[0], '|||' . '61' . ':' . 'xyz' . '|||', '(###(###5###+###5###)###+###|||62:abc|||###)');
    // $newExpressionData = str_replace($expresData,$arr,'{"operand1": {"operand1": {"value": {"type": "unit", "unit": 5}}, "operand2": {"value": {"type": "unit", "unit": 5}}, "operator": "+"}, "operand2": {"value": {"item": {"text": "abc", "value": "62"}, "type": "item"}}, "operator": "+"}');
    //    echo "<pre>";
    // //    print_r($matches);
    //    print_r($newExpressionData);
    //    echo "</pre>";
    //
    //    https://stackoverflow.com/questions/53666210/one-to-many-relationship-in-eloquent-with-json-field
});

/** @var \Dingo\Api\Routing\ */
Route::group(['middleware' => ['api']], function () {
    Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers\Api\Admin'], function () {
        Route::post('/login', 'LoginController@authenticate')->name("login");
        Route::post('/forgot-password', 'LoginController@forgotPasswordOtp')->name("forgotPassword");
        Route::post('/{otp}/reset-password', 'LoginController@resetPasswordViaLink')->name("resetPasswordLink");
        Route::get('/logout', 'LoginController@logout')->name("logout");
        Route::post('/my-command', [CommonController::class, 'callManualCommand']);
        Route::get('/view-log', [CommonController::class, 'viewLog']);

        Route::group(['middleware' => ['authenticateUser:' . config('global.USER_TYPE.ADMIN')]], function () {
            Route::post('/change-password', 'LoginController@changePassword');
            Route::get('/me', 'LoginController@me');
            Route::post('{id}/update-profile', [LoginController::class, 'updateProfile']);
            Route::group(['prefix' => 'subscription'], function () {
                Route::get('/', [SubscriptionController::class, 'index'])->middleware('checkUserPermission:SUBSCRIPTION_INDEX');
                Route::post('/create', [SubscriptionController::class, 'store'])->middleware('checkUserPermission:SUBSCRIPTION_CREATE');
                Route::get('{id}/details', [SubscriptionController::class, 'details'])->middleware('checkUserPermission:SUBSCRIPTION_DETAILS,SUBSCRIPTION_UPDATE');
                Route::post('{id}/update', [SubscriptionController::class, 'update'])->middleware('checkUserPermission:SUBSCRIPTION_UPDATE');
                Route::delete('{id}/delete', [SubscriptionController::class, 'destroy'])->middleware('checkUserPermission:SUBSCRIPTION_DELETE');
                Route::post('{id}/change-status', [SubscriptionController::class, 'changeStatus'])->middleware('checkUserPermission:SUBSCRIPTION_CHANGE_STATUS');
            });

            //categories API
            Route::group(['prefix' => 'metric-categories'], function () {
                Route::post('/', [MetricCategoryController::class, 'index'])->middleware('checkUserPermission:METRIC_CATEGORIES_INDEX');
                Route::post('/create', [MetricCategoryController::class, 'store'])->middleware('checkUserPermission:METRIC_CATEGORIES_CREATE');
                Route::post('{id}/update', [MetricCategoryController::class, 'update'])->middleware('checkUserPermission:METRIC_CATEGORIES_UPDATE');
                Route::delete('{id}/delete', [MetricCategoryController::class, 'destroy'])->middleware('checkUserPermission:METRIC_CATEGORIES_DELETE');
                Route::post('{id}/remove-category', [MetricCategoryController::class, 'removeCategory'])->middleware('checkUserPermission:METRIC_CATEGORIES_UPDATE');
                Route::post('{id}/add-metric', [MetricCategoryController::class, 'addMetric'])->middleware('checkUserPermission:METRIC_CATEGORIES_UPDATE');
                Route::get('{id}/details', [MetricCategoryController::class, 'details'])->middleware('checkUserPermission:METRIC_CATEGORIES_UPDATE,METRIC_CATEGORIES_DETAILS');
                Route::post('{id}/change-status', [MetricCategoryController::class, 'changeStatus'])->middleware('checkUserPermission:METRIC_CATEGORIES_CHANGE_STATUS');
            });

            //metrics API
            Route::group(['prefix' => 'metrics'], function () {
                Route::post('/', [MetricController::class, 'index'])->middleware('checkUserPermission:METRICS_INDEX');
                Route::post('/create', [MetricController::class, 'store'])->middleware('checkUserPermission:METRICS_CREATE');
                Route::post('{id}/update', [MetricController::class, 'update'])->middleware('checkUserPermission:METRICS_UPDATE');
                Route::delete('{id}/delete', [MetricController::class, 'destroy'])->middleware('checkUserPermission:METRICS_DELETE');
                Route::get('{id}/details', [MetricController::class, 'details'])->middleware('checkUserPermission:METRICS_UPDATE,METRICS_DETAILS');
                Route::post('{id}/change-status', [MetricController::class, 'changeStatus'])->middleware('checkUserPermission:METRICS_CHANGE_STATUS');
            });

            //metrics group API
            Route::group(['prefix' => 'metric-group'], function () {
                Route::post('/', [MetricGroupController::class, 'index'])->middleware('checkUserPermission:METRIC_GROUP_INDEX');
                Route::post('/create', [MetricGroupController::class, 'store'])->middleware('checkUserPermission:METRIC_GROUP_CREATE');
                Route::post('{id}/update', [MetricGroupController::class, 'update'])->middleware('checkUserPermission:METRIC_GROUP_UPDATE');
                Route::delete('{id}/delete', [MetricGroupController::class, 'destroy'])->middleware('checkUserPermission:METRIC_GROUP_DELETE');
                Route::get('{id}/details', [MetricGroupController::class, 'details'])->middleware('checkUserPermission:METRIC_GROUP_UPDATE,METRIC_GROUP_DETAILS');
                Route::post('{id}/change-status', [MetricGroupController::class, 'changeStatus'])->middleware('checkUserPermission:METRIC_GROUP_CHANGE_STATUS');
                Route::post('change-category', [MetricGroupController::class, 'changeCategory'])->middleware('checkUserPermission:METRIC_GROUP_UPDATE');
                Route::post('add-metric', [MetricGroupController::class, 'addMetric'])->middleware('checkUserPermission:METRIC_GROUP_UPDATE');
                Route::post('remove-metric', [MetricGroupController::class, 'removeMetric'])->middleware('checkUserPermission:METRIC_GROUP_UPDATE');
                Route::post('{id}/metric-list', [MetricGroupController::class, 'metricList']);
            });

            //Roles  Api
            Route::group(['prefix' => 'roles'], function () {
                Route::get('/', [RolesController::class, 'index'])->middleware('checkUserPermission:ROLES_INDEX');
                Route::post('/create', [RolesController::class, 'store'])->middleware('checkUserPermission:ROLES_CREATE');
                Route::get('{id}/details', [RolesController::class, 'details'])->middleware('checkUserPermission:ROLES_UPDATE,ROLES_DETAILS');
                Route::post('{id}/update', [RolesController::class, 'update'])->middleware('checkUserPermission:ROLES_UPDATE');
                Route::delete('{id}/delete', [RolesController::class, 'destroy'])->middleware('checkUserPermission:ROLES_DELETE');
                Route::post('{id}/change-status', [RolesController::class, 'changeStatus'])->middleware('checkUserPermission:ROLES_CHANGE_STATUS');
            });

            //Team Member Api
            Route::group(['prefix' => 'team-member'], function () {
                Route::post('/', [MyTeamMemberController::class, 'index'])->middleware('checkUserPermission:MY_TEAM_MEMBER_INDEX');
                Route::post('/create', [MyTeamMemberController::class, 'store'])->middleware('checkUserPermission:MY_TEAM_MEMBER_CREATE');
                Route::get('{id}/details', [MyTeamMemberController::class, 'details'])->middleware('checkUserPermission:MY_TEAM_MEMBER_UPDATE,MY_TEAM_MEMBER_DETAILS');
                Route::post('{id}/update', [MyTeamMemberController::class, 'update'])->middleware('checkUserPermission:MY_TEAM_MEMBER_UPDATE');
                Route::post('{id}/change-status', [MyTeamMemberController::class, 'changeStatus'])->middleware('checkUserPermission:MY_TEAM_MEMBER_CHANGE_STATUS');
                Route::post('/client-assign', [MyTeamMemberController::class, 'clientAssign'])->middleware('checkUserPermission:MY_TEAM_MEMBER_ASSIGN_CLIENT');
            });

            //Template Api
            Route::group(['prefix' => 'template'], function () {
                Route::post('/', [TemplateController::class, 'index'])->middleware('checkUserPermission:TEMPLATE_INDEX');
                Route::get('{id}/details', [TemplateController::class, 'details'])->middleware('checkUserPermission:TEMPLATE_UPDATE,TEMPLATE_DETAILS');
                Route::post('{id}/update', [TemplateController::class, 'update'])->middleware('checkUserPermission:TEMPLATE_UPDATE');
                Route::post('{id}/change-status', [TemplateController::class, 'changeStatus'])->middleware('checkUserPermission:TEMPLATE_CHANGE_STATUS');
            });

            // Client Management
            Route::group(['prefix' => 'client-management'], function () {
                Route::post('/', [ClientManagementController::class, 'index'])->middleware('checkUserPermission:CLIENT_MANAGEMENT_INDEX');
                Route::get('{id}/details', [ClientManagementController::class, 'details'])->middleware('checkUserPermission:CLIENT_MANAGEMENT_UPDATE,CLIENT_MANAGEMENT_DETAILS');
                Route::post('{id}/update', [ClientManagementController::class, 'update'])->middleware('checkUserPermission:CLIENT_MANAGEMENT_UPDATE');
                Route::post('{id}/change-status', [ClientManagementController::class, 'changeStatus'])->middleware('checkUserPermission:CLIENT_MANAGEMENT_CHANGE_STATUS');
                Route::post('{id}/users', [ClientManagementController::class, 'users'])->middleware('checkUserPermission:CLIENT_MANAGEMENT_INDEX');
                Route::post('{id}/user-change-status', [ClientManagementController::class, 'userChangeStatus'])->middleware('checkUserPermission:CLIENT_MANAGEMENT_CHANGE_STATUS');
            });

            // Referrals
            Route::group(['prefix' => 'referrals'], function () {
                Route::post('/', [ReferralsController::class, 'index'])->middleware('checkUserPermission:REFERRALS_INDEX');
                Route::post('accept-referral', [ReferralsController::class, 'referralAcccept'])->middleware('checkUserPermission:REFERRALS_ACCEPT_REFERRAL');
                Route::delete('{id}/delete', [ReferralsController::class, 'destroy'])->middleware('checkUserPermission:REFERRALS_DELETE');
                Route::post('{id}/change-status', [ReferralsController::class, 'changeStatus']);
            });
        });

        // Route::post('/register', 'RegistrationController@registration')->name("register");
    });

    Route::group(['namespace' => '\App\Http\Controllers\Api'], function () {
        Route::post('/sync', 'CommonController@sync');
        Route::get('/metric-list', [CommonController::class, 'metric']);
        Route::get('/metric-group-list', [CommonController::class, 'metricGroup']);
        Route::get('/metric-category-list', [CommonController::class, 'metricCategory']);
        Route::get('/language/{local_key}', [CommonController::class, 'languageTranslationData']);
        Route::get('/roles-list', [CommonController::class, 'mstRoles']);
        Route::get('/user-list', [CommonController::class, 'users']);
        Route::get('/privileges-list', [CommonController::class, 'privilegesList']);
        Route::post('/template-sync', [CommonController::class, 'templateSync']);
        Route::get('/company-list', [CommonController::class, 'companyList']);
        Route::get('/webuser-list', [CommonController::class, 'webUserList']);
        Route::get('/stackmodule-list', [CommonController::class, 'stackModuleList']);
        Route::get('/web-metric-and-metric-group-list', [CommonController::class, 'metricAndMetricGroupList']);
        Route::post('/upload-file', [CommonController::class, 'uploadFile']);
        Route::post('/delete-file', [CommonController::class, 'deleteFile']);
    });

    Route::group(['namespace' => '\App\Http\Controllers\Api\Web'], function () {
        //     Route::post('/sign-up', 'LoginController@register');
        Route::group(['prefix' => 'user'], function () {
            Route::post('/login', [WebLoginController::class, 'authenticate']);
            Route::post('/forgot-password', [WebLoginController::class, 'forgotPasswordOtp']);
            Route::post('{otp}/reset-password', [WebLoginController::class, 'resetPasswordViaLink']);
            Route::post('/check-my-email', [WebLoginController::class, 'checkMyEmail']);
            Route::post('/verify-user-change-password', [WebLoginController::class, 'verifyUserChangePassword']);
            Route::post('/login-as-company', [WebLoginController::class, 'loginAsCompany']);
        });

        Route::group(['middleware' => ['authenticateUser:' . config('global.USER_TYPE.ADMIN') . "," . config('global.USER_TYPE.WEB')]], function () {

            Route::group(['prefix' => 'user'], function () {
                Route::get('/', 'UserController@index');
                Route::get('{id}/details', 'UserController@details');
                Route::post('{id}/update', 'UserController@update');
                Route::post('{id}/change-status', 'UserController@changeStatus');
                Route::post('change-password', [WebLoginController::class, 'changePassword']);
                Route::post('{id}/update-profile', [WebLoginController::class, 'updateProfile']);
                Route::get('me', [WebLoginController::class, 'me']);
                Route::post('logout', [WebLoginController::class, 'logout']);
                Route::post('leave-organization', [UserController::class, 'leaveOrganization']);
                Route::post('{id}/profile-image', [UserController::class, 'profileImage']);
                Route::post('/assign-stack', [UserController::class, 'assignStacks']);
            });

            Route::group(['prefix' => 'organization'], function () {
                Route::post('{id}/update', [OrganizationController::class, 'update']);
                Route::post('{id}/change-logo', [OrganizationController::class, 'changeLogo']);
            });

            //Company Project
            Route::group(['prefix' => 'company-project'], function () {
                Route::get('/', [CompanyProjectController::class, 'index']);
                Route::post('/create', [CompanyProjectController::class, 'store']);
                Route::get('{id}/details', [CompanyProjectController::class, 'details']);
                Route::post('{id}/update', [CompanyProjectController::class, 'update']);
                Route::delete('{id}/delete', [CompanyProjectController::class, 'destroy']);
                Route::post('bulk-update', [CompanyProjectController::class, 'bulkUpdate']);
            });

            Route::group(['prefix' => 'companystack-category'], function () {
                Route::get('/', [CompanyStackCategoryController::class, 'index']);
                Route::post('create', [CompanyStackCategoryController::class, 'store']);
                Route::get('{id}/details', [CompanyStackCategoryController::class, 'details']);
                Route::post('{id}/update', [CompanyStackCategoryController::class, 'update']);
                Route::delete('{id}/delete', [CompanyStackCategoryController::class, 'destroy']);
                Route::post('update-sequence', [CompanyStackCategoryController::class, 'updateSequence']);
                Route::post('duplicate-category', [CompanyStackCategoryController::class, 'duplicateCategory']);
                Route::post('bulk-update', [CompanyStackCategoryController::class, 'bulkUpdate']);
            });

            //Refer Client
            Route::group(['prefix' => 'refer-client'], function () {
                Route::get('/', [ReferClientController::class, 'index']);
                Route::post('/create', [ReferClientController::class, 'store']);
                Route::get('{id}/details', [ReferClientController::class, 'details']);
                Route::post('{id}/update', [ReferClientController::class, 'update']);
                Route::delete('{id}/delete', [ReferClientController::class, 'destroy']);
                Route::post('{id}/change-status', [ReferClientController::class, 'changeStatus']);
            });

            //Team-Stack
            Route::group(['prefix' => 'team-stack'], function () {
                Route::get('/', [TeamStackController::class, 'index']);
                Route::post('/create', [TeamStackController::class, 'store']);
                Route::post('details', [TeamStackController::class, 'details']);
                Route::post('{id}/update', [TeamStackController::class, 'update']);
                Route::delete('{id}/delete', [TeamStackController::class, 'destroy']);
                Route::post('{id}/update-role', [TeamStackController::class, 'updateRole']);
                Route::post('save', [TeamStackController::class, 'save']);
            });

            //Scorecard Stack
            Route::group(['prefix' => 'scorecard-stack'], function () {
                Route::get('/', [ScorecardStackController::class, 'index']);
                Route::post('/create', [ScorecardStackController::class, 'store']);
                Route::post('details', [ScorecardStackController::class, 'details']);
                Route::post('{id}/update', [ScorecardStackController::class, 'update']);
                Route::delete('{id}/delete', [ScorecardStackController::class, 'destroy']);
                Route::post('save', [ScorecardStackController::class, 'save']);
                Route::post('node-entry', [ScorecardStackController::class, 'nodeEntry']);
                Route::post('change-value', [ScorecardStackController::class, 'updateScorecardNodeData']);
            });

            Route::group(['prefix' => 'team-member'], function () {
                Route::get('/', [TeamMemberController::class, 'index']);
                Route::post('/create', [TeamMemberController::class, 'store']);
                Route::get('{id}/details', [TeamMemberController::class, 'details']);
                Route::post('{id}/update', [TeamMemberController::class, 'update']);
                Route::post('{id}/update-role', [TeamMemberController::class, 'updateRole']);
                Route::post('{id}/accept-invitation', [TeamMemberController::class, 'acceptInvitation']);
            });

            Route::group(['prefix' => 'goal-stack'], function () {
                Route::get('/', [GoalStackController::class, 'index']);
                Route::post('details', [GoalStackController::class, 'details']);
                Route::delete('{id}/delete', [GoalStackController::class, 'destroy']);
                Route::post('save', [GoalStackController::class, 'save']);
            });

            /* Company Stack Modules */
            Route::group(['prefix' => 'company-stack-module'], function () {
                Route::post('/create', [CompanyStackModuleController::class, 'store']);
                Route::get('{id}/details', [CompanyStackModuleController::class, 'details']);
                Route::post('{id}/update', [CompanyStackModuleController::class, 'update']);
                Route::delete('{id}/delete', [CompanyStackModuleController::class, 'destroy']);
                Route::post('{id}/change-status', [CompanyStackModuleController::class, 'changeStatus']);
                Route::post('bulk-update', [CompanyStackModuleController::class, 'bulkUpdate']);
                Route::post('duplicate-stack', [CompanyStackModuleController::class, 'duplicateStack']);
            });

            Route::group(['prefix' => 'archive'], function () {
                Route::post('/', [ScorecardStackArchiveController::class, 'index']);
                Route::post('save', [ScorecardStackArchiveController::class, 'save']);
                Route::post('{id}/details', [ScorecardStackArchiveController::class, 'details']);
                Route::delete('{id}/restore', [ScorecardStackArchiveController::class, 'restore']);
            });
        });
    });

    //Registration Api
    Route::group(['prefix' => 'api-integration'], function () {
        Route::any('/registration', [ApiIntegrationController::class, 'store']);
        Route::get('/subscription-list', [ApiIntegrationController::class, 'subscriptionList']);
        Route::get('{id}/subscription-details', [ApiIntegrationController::class, 'subscriptionDetail']);
        Route::post('/subscription-payment', [ApiIntegrationController::class, 'subscriptionPayment']);
    });

    Route::group(['prefix' => 'metrics'], function () {
        Route::post('/', [WebMetricController::class, 'index']);
        Route::post('/create', [WebMetricController::class, 'store']);
        Route::post('{id}/update', [WebMetricController::class, 'update']);
        Route::delete('{id}/delete', [WebMetricController::class, 'destroy']);
        Route::get('{id}/details', [WebMetricController::class, 'details']);
        Route::post('{id}/change-status', [WebMetricController::class, 'changeStatus']);
    });
});
