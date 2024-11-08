<?php

namespace Database\Seeders;

use App\Models\LovPrivileges;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LovPrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LovPrivileges::truncate();
        Schema::disableForeignKeyConstraints();
        $sequence   = 0;
        $id         = 10000;
        $parentData = [
            // Roles
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 1,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Roles', 'controller' => '/roles', 'permission_key' => 'ROLES_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/roles/create', 'permission_key' => 'ROLES_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/roles/details', 'permission_key' => 'ROLES_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/roles/update', 'permission_key' => 'ROLES_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/roles/delete', 'permission_key' => 'ROLES_DELETE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 5, 'name' => 'Change Status', 'controller' => '/roles/change-status', 'permission_key' => 'ROLES_CHANGE_STATUS', 'is_active' => 1],
                ]
            ],

            // My Team Member
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 1,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'My Team Members', 'controller' => '/team-member', 'permission_key' => 'MY_TEAM_MEMBER_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/team-member/create', 'permission_key' => 'MY_TEAM_MEMBER_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/team-member/details', 'permission_key' => 'MY_TEAM_MEMBER_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/team-member/update', 'permission_key' => 'MY_TEAM_MEMBER_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Change Status', 'controller' => '/team-member/change-status', 'permission_key' => 'MY_TEAM_MEMBER_CHANGE_STATUS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 5, 'name' => 'Assign Client', 'controller' => '/team-member/assign-client', 'permission_key' => 'MY_TEAM_MEMBER_ASSIGN_CLIENT', 'is_active' => 1],
                ]
            ],

            // Metric Categories
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 0,
                'group_id'  => 1, 'parent_id' => 0,
                'name'      => 'Metric Categories', 'controller' => '/metric-categories', 'permission_key' => 'METRIC_CATEGORIES_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/metric-categories/create', 'permission_key' => 'METRIC_CATEGORIES_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/metric-categories/details', 'permission_key' => 'METRIC_CATEGORIES_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/metric-categories/update', 'permission_key' => 'METRIC_CATEGORIES_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/metric-categories/delete', 'permission_key' => 'METRIC_CATEGORIES_DELETE', 'is_active' => 1],

                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 5, 'name' => 'Change Status', 'controller' => '/metric-categories/change-status', 'permission_key' => 'METRIC_CATEGORIES_CHANGE_STATUS', 'is_active' => 1],
