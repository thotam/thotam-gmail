<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'mail' => [
        'refreshToken' => env('DEFAULT_GOOGLE_MAIL_REFRESH_TOKEN', null),
        'clientSecret' => env('DEFAULT_GOOGLE_MAIL_CLIENT_SECRET', null),
        'clientId' => env('DEFAULT_GOOGLE_MAIL_CLIENT_ID', null),
        'surveyRefreshToken' => env('SurveyMail_GOOGLE_MAIL_REFRESH_TOKEN', null),
    ]
];
