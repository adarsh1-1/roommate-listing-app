# Quick Start Guide

## For Local Development

### Prerequisites

```bash
# Install required packages
composer install
npm install  # if using npm for frontend dependencies
```

### 1. Configure AWS Credentials

Create `.env` file in project root:

```env
# AWS RDS Configuration
AWS_RDS_HOST=localhost  # For testing, use localhost MySQL
AWS_RDS_PORT=3306
AWS_RDS_USER=root
AWS_RDS_PASSWORD=
AWS_RDS_DATABASE=roommate_db

# AWS S3 Configuration (use actual AWS credentials for production)
AWS_S3_REGION=us-east-1
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_S3_BUCKET=your-bucket-name
AWS_S3_PUBLIC_URL=https://your-bucket.s3.amazonaws.com
```

### 2. Setup Local MySQL Database

```bash
# Create database
mysql -u root -p
CREATE DATABASE roommate_db;
USE roommate_db;
EXIT;

# Run schema setup
php config/setup-db.php?action=setup
```

### 3. Start Local Development Server

```bash
# Using PHP built-in server (for development only)
php -S localhost:8000

# Then access
http://localhost:8000
```

### 4. Test User Registration

1. Go to: `http://localhost:8000/index.php?page=signup`
2. Fill in the form:
   - **Name**: John Doe
   - **Email**: john@example.com
   - **Phone**: 9876543210
   - **I am a**: Select "PG Owner"
   - **Password**: Test@123
3. Click "Create Account"

### 5. Test User Login

1. Go to: `http://localhost:8000/index.php?page=login`
2. Enter credentials:
   - **Email**: john@example.com
   - **Password**: Test@123
3. Click "Sign In"

### 6. Create a Test Listing

1. After login, navigate to: `http://localhost:8000/index.php?page=add-listing`
2. Fill the form:
   - **Title**: Cozy 2BHK Apartment
   - **Location**: Bangalore
   - **Rent**: 45000
   - **Available Rooms**: 2
   - **Furnishing**: semi-furnished
   - **Area**: 850
   - **Description**: A beautiful apartment with all modern amenities
   - **Amenities**: Check WiFi, AC, Parking
   - **Image**: Upload any image
3. Click "Publish Listing"

### 7. Browse Listings

1. Go to: `http://localhost:8000/index.php?page=landing`
2. Search and filter listings
3. Click on any listing to view details

## Production Deployment on AWS

### 1. Launch EC2 Instance

```bash
# SSH into instance
ssh -i key.pem ec2-user@your-instance-ip

# Update system
sudo yum update -y

# Install dependencies
sudo yum install -y nginx php-fpm php-mysql php-json php-cli php-gd
sudo yum install -y mysql-community-client

# Install composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Clone Application

```bash
cd /var/www
sudo git clone <your-repo-url> roommate-listing-app
cd roommate-listing-app
sudo composer install
```

### 3. Configure Environment

```bash
# Create .env with AWS credentials
sudo nano .env
# Add all AWS configuration

# Set permissions
sudo chown -R nginx:nginx /var/www/roommate-listing-app
sudo chmod -R 755 /var/www/roommate-listing-app
sudo chmod 600 .env
```

### 4. Setup Nginx

```bash
# Create nginx config
sudo vi /etc/nginx/sites-available/roommate

# Add configuration (see AWS_SETUP_GUIDE.md)
# Enable site
sudo ln -s /etc/nginx/sites-available/roommate /etc/nginx/sites-enabled/

# Test and restart
sudo nginx -t
sudo systemctl restart nginx
```

### 5. Setup PHP-FPM

```bash
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
```

### 6. Initialize Database

```bash
# From EC2, connect to RDS
mysql -h your-rds-endpoint -u admin -p roommate_db < config/setup-db.php

# Or via API
curl -X POST http://your-domain.com/config/setup-db.php?action=setup
```

### 7. SSL/HTTPS

```bash
# Install Let's Encrypt
sudo yum install -y certbot python2-certbot-nginx

# Generate certificate
sudo certbot certonly --nginx -d your-domain.com

# Auto-renewal
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

## API Testing with curl

### Authentication

```bash
# Register
curl -X POST http://localhost:8000/api/auth.php?action=register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "Test@123",
    "confirm_password": "Test@123",
    "phone": "9876543211",
    "role": "owner"
  }'

# Login
curl -X POST http://localhost:8000/api/auth.php?action=login \
  -H "Content-Type: application/json" \
  -d '{"email":"jane@example.com","password":"Test@123"}'
```

### Listings

```bash
# Get all listings
curl http://localhost:8000/api/listings.php?action=get_listings

# Get single listing
curl http://localhost:8000/api/listings.php?action=get_listing&id=1

# Search listings
curl "http://localhost:8000/api/listings.php?action=search&location=Bangalore&max_rent=50000"

# Create listing (with image)
curl -X POST http://localhost:8000/api/listings.php?action=create \
  -F "title=2BHK Apartment" \
  -F "location=Bangalore" \
  -F "rent=45000" \
  -F "description=Modern apartment" \
  -F "furnishing=semi-furnished" \
  -F "available_rooms=2" \
  -F "image=@/path/to/image.jpg" \
  -F "amenities=[\"WiFi\",\"AC\",\"Parking\"]"

# Update listing
curl -X POST http://localhost:8000/api/listings.php?action=update \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "title": "Updated Title",
    "rent": 50000
  }'

# Delete listing
curl -X POST http://localhost:8000/api/listings.php?action=delete \
  -H "Content-Type: application/json" \
  -d '{"id":1}'
```

## Troubleshooting

### Can't connect to RDS

```bash
# Check security group
# Verify credentials
# Test connection
mysql -h your-rds-endpoint -u admin -p

# Check logs
tail -f /var/log/php-fpm.log
```

### S3 upload fails

1. Verify IAM user credentials
2. Check S3 bucket exists and is accessible
3. Verify CORS configuration on bucket
4. Check file size and permissions

### 404 errors on pages

1. Check `.htaccess` rewrite rules
2. Verify Nginx configuration
3. Ensure index.php is in document root
4. Restart web server

### Images not displaying

1. Check S3 URL in database
2. Verify bucket public read access
3. Check CloudFront distribution (if used)
4. Verify CORS headers

## Performance Testing

```bash
# Load testing with Apache Bench
ab -n 1000 -c 10 http://localhost:8000/api/listings.php?action=get_listings

# Stress testing with wrk
wrk -t4 -c100 -d30s http://localhost:8000/
```

## Database Optimization

```sql
-- Check indexes
SHOW INDEXES FROM pg_listings;

-- Analyze query performance
EXPLAIN SELECT * FROM pg_listings WHERE location LIKE '%Bangalore%';

-- Create additional indexes if needed
CREATE INDEX idx_location_rent ON pg_listings(location, rent);
```

## Monitoring

```bash
# Check system resources
free -h          # Memory
df -h            # Disk space
top              # Process monitor

# Check web server status
sudo systemctl status nginx
sudo systemctl status php-fpm

# Check error logs
tail -f /var/log/nginx/error.log
tail -f /var/log/php-fpm.log
```

## Next Steps

1. Read `AWS_SETUP_GUIDE.md` for detailed AWS configuration
2. Configure custom domain and SSL
3. Set up monitoring and alerts
4. Implement CI/CD pipeline
5. Add payment integration
6. Enable user email notifications

## Support

For issues or questions:
1. Check `AWS_SETUP_GUIDE.md`
2. Review error logs
3. Check database connectivity
4. Verify AWS permissions

---

**Happy deploying!** 🚀
