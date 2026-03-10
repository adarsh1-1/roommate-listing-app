# AWS Integration Setup Guide

## Overview

This PG Room Listing Platform is fully integrated with AWS services. This guide explains how to set up and configure the application to work with AWS RDS and S3.

## Prerequisites

- AWS Account with appropriate permissions
- PHP 7.4+ with composer
- Nginx or Apache web server
- npm or yarn (for dependency management)

## Step 1: AWS RDS Setup

### Create RDS Instance

1. Go to AWS RDS Console
2. Click "Create database"
3. Select Engine: **MySQL 8.0**
4. DB Instance Identifier: `roommate-db`
5. Master username: `admin`
6. Master password: (set secure password)
7. DB Instance Class: `db.t3.micro` (for testing)
8. Storage: `20 GB` (adjustable)
9. VPC Security Group: Create new or use existing
   - Allow inbound rule on port 3306 from your EC2 instance or application server
10. Database name: `roommate_db`
11. Backup retention: `7 days`
12. Enable automated backups

### Security Group Configuration

**Inbound Rules:**
- Type: MySQL/Aurora
- Protocol: TCP
- Port: 3306
- Source: Your EC2 instance security group or specific IP

### Get Connection Details

After creating the RDS instance, note down:
- Endpoint (e.g., `roommate-db.xxxxx.us-east-1.rds.amazonaws.com`)
- Port: `3306`
- Username: `admin`
- Password: (your password)
- Database: `roommate_db`

## Step 2: AWS S3 Setup

### Create S3 Bucket

1. Go to AWS S3 Console
2. Click "Create bucket"
3. Bucket name: `roommate-listings-{your-unique-id}` (must be globally unique)
4. Region: Select your preferred region (same as RDS for better performance)
5. Block Public Access: **Uncheck** "Block all public access"
6. Create bucket

### Configure Bucket for Public Read Access

