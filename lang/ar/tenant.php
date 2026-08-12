<?php

return [
    'app' => [
        'name'        => 'وحدة تحكم المستأجرين',
        'tagline'     => 'لوحة التحكم المركزية',
        'greeting'    => 'مرحبًا بعودتك',
        'view_site'   => 'فتح التطبيق',
        'logout'      => 'تسجيل الخروج',
        'logout_confirm_title' => 'تسجيل الخروج من وحدة التحكم؟',
        'logout_confirm_text'  => 'ستنتهي جلستك وستحتاج إلى تسجيل الدخول مرة أخرى.',
        'logout_confirm_yes'   => 'نعم، خروج',
        'logout_confirm_no'    => 'إلغاء',
        'language'    => 'اللغة',
    ],

    'nav' => [
        'dashboard' => 'لوحة التحكم',
        'tenant'    => 'المدارس',
        'hak_akses'  => 'الصلاحيات',
        'migrasi'   => 'ترحيل',
        'invoice'   => 'الفواتير',
        'transaksi' => 'المعاملات',
    ],

    'dashboard' => [
        'title'           => 'لوحة التحكم',
        'subtitle'        => 'ملخص العمليات لجميع مدارس المستأجرين.',
        'filter_all'      => 'جميع المدارس',
        'filter_label'    => 'تصفية المدارس',
        'filter_caption'  => 'يتم عرض البيانات من جميع مدارس المستأجرين.',
        'filter_select'   => 'اختر المدرسة',

        'stats' => [
            'total_schools'   => 'إجمالي المدارس',
            'total_owners'    => 'إجمالي المالكين',
            'new_schools'     => 'مدارس جديدة',
            'active_schools'  => 'مدارس نشطة',
            'invoice_total'   => 'إجمالي الفواتير',
            'invoice_paid'    => 'فواتير مدفوعة',
            'invoice_open'    => 'فواتير غير مدفوعة',
            'nominal_total'   => 'إجمالي المبلغ',
            'this_month'      => 'هذا الشهر',
            'this_year'       => 'هذا العام',
        ],

        'chart' => [
            'income_title'    => 'إيرادات الفواتير',
            'income_subtitle' => 'آخر 6 أشهر',
            'tenant_title'    => 'توزيع المدارس',
            'tenant_subtitle' => 'حسب حالة الاشتراك',
            'no_data'         => 'لا توجد بيانات',
        ],

        'tables' => [
            'recent_invoices' => 'الفواتير الأخيرة',
            'view_all'        => 'عرض الكل',
            'recent_schools'  => 'المدارس الأخيرة',
            'recent_payments' => 'المدفوعات الأخيرة',
            'recent_owners'   => 'المالكون الأخيرون',
            'no_data'         => 'لا توجد بيانات.',
            'col_jenis'       => 'النوع',
            'col_tanggal'     => 'التاريخ',
            'col_sekolah'     => 'المدرسة',
            'col_pemilik'     => 'المالك',
            'col_nominal'     => 'المبلغ',
            'col_status'      => 'الحالة',
            'col_aksi'        => 'إجراء',
            'col_nama'        => 'الاسم',
            'col_email'       => 'البريد',
            'col_domain'      => 'النطاق',
            'col_dibuat'      => 'تاريخ الإنشاء',
            'status_paid'     => 'مدفوع',
            'status_unpaid'   => 'غير مدفوع',
            'status_active'   => 'نشط',
            'status_inactive' => 'غير نشط',
            'detail'          => 'التفاصيل',
        ],

        'actions' => [
            'add_school'      => 'إضافة مدرسة',
            'view_schools'    => 'عرض جميع المدارس',
            'manage_access'   => 'إدارة الصلاحيات',
            'placeholder'     => 'قريبًا',
        ],
    ],
];
