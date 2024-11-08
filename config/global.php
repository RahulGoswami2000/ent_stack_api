<?php

return [

    'STACK_MODULES' => [
        'SCORECARD_STACK' => ['id' => 1, 'name' => 'Scorecard Stack', 'can_copy' => 1],
        'TEAM_STACK'      => ['id' => 2, 'name' => 'Team Stack', 'can_copy' => 1],
        'GOAL_STACK'      => ['id' => 3, 'name' => 'Goal Stack', 'can_copy' => 1]
    ],

    'MAIL_TEMPLATE' => [
        //        'RESET_PASSWORD' => ['key' => 'reset_password', 'type' => 'email', 'name' => 'Reset Password'],
        //        'WELCOME_EMAIL'  => ['key' => 'welcome_email', 'type' => 'email', 'name' => 'Welcome Email'],
        //        'EMAIL'          => ['key' => 'email', 'type' => 'email', 'name' => 'Email', 'payload' => ['first_name' => '{{first_name}}', 'last_name' => '{{last_name}}', 'email' => '{{email}}']],
        'METRIC_ENTRY_REMINDERS' => [
            'key'     => 'metric_entry_reminders',
            'type'    => 'email',
            'name'    => 'Metric Entry Reminders',
            'payload' => [
                'first_name'    => '{{first_name}}',
                'last_name'     => '{{last_name}}',
                'email'         => '{{email}}',
                'project_name'  => '{{project_name}}',
                'category_name' => '{{category_name}}',
                'title'         => '{{title}}',
                'stack'         => '{{stack}}',
                'country_code'  => '{{country_code}}',
                'mobile_no'     => '{{mobile_number}}',
            ]
        ],
        'FORGOT_PASSWORD' => [
            'key'     => 'forgot_password',
            'type'    => 'email',
            'name'    => 'Forgot Password',
            'payload' => [
                'first_name'   => '{{first_name}}',
                'last_name'    => '{{last_name}}',
                'email'        => '{{email}}',
                'link'         => '{{link}}',
                'country_code' => '{{country_code}}',
                'mobile_no'    => '{{mobile_number}}',
            ]
        ],
        'NEW_MEMBER_ADDED' => [
            'key'     => 'new_member_added',
            'type'    => 'email',
            'name'    => 'New Member Added',
            'payload' => [
                'email'            => '{{email}}',
                'link'             => '{{link}}',
                'job_title'        => '{{job_title}}',
                'job_role'         => '{{job_role}}',
                'owner_first_name' => '{{owner_first_name}}',
                'owner_last_name'  => '{{owner_last_name}}',
                'owner_email'      => '{{owner_email}}',
            ]
        ],
    ],

    'ROLES'        => [
        'SUPER_ADMIN' => 'Super Admin',
        'ADMIN'       => 'Admin',
        'OWNER'       => 'Owner',
        'CONTRIBUTOR' => 'Contributor',
        'VIEWERS'     => 'Viewer',
    ],
    'USER_TYPE'        => [
        'ADMIN' => 1,
        'WEB'   => 2,
    ],
    'UPLOAD_PATHS'     => [
        'USER_PROFILE' => 'uploads/general/user/',
        'COMPANY_LOGO' => 'uploads/general/company/',
    ],
    'FORMAT_OF_MATRIX' => [
        'DOLLAR'     => ['id' => 1, 'name' => '$'],
        'PERCENTAGE' => ['id' => 2, 'name' => '%'],
        'QTY'        => ['id' => 3, 'name' => 'Qty'],
    ],
    'SCORECARD_TYPE'   => [
        'WEEKLY'       => ['id' => 1, 'name' => 'Weekly'],
        'BI_WEEKLY'    => ['id' => 2, 'name' => 'Bi-weekly'],
        //        'SEMI_MONTHLY' => ['id' => 3, 'name' => 'Semi-monthly'],
        'MONTHLY'      => ['id' => 4, 'name' => 'Monthly'],
        'QUARTERLY'    => ['id' => 5, 'name' => 'Quarterly'],
        'ANNUALLY'     => ['id' => 6, 'name' => 'Annually'],
    ],
    'MENU_TYPE'   => [
        'BOTH'  => ['id' => 0, 'name' => 'Both'],
        'ADMIN' => ['id' => 1, 'name' => 'Admin'],
        'WEB'   => ['id' => 2, 'name' => 'Web'],
    ],
    'ROLE_TYPE' => [
        'ADMIN' => ['id' => 1, 'name' => 'Admin'],
        'WEB'   => ['id' => 2, 'name' => 'Web'],
    ],
    'STATUS' => [
        'ACTIVE'   => ['id' => 1, 'name' => 'Active'],
        'INACTIVE' => ['id' => 0, 'name' => 'Inactive'],
    ],
    'REFERRAL_STATUS' => [
        'PENDING'   => ['id' => 0, 'name' => 'Pending'],
        'REFERRED'  => ['id' => 1, 'name' => 'Referred'],
        'Cancelled' => ['id' => 2, 'name' => 'Cancelled'],
    ],
    'DAYS' => [
        'SUNDAY'    => ['id' => 0, 'name' => 'Sunday'],
        'MONDAY'    => ['id' => 1, 'name' => 'Monday'],
        'TUESDAY'   => ['id' => 2, 'name' => 'Tuesday'],
        'WEDNESDAY' => ['id' => 3, 'name' => 'Wednesday'],
        'THURSDAY'  => ['id' => 4, 'name' => 'Thursday'],
        'FRIDAY'    => ['id' => 5, 'name' => 'Friday'],
        'SATURDAY'  => ['id' => 6, 'name' => 'Saturday'],
    ],
    'SCORECARD_ARCHIVE_TYPE' => [
        'SCORECARD' => ['id' => 1, 'name' => 'Scorecard'],
        'METRIC'    => ['id' => 2, 'name' => 'Metric'],
    ],
];
