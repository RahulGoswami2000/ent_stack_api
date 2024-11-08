<?php

namespace App\Providers;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\CompanyMatrix;
use App\Models\CompanyProject;
use App\Models\CompanyStackCategory;
use App\Models\CompanyStackModules;
use App\Models\GoalStack;
use App\Models\Metric;
use App\Models\MetricCategory;
use App\Models\MetricGroup;
use App\Models\MetricGroupMatrix;
use App\Models\TeamStack;
use App\Models\ReferClient;
use App\Models\ScorecardStack;
use App\Models\ScorecardStackArchive;
use App\Models\ScorecardStackNodeData;
use App\Models\ScorecardStackNodes;
use App\Models\Stack;
use App\Models\SubscriptionHistory;
use App\Models\Template;
use App\Models\UserStackAccess;
use App\Observers\CompanyMatrixObserver;
use App\Observers\CompanyObserver;
use App\Observers\CompanyProjectObserver;
use App\Observers\ReferClientObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\ScorecardStackObserver;
use App\Observers\UserObserver;
use App\Observers\RoleObserver;
use App\Observers\TeamStackObserver;
use App\Observers\CompanyStackModuleObserve;
use App\Observers\GoalStackObserver;
use App\Observers\MetricCategoryObserver;
use App\Observers\MetricGroupMatrixObserver;
use App\Observers\MetricGroupObserver;
use App\Observers\MetricObserver;
use App\Observers\ProjectCategoryObserver;
use App\Observers\ScorecardStackArchiveObserver;
use App\Observers\ScorecardStackNodeDataObserver;
use App\Observers\ScorecardStackNodesObserver;
use App\Observers\StackObserver;
use App\Observers\SubscriptionHistoryObserver;
use App\Observers\TemplateObserver;
use App\Observers\UserStackAccessObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            // SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Subscription::observe(SubscriptionObserver::class);
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        CompanyMatrix::observe(CompanyMatrixObserver::class);
        Company::observe(CompanyObserver::class);
        CompanyProject::observe(CompanyProjectObserver::class);
        TeamStack::observe(TeamStackObserver::class);
        ReferClient::observe(ReferClientObserver::class);
        Template::observe(TemplateObserver::class);
        ScorecardStack::observe(ScorecardStackObserver::class);
        CompanyStackModules::observe(CompanyStackModuleObserve::class);
        Metric::observe(MetricObserver::class);
        MetricCategory::observe(MetricCategoryObserver::class);
        MetricGroup::observe(MetricGroupObserver::class);
        MetricGroupMatrix::observe(MetricGroupMatrixObserver::class);
        CompanyStackCategory::observe(ProjectCategoryObserver::class);
        Stack::observe(StackObserver::class);
        UserStackAccess::observe(UserStackAccessObserver::class);
        SubscriptionHistory::observe(SubscriptionHistoryObserver::class);
        ScorecardStackNodes::observe(ScorecardStackNodesObserver::class);
        ScorecardStackNodeData::observe(ScorecardStackNodeDataObserver::class);
        GoalStack::observe(GoalStackObserver::class);
        ScorecardStackArchive::observe(ScorecardStackArchiveObserver::class);
    }
}
