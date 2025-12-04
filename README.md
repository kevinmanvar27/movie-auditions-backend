# Movie Auditions Backend

This is the backend for the Movie Auditions platform, built with Laravel.

## API Documentation

The API is documented using Swagger/OpenAPI. You can access the documentation at:

- [API Documentation](/api-docs.html)

## API Endpoints

### Authentication

All API endpoints require authentication using Laravel Sanctum tokens.

### Auditions

- `GET /api/v1/auditions` - Get list of auditions for the authenticated user
- `POST /api/v1/auditions` - Create a new audition
- `GET /api/v1/auditions/{id}` - Get a specific audition
- `PUT /api/v1/auditions/{id}` - Update an audition
- `DELETE /api/v1/auditions/{id}` - Delete an audition

### Movies

- `GET /api/v1/movies` - Get list of movies
- `GET /api/v1/movies/{id}` - Get a specific movie

### Admin Movies

- `GET /api/v1/admin/movies` - Get list of all movies
- `POST /api/v1/admin/movies` - Create a new movie
- `GET /api/v1/admin/movies/{id}` - Get a specific movie
- `PUT /api/v1/admin/movies/{id}` - Update a movie
- `DELETE /api/v1/admin/movies/{id}` - Delete a movie

### Admin Users

- `GET /api/v1/admin/users` - Get list of all users
- `POST /api/v1/admin/users` - Create a new user
- `GET /api/v1/admin/users/{id}` - Get a specific user
- `PUT /api/v1/admin/users/{id}` - Update a user
- `DELETE /api/v1/admin/users/{id}` - Delete a user

### Admin Roles

- `GET /api/v1/admin/roles` - Get list of all roles
- `POST /api/v1/admin/roles` - Create a new role
- `GET /api/v1/admin/roles/{id}` - Get a specific role
- `PUT /api/v1/admin/roles/{id}` - Update a role
- `DELETE /api/v1/admin/roles/{id}` - Delete a role

### Admin Settings

- `GET /api/v1/admin/settings` - Get system settings
- `PUT /api/v1/admin/settings` - Update system settings
- `GET /api/v1/admin/profile` - Get admin profile
- `PUT /api/v1/admin/profile` - Update admin profile
- `PUT /api/v1/admin/profile/password` - Update admin password

## Generating API Documentation

To generate the Swagger/OpenAPI documentation, run:

```
php generate-swagger.php
```

This will generate the `swagger.json` file in the `public` directory.
