<?php
return [
  'key' => env('AWS_S3_KEY', 'AKIAJXVZFNUD6Q6V6AEA'),
  'secret' => env('AWS_S3_SECRET', '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'),
  'region' => env('AWS_S3_REGION', 'ap-northeast-1'),
  'icon_folder' => env('AWS_S3_ICON_FOLDER', 'user_icon'),
  'bucket' => env('AWS_S3_BUCKET', 'lms-file'),
];
