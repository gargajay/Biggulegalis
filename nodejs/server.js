const express = require('express');
const http = require('http');
const axios = require('axios');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);
const port = process.env.SOCKET_PORT || 3000;
const secret = process.env.SOCKET_SERVER_SECRET || 'change-me';
const appBaseUrl = process.env.APP_URL || 'https://biggulegalis.com';
const authUrl = process.env.SOCKET_AUTH_URL || appBaseUrl.replace(/\/$/, '') + '/api/chat-socket-auth';
const socketPath = process.env.SOCKET_IO_PATH || '/nodejs/socket.io';

app.use(express.json({ limit: '2mb' }));

app.use((req, res, next) => {
    req.url = req.url.replace(/^\/nodejs/, '');
    next();
});

const io = new Server(server, {
    path: socketPath,
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
});



app.get('/', function (req, res) {
    console.log('Received request on /');
    res.json({
        success: true,
        message: 'Biggulegalis chat socket server is running.'
    });
});

app.get('/test', function (req, res) {
    res.sendFile(__dirname + '/socket.html');
});

app.get('/health', function (req, res) {
    res.json({
        success: true,
        online_users: io.sockets.adapter.rooms.size
    });
});

app.post('/emit-message', function (req, res) {
    if (req.header('x-socket-secret') !== secret) {
        return res.status(401).json({
            success: false,
            message: 'Unauthorized.'
        });
    }

    const message = req.body.message;
    const receiverId = req.body.receiver_id;
    const senderId = req.body.sender_id;

    if (!message || !receiverId || !senderId) {
        return res.status(422).json({
            success: false,
            message: 'message, receiver_id and sender_id are required.'
        });
    }

    const receiverInChat = isUserInChat(receiverId, message.chat_id);

    io.to(userRoom(receiverId)).emit('new_message', message);
    io.to(userRoom(senderId)).emit('new_message', message);

    return res.json({
        success: true,
        event: 'new_message',
        receiver_in_chat: receiverInChat
    });
});

app.post('/emit-chat-request', function (req, res) {
    if (req.header('x-socket-secret') !== secret) {
        return res.status(401).json({
            success: false,
            message: 'Unauthorized.'
        });
    }

    const chatRequest = req.body.chat_request;
    const receiverId = req.body.receiver_id;
    const requesterId = req.body.requester_id;

    if (!chatRequest || !receiverId || !requesterId) {
        return res.status(422).json({
            success: false,
            message: 'chat_request, receiver_id and requester_id are required.'
        });
    }

    io.to(userRoom(receiverId)).emit('chat_request_updated', chatRequest);
    io.to(userRoom(requesterId)).emit('chat_request_updated', chatRequest);

    return res.json({
        success: true,
        event: 'chat_request_updated'
    });
});

io.use(async function (socket, next) {
    try {
        const token = bearerToken(socket);

        if (!token) {
            return next(new Error('Authentication token is required.'));
        }

        const response = await axios.get(authUrl, {
            headers: {
                Authorization: 'Bearer ' + token,
                Accept: 'application/json'
            },
            timeout: 5000
        });

        const userId = parseInt(response.data && response.data.data && response.data.data.user_id, 10);

        if (!userId) {
            return next(new Error('Invalid authentication token.'));
        }

        socket.data.user_id = userId;
        return next();
    } catch (error) {
        return next(new Error('Invalid authentication token.'));
    }
});

io.on('connection', function (socket) {
    socket.on('join', function (data, callback) {
        const userId = parseInt(data && data.user_id, 10);

        if (!userId) {
            return ack(callback, {
                success: false,
                message: 'user_id is required.'
            });
        }

        if (userId !== socket.data.user_id) {
            return ack(callback, {
                success: false,
                message: 'You can only join your authenticated user room.'
            });
        }

        socket.join(userRoom(userId));

        return ack(callback, {
            success: true,
            message: 'Joined successfully.',
            room: userRoom(userId)
        });
    });

    socket.on('chat_opened', function (data, callback) {
        const chatId = parseInt(data && data.chat_id, 10);

        if (!chatId) {
            return ack(callback, {
                success: false,
                message: 'chat_id is required.'
            });
        }

        socket.data.active_chat_id = chatId;

        return ack(callback, {
            success: true,
            message: 'Chat marked active.',
            chat_id: chatId
        });
    });

    socket.on('chat_closed', function (data, callback) {
        const chatId = parseInt(data && data.chat_id, 10);

        if (!chatId || socket.data.active_chat_id === chatId) {
            socket.data.active_chat_id = null;
        }

        return ack(callback, {
            success: true,
            message: 'Chat marked inactive.'
        });
    });

    socket.on('disconnect', function () {
        console.log('Socket disconnected', socket.id);
    });
});

function userRoom(userId) {
    return 'user_' + userId;
}

function isUserInChat(userId, chatId) {
    const room = io.sockets.adapter.rooms.get(userRoom(userId));

    if (!room || !chatId) {
        return false;
    }

    for (const socketId of room) {
        const socket = io.sockets.sockets.get(socketId);

        if (socket && parseInt(socket.data.active_chat_id, 10) === parseInt(chatId, 10)) {
            return true;
        }
    }

    return false;
}

function bearerToken(socket) {
    const authToken = socket.handshake.auth && socket.handshake.auth.token;
    const header = socket.handshake.headers && socket.handshake.headers.authorization;
    const token = authToken || header;

    if (!token) {
        return null;
    }

    return String(token).replace(/^Bearer\s+/i, '').trim();
}

function ack(callback, data) {
    if (typeof callback === 'function') {
        callback(data);
    }
}

server.listen(port, function () {
    console.log('Biggulegalis chat socket server listening on *:' + port);
});
