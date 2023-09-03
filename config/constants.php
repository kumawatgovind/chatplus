<?php
$gender = [
    1 => 'Male',
    2 => 'Female',
    3 => 'Transgender',
];
$maritalStatus = [
    1 => 'Single',
    2 => 'Married',
    3 => 'Widowed',
    4 => 'Divorced',
    5 => 'Separated',
];
$userSelectFields = [
    'id',
    'parent_id',
    'referral_code',
    'username',
    'name',
    'email',
    'country_code',
    'phone_number',
    'device_id',
    'device_type',
    'api_token',
    'status',
    'profile_image',
    'firebase_id',
    'firebase_email',
    'firebase_password',
    'fcm_token',
    'gender',
    'marital_status',
    'dob',
    'bio',
    'janam_din',
    'created_at',
    'updated_at',
];
$residentialPropertyType = [
    1 => 'Flat/Apartment',
    2 => 'House',
    3 => 'Villa',
    4 => 'Plot',
    5 => 'Studio Apartment',
    6 => 'Penthouse',
    7 => 'Farm House'
];
$commercialPropertyType = [
    1 => 'Office Space',
    2 => 'Shop',
    3 => 'Showroom',
    4 => 'Commercial Land',
    5 => 'Warehouse/Godown',
    6 => 'Industrial Land',
    7 => 'Industrial Building',
    8 => 'Agricultural Land'
];
$masterData = [
    'type' => [
        'Property' => [
            'Sell' => [
                'Residential' => $residentialPropertyType,
                'Commercial' => $commercialPropertyType
            ],
            'Rent/Lease' => [
                'Residential' => $residentialPropertyType,
                'Commercial' => $commercialPropertyType
            ],
            'Requirement' => [
                'Buy' => [
                    'Residential' => $residentialPropertyType,
                    'Commercial' => $commercialPropertyType
                ],
                'Rent/Lease' => [
                    'Residential' => $residentialPropertyType,
                    'Commercial' => $commercialPropertyType
                ]
            ],
        ],
        'Other' => [
            1 => 'Sell',
            2 => 'Buy'
        ]
    ],
    'condition' => [
        1 => 'New',
        2 => 'Used- Like new',
        3 => 'Used- Good',
        4 => 'Used- Fair'
    ],
    'furnishing' => [
        1 => 'None',
        2 => 'Semi Furnished',
        3 => 'Full Furnished'
    ],
    'facing' => [
        1 => 'East',
        2 => 'North',
        3 => 'North-East',
        4 => 'North west',
        5 => 'South',
        6 => 'South-East',
        7 => 'South-West',
        8 => 'West'
    ],
    'property_status' => [
        1 => 'Under Construction',
        2 => 'Ready to Move'
    ],
    'bedroom' => [
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => '10+',
    ],
    'bathroom' => [
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => '10+',
    ],
    'floor' => [
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => '10+',
    ],
    'area' => [
        1 => 'Feet',
        2 => 'Meter',
        3 => 'Sqft',
        4 => 'Sqyrd',
        5 => 'Sqm',
        6 => 'Acre',
        7 => 'Bigha',
        8 => 'Hectare',
        9 => 'Marla',
        10 => 'Kanal',
        11 => 'Biswa1',
        12 => 'Biswa2',
        13 => 'Ground',
        14 => 'Aankadam',
        15 => 'Rood',
        16 => 'Chatak',
        17 => 'Kottah',
        18 => 'Marla',
        19 => 'Cent',
        20 => 'Perch',
        21 => 'Guntha',
        22 => 'Are',
    ],

];
$commissionPercentages = [
    1 => 10,
    2 => 7.5,
    3 => 5,
    4 => 5,
    5 => 3,
    6 => 3,
    7 => 3,
];
$kycStatus = [
    0 => 'Pending',
    1 => 'Completed',
    2 => 'In-Progress',
    3 => 'Failed',
];
$transactionType = [
    0 => 'Other',
    1 => 'Refer',
    2 => 'Bank transfer',
    3 => 'Prime Subscription',
    4 => 'Subscription In-Progress',
    5 => 'Subscription Canceled',
    6 => 'Subscription Failed',
];
$notificationMessage = [
    'service_product_other_buy' => [
        'title' => 'New Service product in your area.',
        'message' => 'One of our user looking for buy service product in your area, tap to see details.'
    ],
    'service_product_other_sell' => [
        'title' => 'New Service product in your area.',
        'message' => 'One of our user looking for sell service product in your area, tap to see details.'
    ],
    'service_product_property_sell' => [
        'title' => 'New Property for sell in your area.',
        'message' => 'One of over user selling a property in your area, tap to see details.'
    ],
    'service_product_property_rent' => [
        'title' => 'New Property for rent in your area.',
        'message' => 'One of over user looking for rent a property in your area, tap to see details.'
    ],
    'service_product_property_requirement' => [
        'title' => 'New Property requirement in your area.',
        'message' => 'One of over user required a property for buy/rent in your area, tap to see details.'
    ],
    'admin_block_message' => [
        'title' => 'Block by admin',
        'message' => 'We have found some suspicious activity in you account, So we are temporarily block your account.'
    ],
    'payment_withdrawal_pending' => [
        'title' => 'Payment withdrawal request in pending',
        'message' => 'We have process your payment withdrawal request. We will notify when its status change.'
    ],
    'payment_withdrawal_inProgress' => [
        'title' => 'Payment withdrawal request in in-progress',
        'message' => 'Your payment withdrawal request in progress. We will notify when its status change.'
    ],
    'payment_withdrawal_complete' => [
        'title' => 'Payment withdrawal request completed',
        'message' => 'We have successfully credited amount in your bank account. Now we have close your withdrawal request.'
    ],
    'payment_withdrawal_cancelled' => [
        'title' => 'Payment withdrawal request cancelled',
        'message' => 'Due to some technical issue we cannot complete your withdrawal request. We request you please raise new withdrawal request.'
    ],
    'kyc_failed' => [
        'title' => 'Kyc Update',
        'message' => 'Your Kyc rejected due to insufficient information. Please re-submit for complete this.'
    ],
    'kyc_inProgress' => [
        'title' => 'Kyc Update',
        'message' => 'Your Kyc in progress. We will notify once its status change.'
    ],
    'kyc_complete' => [
        'title' => 'Kyc Update',
        'message' => 'Your Kyc successfully verified with us.'
    ],
];
return [
    'DEBUG_MODE' => TRUE,
    'GENDER' => $gender,
    'MARITAL_STATUS' => $maritalStatus,
    'ALLOWED_EXT' => ['gif', 'jpeg', 'png', 'jpg', 'tif', 'bmp', 'ico'],
    'USER_SELECT_FIELDS' => $userSelectFields,
    'MASTER_DATA' => $masterData,
    'CURRENCY' => 'INR',
    'STRIPE_VERSION' => '2022-11-15',
    'STRIPE_SECRET_KEY' => env('STRIPE_SECRET'),
    'STRIPE_PUBLISHABLE_KEY' => env('STRIPE_KEY'),
    'STRIPE_WEBHOOK_SECRET' => env('STRIPE_WEBHOOK'),
    'SERVICE_PRODUCT_LIMIT' => 30,
    'COMMISSION_PERCENTAGES' => $commissionPercentages,
    'KYC_STATUS' => $kycStatus,
    'TRANSACTION_TYPE' => $transactionType,
    'MINIMUM_PAYOUT' => 100,
    'PAYOUT_URL' => 'https://api.razorpay.com/v1/',
    'PAYOUT_KEY_ID' => env('PAYOUT_KEY_ID'),
    'PAYOUT_KEY_SECRET' => env('PAYOUT_KEY_SECRET'),
    'PAYMENT_MODE' => 'IMPS',
    'PAYMENT_PURPOSE' => 'payout',
    'PAYOUT_ACCOUNT_NUMBER' => '2323230025300756',
    'DEFAULT_COUNTRY' => 1,
    'NOTIFICATION_MESSAGE' => $notificationMessage
];
