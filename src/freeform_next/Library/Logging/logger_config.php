<?php

if (!function_exists('getFileRotationLoggerSettings')) {
    function getFileRotationLoggerSettings($name = 'freeform_next')
    {
        return [
            'class'  => 'LoggerAppenderRollingFile',
            'layout' => [
                'class' => 'LoggerLayoutPattern',
            ],
            'params' => [
                'file'           => __DIR__ . '/../../logs/' . strtolower($name) . '.log',
                'append'         => true,
                'maxFileSize'    => '1MB',
                'maxBackupIndex' => 5,
            ],
        ];
    }
}

return [
    'rootLogger' => [
        'appenders' => ['default'],
    ],
    'loggers'    => [
        'MailChimp'       => ['additivity' => false, 'appenders' => ['MailChimp']],
        'Salesforce'      => ['additivity' => false, 'appenders' => ['Salesforce']],
        'HubSpot'         => ['additivity' => false, 'appenders' => ['HubSpot']],
        'ConstantContact' => ['additivity' => false, 'appenders' => ['ConstantContact']],
        'CampaignMonitor' => ['additivity' => false, 'appenders' => ['CampaignMonitor']],
    ],
    'appenders'  => [
        'default'         => getFileRotationLoggerSettings(),
        'MailChimp'       => getFileRotationLoggerSettings('MailChimp'),
        'Salesforce'      => getFileRotationLoggerSettings('Salesforce'),
        'HubSpot'         => getFileRotationLoggerSettings('HubSpot'),
        'ConstantContact' => getFileRotationLoggerSettings('ConstantContact'),
        'CampaignMonitor' => getFileRotationLoggerSettings('CampaignMonitor'),
    ],
];
