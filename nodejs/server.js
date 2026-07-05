const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);
const port = process.env.SOCKET_PORT || 3000;
const secret = process.env.SOCKET_SERVER_SECRET || 'change-me';

app.use(express.json({ limit: '2mb' }));

app.use((req, res, next) => {
    req.url = req.url.replace(/^\/nodejs/, '');
    next();
});

const io = new Server(server, {
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

    io.to(userRoom(receiverId)).emit('new_message', message);
    io.to(userRoom(senderId)).emit('new_message', message);

    return res.json({
        success: true,
        event: 'new_message'
    });
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

        socket.join(userRoom(userId));
        socket.data.user_id = userId;

        return ack(callback, {
            success: true,
            message: 'Joined successfully.',
            room: userRoom(userId)
        });
    });

    socket.on('disconnect', function () {
        console.log('Socket disconnected', socket.id);
    });
});

function userRoom(userId) {
    return 'user_' + userId;
}

function ack(callback, data) {
    if (typeof callback === 'function') {
        callback(data);
    }
}

server.listen(port, function () {
    console.log('Biggulegalis chat socket server listening on *:' + port);
});
