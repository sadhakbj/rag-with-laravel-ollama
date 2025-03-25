import { useState } from 'react';
import ReactMarkdown from 'react-markdown';
import { Prism as SyntaxHighlighter } from 'react-syntax-highlighter';
import { dracula } from 'react-syntax-highlighter/dist/esm/styles/prism';
// Import Shadcn button component
import { Button } from '@/components/ui/Button';

const Chat = () => {
    const [query, setQuery] = useState('');
    const [message, setMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const sendMessage = () => {
        if (!query.trim()) return;

        setMessage(''); // Clear previous messages
        setIsLoading(true); // Disable input while loading

        const eventSource = new EventSource(`/api/chat?query=${encodeURIComponent(query)}`);

        eventSource.onmessage = (event) => {
            const data = event.data;

            if (data === '</stream>') {
                eventSource.close();
                setIsLoading(false);
                return;
            }

            try {
                const jsonData = JSON.parse(data);
                if (jsonData.response) {
                    setMessage((prevMessage) => prevMessage + jsonData.response);
                }
            } catch (error) {
                console.error('Error parsing data:', error);
            }
        };

        eventSource.onerror = (error) => {
            console.error('EventSource error:', error);
            eventSource.close();
            setIsLoading(false);
        };
    };

    return (
        <div className="mx-auto max-w-2xl space-y-4 p-4">
            <h2 className="text-center text-2xl font-bold">Chat with AI</h2>
            <div className="flex space-x-2">
                <input
                    type="text"
                    className="flex-1 rounded border p-2 shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    placeholder="Ask something..."
                    value={query}
                    onChange={(e) => setQuery(e.target.value)}
                    disabled={isLoading}
                />
                <Button onClick={sendMessage} disabled={isLoading}>
                    {isLoading ? 'Thinking...' : 'Send'}
                </Button>
            </div>
            <div className="min-h-[300px] rounded border bg-gray-100 p-3 shadow-inner">
                {message ? (
                    <ReactMarkdown
                        components={{
                            code({ node, inline, className, children, ...props }) {
                                const match = /language-(\w+)/.exec(className || '');
                                return !inline && match ? (
                                    <SyntaxHighlighter style={dracula} language={match[1]} PreTag="div" {...props}>
                                        {String(children).replace(/\n$/, '')}
                                    </SyntaxHighlighter>
                                ) : (
                                    <code className="rounded bg-gray-200 px-1 py-0.5" {...props}>
                                        {children}
                                    </code>
                                );
                            },
                        }}
                    >
                        {message}
                    </ReactMarkdown>
                ) : isLoading ? (
                    'Generating response...'
                ) : (
                    'Enter a question to get started.'
                )}
            </div>
        </div>
    );
};

export default Chat;