1. Go to Bucket Permissions
2. Click "Bucket Policy"
3. Add this policy (replace `your-bucket-name`):

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "PublicReadGetObject",
      "Effect": "Allow",
      "Principal": "*",
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::your-bucket-name/*"
    }
  ]
}
```

### Configure CORS (if using cross-origin requests)

1. Go to Bucket Settings → CORS
2. Add this configuration:

```xml
[
  {
    "AllowedHeaders": ["*"],
    "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
    "AllowedOrigins": ["https://your-domain.com"],
    "ExposeHeaders": ["ETag"],
    "MaxAgeSeconds": 3000
  }
]
```

## Step 3: AWS IAM Setup

### Create IAM User for Application

1. Go to AWS IAM Console
2. Click "Users" → "Add user"
3. Username: `roommate-app`
4. Access type: **Programmatic access**
5. Click "Next: Permissions"
6. Select "Attach existing policies directly"
7. Search and select:
   - `AmazonS3FullAccess` (for S3 operations)
   - `AmazonRDSDataFullAccess` (for RDS access)
8. Click "Create user"
9. **Save Access Key ID and Secret Access Key**

## Step 4: Environment Configuration

### Option 1: Using .env File (Recommended)

Create a `.env` file in the project root:

```bash
# AWS RDS Configuration
AWS_RDS_HOST=roommate-db.xxxxx.us-east-1.rds.amazonaws.com
AWS_RDS_PORT=3306
AWS_RDS_USER=admin
AWS_RDS_PASSWORD=your-secure-password
AWS_RDS_DATABASE=roommate_db

# AWS S3 Configuration
AWS_S3_REGION=us-east-1
AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_S3_BUCKET=roommate-listings-unique-id
AWS_S3_PUBLIC_URL=https://roommate-listings-unique-id.s3.amazonaws.com
```

### Option 2: Using Environment Variables on EC2

If hosting on EC2, set environment variables in your instance:

```bash
export AWS_RDS_HOST=roommate-db.xxxxx.us-east-1.rds.amazonaws.com
export AWS_RDS_PORT=3306
export AWS_RDS_USER=admin
export AWS_RDS_PASSWORD=your-secure-password
export AWS_RDS_DATABASE=roommate_db
export AWS_S3_REGION=us-east-1
export AWS_ACCESS_KEY_ID=your-access-key-id
export AWS_SECRET_ACCESS_KEY=your-secret-access-key
export AWS_S3_BUCKET=roommate-listings-unique-id
export AWS_S3_PUBLIC_URL=https://roommate-listings-unique-id.s3.amazonaws.com
```

Add to `/etc/profile` or `/home/ec2-user/.bashrc` for persistence:

```bash
echo 'export AWS_RDS_HOST=...' >> ~/.bashrc
source ~/.bashrc
```

## Step 5: Install PHP Dependencies

### Using Composer

The application uses AWS SDK for PHP. Install dependencies:

```bash
cd /path/to/application
composer require aws/aws-sdk-php
```

Alternatively, manually add to `composer.json`:

```json
{
  "require": {
    "aws/aws-sdk-php": "^3.0"
  }
}
```

Then run: `composer install`

The AWS SDK will be available at `vendor/aws/aws-sdk-php`

## Step 6: Database Setup

### Initialize Database Schema

1. Access the application setup page:
   ```
   http://your-domain.com/config/setup-db.php?action=setup
   ```

2. Send a POST request:
   ```bash
   curl -X POST "http://your-domain.com/config/setup-db.php?action=setup"
   ```

This will create the following tables:
- `users` - User accounts and login data
- `pg_listings` - Property listings
- `listing_images` - Images for properties (stored in S3)
- `search_preferences` - Saved search preferences
- `user_favorites` - Bookmarked listings
- `inquiries` - User inquiries about listings

### Verify Connection

Test RDS connection:

```bash
mysql -h roommate-db.xxxxx.us-east-1.rds.amazonaws.com -u admin -p roommate_db
```

If successful, you should see the MySQL prompt.

## Step 7: Nginx Configuration

### Virtual Host Configuration

Create `/etc/nginx/sites-available/roommate.conf`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/roommate-listing-app;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/roommate.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## Step 8: Security Best Practices

### Protect Configuration Files

Make sure `config/aws-config.php` and `.env` are not publicly accessible:

```bash
# In .htaccess or nginx config
<Files "aws-config.php">
    Deny from all
</Files>
```

### SSL Certificate (HTTPS)

Use AWS Certificate Manager or Let's Encrypt:

```bash
sudo certbot certonly -d your-domain.com
sudo certbot install --nginx
```

### File Permissions

Set proper permissions:

```bash
chmod 750 config/
chmod 600 config/aws-config.php
chmod 755 pages/
chmod 755 api/
chmod 755 uploads/
```

## Step 9: Environment-Specific Notes

### Development

```env
APP_DEBUG=true
AWS_S3_PUBLIC_URL=http://localhost:8000
```

### Production

```env
APP_DEBUG=false
AWS_RDS_PASSWORD=... (use strong password)
AWS_S3_PUBLIC_URL=https://your-bucket.s3.amazonaws.com
```

## API Endpoints

All images uploaded by PG owners are stored in S3, and URLs are stored in the database.

### Authentication Endpoints

- `POST /api/auth.php?action=register` - New user registration
- `POST /api/auth.php?action=login` - User login
- `POST /api/auth.php?action=logout` - User logout
- `GET /api/auth.php?action=user` - Get current user

### Listings Endpoints

- `GET /api/listings.php?action=get_listings&page=1` - Get all listings
- `GET /api/listings.php?action=get_listing&id=1` - Get single listing
- `GET /api/listings.php?action=search&q=location&max_rent=50000` - Search listings
- `POST /api/listings.php?action=create` - Create new listing (multipart form-data with image)
- `POST /api/listings.php?action=update` - Update listing
- `POST /api/listings.php?action=delete` - Delete listing
- `GET /api/listings.php?action=owner_listings` - Get owner's listings

## Troubleshooting

### Connection to RDS fails

1. Check security group allows your IP on port 3306
2. Verify credentials in `aws-config.php`
3. Test connection: `mysql -h endpoint -u admin -p`

### S3 upload fails

1. Verify IAM user has S3 permissions
2. Check bucket name matches exactly
3. Ensure bucket CORS is properly configured
4. Check disk space on server

### Database tables not created

1. Verify RDS connection working
2. Check file permissions for `setup-db.php`
3. Ensure `config/database.php` is properly configured

## Performance Optimization

### RDS

- Use Read Replicas for high traffic
- Enable Multi-AZ for production
- Use Reserved Instances for cost savings
- Monitor CloudWatch metrics

### S3

- Use CloudFront CDN for image delivery
- Enable S3 Transfer Acceleration for faster uploads
- Use S3 Intelligent-Tiering for cost optimization

## Monitoring & Logging

### CloudWatch

Monitor your application:

```bash
# View RDS logs
aws logs tail /aws/rds/instance/roommate-db

# View application logs
tail -f /var/log/nginx/access.log
tail -f /var/log/php-fpm.log
```

## Cost Estimation (India Pricing - Approximate)

- **RDS db.t3.micro**: ₹3,000-4,000/month
- **S3**: ₹0.024 per GB stored + data transfer costs
- **Data transfer**: ₹12-15 per GB (outbound)
- **Total**: ₹4,000-6,000/month for small-medium traffic

(Prices vary based on region and usage)

## Support & Resources

- AWS Documentation: https://docs.aws.amazon.com/
- AWS SDK for PHP: https://docs.aws.amazon.com/sdk-for-php/
- Application GitHub Repo: (link to your repo)
- Issue Tracker: (link to issue tracker)
