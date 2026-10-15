# Recipe Management System (Laravel 11)

A full-stack Web Application built with Laravel 11, Tailwind CSS, Alpine.js, and MySQL. It features full recipe CRUD management, user authentication, multi-user isolation, real-time search & filtering, community recipe sharing, and an integrated AI recipe generator.

---

## Key Features

- **Full Recipe CRUD:** Create, view, update, and delete recipes with title, cuisine, prep time, ingredients, and instructions.
- **Status & Organization:** Tag recipes as `To Try`, `Favorite`, or `Made Before`.
- **Search & Dynamic Filtering:** Filter recipes in real-time by search query (title/ingredients), status, or cuisine type.
- **Multi-User Authentication:** Built with Laravel Breeze. Each user manages their own private recipe library.
- **Community Sharing:** Toggle `is_public` on any recipe to publish it to the shared Community Gallery.
- **✨ AI Recipe Generator:** Input ingredients on hand to automatically generate structured recipe fields using Alpine.js and Laravel backend processing.

---

## Tech Stack

- **Framework:** Laravel 11
- **Language:** PHP 8.3
- **Database:** MySQL
- **Frontend:** Tailwind CSS, Alpine.js, Blade Templates
- **Authentication:** Laravel Breeze

---

## Local Setup & Installation

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL Database (e.g., Laragon / XAMPP)

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone <your-repository-url>
   cd recipe-app-laravel