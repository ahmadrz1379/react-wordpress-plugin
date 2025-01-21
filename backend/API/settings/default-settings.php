<?php 
function load_default_settings() {
    $default_settings = [
        [
            'id' => 'home-page',
            'title' => 'Home Page',
            'icon' => 'fas fas-home',
            'fields' => [
                [
                    'id' => 'home_name',
                    'title' => 'Enter your name',
                    'type' => 'text',
                    'default' => 'ahmadreza',
                ],
                [
                    'id' => 'welcome_message', // فیلد جدید
                    'title' => 'Welcome Message',
                    'type' => 'text',
                    'default' => 'Welcome to our site!',
                ],
            ],
        ],
        [
            'id' => 'about-page',
            'title' => 'About Page',
            'icon' => 'fas fas-about',
            'fields' => [
                [
                    'id' => 'about_bio',
                    'title' => 'Enter your bio',
                    'type' => 'text',
                    'default' => 'Lorem ipsum dolor sit amet.',
                ],
            ],
        ],
        [
            'id' => 'xxx-page',
            'title' => 'XXX Page',
            'icon' => 'fas fas-page',
            'fields' => [
                [
                    'id' => 'xxx_content',
                    'title' => 'Enter XXX Content',
                    'type' => 'text',
                    'default' => 'Default content for XXX page.',
                ],
            ],
        ], 
        [
            'id' => 'sps' , 
            'title' => 'handler' , 
            'icon' => 'fas fas-handler' ,
            'fields' => [
                [
                    'id' => 'asdfasf',
                    'title' => 'Enter asfasfasf Content',
                    'type' => 'text',
                    'default' => 'asfasfasf asfasf for asfasf page.',
                ]
            ]
        ]
    ];

    // دریافت تنظیمات موجود از دیتابیس
    $existing_settings = get_option('react_wordpress_plugin_settings', []);

    // ترکیب تنظیمات پیش‌فرض و موجود
    $merged_settings = merge_settings($existing_settings, $default_settings);

    // ذخیره تنظیمات ترکیب‌شده
    update_option('react_wordpress_plugin_settings', $merged_settings);
}

/**
 * ترکیب تنظیمات پیش‌فرض و تنظیمات موجود
 *
 * این تابع تنظیمات پیش‌فرض را با تنظیمات موجود ادغام می‌کند:
 * - فیلدهای جدید را اضافه می‌کند
 * - فیلدهای قدیمی را بروزرسانی می‌کند
 * - داده‌های کاربر را حفظ می‌کند
 *
 * @param array $existing_settings تنظیمات موجود
 * @param array $default_settings تنظیمات پیش‌فرض
 * @return array تنظیمات ادغام‌شده
 */
function merge_settings($existing_settings, $default_settings) {
    foreach ($default_settings as $default_page) {
        // یافتن صفحه با ID مشابه در تنظیمات موجود
        $page_exists = array_search($default_page['id'], array_column($existing_settings, 'id'));

        if ($page_exists === false) {
            // اگر صفحه وجود ندارد، اضافه شود
            $existing_settings[] = $default_page;
        } else {
            // اگر صفحه وجود دارد، فیلدهای آن را بروزرسانی/ترکیب کن
            $existing_page = $existing_settings[$page_exists];

            foreach ($default_page['fields'] as $default_field) {
                // بررسی وجود فیلد در تنظیمات موجود
                $field_exists = array_search($default_field['id'], array_column($existing_page['fields'], 'id'));

                if ($field_exists === false) {
                    // اگر فیلد وجود ندارد، آن را اضافه کن
                    $existing_page['fields'][] = $default_field;
                } else {
                    // اگر فیلد وجود دارد، مقادیر قابل تغییر را بروزرسانی کن
                    $existing_page['fields'][$field_exists] = array_merge(
                        $existing_page['fields'][$field_exists],
                        [
                            'title' => $default_field['title'], // بروزرسانی عنوان
                            'type' => $default_field['type'],   // بروزرسانی نوع
                        ]
                    );

                    // حفظ مقدار کاربر یا تنظیم مقدار پیش‌فرض در صورت خالی بودن
                    if (empty($existing_page['fields'][$field_exists]['default'])) {
                        $existing_page['fields'][$field_exists]['default'] = $default_field['default'];
                    }
                }
            }

            // بروزرسانی صفحه اصلی (مانند عنوان و آیکون)
            $existing_settings[$page_exists] = array_merge($existing_page, [
                'title' => $default_page['title'],
                'icon' => $default_page['icon'],
            ]);
        }
    }

    return $existing_settings;
}
