<?php
// AWS SDK Integration
// This file handles AWS S3 and RDS connections

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'aws-config.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AWSManager {
    private $s3Client;
    private $dbConnection;
    
    public function __construct() {
        $this->initializeS3Client();
    }
    
    /**
     * Initialize AWS S3 Client
     */
    private function initializeS3Client() {
        try {
            $this->s3Client = new S3Client([
                'version' => 'latest',
                'region'  => AWS_S3_REGION,
                'credentials' => [
                    'key'    => AWS_ACCESS_KEY_ID,
                    'secret' => AWS_SECRET_ACCESS_KEY,
                ]
            ]);
        } catch (AwsException $e) {
            throw new Exception("Failed to initialize S3 Client: " . $e->getMessage());
        }
    }
    
    /**
     * Upload file to S3 bucket
     * 
     * @param string $fileKey - The key/path where file will be stored (e.g., 'property-images/123.jpg')
     * @param string $filePath - The local file path
     * @return array - Contains 'success' and 'url' or 'error'
     */
    public function uploadToS3($fileKey, $filePath) {
        try {
            if (!file_exists($filePath)) {
                return [
                    'success' => false,
                    'error' => 'File not found: ' . $filePath
                ];
            }
            
            $result = $this->s3Client->putObject([
                'Bucket' => AWS_S3_BUCKET,
                'Key'    => $fileKey,
                'SourceFile' => $filePath,
                'ContentType' => mime_content_type($filePath)
            ]);
            
            $objectUrl = $result['ObjectURL'] ?? (rtrim(AWS_S3_PUBLIC_URL, '/') . '/' . ltrim($fileKey, '/'));
            
            return [
                'success' => true,
                'url' => $objectUrl,
                'key' => $fileKey
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => 'S3 Upload failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Upload file from $_FILES to S3
     * 
     * @param array $file - $_FILES array element
     * @param string $folder - Folder in S3 (e.g., 'property-images/')
     * @return array - Contains 'success' and 'url' or 'error'
     */
    public function uploadFromFilesArray($file, $folder = S3_UPLOAD_FOLDER) {
        try {
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return [
                    'success' => false,
                    'error' => 'File upload error: ' . $file['error']
                ];
            }
            
            // Check file size
            if ($file['size'] > S3_MAX_FILE_SIZE) {
                return [
                    'success' => false,
                    'error' => 'File size exceeds maximum of ' . (S3_MAX_FILE_SIZE / 1024 / 1024) . 'MB'
                ];
            }
            
            // Check file extension
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, S3_ALLOWED_EXTENSIONS)) {
                return [
                    'success' => false,
                    'error' => 'File type not allowed. Allowed types: ' . implode(', ', S3_ALLOWED_EXTENSIONS)
                ];
            }
            
            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.' . $ext;
            $fileKey = $folder . $filename;
            
            // Upload to S3
            return $this->uploadToS3($fileKey, $file['tmp_name']);
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete file from S3
     * 
     * @param string $key - The S3 key/path
     * @return array - Contains 'success' or 'error'
     */
    public function deleteFromS3($key) {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => AWS_S3_BUCKET,
                'Key'    => $key
            ]);
            
            return ['success' => true];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'error' => 'S3 Delete failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get S3 client instance
     */
    public function getS3Client() {
        return $this->s3Client;
    }
}

// Create singleton instance
$awsManager = new AWSManager();
?>
