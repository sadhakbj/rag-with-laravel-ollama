import { useState } from 'react';

const Chat = () => {
    const [query, setQuery] = useState('');
    const [message, setMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const sendMessage = () => {
        if (!query.trim()) return;

        setMessage(''); // Clear previous messages
        setIsLoading(true); // Disable input while loading

        // const eventSource = new EventSource(`/api/chat?query=${encodeURIComponent(query)}`);

        const eventSource = new EventSource(`/check-pr?query=${encodeURIComponent(query)}`);

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
        <div className="mx-auto max-w-md space-y-4 p-4">
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
                <button
                    className={`rounded bg-blue-500 px-5 py-2 text-white shadow-md transition-colors hover:bg-blue-600 ${
                        isLoading ? 'cursor-not-allowed opacity-50' : ''
                    }`}
                    onClick={sendMessage}
                    disabled={isLoading}
                >
                    {isLoading ? 'Thinking...' : 'Send'}
                </button>
            </div>
            <div className="min-h-[150px] rounded border bg-gray-100 p-3 shadow-inner">
                {message || (isLoading ? 'Generating response...' : 'Enter a question to get started.')}
            </div>
        </div>
    );
};

export default Chat;
