<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /**
        A list of database tables/models/etc to skip. These can be in any format, they will be run through camelCase, Studly, snake_case filters

    */
    'ignore' => [
        'User',
        'ActionEvent',
        'action_events',
        'CreateWidgetsTable',
        'BoardFilter',
        'BoardStandard',
        'BoardWidget',
        'Board',
        'FilterStandard',
        'Filter',
        'MetricStandard',
        'VisualStandard',
        'WidgetConfiguration',
        'Widget'
    ],
];
