# Nexus Bank System

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)
![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)
![Status](https://img.shields.io/badge/Status-Active-success)
![Made with Love](https://img.shields.io/badge/Made%20with-%F0%9F%92%9B-pink)

**Nexus Bank System** is a secure, web-based digital banking platform with integrated **loans, investments, and user management** features.  
It provides both **clients** and **administrators** with tools for managing financial accounts, transactions, and security—designed for performance, scalability, and data protection.

---

## 🚀 Features

### 🔹 For Users
- Create and manage bank accounts
- Deposit, withdraw, and transfer funds
- View transaction history with downloadable PDF receipts
- Apply for loans and track approval status
- Invest in available investment plans and monitor maturity dates
- Receive OTP and email confirmation for secure logins and transactions
- Manage profile and account security settings

### 🔹 For Administrators
- Manage users, accounts, loans, and investments
- Approve or reject loan applications
- Create and manage investment plans
- View system-wide transaction reports
- Monitor login records and detect suspicious activity
- Role-based access control for enhanced security

---

## 🛠️ Tech Stack

**Frontend:**
- HTML5  
- CSS3 (Flexbox, CSS Grid, Responsive Design with Media Queries)  
- JavaScript (ES6+, jQuery, ApexCharts.js for interactive charts)

**Backend:**
- PHP 8.0+ (PDO, prepared statements, session handling)
- MySQL 8.0 (Normalized to 3NF, indexed for performance)

**Tools & Platforms:**
- Visual Studio Code
- XAMPP / WAMP (local development)
- Git & GitHub (version control)
- Hostinger (deployment)
- PHPMailer (OTP, email verification)
- Adobe Photoshop / Illustrator, Canva (UI design assets)

---

## 🔒 Security Highlights
- **One-Time Password (OTP)** authentication (6-digit code, 5-minute expiry)
- **Password hashing** with bcrypt
- **Session-based access control** with inactivity timeouts
- **IP and device tracking** for anomaly detection
- **Rate limiting** on failed login attempts
- **SQL injection prevention** with prepared statements
- **Two-step loan identity verification** (ID upload + selfie verification)

---

## 📊 Database Overview
- **Core Tables:** `users`, `accounts`, `transactions`, `loans`, `investments`, `login_records`, `otp_verification`
- **Entity Relationships:** Proper foreign key constraints for referential integrity
- **Indexes:** Primary, foreign, unique, and composite indexes for fast queries
- **Normalization:** Fully normalized to **Third Normal Form (3NF)**

---

## 📥 Installation & Setup

### 1️⃣ Clone Repository
```bash
git clone https://github.com/PaulPaolo2929/Nexus-Banksystem.git

https://nexusbank.ccs-octa.com/login.php
https://nexusbank.ccs-octa.com/services.php
