<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ollamaService = app(\App\OllamaService::class);
        $texts = [
            "Alice is from the United States. She is 28 years old and lives in San Francisco, California. She is a software engineer with a passion for developing scalable web applications. Alice primarily codes in JavaScript and Python, and she has extensive experience with React and Django. In her free time, she enjoys hiking and exploring the outdoors. Her favorite IDE is Visual Studio Code.",
            "Bob is from Canada. He is 30 years old and resides in Toronto, Ontario. Bob is a backend developer who specializes in building robust APIs. He is proficient in Java and Spring Boot, and he also has a good grasp of Docker and Kubernetes. Bob loves playing chess and reading science fiction novels. His favorite IDE is IntelliJ IDEA.",
            "Charlie is from the United Kingdom. He is 27 years old and lives in London. Charlie is a full-stack developer with a strong focus on front-end technologies. He codes in TypeScript and Angular, and he has a keen interest in user experience design. In his spare time, Charlie enjoys painting and visiting art galleries. His favorite IDE is WebStorm.",
            "Diana is from Germany. She is 32 years old and resides in Berlin. Diana is a data scientist who works with large datasets to derive meaningful insights. She is skilled in R and Python, and she frequently uses libraries like Pandas and TensorFlow. Diana enjoys cooking and experimenting with new recipes. Her favorite IDE is Jupyter Notebook.",
            "Ethan is from Australia. He is 29 years old and lives in Sydney. Ethan is a mobile app developer who creates applications for both Android and iOS platforms. He codes in Kotlin and Swift, and he has a strong understanding of mobile UI/UX principles. Ethan loves surfing and spending time at the beach. His favorite IDE is Android Studio.",
            "Fiona is from India. She is 26 years old and resides in Bangalore. Fiona is a DevOps engineer who focuses on automating deployment pipelines and ensuring system reliability. She is proficient in Bash scripting and has experience with tools like Jenkins and Ansible. Fiona enjoys practicing yoga and meditation. Her favorite IDE is PyCharm.",
            "George is from Brazil. He is 31 years old and lives in São Paulo. George is a cloud engineer who specializes in designing and managing cloud infrastructure. He is skilled in AWS and Terraform, and he has a deep understanding of cloud security best practices. George enjoys playing soccer and watching movies. His favorite IDE is Sublime Text.",
            "Hannah is from Japan. She is 33 years old and resides in Tokyo. Hannah is a machine learning engineer who builds predictive models and works on natural language processing projects. She codes in Python and uses frameworks like Scikit-learn and Keras. Hannah enjoys reading manga and playing video games. Her favorite IDE is PyCharm.",
            "Ivan is from Russia. He is 34 years old and lives in Moscow. Ivan is a cybersecurity expert who focuses on protecting systems from cyber threats. He is proficient in C++ and Python, and he has experience with penetration testing tools like Metasploit and Wireshark. Ivan enjoys playing the piano and attending classical music concerts. His favorite IDE is CLion.",
            "Julia is from France. She is 29 years old and resides in Paris. Julia is a frontend developer who creates visually appealing and responsive web interfaces. She codes in HTML, CSS, and JavaScript, and she has experience with frameworks like Vue.js and Svelte. Julia enjoys photography and traveling to new places. Her favorite IDE is Visual Studio Code."
        ];

        foreach ($texts as $text) {
            $chunks = $this->chunkText($text, 250); // Chunk the text into pieces of 100 characters

            foreach ($chunks as $chunk) {
                $embedding = $ollamaService->getEmbedding($chunk);

                DB::insert("
                INSERT INTO documents (content, embedding, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ", [$chunk, json_encode($embedding)]);
            }
        }
    }

    /**
     * Chunk the text into smaller pieces.
     *
     * @param string $text
     * @param int $chunkSize
     * @return array
     */
    private function chunkText(string $text, int $chunkSize): array
    {
        $chunks = [];
        $length = strlen($text);

        for ($i = 0; $i < $length; $i += $chunkSize) {
            $chunks[] = substr($text, $i, $chunkSize);
        }

        return $chunks;
    }
}
