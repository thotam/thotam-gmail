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
        'kaizenRefreshToken' => env('KaizenMail_GOOGLE_MAIL_REFRESH_TOKEN', null),
        'buddyRefreshToken' => env('BuddyMail_GOOGLE_MAIL_REFRESH_TOKEN', null),
        'kbytRefreshToken' => env('KbytMail_GOOGLE_MAIL_REFRESH_TOKEN', null),
    ]
];
