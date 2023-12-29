## About Trainee Management System

Trainee Management System, or TMS, is a web application that allows:
- a company to manage all trainees with ease.
- supervisors to keep track of their trainees' task progress.
- supervisors to assign a task to their trainees.
- trainees to upload their logbooks to be signed.
- trainees to keep track of the task given by their supervisors.

## Features

**Admin**
- view the statistics for the week and the current week's seating plan via the **Dashboard**.
- manage all user accounts.
- create, edit and delete trainee records.
- create and edit a seating plan for the desired week.
- assign supervisors to a trainee or remove supervisors from a trainee.
- view, add, edit and delete a trainee task.
- view and upload trainee resume.
- view, upload and delete a trainee logbook.
- receive a Telegram notification when there are any important user activities.

**Supervisors**
- view all the trainees that are assigned to them.
- view their trainees information.
- view, add, edit and delete their trainee tasks.
- view, upload and delete their trainee logbooks.
- view trainee seating plans for the month.
- receive a Telegram notification when there are any important user activities.

**Trainees**
- view all the supervisors that are assigned to them.
- view, add, edit and delete tasks.
- view, upload and delete logbooks.
- view, upload and delete resumes.
- view seating plans for the month.


## Requirements
- Laravel Version 10.34.2 and above 
- PHP Version 8.2.0 and above
- Composer Version 2.6.3 and above

## Installation
1. Clone the project.

2. Download the `env.zip` file at [here](https://drive.sains.com.my/index.php/f/18674969)

3. Extract the `.env` file and put it under the directory of the cloned project.

4. Run `composer install` at the terminal.

5. Run `php artisan migrate` to create all the needed table or download the database `tms-db.zip` file [here](https://drive.sains.com.my/index.php/f/18674969)

6. Run `php artisan serve` or use XAMPP to run the server.

7. If you are using XAMPP to run the server, you need to import the sql files downloaded from `tms-db.zip` by own to the phpMyAdmin.

## User Manuals
You can refer to these documents for further information:

**Admin**: [here](https://drive.sains.com.my/index.php/s/jxN3GyYrfNZ6BCn)

**Supervisor**: [here](https://drive.sains.com.my/index.php/s/zpJLnNJpYq6zTyc)

**Trainee**: [here](https://drive.sains.com.my/index.php/s/YqxjkiR2fT3mCHX)


