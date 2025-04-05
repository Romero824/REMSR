# Real Estate Management System (REMSR)

A comprehensive real estate management system built with PHP and MySQL, featuring different user roles (Admin, Buyer, and Tenant) with specific functionalities.

## Features

- User Authentication (Login/Register)
- Role-based Access Control
- Property Management
- Transaction Management
- User Management
- Responsive Design

## Requirements

- XAMPP (Apache, MySQL, PHP)
- PHP 7.4 or higher
- MySQL 5.7 or higher

## Installation

1. Clone or download this repository to your XAMPP's htdocs folder:
   ```
   C:\xampp\htdocs\REMSR
   ```

2. Start XAMPP and ensure Apache and MySQL services are running.

3. Create the database:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database.sql` file
   - The database and tables will be created automatically

4. Configure the database connection:
   - Open `config/database.php`
   - Update the database credentials if needed (default is root with no password)

5. Access the application:
   - Open your browser and go to: http://localhost/REMSR

## Default Admin Credentials

- Email: admin@remsr.com
- Password: password

## User Roles

### Admin
- Manage properties
- Manage users
- Handle transactions
- Generate reports

### Buyer
- Browse properties
- Make inquiries
- Purchase properties
- View transaction history

### Tenant
- Browse rental properties
- Apply for rentals
- Make rental payments
- View rental history

## Security Features

- Password hashing
- SQL injection prevention
- Session management
- Input validation
- XSS protection

## Contributing

Feel free to submit issues and enhancement requests.

## License

This project is licensed under the MIT License. 