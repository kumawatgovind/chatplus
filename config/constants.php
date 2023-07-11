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
    5 => 'South',
    6 => 'Studio Apartment',
    7 => 'Penthouse',
    8 => 'Farm House'
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
    'COMMISSION_PERCENTAGES' => [
        1 => 10,
        2 => 7.5,
        3 => 5,
        4 => 5,
        5 => 3,
        6 => 3,
        7 => 3,
    ]
];
