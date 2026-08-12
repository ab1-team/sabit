<?php

return [
    'app' => [
        'name'        => 'Tenant Console',
        'tagline'     => 'Central control panel',
        'greeting'    => 'Welcome back',
        'view_site'   => 'Open Application',
        'logout'      => 'Sign out',
        'logout_confirm_title' => 'Sign out of Tenant Console?',
        'logout_confirm_text'  => 'Your session will end and you will need to sign in again.',
        'logout_confirm_yes'   => 'Yes, sign out',
        'logout_confirm_no'    => 'Cancel',
        'language'    => 'Language',
    ],

    'nav' => [
        'dashboard' => 'Dashboard',
        'tenant'    => 'Schools',
        'hak_akses'  => 'Access',
        'migrasi'   => 'Migration',
        'invoice'   => 'Invoices',
        'transaksi' => 'Transactions',
    ],

    'dashboard' => [
        'title'           => 'Dashboard',
        'subtitle'        => 'Operational summary across all tenant schools.',
        'filter_all'      => 'All Schools',
        'filter_label'    => 'School Filter',
        'filter_caption'  => 'Data shown from every tenant school.',
        'filter_select'   => 'Choose School',

        'stats' => [
            'total_schools'   => 'Total Schools',
            'total_owners'    => 'Total Owners',
            'new_schools'     => 'New Schools',
            'active_schools'  => 'Active Schools',
            'invoice_total'   => 'Total Invoices',
            'invoice_paid'    => 'Paid Invoices',
            'invoice_open'    => 'Unpaid Invoices',
            'nominal_total'   => 'Total Amount',
            'this_month'      => 'This Month',
            'this_year'       => 'This Year',
        ],

        'chart' => [
            'income_title'    => 'Invoice Revenue',
            'income_subtitle' => 'Last 6 months',
            'tenant_title'    => 'Schools Distribution',
            'tenant_subtitle' => 'By subscription status',
            'no_data'         => 'No data yet',
        ],

        'tables' => [
            'recent_invoices' => 'Recent Invoices',
            'view_all'        => 'View all',
            'recent_schools'  => 'Recent Schools',
            'recent_payments' => 'Recent Payments',
            'recent_owners'   => 'Recent Owners',
            'no_data'         => 'No data yet.',
            'col_jenis'       => 'Type',
            'col_tanggal'     => 'Date',
            'col_sekolah'     => 'School',
            'col_pemilik'     => 'Owner',
            'col_nominal'     => 'Amount',
            'col_status'      => 'Status',
            'col_aksi'        => 'Action',
            'col_nama'        => 'Name',
            'col_email'       => 'Email',
            'col_domain'      => 'Domain',
            'col_dibuat'      => 'Created',
            'status_paid'     => 'Paid',
            'status_unpaid'   => 'Unpaid',
            'status_active'   => 'Active',
            'status_inactive' => 'Inactive',
            'detail'          => 'Detail',
        ],

        'actions' => [
            'add_school'      => 'Add School',
            'view_schools'    => 'View All Schools',
            'manage_access'   => 'Manage Access',
            'placeholder'     => 'Coming soon',
        ],
    ],
];
