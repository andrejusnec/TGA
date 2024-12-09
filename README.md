# TGA (Teacher Group assignment)

## Description

A simple app for teachers to manage students and groups in projects. Teachers can create projects, add/delete students, and assign them to groups.
Built with Symfony 6.4, it uses OOP principles and supports RESTful APIs for some features.

---

## Technical Decisions

### Stack
- **Symfony 6.4**
- **MySQL 8.0**
- **Bootstrap 5.3.3**
- **Docker**
- **JQuery 3.7.1**

### Design
- RESTful API for adding students functionality.
- Other functionalities uses standard Symfony components like controllers, services, and event listeners.
---

## Features

1. **Projects**
    - Create projects (title, groups, max students/group).
    - Automatic group initialization on project create.

2. **Students**
    - Ability to create new students.
    - Validations to student data.
    - View all students.
    - Ability to delete a student.

3. **Group Assignments**
    - Assign students to groups.
    - Remove student from a group.
    - Warns when groups are full.

4. **Real-Time Updates**
    - Status page data fetch every 10 seconds.

5. **Testing**
    - Automated tests for deleting students.

---

## Known issues
- Overall code quality improvement using tools like phpstan, csfixer.
- No possibility to change already created projects maximum student amount per group and count of groups.
- Page refreshes instead of dynamic data changes on some places of the app pages
- General UX and styling 


## Project Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/andrejusnec/TGA.git
   ```
2. **Docker**
   - Build and start the Docker containers:
   ```bash
     docker compose up -d
     ```
   or
   ```bash
     docker-compose up -d
     ```
     then
   ```bash
     docker exec -it app bash
     ```
3. **Install Dependencies**
     ```bash
     composer install
     ```

4. **Set Up the Database**
   - Create and migrate the database:
    ```bash
     php bin/console doctrine:database:create
     php bin/console doctrine:migrations:migrate
     ```

5. **Run the Application**

   - Open the app in your browser at [http://localhost:8080](http://localhost:8080).

6. **Tests**
   - To set up test DB run:
   ```bash
     composer setup-test-db
     ```
   - To execute tests run:
   ```bash
     ./vendor/bin/phpunit tests
     ```