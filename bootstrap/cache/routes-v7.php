<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xzRakphUCXaHbA8M',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JAGVIOi3PB83iub2',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JEuInKhQ0gqYuxDx',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'logout',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/evaluations/pending-requests' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.pending-requests',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/evaluations' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/evaluations/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/reports' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/organizations' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/organizations/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.logs.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/logs/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.logs.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/logs/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.logs.auth',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/logs/action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.logs.action',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/logs/clear' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.logs.clear',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.profile.update',
          ),
          1 => NULL,
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/president/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/president/events' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/president/events/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/president/students' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.students.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'president.students.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/president/students/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.students.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/president/students/bulk-upload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.students.bulk-upload',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'president.students.bulk-upload.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/president/evaluations' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.evaluations.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/president/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'president.profile.update',
          ),
          1 => NULL,
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adviser/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adviser/events' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.events.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adviser/approvals' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.approvals.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adviser/approvals/history' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.approvals.history',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adviser/students' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.students.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adviser/evaluations' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.evaluations.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adviser/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.profile.update',
          ),
          1 => NULL,
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/treasurer/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/treasurer/collections' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collections.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/treasurer/reports' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.reports.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/treasurer/reports/summary' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.reports.summary',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/treasurer/reports/collection' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.reports.collection',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/treasurer/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.profile.update',
          ),
          1 => NULL,
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/debug-session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::g6RHitEazwVGqrYO',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/evaluations/(?|([^/]++)(?|/(?|form(*:42)|verify(?|(*:58)|\\-page(*:71))|student\\-submissions(*:99)|already\\-submitted(*:124))|(*:133))|thankyou(*:150)|([^/]++)/dates(*:172))|/ad(?|min/(?|evaluations/([^/]++)(?|(*:217)|/(?|e(?|dit(*:236)|ligibility\\-info(*:260))|a(?|ctivate(*:280)|i\\-insights(*:299))|qr(*:310)|close(*:323)|r(?|eopen(*:340)|aw\\-responses(*:361))|generate\\-insights(*:388)|stats(*:401)|bulk\\-upload(*:421)|download\\-template(*:447))|(*:456))|reports/([^/]++)/(?|generate(*:493)|regenerate(*:511)|view(*:523)|download(*:539)|send(*:551))|organizations/([^/]++)(?|(*:585)|/(?|edit(*:601)|members(?|(*:619))|settings(*:636))|(*:645))|users/([^/]++)(?|/(?|block(*:680)|unblock(*:695)|reset\\-password(*:718))|(*:727)))|viser/(?|ev(?|ents/([^/]++)(*:764)|aluations/([^/]++)(*:790))|approvals/(?|([^/]++)(?|(*:823)|/(?|approve(*:842)|reject(*:856)))|stats(*:871))))|/president/(?|ev(?|ents/([^/]++)(?|(*:917)|/(?|edit(*:933)|upload\\-document(*:957)|mark\\-finished(*:979)|re(?|quest\\-evaluation(*:1009)|fresh\\-students(*:1033))|guests(?|(*:1052)|/(?|bulk(*:1069)|([^/]++)(*:1086)|template(*:1103))))|(*:1115))|aluations/([^/]++)(*:1143))|students/([^/]++)(?|/edit(*:1178)|(*:1187)))|/treasurer/(?|collection(?|s/([^/]++)(?|(*:1238)|/(?|([^/]++)/pay(*:1263)|bulk\\-pay(*:1281)|summary(*:1297)))|\\-reports/([^/]++)/(?|generate(*:1338)|regenerate(*:1357)|view(*:1370)|download(*:1387)))|receipts/([^/]++)/([^/]++)/(?|download(*:1436)|view(*:1449)|resend(*:1464)))|/storage/(.*)(*:1488))/?$}sDu',
    ),
    3 => 
    array (
      42 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'evaluations.form',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      58 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'evaluations.verify',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      71 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'evaluations.verify-page',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      99 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'evaluations.student-submissions',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      124 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'evaluations.already-submitted',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      133 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'evaluations.store',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      150 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'evaluations.thankyou',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      172 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'evaluations.dates',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      217 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.show',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      236 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.edit',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      260 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.eligibility-info',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      280 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.activate',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      299 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.ai-insights',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      310 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.qr',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      323 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.close',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      340 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.reopen',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      361 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.raw-responses',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      388 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.generate-insights',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      401 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.stats',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      421 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.bulk-upload',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      447 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.download-template',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      456 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.update',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.evaluations.destroy',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      493 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.generate',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      511 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.regenerate',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      523 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.view',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      539 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.download',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      551 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.reports.send',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      585 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.show',
          ),
          1 => 
          array (
            0 => 'organization',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      601 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.edit',
          ),
          1 => 
          array (
            0 => 'organization',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      619 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.members',
          ),
          1 => 
          array (
            0 => 'organization',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.members.store',
          ),
          1 => 
          array (
            0 => 'organization',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      636 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.settings',
          ),
          1 => 
          array (
            0 => 'organization',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      645 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.update',
          ),
          1 => 
          array (
            0 => 'organization',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.organizations.destroy',
          ),
          1 => 
          array (
            0 => 'organization',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      680 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.block',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      695 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.unblock',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      718 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.reset-password',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      727 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.update',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.users.delete',
          ),
          1 => 
          array (
            0 => 'user',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      764 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.events.show',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      790 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.evaluations.show',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      823 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.approvals.show',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      842 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.approvals.approve',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      856 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.approvals.reject',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      871 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adviser.approvals.stats',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      917 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.show',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      933 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.edit',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      957 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.upload-document',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      979 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.mark-finished',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1009 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.request-evaluation',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1033 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.refresh-students',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1052 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.guests',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.guests.store',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1069 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.guests.bulk',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1086 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.guests.delete',
          ),
          1 => 
          array (
            0 => 'event',
            1 => 'guest',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1103 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.guests.template',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1115 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.update',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'president.events.destroy',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1143 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.evaluations.show',
          ),
          1 => 
          array (
            0 => 'evaluation',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1178 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.students.edit',
          ),
          1 => 
          array (
            0 => 'student',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1187 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'president.students.update',
          ),
          1 => 
          array (
            0 => 'student',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'president.students.destroy',
          ),
          1 => 
          array (
            0 => 'student',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1238 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collections.show',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1263 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collections.pay',
          ),
          1 => 
          array (
            0 => 'event',
            1 => 'student',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1281 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collections.bulk-pay',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1297 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collections.summary',
          ),
          1 => 
          array (
            0 => 'event',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1338 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collection-reports.generate',
          ),
          1 => 
          array (
            0 => 'eventId',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1357 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collection-reports.regenerate',
          ),
          1 => 
          array (
            0 => 'eventId',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1370 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collection-reports.view',
          ),
          1 => 
          array (
            0 => 'eventId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1387 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.collection-reports.download',
          ),
          1 => 
          array (
            0 => 'eventId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1436 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.receipts.download',
          ),
          1 => 
          array (
            0 => 'eventId',
            1 => 'studentId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1449 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.receipts.view',
          ),
          1 => 
          array (
            0 => 'eventId',
            1 => 'studentId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1464 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'treasurer.receipts.resend',
          ),
          1 => 
          array (
            0 => 'eventId',
            1 => 'studentId',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1488 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'generated::xzRakphUCXaHbA8M' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:889:"function () {
                    $exception = null;

                    try {
                        \\Illuminate\\Support\\Facades\\Event::dispatch(new \\Illuminate\\Foundation\\Events\\DiagnosingHealth);
                    } catch (\\Throwable $e) {
                        if (app()->hasDebugModeEnabled()) {
                            throw $e;
                        }

                        report($e);

                        $exception = $e->getMessage();
                    }

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'C:\\\\Users\\\\Administrator\\\\Codes\\\\xampp\\\\htdocs\\\\Capstone Project Event Management System\\\\vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Foundation\\\\Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000003db0000000000000000";}}',
        'as' => 'generated::xzRakphUCXaHbA8M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JAGVIOi3PB83iub2' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:55:"function () {
    return \\redirect()->route(\'login\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000003d50000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::JAGVIOi3PB83iub2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthenticatedSessionController@create',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthenticatedSessionController@create',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JEuInKhQ0gqYuxDx' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthenticatedSessionController@store',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthenticatedSessionController@store',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::JEuInKhQ0gqYuxDx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthenticatedSessionController@destroy',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthenticatedSessionController@destroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'evaluations.form' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'evaluations/{evaluation}/form',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Public\\EvaluationController@form',
        'controller' => 'App\\Http\\Controllers\\Public\\EvaluationController@form',
        'as' => 'evaluations.form',
        'namespace' => NULL,
        'prefix' => '/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'evaluations.verify' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'evaluations/{evaluation}/verify',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Public\\EvaluationController@verifyStudent',
        'controller' => 'App\\Http\\Controllers\\Public\\EvaluationController@verifyStudent',
        'as' => 'evaluations.verify',
        'namespace' => NULL,
        'prefix' => '/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'evaluations.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'evaluations/{evaluation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Public\\EvaluationController@store',
        'controller' => 'App\\Http\\Controllers\\Public\\EvaluationController@store',
        'as' => 'evaluations.store',
        'namespace' => NULL,
        'prefix' => '/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'evaluations.student-submissions' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'evaluations/{evaluation}/student-submissions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Public\\EvaluationController@getStudentSubmissions',
        'controller' => 'App\\Http\\Controllers\\Public\\EvaluationController@getStudentSubmissions',
        'as' => 'evaluations.student-submissions',
        'namespace' => NULL,
        'prefix' => '/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'evaluations.verify-page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'evaluations/{evaluation}/verify-page',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Public\\EvaluationController@form',
        'controller' => 'App\\Http\\Controllers\\Public\\EvaluationController@form',
        'as' => 'evaluations.verify-page',
        'namespace' => NULL,
        'prefix' => '/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'evaluations.already-submitted' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'evaluations/{evaluation}/already-submitted',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Public\\EvaluationController@alreadySubmitted',
        'controller' => 'App\\Http\\Controllers\\Public\\EvaluationController@alreadySubmitted',
        'as' => 'evaluations.already-submitted',
        'namespace' => NULL,
        'prefix' => '/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'evaluations.thankyou' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'evaluations/thankyou',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Public\\EvaluationController@thankyou',
        'controller' => 'App\\Http\\Controllers\\Public\\EvaluationController@thankyou',
        'as' => 'evaluations.thankyou',
        'namespace' => NULL,
        'prefix' => '/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'evaluations.dates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'evaluations/{evaluation}/dates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Public\\EvaluationController@getAvailableDates',
        'controller' => 'App\\Http\\Controllers\\Public\\EvaluationController@getAvailableDates',
        'as' => 'evaluations.dates',
        'namespace' => NULL,
        'prefix' => '/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\DashboardController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\DashboardController@index',
        'as' => 'admin.dashboard',
        'namespace' => NULL,
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.pending-requests' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/pending-requests',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getPendingRequests',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getPendingRequests',
        'as' => 'admin.evaluations.pending-requests',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@index',
        'as' => 'admin.evaluations.index',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@create',
        'as' => 'admin.evaluations.create',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/evaluations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@store',
        'as' => 'admin.evaluations.store',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/{evaluation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@show',
        'as' => 'admin.evaluations.show',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/{evaluation}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@edit',
        'as' => 'admin.evaluations.edit',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'admin/evaluations/{evaluation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@update',
        'as' => 'admin.evaluations.update',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.activate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/evaluations/{evaluation}/activate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@activate',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@activate',
        'as' => 'admin.evaluations.activate',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.qr' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/{evaluation}/qr',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@showQRCode',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@showQRCode',
        'as' => 'admin.evaluations.qr',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.close' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/evaluations/{evaluation}/close',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@close',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@close',
        'as' => 'admin.evaluations.close',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.reopen' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/evaluations/{evaluation}/reopen',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@reopen',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@reopen',
        'as' => 'admin.evaluations.reopen',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.generate-insights' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/evaluations/{evaluation}/generate-insights',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@generateInsights',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@generateInsights',
        'as' => 'admin.evaluations.generate-insights',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.ai-insights' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/{evaluation}/ai-insights',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getAIInsights',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getAIInsights',
        'as' => 'admin.evaluations.ai-insights',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.raw-responses' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/{evaluation}/raw-responses',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getRawResponses',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getRawResponses',
        'as' => 'admin.evaluations.raw-responses',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.stats' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/{evaluation}/stats',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getStatsByDate',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getStatsByDate',
        'as' => 'admin.evaluations.stats',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.bulk-upload' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/evaluations/{evaluation}/bulk-upload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@bulkUpload',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@bulkUpload',
        'as' => 'admin.evaluations.bulk-upload',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.download-template' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/{evaluation}/download-template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@downloadCsvTemplate',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@downloadCsvTemplate',
        'as' => 'admin.evaluations.download-template',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/evaluations/{evaluation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@destroy',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@destroy',
        'as' => 'admin.evaluations.destroy',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.evaluations.eligibility-info' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/evaluations/{evaluation}/eligibility-info',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getEligibilityInfo',
        'controller' => 'App\\Http\\Controllers\\Admin\\EvaluationController@getEligibilityInfo',
        'as' => 'admin.evaluations.eligibility-info',
        'namespace' => NULL,
        'prefix' => 'admin/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/reports',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@index',
        'as' => 'admin.reports.index',
        'namespace' => NULL,
        'prefix' => 'admin/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.generate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/reports/{evaluation}/generate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@generateReport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@generateReport',
        'as' => 'admin.reports.generate',
        'namespace' => NULL,
        'prefix' => 'admin/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.regenerate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/reports/{evaluation}/regenerate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@regenerateReport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@regenerateReport',
        'as' => 'admin.reports.regenerate',
        'namespace' => NULL,
        'prefix' => 'admin/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/reports/{evaluation}/view',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@viewReport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@viewReport',
        'as' => 'admin.reports.view',
        'namespace' => NULL,
        'prefix' => 'admin/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.download' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/reports/{evaluation}/download',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@downloadReport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@downloadReport',
        'as' => 'admin.reports.download',
        'namespace' => NULL,
        'prefix' => 'admin/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.reports.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/reports/{evaluation}/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\ReportController@sendReport',
        'controller' => 'App\\Http\\Controllers\\Admin\\ReportController@sendReport',
        'as' => 'admin.reports.send',
        'namespace' => NULL,
        'prefix' => 'admin/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/organizations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@indexOrganizations',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@indexOrganizations',
        'as' => 'admin.organizations.index',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/organizations/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@create',
        'as' => 'admin.organizations.create',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/organizations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@store',
        'as' => 'admin.organizations.store',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/organizations/{organization}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@showOrganization',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@showOrganization',
        'as' => 'admin.organizations.show',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/organizations/{organization}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@edit',
        'as' => 'admin.organizations.edit',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'admin/organizations/{organization}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@update',
        'as' => 'admin.organizations.update',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/organizations/{organization}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@destroyOrganization',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@destroyOrganization',
        'as' => 'admin.organizations.destroy',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.members' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/organizations/{organization}/members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@getOrganizationMembers',
        'controller' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@getOrganizationMembers',
        'as' => 'admin.organizations.members',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.settings' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/organizations/{organization}/settings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@getOrganizationSettings',
        'controller' => 'App\\Http\\Controllers\\Admin\\OrganizationRegistrationController@getOrganizationSettings',
        'as' => 'admin.organizations.settings',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.organizations.members.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/organizations/{organization}/members',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@addOrganizationMember',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@addOrganizationMember',
        'as' => 'admin.organizations.members.store',
        'namespace' => NULL,
        'prefix' => 'admin/organizations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.block' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/users/{user}/block',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@blockUser',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@blockUser',
        'as' => 'admin.users.block',
        'namespace' => NULL,
        'prefix' => 'admin/users',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.unblock' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/users/{user}/unblock',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@unblockUser',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@unblockUser',
        'as' => 'admin.users.unblock',
        'namespace' => NULL,
        'prefix' => 'admin/users',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'admin/users/{user}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@updateOrganizationMember',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@updateOrganizationMember',
        'as' => 'admin.users.update',
        'namespace' => NULL,
        'prefix' => 'admin/users',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.delete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'admin/users/{user}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@deleteOrganizationMember',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@deleteOrganizationMember',
        'as' => 'admin.users.delete',
        'namespace' => NULL,
        'prefix' => 'admin/users',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.users.reset-password' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/users/{user}/reset-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@resetMemberPassword',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@resetMemberPassword',
        'as' => 'admin.users.reset-password',
        'namespace' => NULL,
        'prefix' => 'admin/users',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.logs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LogController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\LogController@index',
        'as' => 'admin.logs.index',
        'namespace' => NULL,
        'prefix' => 'admin/logs',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.logs.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/logs/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LogController@export',
        'controller' => 'App\\Http\\Controllers\\Admin\\LogController@export',
        'as' => 'admin.logs.export',
        'namespace' => NULL,
        'prefix' => 'admin/logs',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.logs.auth' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/logs/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LogController@getAuthLogs',
        'controller' => 'App\\Http\\Controllers\\Admin\\LogController@getAuthLogs',
        'as' => 'admin.logs.auth',
        'namespace' => NULL,
        'prefix' => 'admin/logs',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.logs.action' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/logs/action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LogController@getActionLogs',
        'controller' => 'App\\Http\\Controllers\\Admin\\LogController@getActionLogs',
        'as' => 'admin.logs.action',
        'namespace' => NULL,
        'prefix' => 'admin/logs',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.logs.clear' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/logs/clear',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\LogController@clear',
        'controller' => 'App\\Http\\Controllers\\Admin\\LogController@clear',
        'as' => 'admin.logs.clear',
        'namespace' => NULL,
        'prefix' => 'admin/logs',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@profile',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@profile',
        'as' => 'admin.profile',
        'namespace' => NULL,
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'admin/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminController@updateProfile',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminController@updateProfile',
        'as' => 'admin.profile.update',
        'namespace' => NULL,
        'prefix' => '/admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\DashboardController@index',
        'controller' => 'App\\Http\\Controllers\\President\\DashboardController@index',
        'as' => 'president.dashboard',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/events',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@index',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@index',
        'as' => 'president.events.index',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/events/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@create',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@create',
        'as' => 'president.events.create',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/events',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@store',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@store',
        'as' => 'president.events.store',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/events/{event}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@show',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@show',
        'as' => 'president.events.show',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/events/{event}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@edit',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@edit',
        'as' => 'president.events.edit',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'president/events/{event}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@update',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@update',
        'as' => 'president.events.update',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'president/events/{event}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@destroy',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@destroy',
        'as' => 'president.events.destroy',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.upload-document' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/events/{event}/upload-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@uploadDocument',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@uploadDocument',
        'as' => 'president.events.upload-document',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.mark-finished' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/events/{event}/mark-finished',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@markAsFinished',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@markAsFinished',
        'as' => 'president.events.mark-finished',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.request-evaluation' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/events/{event}/request-evaluation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@requestEvaluation',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@requestEvaluation',
        'as' => 'president.events.request-evaluation',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.refresh-students' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/events/{event}/refresh-students',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@refreshEligibleStudents',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@refreshEligibleStudents',
        'as' => 'president.events.refresh-students',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.students.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/students',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\StudentController@index',
        'controller' => 'App\\Http\\Controllers\\President\\StudentController@index',
        'as' => 'president.students.index',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.students.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/students/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\StudentController@create',
        'controller' => 'App\\Http\\Controllers\\President\\StudentController@create',
        'as' => 'president.students.create',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.students.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/students',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\StudentController@store',
        'controller' => 'App\\Http\\Controllers\\President\\StudentController@store',
        'as' => 'president.students.store',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.students.bulk-upload' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/students/bulk-upload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\StudentController@bulkUpload',
        'controller' => 'App\\Http\\Controllers\\President\\StudentController@bulkUpload',
        'as' => 'president.students.bulk-upload',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.students.bulk-upload.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/students/bulk-upload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\StudentController@bulkStore',
        'controller' => 'App\\Http\\Controllers\\President\\StudentController@bulkStore',
        'as' => 'president.students.bulk-upload.store',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.students.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/students/{student}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\StudentController@edit',
        'controller' => 'App\\Http\\Controllers\\President\\StudentController@edit',
        'as' => 'president.students.edit',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.students.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'president/students/{student}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\StudentController@update',
        'controller' => 'App\\Http\\Controllers\\President\\StudentController@update',
        'as' => 'president.students.update',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.students.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'president/students/{student}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\StudentController@destroy',
        'controller' => 'App\\Http\\Controllers\\President\\StudentController@destroy',
        'as' => 'president.students.destroy',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.guests' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/events/{event}/guests',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@showGuests',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@showGuests',
        'as' => 'president.events.guests',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.guests.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/events/{event}/guests',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@addGuest',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@addGuest',
        'as' => 'president.events.guests.store',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.guests.bulk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'president/events/{event}/guests/bulk',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@bulkAddGuests',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@bulkAddGuests',
        'as' => 'president.events.guests.bulk',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.guests.delete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'president/events/{event}/guests/{guest}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@deleteGuest',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@deleteGuest',
        'as' => 'president.events.guests.delete',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.events.guests.template' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/events/{event}/guests/template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EventController@downloadGuestTemplate',
        'controller' => 'App\\Http\\Controllers\\President\\EventController@downloadGuestTemplate',
        'as' => 'president.events.guests.template',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.evaluations.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/evaluations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EvaluationController@index',
        'controller' => 'App\\Http\\Controllers\\President\\EvaluationController@index',
        'as' => 'president.evaluations.index',
        'namespace' => NULL,
        'prefix' => 'president/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.evaluations.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/evaluations/{evaluation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\EvaluationController@show',
        'controller' => 'App\\Http\\Controllers\\President\\EvaluationController@show',
        'as' => 'president.evaluations.show',
        'namespace' => NULL,
        'prefix' => 'president/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'president/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\ProfileController@edit',
        'controller' => 'App\\Http\\Controllers\\President\\ProfileController@edit',
        'as' => 'president.profile',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'president.profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'president/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:president',
        ),
        'uses' => 'App\\Http\\Controllers\\President\\ProfileController@update',
        'controller' => 'App\\Http\\Controllers\\President\\ProfileController@update',
        'as' => 'president.profile.update',
        'namespace' => NULL,
        'prefix' => '/president',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\DashboardController@index',
        'controller' => 'App\\Http\\Controllers\\Adviser\\DashboardController@index',
        'as' => 'adviser.dashboard',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.events.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/events',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\EventController@index',
        'controller' => 'App\\Http\\Controllers\\Adviser\\EventController@index',
        'as' => 'adviser.events.index',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.events.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/events/{event}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\EventController@show',
        'controller' => 'App\\Http\\Controllers\\Adviser\\EventController@show',
        'as' => 'adviser.events.show',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.approvals.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/approvals',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@index',
        'controller' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@index',
        'as' => 'adviser.approvals.index',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.approvals.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/approvals/history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@history',
        'controller' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@history',
        'as' => 'adviser.approvals.history',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.approvals.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/approvals/{event}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@show',
        'controller' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@show',
        'as' => 'adviser.approvals.show',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.approvals.approve' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adviser/approvals/{event}/approve',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@approve',
        'controller' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@approve',
        'as' => 'adviser.approvals.approve',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.approvals.reject' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adviser/approvals/{event}/reject',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@reject',
        'controller' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@reject',
        'as' => 'adviser.approvals.reject',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.approvals.stats' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/approvals/stats',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@getStats',
        'controller' => 'App\\Http\\Controllers\\Adviser\\ApprovalController@getStats',
        'as' => 'adviser.approvals.stats',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.students.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/students',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\StudentController@index',
        'controller' => 'App\\Http\\Controllers\\Adviser\\StudentController@index',
        'as' => 'adviser.students.index',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.evaluations.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/evaluations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\EvaluationController@index',
        'controller' => 'App\\Http\\Controllers\\Adviser\\EvaluationController@index',
        'as' => 'adviser.evaluations.index',
        'namespace' => NULL,
        'prefix' => 'adviser/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.evaluations.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/evaluations/{evaluation}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\EvaluationController@show',
        'controller' => 'App\\Http\\Controllers\\Adviser\\EvaluationController@show',
        'as' => 'adviser.evaluations.show',
        'namespace' => NULL,
        'prefix' => 'adviser/evaluations',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adviser/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\ProfileController@edit',
        'controller' => 'App\\Http\\Controllers\\Adviser\\ProfileController@edit',
        'as' => 'adviser.profile',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adviser.profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adviser/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:adviser',
        ),
        'uses' => 'App\\Http\\Controllers\\Adviser\\ProfileController@update',
        'controller' => 'App\\Http\\Controllers\\Adviser\\ProfileController@update',
        'as' => 'adviser.profile.update',
        'namespace' => NULL,
        'prefix' => '/adviser',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\DashboardController@index',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\DashboardController@index',
        'as' => 'treasurer.dashboard',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collections.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/collections',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@index',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@index',
        'as' => 'treasurer.collections.index',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collections.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/collections/{event}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@show',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@show',
        'as' => 'treasurer.collections.show',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collections.pay' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'treasurer/collections/{event}/{student}/pay',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@pay',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@pay',
        'as' => 'treasurer.collections.pay',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collections.bulk-pay' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'treasurer/collections/{event}/bulk-pay',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@bulkPay',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@bulkPay',
        'as' => 'treasurer.collections.bulk-pay',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collections.summary' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/collections/{event}/summary',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@summary',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@summary',
        'as' => 'treasurer.collections.summary',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.receipts.download' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/receipts/{eventId}/{studentId}/download',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@downloadReceipt',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@downloadReceipt',
        'as' => 'treasurer.receipts.download',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.receipts.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/receipts/{eventId}/{studentId}/view',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@viewReceipt',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@viewReceipt',
        'as' => 'treasurer.receipts.view',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.receipts.resend' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'treasurer/receipts/{eventId}/{studentId}/resend',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@resendReceiptEmail',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\CollectionController@resendReceiptEmail',
        'as' => 'treasurer.receipts.resend',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.reports.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/reports',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ReportController@index',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ReportController@index',
        'as' => 'treasurer.reports.index',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collection-reports.generate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'treasurer/collection-reports/{eventId}/generate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ReportController@generate',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ReportController@generate',
        'as' => 'treasurer.collection-reports.generate',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collection-reports.regenerate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'treasurer/collection-reports/{eventId}/regenerate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ReportController@regenerate',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ReportController@regenerate',
        'as' => 'treasurer.collection-reports.regenerate',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collection-reports.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/collection-reports/{eventId}/view',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ReportController@view',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ReportController@view',
        'as' => 'treasurer.collection-reports.view',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.collection-reports.download' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/collection-reports/{eventId}/download',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ReportController@download',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ReportController@download',
        'as' => 'treasurer.collection-reports.download',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.reports.summary' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'treasurer/reports/summary',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ReportController@summaryReport',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ReportController@summaryReport',
        'as' => 'treasurer.reports.summary',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.reports.collection' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'treasurer/reports/collection',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ReportController@collectionReport',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ReportController@collectionReport',
        'as' => 'treasurer.reports.collection',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'treasurer/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ProfileController@edit',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ProfileController@edit',
        'as' => 'treasurer.profile',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'treasurer.profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'treasurer/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'org_user:treasurer',
        ),
        'uses' => 'App\\Http\\Controllers\\Treasurer\\ProfileController@update',
        'controller' => 'App\\Http\\Controllers\\Treasurer\\ProfileController@update',
        'as' => 'treasurer.profile.update',
        'namespace' => NULL,
        'prefix' => '/treasurer',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::g6RHitEazwVGqrYO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'debug-session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:195:"function() {
    return \\response()->json([
        \'session\' => \\session()->all(),
        \'auth_org_user\' => \\auth()->guard(\'org_user\')->user(),
        \'auth_web\' => \\auth()->user(),
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000003dc0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::g6RHitEazwVGqrYO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:102:"C:\\Users\\Administrator\\Codes\\xampp\\htdocs\\Capstone Project Event Management System\\storage\\app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000004530000000000000000";}}',
        'as' => 'storage.local',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
