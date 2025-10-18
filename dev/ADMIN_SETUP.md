# GRADLINK Admin System Setup

This document explains how to set up and use the new admin system for GRADLINK.

## Overview

The admin system provides a secure way for administrators to access the GRADLINK platform with elevated privileges. It includes:

- Dedicated admin login page at `/admin`
- Role-based access control
- Admin dashboard with system overview
- User management capabilities
- Analytics and reporting features

## Setup Instructions

### 1. Database Setup

First, ensure your database has the required tables. You can either:

**Option A: Run the SQL schema file**
```bash
mysql -u root -p GL_db < dev/basic_schema.sql
```

**Option B: Run the admin creation script**
```bash
php dev/create_admin.php
```

This will create an admin user with:
- Email: `admin@gradlink.com`
- Password: `admin123` (or `password` if using SQL file)
- Role: `admin`

**IMPORTANT**: Change the default password after first login and delete the setup files for security.

### 2. Access Admin Panel

1. Navigate to `http://localhost/gradlink/admin`
2. You'll be redirected to the admin login page
3. Enter your admin credentials
4. After successful login, you'll be redirected to the admin dashboard

### 3. Admin Dashboard Features

The admin dashboard provides access to:

- **Overview**: System statistics and metrics
- **User Management**: View and manage all users
- **Analytics**: Engagement metrics and reports
- **Content Management**: Moderate posts and content
- **System Settings**: Configure platform settings

## URL Structure

- `/admin` - Admin login page (redirects to dashboard if already logged in)
- `/admin/dashboard` - Main admin dashboard
- `/admin/users` - User management
- `/admin/engagement` - Analytics and engagement metrics

## Security Features

- **Role-based Access**: Only users with `admin` role can access admin features
- **Session Management**: Secure session handling with automatic logout
- **Input Validation**: All inputs are sanitized and validated
- **Password Hashing**: Passwords are securely hashed using PHP's built-in functions

## File Structure

```
app/
├── controllers/
│   ├── Admin.php          # Main admin controller
│   └── AdminLogin.php     # Admin authentication controller
├── models/
│   └── M_admin.php        # Admin model with authentication methods
├── views/admin/
│   ├── v_admin_dashboard.php  # Main dashboard view
│   ├── v_admin_login.php      # Admin login view
│   └── ...                   # Other admin views
└── helpers/
    └── SessionManager.php     # Session and role management
```

## Customization

### Adding New Admin Features

1. Add new methods to `Admin` controller
2. Create corresponding views in `app/views/admin/`
3. Update the sidebar menu in `v_admin_dashboard.php`
4. Add necessary database queries to `M_admin` model

### Styling

Admin styles are now organized under `public/css/admin/` with the shared/common styles in `public/css/admin/common.css`. The design follows a modern, professional aesthetic with:

- Dark sidebar with gradient background
- Clean, card-based layout
- Responsive design for mobile devices
- Consistent color scheme and typography

## Troubleshooting

### Common Issues

1. **404 Error on `/admin`**: Ensure `.htaccess` is properly configured
2. **Login not working**: Check database connection and admin user exists
3. **Session issues**: Verify `SessionManager` is properly loaded
4. **Permission denied**: Ensure user has `admin` role in database

### Debug Mode

To debug issues, check:
- PHP error logs
- Database connection
- Session configuration
- File permissions

## Security Best Practices

1. **Change Default Password**: Always change the default admin password
2. **Delete Setup Files**: Remove `dev/create_admin.php` after use
3. **Regular Updates**: Keep the system updated with security patches
4. **Access Logging**: Monitor admin access for suspicious activity
5. **Strong Passwords**: Use complex, unique passwords for admin accounts

## Support

For technical support or questions about the admin system, refer to the main GRADLINK documentation or contact the development team.
