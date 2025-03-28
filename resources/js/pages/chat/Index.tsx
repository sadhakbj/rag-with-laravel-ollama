import { Button } from '@/components/ui/button';
import { Head } from '@inertiajs/react';
import { LoaderCircle, MessageCircleCode } from 'lucide-react';
import { useState } from 'react';
import ReactMarkdown from 'react-markdown';
import { Prism as SyntaxHighlighter } from 'react-syntax-highlighter';
import { vscDarkPlus } from 'react-syntax-highlighter/dist/esm/styles/prism';
import { Textarea } from '@/components/ui/textarea';

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
            <Head title="My Local RAG" />
            <h2 className="text-center text-2xl font-bold">My Local RAG</h2>
            <div className="flex space-x-2">
                 <Textarea placeholder="Ask something about COOLPHP...."
                           value={query}
                           onChange={(e) => setQuery(e.target.value)}
                           disabled={isLoading}
                 />

                <Button onClick={sendMessage} disabled={isLoading}>
                    {isLoading ? <LoaderCircle className="h-4 w-4 animate-spin" /> :
                    <MessageCircleCode className="h-4 w-4" />
                    }
                </Button>
            </div>
            <div className="min-h-[300px] rounded border bg-gray-100 p-3 shadow-inner">
                {message ? (
                    <ReactMarkdown
                        components={{
                            code({ node, inline, className, children, ...props }) {
                                const match = /language-(\w+)/.exec(className || '');
                                return !inline && match ? (
                                    <SyntaxHighlighter style={vscDarkPlus} language={match[1]} PreTag="div" {...props}>
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
                    <LoaderCircle className="h-4 w-4 animate-spin" />
                ) : (
                    'Enter a question to get started.'
                )}
            </div>
        </div>
    );
};

export default Chat;