//                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 6, 'name' => 'Remove Category', 'controller' => '/metric-categories/remove-category', 'permission_key' => 'METRIC_CATEGORIES_UPDATE', 'is_active' => 1],
//                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 7, 'name' => 'Add Metric', 'controller' => '/metric-categories/add-metric', 'permission_key' => 'METRIC_CATEGORIES_UPDATE', 'is_active' => 1],
                ]
            ],

            // Metrics
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 0,
                'group_id'  => 1, 'parent_id' => 0,
                'name'      => 'Metrics', 'controller' => '/metrics', 'permission_key' => 'METRICS_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/metrics/create', 'permission_key' => 'METRICS_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/metrics/details', 'permission_key' => 'METRICS_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/metrics/update', 'permission_key' => 'METRICS_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/metrics/delete', 'permission_key' => 'METRICS_DELETE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 5, 'name' => 'Change Status', 'controller' => '/metrics/change-status', 'permission_key' => 'METRICS_CHANGE_STATUS', 'is_active' => 1],
                ],
            ],

            // Metric Box/Group
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 0,
                'group_id'  => 1, 'parent_id' => 0,
                'name'      => 'Metrics Box', 'controller' => '/metric-box', 'permission_key' => 'METRIC_GROUP_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/metric-box/create', 'permission_key' => 'METRIC_GROUP_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/metric-box/details', 'permission_key' => 'METRIC_GROUP_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/metric-box/update', 'permission_key' => 'METRIC_GROUP_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/metric-box/delete', 'permission_key' => 'METRIC_GROUP_DELETE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 5, 'name' => 'Change Status', 'controller' => '/metric-box/change-status', 'permission_key' => 'METRIC_GROUP_CHANGE_STATUS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 6, 'name' => 'Change Category', 'controller' => '/metric-box/change-category', 'permission_key' => 'METRIC_GROUP_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 7, 'name' => 'Add Metric', 'controller' => '/metric-box/add-metric', 'permission_key' => 'METRIC_GROUP_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 1, 'parent_id' => $id - 8, 'name' => 'Remove Metric', 'controller' => '/metric-box/remove-metric', 'permission_key' => 'METRIC_GROUP_UPDATE', 'is_active' => 1],
                ],
            ],

            //Client Management
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 0,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Clients', 'controller' => '/client-management', 'permission_key' => 'CLIENT_MANAGEMENT_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'Detail View', 'controller' => '/client-management/details', 'permission_key' => 'CLIENT_MANAGEMENT_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Edit', 'controller' => '/client-management/update', 'permission_key' => 'CLIENT_MANAGEMENT_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 0, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Change Status', 'controller' => '/client-management/change-status', 'permission_key' => 'CLIENT_MANAGEMENT_CHANGE_STATUS', 'is_active' => 1],
                ]
            ],

            // Subscription
            //    [
            //        'id'        => $id += 1,
            //        'sequence'  => $sequence += 1,
            //        'menu_type' => 1,
            //        'group_id'  => 0, 'parent_id' => 0,
            //        'name'      => 'Subscriptions', 'controller' => '/subscription', 'permission_key' => 'SUBSCRIPTION_INDEX',
            //        'is_active' => 1,
            //        'childData' => [
            //            ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/subscription/create', 'permission_key' => 'SUBSCRIPTION_CREATE', 'is_active' => 1],
            //            ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/subscription/details', 'permission_key' => 'SUBSCRIPTION_DETAILS', 'is_active' => 1],
            //            ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/subscription/update', 'permission_key' => 'SUBSCRIPTION_UPDATE', 'is_active' => 1],
            //            ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/subscription/delete', 'permission_key' => 'SUBSCRIPTION_DELETE', 'is_active' => 1],
            //            ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 5, 'name' => 'Change Status', 'controller' => '/subscription/change-status', 'permission_key' => 'SUBSCRIPTION_CHANGE_STATUS', 'is_active' => 1],
            //        ]
            //    ],

            //Template
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 1,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Templates', 'controller' => '/template', 'permission_key' => 'TEMPLATE_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'Detail View', 'controller' => '/template/details', 'permission_key' => 'TEMPLATE_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Edit', 'controller' => '/template/update', 'permission_key' => 'TEMPLATE_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Change Status', 'controller' => '/template/change-status', 'permission_key' => 'TEMPLATE_CHANGE_STATUS', 'is_active' => 1],
                ]
            ],

            // Referrals
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 1,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Referrals', 'controller' => '/referrals', 'permission_key' => 'REFERRALS_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'Accept Referral', 'controller' => '/referrals/accept-referral', 'permission_key' => 'REFERRALS_ACCEPT_REFERRAL', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Delete', 'controller' => '/referrals/delete', 'permission_key' => 'REFERRALS_DELETE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 1, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Change Status', 'controller' => '/referrals/change-status', 'permission_key' => 'REFERRALS_CHANGE_STATUS', 'is_active' => 1]
                ]
            ],

            // Company Project
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 2,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Company Project', 'controller' => '/company-project', 'permission_key' => 'COMPANY_PROJECT_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/company-project/create', 'permission_key' => 'COMPANY_PROJECT_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/company-project/details', 'permission_key' => 'COMPANY_PROJECT_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/company-project/update', 'permission_key' => 'COMPANY_PROJECT_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/company-project/delete', 'permission_key' => 'COMPANY_PROJECT_DELETE', 'is_active' => 1],
                ],
            ],

            // Project Category
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 2,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Project Category', 'controller' => '/project-category', 'permission_key' => 'COMPANY_STACK_CATEGORY_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/company-project/create', 'permission_key' => 'COMPANY_STACK_CATEGORY_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/company-project/details', 'permission_key' => 'COMPANY_STACK_CATEGORY_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/company-project/update', 'permission_key' => 'COMPANY_STACK_CATEGORY_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/company-project/delete', 'permission_key' => 'COMPANY_STACK_CATEGORY_DELETE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 5, 'name' => 'Update Sequence', 'controller' => '/company-project/update-sequence', 'permission_key' => 'COMPANY_STACK_CATEGORY_UPDATE_SEQUENCE', 'is_active' => 1],
                ],
            ],

            // Refer Client
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 2,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Refer Client', 'controller' => '/refer-client', 'permission_key' => 'REFER_CLIENT_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/refer-client/create', 'permission_key' => 'REFER_CLIENT_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/refer-client/details', 'permission_key' => 'REFER_CLIENT_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/refer-client/update', 'permission_key' => 'REFER_CLIENT_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/refer-client/delete', 'permission_key' => 'REFER_CLIENT_DELETE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Change Status', 'controller' => '/refer-client/change-status', 'permission_key' => 'REFER_CHANGE_STATUS', 'is_active' => 1]
                ]
            ],

            //Team Stack
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 2,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Team Stack', 'controller' => '/team-stack', 'permission_key' => 'TEAM_STACK_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/team-stack/create', 'permission_key' => 'TEAM_STACK_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/team-stack/details', 'permission_key' => 'TEAM_STACK_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/team-stack/update', 'permission_key' => 'TEAM_STACK_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/team-stack/delete', 'permission_key' => 'TEAM_STACK_DELETE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 5, 'name' => 'Update Role', 'controller' => '/team-stack/update-role', 'permission_key' => 'TEAM_STACK_UPDATE_ROLE', 'is_active' => 1],
                ]
            ],

            // Scorecard Stack
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 2,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Scorecard Stack', 'controller' => '/scorecard-stack', 'permission_key' => 'SCORECARD_STACK_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/scorecard-stack/create', 'permission_key' => 'SCORECARD_STACK_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/scorecard-stack/details', 'permission_key' => 'SCORECARD_STACK_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/scorecard-stack/update', 'permission_key' => 'SCORECARD_STACK_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/scorecard-stack/delete', 'permission_key' => 'SCORECARD_STACK_DELETE', 'is_active' => 1],
                ]
            ],

            //Team Member
            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 2,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Team Member', 'controller' => '/team-member', 'permission_key' => 'TEAM_MEMBER_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/team-member/create', 'permission_key' => 'TEAM_MEMBER_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/team-member/details', 'permission_key' => 'TEAM_MEMBER_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/team-member/update', 'permission_key' => 'TEAM_MEMBER_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Update Role', 'controller' => '/team-member/update-role', 'permission_key' => 'TEAM_MEMBER_UPDATE_ROLE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 5, 'name' => 'Accept Invitation', 'controller' => '/team-member/accept-invitation', 'permission_key' => 'TEAM_MEMBER_ACCEPT_INVITATION', 'is_active' => 1],
                ]
            ],

            // Goal Stack

            [
                'id'        => $id += 1,
                'sequence'  => $sequence += 1,
                'menu_type' => 2,
                'group_id'  => 0, 'parent_id' => 0,
                'name'      => 'Goal Stack', 'controller' => '/goal-stack', 'permission_key' => 'GOAL_STACK_INDEX',
                'is_active' => 1,
                'childData' => [
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 1, 'name' => 'New', 'controller' => '/goal-stack/create', 'permission_key' => 'GOAL_STACK_CREATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 2, 'name' => 'Detail View', 'controller' => '/goal-stack/details', 'permission_key' => 'GOAL_STACK_DETAILS', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 3, 'name' => 'Edit', 'controller' => '/goal-stack/update', 'permission_key' => 'GOAL_STACK_UPDATE', 'is_active' => 1],
                    ['id' => $id += 1, 'sequence' => $sequence += 1, 'menu_type' => 2, 'group_id' => 0, 'parent_id' => $id - 4, 'name' => 'Delete', 'controller' => '/goal-stack/delete', 'permission_key' => 'GOAL_STACK_DELETE', 'is_active' => 1],
                ]
            ],
        ];

        foreach ($parentData as $value) {
            LovPrivileges::create([
                'id'             => $value['id'],
                'sequence'       => $value['sequence'],
                'menu_type'      => $value['menu_type'],
                'group_id'       => $value['group_id'],
                'parent_id'      => $value['parent_id'],
                'name'           => $value['name'],
                'controller'     => $value['controller'],
                'permission_key' => $value['permission_key'],
                'is_active'      => $value['is_active'],
            ]);

            if (!empty($value['childData'])) {
                foreach ($value['childData'] as $value1) {
                    LovPrivileges::create([
                        'id'             => $value1['id'],
                        'sequence'       => $value1['sequence'],
                        'menu_type'      => $value1['menu_type'],
                        'group_id'       => $value1['group_id'],
                        'parent_id'      => $value1['parent_id'],
                        'name'           => $value1['name'],
                        'controller'     => $value1['controller'],
                        'permission_key' => $value1['permission_key'],
                        'is_active'      => $value1['is_active'],
                    ]);
                }
            }
        }

        $this->call(RoleSeeder::class);
        Schema::enableForeignKeyConstraints();
    }
}
