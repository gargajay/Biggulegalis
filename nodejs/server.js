const app = require('express')();
const server = require('http').createServer(app);
const io = require('socket.io')(server);
const axios = require('axios');

app.get('/', (req, res) => {
    res.sendFile(__dirname + '/socket.html');
});

const serverUrl = "https://pro.strengthennumbers.co/api/";

var clients = {};
io.on("connection", function (socket) {
    console.log("Socket Connected");

    socket.on('join', function (data, callback) {
        if (!data.sender_id || !data.receiver_id || !data.type) {
            const errorMessage = "Required key(s) missing.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }

        if (!Number.isInteger(data.sender_id) || data.sender_id <= 0) {
            const errorMessage = "Invalid sender_id. Must be a positive integer.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }

        if (!Number.isInteger(data.receiver_id) || data.receiver_id <= 0) {
            const errorMessage = "Invalid receiver_id. Must be a positive integer.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }

        if (!Number.isInteger(data.type) || ![1, 2].includes(data.type)) {
            const errorMessage = "Invalid type. Must be either 1 or 2.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }
        let name = '';
        if (data.type == 1) {
            name = data.sender_id + '-' + data.receiver_id;
        }
        if (data.type == 2) {
            name = data.sender_id + '@' + data.receiver_id;
        }
        clients[name] = socket.id;
        handleValidationFailure(callback, 'Acknowledgment: Successfully joined as ' + name);
    });

    socket.on("sendData", function (data, callback) {
        if (!data || typeof data !== "object") {
            const errorMessage = "Invalid data format. Data must be an object.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }

        if (!data.route) {
            const errorMessage = "Required route key missing in data.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }

        if (!data.hasOwnProperty('param')) {
            const errorMessage = "Required param key missing in data.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }

        if (!data.hasOwnProperty('headers')) {
            const errorMessage = "Required headers key missing in data.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }


        const senderName = getClientNameBySocketId(socket.id);
        if (!senderName) {
            const errorMessage = "You have not joined.";
            handleValidationFailure(callback, errorMessage);
            socket.emit('error', errorMessage);
            return;
        }
        axios.post(serverUrl + data.route, data.param, {
            headers: data.headers
        })
            .then(function (response) {
                handleValidationFailure(callback, response.data);
                if (response.data && response.data.receiver_ids && response.data.data && response.data.success == true) {
                    for (const receiver_id of response.data.receiver_ids) {
                        const receiverSocketId = clients[receiver_id];
                        if (receiverSocketId) {
                            io.to(receiverSocketId).emit('receiveData', response.data);
                        }
                    }
                } else if (response.data.success == true) {
                    socket.emit('receiveData', response.data);
                } else {
                    handleValidationFailure(callback, response.data);
                    socket.emit('error', response.data);
                }
            })
            .catch(function (error) {
                handleValidationFailure(callback, error);
                socket.emit('error', error);
            });
    });

    socket.on("disconnect", function () {
        const disconnectedId = socket.id;

        for (const name in clients) {
            if (clients[name] === disconnectedId) {
                delete clients[name];
                console.log("User with socket ID " + disconnectedId + " has disconnected");
                break;
            }
        }
    });
});

function handleValidationFailure(callback, errorMessage) {
    if (typeof callback === "function") {
        callback(errorMessage);
    }
}

function getClientNameBySocketId(socketId) {
    return Object.keys(clients).find(name => clients[name] === socketId);
}

server.listen(3000, () => {
    console.log('listening on *:3000');
});;
