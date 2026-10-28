# PrimeVest – Investment Platform (Prototype)

A complete, functional investment website built with **PHP**, **MySQL**, **Bootstrap 5**, **HTML**, **CSS**, and **JavaScript**.

**Style:** Clean white modern design with Netflix-inspired red accents, smooth cards, and professional UI.  
**Currency:** USD ($)  
**Mode:** Fully virtual / demo (no real payments)

## Features
- User Registration & Login (secure password hashing)
- Dashboard with balance, active investments, expected profit, transaction history
- Multiple Investment Plans with ROI & duration
- Virtual Deposits (instant credit)
- Virtual Withdrawals
- Invest in plans (balance deduction + investment tracking)
- Responsive design (mobile-friendly)
- Clean, modern UI

## Requirements
- PHP 7.4+ (or 8.x recommended)
- MySQL 5.7+ / MariaDB
- Apache or Nginx with PHP support
- PDO MySQL extension enabled

## How to Run Locally (XAMPP / WAMP / Laragon / MAMP)

1. **Download & Extract**
   - Place the `primevest` folder inside your web root:
     - XAMPP: `C:\xampp\htdocs\primevest`
     - WAMP: `C:\wamp64\www\primevest`
     - Laragon: `C:\laragon\www\primevest`
     - Linux: `/var/www/html/primevest`

2. **Create Database**
   - Open phpMyAdmin → Create a new database named `primevest`
   - Import the file `database.sql` (or run the SQL commands)

3. **Configure Database Credentials**
   - Open `config.php`
   - Update if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'primevest');
     define('DB_USER', 'root');
     define('DB_PASS', '');   // empty on XAMPP by default
     ```

4. **Start the Server**
   - Start Apache + MySQL in XAMPP/WAMP/Laragon
   - Visit: `http://localhost/primevest`

5. **Create an Account**
   - Go to Register → create a new user
   - Login → Deposit virtual funds → Choose a plan and invest

## Free Hosting (No Watermark)

Recommended free hosts that support PHP + MySQL and have **no forced watermark**:

| Host              | PHP | MySQL | Notes                              | Link                          |
|-------------------|-----|-------|------------------------------------|-------------------------------|
| **InfinityFree**  | Yes | Yes   | Completely free, no ads/watermark  | https://infinityfree.net     |
| **000webhost**    | Yes | Yes   | Free tier available                | https://www.000webhost.com   |
| **AwardSpace**   | Yes | Yes   | Free plan                          | https://www.awardspace.com   |
| **FreeHosting.com**| Yes | Yes  | Basic free plan                    | https://www.freehosting.com  |

### How to Deploy on InfinityFree (Recommended)

1. Sign up at https://infinityfree.net
2. Create a free account + website (subdomain or free domain)
3. Go to **Control Panel → MySQL Databases** → Create a database
4. Note the DB host, name, username, password
5. Upload all files via **File Manager** or FTP into the `htdocs` folder
6. Edit `config.php` with the new database credentials
7. Import `database.sql` via phpMyAdmin in the control panel
8. Visit your free subdomain

**Important:** Change `SITE_URL` in `config.php` to your live domain.

## Security Notes (for production)
- Turn off `display_errors` in `config.php`
- Use HTTPS
- Add CSRF tokens
- Rate-limit login attempts
- Never use this with real money without proper audits, payment gateways, and legal compliance

## File Structure
```
primevest/
├── assets/css/style.css
├── assets/js/app.js
├── includes/
│   ├── db.php
│   ├── auth.php
│   ├── header.php
│   └── footer.php
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── plans.php
├── deposit.php
├── withdraw.php
├── invest.php
├── logout.php
├── config.php
├── database.sql
└── README.md
```

## Demo Flow
1. Register a new account
2. Deposit virtual money (e.g. $1000)
3. Go to Investment Plans → choose one → invest
4. View Dashboard to see active investments and history
5. Withdraw remaining balance if desired

---
**PrimeVest** – Demo / Educational Prototype only.  
No real financial services are provided.
