<?php

$providers = [
    'sendgrid' => function ($to, $subject, $body) {

        echo "Trying SendGrid...\n";
        return rand(0, 1) === 1; // Simulate success/failure randomly
    },
    'mailgun' => function ($to, $subject, $body) {

        echo "Trying Mailgun...\n";
        return rand(0, 1) === 1;
    },
    'smtp' => function ($to, $subject, $body) {

        echo "Trying SMTP...\n";
        return rand(0, 1) === 1;
    },
    'sample1' => function ($to, $subject, $body) {

        echo "Trying Sample 1...\n";
        return rand(0, 1) === 1;
    },
    'sample1' => function ($to, $subject, $body) {

        throw new Exception('Server is down.');
        return rand(0, 1) === 1;
    }
];
