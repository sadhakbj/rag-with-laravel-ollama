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
- **Node.js** (for frontend dependencies)
- **Ollama** (installed locally and running on port **11434** for AI generation)

## Installation

Follow these steps to set up the project:

1. Clone the repository:

    ```bash
    git clone git@github.com:sadhakbj/rag-with-laravel-ollama.git laravel-rag
    cd laravel-rag
    ```

2. Copy the `.env` file and configure environment variables:

    ```bash
    cp .env.example .env
    ```

    Update the `.env` file with the necessary variables, such as database credentials and other configurations.

3. Generate the application key:

    ```bash
    php artisan key:generate
    ```

4. Install PHP dependencies:

    ```bash
    composer install
    ```

5. Start the PostgreSQL database with pgvector support:

    ```bash
    docker compose up pgsql
    ```

6. Run database migrations:

    ```bash
    php artisan migrate
    ```

7. Seed the database with initial data:

    ```bash
    php artisan db:seed
    ```

8. Install frontend dependencies:

    ```bash
    npm install
    ```

9. Build and run the frontend assets:

    ```bash
    npm run dev
    ```

10. Ensure Ollama is running on port **11434**:

    ```bash
    ollama serve
    ```

11. Start the Laravel development server:

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
- **Can you please teach me how to scaffold a new project using CoolPHP on my macos from scratch in 4 quick steps. Step by step?**
- **How can I create a route for the About page that returns the simple string "Hello, I am Bijaya"?**
- **How can I list the routes that begin with api/v1 in COOLPHP?**
- **How can I generate a new migration file and run for a users table with `id`, `name`, `email`, and `password`?**

The AI will retrieve relevant information and generate responses based on your queries.

## License

This project is open-source and available under the [MIT License](LICENSE).
