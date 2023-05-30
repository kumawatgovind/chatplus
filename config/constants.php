<?php

return [
    'DEBUG_MODE' => TRUE,
    'POST_TYPE' => [
        1 => 'image',
        2 => 'video',
        3 => 'text',
    ],
    'POST_VISIBILITY' => [
        1 => 'only me',
        2 => 'followers',
        3 => 'public',
    ],
    'ALLOWED_EXT' => ['gif', 'jpeg', 'png', 'jpg', 'tif', 'bmp', 'ico'],
    'MESSAGE_API_KEY' => 'FP1j9S31cOKxHTAwDVdScGMHkwoYtg6sLVOvrg/ab+U=',
    'CLIENT_ID' => '0b79e81d-2041-46c2-97dc-7b3d5287c588',
    'SMS_API_ENDPOINT' => 'http://sendsms.webcomkenya.com:6005/api/v2/',
    'SENDER_ID' => 'LIQUIBILL',
    'SENDER_ID' => 'LIQUIBILL',
    'USER_SELECT_FIELDS' => [
        "id",
        "parent_id",
        "username",
        "name",
        "email",
        "country_code",
        "phone_number",
        "device_id",
        "device_type",
        "api_token",
        "status",
        "profile_image",
        "firebase_email",
        "firebase_password",
        "uId",
        "created_at",
        "updated_at",
    ],

    'NOTIFICATION_LIST' => [
        'A' => ['subject' => 'approved advert posts.', 'message' => 'When admin approved Advert posts'],
        'B' => ['subject' => 'submit their review.', 'message' => 'When visitors submit their review on Advert posts.'],
        'C' => ['subject' => 'recevies a new message.', 'message' => 'When Advertiser/user recevies a new message.'],
        'D' => ['subject' => 'subscribtion is about to expire.', 'message' => 'When Subscribtion is about to expire.'],
        'E' => ['subject' => 'ad is under review.', 'message' => 'Your ad is under review please wait for the next update.'],
        'F' => ['subject' => 'new ad for the reviewing.', 'message' => 'You have a new ad for the reviewing.'],
        'G' => ['subject' => 'edited an ad.', 'message' => 'You have edited ad.'],
        'H' => ['subject' => 'has been edited advertisement.', 'message' => 'Advertisement has been edited.'],
        'I' => ['subject' => 'review has been added.', 'message' => 'A review has been added to your advertisement.'],
        'J' => ['subject' => 'received a message.', 'message' => 'You have received a message.'],
        'K' => ['subject' => 'accepted your advertisement.', 'message' => 'Your advertisement has been accepted.'],
        'L' => ['subject' => 'declined your advertisement.', 'message' => 'Your advertisement has been declined.'],
        'M' => ['subject' => 'subscription expire reminder.', 'message' => 'Your subscription plan will expire in 2 days.'],
        'N' => ['subject' => 'Your subscription plan expired.', 'message' => 'Your subscription plan has been expired.'],
        'O' => ['subject' => 'purchased a subscription plan.', 'message' => 'Subscription plan has been purchased.'],
        'P' => ['subject' => 'upgraded a subscription plan.', 'message' => 'Your subscription plan has been upgraded.'],


    ],

];
