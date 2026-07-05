# Biggulegalis Chat Socket Server

Open-source Socket.IO server for one-to-one mobile chat.

## Start Server

```bash
cd nodejs
npm install
SOCKET_SERVER_SECRET=change-me SOCKET_PORT=3000 npm start
```

Use the same secret in Laravel `.env`.

```env
SOCKET_SERVER_URL=https://biggulegalis.com
SOCKET_SERVER_SECRET=change-me
```

## Mobile App Connection

Connect:

```js
const socket = io('https://biggulegalis.com', {
  transports: ['websocket']
});
```

Join the logged-in user room:

```js
socket.emit('join', { user_id: LOGGED_IN_USER_ID }, (response) => {
  console.log(response);
});
```

Listen for new messages:

```js
socket.on('new_message', (message) => {
  console.log('New message:', message);
});
```

## Flow

1. Mobile app calls Laravel `send-message` API.
2. Laravel saves the message.
3. Laravel posts the saved message to this Node server.
4. Node emits `new_message` to sender and receiver rooms.

## Production Domain Setup

To serve Socket.IO from `https://biggulegalis.com/`, your web server must reverse proxy WebSocket traffic to the Node process on port `3000`.

If you use Apache, the host should add a vhost rule similar to:

```apache
ProxyPreserveHost On
ProxyPass /socket.io http://127.0.0.1:3000/socket.io
ProxyPassReverse /socket.io http://127.0.0.1:3000/socket.io
ProxyPass /socket.io/socket.io.js http://127.0.0.1:3000/socket.io/socket.io.js
```

If you use Nginx, the host should proxy `/socket.io/` to `127.0.0.1:3000` with websocket headers enabled.

If you cannot edit Apache/Nginx config, use a socket subdomain like `https://socket.biggulegalis.com` and point that subdomain to the Node server instead.

## Events

- Client emits: `join`
- Client listens: `new_message`

## Health Check

```text
GET /health
```
