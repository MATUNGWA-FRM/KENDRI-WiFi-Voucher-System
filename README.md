KENDRI WiFi Voucher Management System

A PHP and MySQL web application for creating, activating, and checking WiFi voucher codes.

Features

- Generate and store WiFi voucher codes.
- Activate vouchers and start their validity period.
- Check voucher status: unused, active, or expired.
- Display a countdown timer for active vouchers.
- Store voucher information in a MySQL database.
- Responsive user interface built with HTML and CSS.

Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP
- Git and GitHub

Installation

1. Clone the repository

git clone https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git

Replace the placeholders with your GitHub username and repository name.

2. Set up XAMPP

Copy the project folder into:

C:\xampp\htdocs\mspot_wifi\

Start Apache and MySQL from the XAMPP Control Panel.

3. Set up the database

1. Open "phpMyAdmin" (http://localhost/phpmyadmin/).
2. Import the "database.sql" file.
3. Check "db.php" to ensure the database connection details match your local setup.

4. Run the application

Open the following URL in your browser:

http://localhost/mspot_wifi/

Voucher checking page:

http://localhost/mspot_wifi/check_voucher.php

How It Works

1. A voucher code is generated and stored in the database.
2. The user activates the voucher.
3. The system records the activation and expiry times.
4. The user can check the voucher status.
5. Active vouchers display the remaining validity time.
6. Expired vouchers are marked as expired when checked by the PHP application.

Current Limitations

This project is a web application prototype. It does not yet connect to a physical WiFi router, automatically disconnect devices, or process real payments.

Router or captive portal integration and a payment gateway would be needed to support those features.

Future Improvements

- Router and captive portal integration.
- Administrator dashboard.
- Online payment integration.
- Voucher usage reports.
- Additional security controls and automated tests.

Author

MSPOT WiFi Voucher Management System

Developed as a learning project to practise PHP, MySQL, HTML, CSS, and JavaScript.