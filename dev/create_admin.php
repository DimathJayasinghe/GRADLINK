<?php
/**
 * Admin User Creation Script
 * Run this script once to create an admin user
 * DELETE THIS FILE AFTER USE FOR SECURITY
 */

// Database configuration
$host = 'localhost';
$dbname = 'GL_db';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Admin user data
    $adminEmail = 'admin@gradlink.com';
    $adminPassword = 'admin123'; // Change this to a secure password
    $adminName = 'System Administrator';
    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$adminEmail]);
    
    if ($stmt->fetch()) {
        echo "Admin user already exists!\n";
        echo "Email: $adminEmail\n";
        echo "Password: $adminPassword\n";
    } else {
        // Create admin user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'admin', NOW())");
        $stmt->execute([$adminName, $adminEmail, $hashedPassword]);
        
        echo "Admin user created successfully!\n";
        echo "Email: $adminEmail\n";
        echo "Password: $adminPassword\n";
        echo "Please change the password after first login!\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nIMPORTANT: Delete this file after use for security!\n";
?>
