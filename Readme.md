# Laravel RAG with Ollama

This project is a sample **Retrieval-Augmented Generation (RAG)** implementation using **Laravel PHP** and **Ollama**. It demonstrates how to build a conversational AI system that retrieves relevant information from a database and generates responses using AI.

## Technologies Used

- **Laravel 12**: Backend framework for building robust web applications.
- **Tailwind CSS**: Utility-first CSS framework for styling.
- **Shadcn UI**: Pre-built UI components for React.
- **React with Inertia**: Frontend framework for building single-page applications.
- **TypeScript**: Strongly typed JavaScript for better development experience.

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** (>= 8.3)
- **Composer**
- **Docker** (for running PostgreSQL with pgvector)
- **Ollama** (installed locally for AI generation)

## Installation

Follow these steps to set up the project:

1. Clone the repository:

    ```bash
    git clone git@github.com:sadhakbj/rag-with-laravel-ollama.git laravel-rag
    cd laravel-rag
    ```

2. Install PHP dependencies:

    ```bash
    composer install
    ```

3. Start the PostgreSQL database with pgvector support:

    ```bash
    docker compose up pgsql
    ```

4. Run database migrations:

    ```bash
    php artisan migrate
    ```

5. Seed the database with initial data:

    ```bash
    php artisan db:seed
    ```

6. Start the Laravel development server:
    ```bash
    php artisan serve
    ```

## Access

Once the server is running, you can access the application at:

```
http://localhost:8000
```

## Usage

You can interact with the AI by asking questions in the chat interface. Here are some example questions you can try:

- **Tell me about CoolPHP in 5 lines.**
- **How can I generate a simple route in CoolPHP?**
- **What is migration in CoolPHP, and how can I create a new migration for a users table with `id`, `name`, `email`, and `password`?**

The AI will retrieve relevant information and generate responses based on your queries.

## License

This project is open-source and available under the [MIT License](LICENSE).
