# Dental Appointment Booking System

This is a web-based dentist appointment booking system built with PHP and MySQL. It allows users to register, log in, book appointments with dentists, and manage their bookings. The project follows an MVC structure and supports RESTful APIs and JSON responses.

## Features

- User registration and login (session-based)
- Password reset via email (PHPMailer)
- View and search user's appointments
- Book, cancel, and modify appointments
- Filter doctors by service
- Only logged-in users can access appointments
- Prevent double-booking
- Backend: PHP (with MVC pattern)
- Frontend: Bootstrap + Vanilla JS
- MySQL database with phpMyAdmin
- Dockerized setup

## Sample Credentials

You can log in with the following user:

```
Email: test@gmail.com
Password: test
```

## Tech Stack

- PHP 8+
- MySQL
- Composer (for PHPMailer)
- Docker + Docker Compose
- Bootstrap 5

## How to Run (Locally via Docker)

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/appointment-booking-system.git
   cd appointment-booking-system
   ```

2. Run the project:
   ```bash
   docker-compose up --build
   ```

3. Access the app:
   - Frontend: `http://localhost:8080`
   - phpMyAdmin: `http://localhost:8081`  
     Login: `root / root`  
     DB: `appointment_system`

4. To install dependencies (inside container or locally):
   ```bash
   composer install
   ```

## Reset Password Logic

- Users can request a password reset.
- System sends a reset link via email (token-based).
- Repeated rapid requests won’t trigger multiple emails.

## Database

A sample SQL file for creating and populating the database is included in the `initdb/` folder.

## Notes

- The `vendor/` folder is excluded via `.gitignore`.
- Run `composer install` after cloning.

