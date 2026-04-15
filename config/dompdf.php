<?php

return [
    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,
    'options' => [
        'font_dir' => storage_path('app/fonts'),
        'font_cache' => storage_path('app/fonts'),
        'temp_dir' => storage_path('app/temp'),
        'chroot' => realpath(base_path()),
        'allowed_protocols' => [],
        'log_output_file' => storage_path('logs/dompdf.log'),
        'enable_font_subsetting' => false,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',
        'default_font' => 'sans-serif',
        'dpi' => 96,
        'enable_php' => false,
        'enable_javascript' => false,
        'enable_remote' => false,
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => false,
        'debug' => false,
    ],
];