# Blog API

This project is a RESTful Blog API built with Laravel, MySQL, and PHP. It allows users to create accounts, write blog posts, assign tags, like posts, and manage blogs through a dashboard. The project avoids the direct use of Eloquent ORM and utilizes Query Builder for database interactions.

## Features

- **User Authentication**: Register, login, and token-based authentication for users.
- **Blog Management**: Users can create, update, delete, and view their own blogs.
- **Tags**: Blogs can have multiple tags.
- **Likes**: Users can like or unlike blog posts. Each user can like a post only once.
- **Public Blog Viewing**: Blogs are visible to all users, even without authentication.
- **Dashboard**: Authenticated users can manage their own blog posts.
- **API**: Fully functional API built for interacting with the blog system.

## Installation

### Prerequisites

- PHP >= 8.0
- MySQL
- Composer

### Steps

1. Clone the repository:

   ```bash
   git clone your-repo-url
   ```

2. Navigate to the project directory:

   ````bash
   cd your-repo-name
   ````

3. Install dependencies:

   ````bash
   composer install
   ````

4. Set up your `.env` file:

   ````bash
   cp .env.example .env
   ````

   Configure the following fields in the `.env` file:

   ````bash
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ````

5. Generate an application key:

   ````bash
   php artisan key:generate
   ````

6. Run database migrations and seed the database, including entering admin info:

   ````bash
   php artisan migrate --seed
   ````

7. Serve the application locally:

   ````bash
   php artisan serve
   ````

8. Start the queue worker (if applicable):

   ````bash
   php artisan queue:work
   ````

## Export Blogs

To export blogs within a specific date range, use:

```bash
php artisan blogs:export --start=2024-01-01 --end=2024-01-31
```

To export all blogs, use:

```bash
php artisan blogs:export
```rtisan test
```

## Folder Structure

- **app/Http/Controllers**: Contains API controllers.
- **app/Models**: Custom model logic.
- **database/migrations**: Database migration files.
- **database/factories**: Factories for seeding the database.
- **routes/api.php**: API routes for the project.

## Contributing

Feel free to submit issues or pull requests. Contributions are always welcome!

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Additional Commands

Remember to update the README at the end of your project with the following commands:

````markdown
```bash
# Database seed and enter admin info
php artisan migrate --seed

# Start the queue worker
php artisan queue:work

# Export blogs within a specific date range
php artisan blogs:export --start=2024-01-01 --end=2024-01-31

# Export all blogs
php artisan blogs:export
```
````

