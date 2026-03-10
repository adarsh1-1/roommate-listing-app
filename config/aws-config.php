<?php
// AWS Configuration File
// Update these values with your actual AWS credentials and endpoints

// ===== AWS RDS Database Configuration =====
define('AWS_RDS_HOST', getenv('AWS_RDS_HOST') ?: 'your-rds-endpoint.rds.amazonaws.com');
define('AWS_RDS_PORT', getenv('AWS_RDS_PORT') ?: 3306);
define('AWS_RDS_USER', getenv('AWS_RDS_USER') ?: 'admin');
define('AWS_RDS_PASSWORD', getenv('AWS_RDS_PASSWORD') ?: 'your-password');
define('AWS_RDS_DATABASE', getenv('AWS_RDS_DATABASE') ?: 'roommate_db');
define('AWS_RDS_CHARSET', 'utf8mb4');

// ===== AWS S3 Configuration =====
define('AWS_S3_REGION', getenv('AWS_S3_REGION') ?: 'us-east-1');
define('AWS_ACCESS_KEY_ID', getenv('AWS_ACCESS_KEY_ID') ?: 'your-access-key');
define('AWS_SECRET_ACCESS_KEY', getenv('AWS_SECRET_ACCESS_KEY') ?: 'your-secret-key');
define('AWS_S3_BUCKET', getenv('AWS_S3_BUCKET') ?: 'your-bucket-name');
define('AWS_S3_PUBLIC_URL', getenv('AWS_S3_PUBLIC_URL') ?: 'https://your-bucket-name.s3.amazonaws.com');

// ===== AWS SDK Settings =====
define('AWS_SDK_PATH', __DIR__ . '/../vendor/aws/aws-sdk-php');

// ===== S3 File Upload Settings =====
define('S3_MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('S3_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('S3_UPLOAD_FOLDER', 'property-images/');

// ===== Verify AWS Configuration =====
function verify_aws_config() {
    $errors = [];
    
    if (AWS_RDS_HOST === 'your-rds-endpoint.rds.amazonaws.com') {
        $errors[] = "AWS RDS endpoint not configured";
    }
    if (AWS_ACCESS_KEY_ID === 'your-access-key') {
        $errors[] = "AWS access key not configured";
    }
    if (AWS_SECRET_ACCESS_KEY === 'your-secret-key') {
        $errors[] = "AWS secret key not configured";
    }
    if (AWS_S3_BUCKET === 'your-bucket-name') {
        $errors[] = "AWS S3 bucket not configured";
    }
    
    return [
        'valid' => count($errors) === 0,
        'errors' => $errors
    ];
}
?>
