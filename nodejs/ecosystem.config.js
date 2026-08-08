module.exports = {
    apps: [
        {
            name: 'biggulegalis-chat-socket',
            script: 'server.js',
            cwd: __dirname,
            env: {
                SOCKET_PORT: 3000,
                SOCKET_SERVER_SECRET: 'change-me',
                SOCKET_IO_PATH: '/nodejs/socket.io',
                APP_URL: 'https://biggulegalis.com'
            }
        }
    ]
};
