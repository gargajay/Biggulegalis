# Chat Frontend Integration Contract

Use this document to integrate the Flutter chat module with the Laravel API and Socket.IO server.

## Base URLs

- API base URL: `https://biggulegalis.com/api`
- Socket.IO server URL: `https://biggulegalis.com`
- Socket.IO path: `/nodejs/socket.io`

## Shared Headers

All API requests below require the logged-in user token returned by login.

```http
Authorization: Bearer <access_token>
Accept: application/json
```

For JSON requests, also send:

```http
Content-Type: application/json
```

## Standard API Response

Successful responses use this wrapper:

```json
{
  "success": true,
  "status": 200,
  "message": "Message text",
  "data": {}
}
```

Paginated chat endpoints return `data` like:

```json
{
  "data": [],
  "totalPages": 1,
  "nextPageUrl": null
}
```

## Status Values

Chat request status:

- `0`: pending
- `1`: accepted
- `2`: rejected / denied

Message media type:

- `text`
- `image`
- `document`
- `link`

## API Endpoints

### 1. Member List

Fetch association members available for chat.

```http
POST /chat-member-list
```

Request body:

```json
{
  "association_id": "1",
  "search": ""
}
```

Notes:

- `association_id` is optional. If empty, backend uses the logged-in user's association.
- `search` is optional and matches `full_name`.

Important response fields inside `data.data[]`:

```json
{
  "id": 2,
  "full_name": "Member Name",
  "image": "https://...",
  "chat_request_status": 0,
  "chat_id": null
}
```

### 2. Send Chat Request

Send a chat request to another association member.

```http
POST /send-chat-request
```

Request body:

```json
{
  "receiver_id": 2
}
```

Response `data`:

```json
{
  "id": 1,
  "requester_id": 1,
  "receiver_id": 2,
  "chat_id": null,
  "status": 0,
  "requester": {
    "id": 1,
    "full_name": "Sender Name",
    "image": "https://..."
  },
  "receiver": {
    "id": 2,
    "full_name": "Receiver Name",
    "image": "https://..."
  }
}
```

### 3. Chat Request List

Fetch sent or received chat requests.

```http
POST /chat-request-list
```

Received requests:

```json
{
  "type": "received"
}
```

Sent requests:

```json
{
  "type": "sent"
}
```

Notes:

- If `type` is missing, backend defaults to `received`.
- Response is paginated.

### 4. Chat Request Action

Accept or reject a pending request.

```http
POST /chat-request-action
```

Accept:

```json
{
  "request_id": 1,
  "status": 1
}
```

Reject:

```json
{
  "request_id": 1,
  "status": 2
}
```

Accepted response includes `chat_id` and `chat`.

```json
{
  "id": 1,
  "requester_id": 1,
  "receiver_id": 2,
  "chat_id": 10,
  "status": 1,
  "requester": {},
  "receiver": {},
  "chat": {
    "id": 10,
    "sender_id": 1,
    "receiver_id": 2,
    "model": "App\\Models\\User",
    "model_id": 2,
    "type": 1
  }
}
```

### 5. Chat List

Fetch active accepted one-to-one chats.

```http
GET /chat-list
```

Response `data[]`:

```json
{
  "id": 10,
  "sender_id": 1,
  "receiver_id": 2,
  "model": "App\\Models\\User",
  "model_id": 2,
  "type": 1,
  "sender_chat_hide": null,
  "receiver_chat_hide": null,
  "sender_delete_chat_at": null,
  "receiver_delete_chat_at": null,
  "sender_detail": {
    "id": 1,
    "name": "Sender Name",
    "email": "sender@example.com",
    "image": "https://..."
  },
  "member": {
    "id": 2,
    "full_name": "Other User",
    "image": "https://..."
  },
  "last_message": {
    "id": 100,
    "chat_id": 10,
    "sender_id": 1,
    "receiver_id": 2,
    "message": "Hello",
    "media_type": "text",
    "media_url": null,
    "media_extension": null,
    "is_seen": false,
    "created_at": "2026-07-05T00:00:00.000000Z"
  },
  "unread_count": 1
}
```

### 6. Chat Messages

Fetch messages for an accepted chat.

```http
POST /chat-messages
```

Request body:

```json
{
  "chat_id": 10
}
```

Notes:

- Backend marks messages as seen where `receiver_id` is the logged-in user.
- Backend denies access if logged-in user is not part of the chat.
- Response is paginated.

Response `data.data[]`:

```json
{
  "id": 100,
  "chat_id": 10,
  "sender_id": 1,
  "receiver_id": 2,
  "message": "Hello",
  "media_type": "text",
  "media_url": null,
  "media_extension": null,
  "is_seen": false,
  "created_at": "2026-07-05T00:00:00.000000Z",
  "sender_detail": {
    "id": 1,
    "full_name": "Sender Name",
    "image": "https://..."
  },
  "receiver_detail": {
    "id": 2,
    "full_name": "Receiver Name",
    "image": "https://..."
  }
}
```

### 7. Send Message

Send text, link, image, or document messages.

```http
POST /send-message
```

Text message body:

```json
{
  "chat_id": 10,
  "message": "Hello",
  "media_type": "text"
}
```

Link message body:

```json
{
  "chat_id": 10,
  "message": "https://barcouncilofpunjab.org",
  "media_type": "link"
}
```

Multipart file message:

```text
chat_id: 10
message: Please check this file.
media_type: image
media: <file>
```

Use `media_type: image` for photos and `media_type: document` for PDFs/docs.
The backend will also infer the correct file type from the uploaded file MIME type.

Validation notes:

- `chat_id` is required.
- `message` is optional only if `media` is present.
- `media` field name must be exactly `media`.
- Backend denies send if logged-in user is not part of the accepted chat.

Response `data` is the saved message object.

Attachment response fields:

```json
{
  "media_type": "document",
  "media_url": "https://biggulegalis.com/public/upload/chat/local-file.pdf",
  "media_extension": "pdf",
  "media_file_name": "local-file.pdf",
  "media_display_type": "document",
  "media_icon": "pdf",
  "media_label": "PDF file"
}
```

Frontend rendering:

- If `media_display_type == "image"`, show an image preview from `media_url`.
- If `media_display_type == "document"`, show a document bubble with an icon based on `media_icon`, extension/title text, and open `media_url` on tap/click.
- Suggested icon mapping: `pdf`, `word`, `excel`, `powerpoint`, `text`, `archive`, fallback `file`.
- Web can use an anchor/link with `target="_blank"` for documents.
- Android can open `media_url` with the platform URL/file opener after downloading or by launching the URL.

## Socket.IO Integration

Use Socket.IO client with polling transport on this hosting path. The public `/nodejs` proxy currently serves Socket.IO polling correctly, but does not complete WebSocket upgrades.

```dart
final socket = IO.io(
  'https://biggulegalis.com',
  IO.OptionBuilder()
      .setTransports(['polling'])
      .setPath('/nodejs/socket.io')
      .setAuth({'token': accessToken})
      .disableAutoConnect()
      .build(),
);

socket.connect();
```

The token is the same API bearer token. Do not include the text `Bearer ` in the auth token value unless your client requires it; the backend accepts either raw token or `Bearer <token>`.

After connect, join the authenticated user room:

```dart
socket.emitWithAck(
  'join',
  {'user_id': loggedInUserId},
  ack: (response) {
    // { success: true, message: "Joined successfully.", room: "user_1" }
  },
);
```

Important:

- `user_id` must match the token user.
- The socket server rejects joining another user's room.

### Mark Active Chat

The backend sends push notifications only when the receiver is not actively viewing the same chat. The app must tell Socket.IO when a chat screen is open.

When opening a chat screen:

```dart
socket.emitWithAck(
  'chat_opened',
  {'chat_id': chatId},
  ack: (response) {
    // { success: true, message: "Chat marked active.", chat_id: 10 }
  },
);
```

When leaving the chat screen:

```dart
socket.emitWithAck(
  'chat_closed',
  {'chat_id': chatId},
  ack: (response) {
    // { success: true, message: "Chat marked inactive." }
  },
);
```

Behavior:

- If receiver has this `chat_id` active, they receive only the socket `new_message` event.
- If receiver is offline, not connected, or viewing another chat, backend also creates/sends a `NEW_MESSAGE` notification.
- If the app does not emit `chat_opened`, backend treats the user as not in chat and sends notification.

### Listen For New Messages

```dart
socket.on('new_message', (payload) {
  // payload is the saved message object
});
```

Payload:

```json
{
  "id": 100,
  "chat_id": 10,
  "sender_id": 1,
  "receiver_id": 2,
  "message": "Hello",
  "media_type": "text",
  "media_url": null,
  "media_extension": null,
  "media_file_name": null,
  "media_display_type": null,
  "media_icon": null,
  "media_label": null,
  "is_seen": false,
  "created_at": "2026-07-05T00:00:00.000000Z",
  "sender_detail": {},
  "receiver_detail": {}
}
```

### Listen For Chat Request Updates

```dart
socket.on('chat_request_updated', (payload) {
  // payload is the chat request object
});
```

This fires when:

- A user sends a new chat request.
- A rejected request is sent again.
- A receiver accepts a request.
- A receiver rejects a request.

Payload:

```json
{
  "id": 1,
  "requester_id": 1,
  "receiver_id": 2,
  "chat_id": 10,
  "status": 1,
  "requester": {},
  "receiver": {},
  "chat": {}
}
```

## Suggested Frontend Flow

1. Login and store `access_token` and logged-in user `id`.
2. Connect Socket.IO with `auth: { token: accessToken }`.
3. On socket connect, emit `join` with logged-in user id.
4. Load `/chat-member-list` for member discovery.
5. Use `/send-chat-request` from member profile/list.
6. Use `/chat-request-list` for received/sent request screens.
7. On accept/reject, call `/chat-request-action`.
8. Use `/chat-list` for inbox.
9. Use `/chat-messages` when opening a chat.
10. Use `/send-message` to send messages.
11. On `new_message`, update the active thread or increment unread count.
12. On `chat_request_updated`, refresh request lists/member status/chat list as needed.

## Prompt For Frontend Developer Or AI

Use this prompt if another frontend developer or AI needs to implement the Flutter side:

```text
Integrate the Flutter chat module with the Laravel backend at https://biggulegalis.com/api and Socket.IO at https://biggulegalis.com.

Use the existing login access_token for all API requests as Authorization: Bearer <access_token>. All chat APIs return { success, status, message, data }.

Implement these endpoints:
- POST /chat-member-list with { association_id?: string, search?: string }
- POST /send-chat-request with { receiver_id: int }
- POST /chat-request-list with { type: "received" | "sent" }
- POST /chat-request-action with { request_id: int, status: 1 | 2 }
- GET /chat-list
- POST /chat-messages with { chat_id: int }
- POST /send-message with JSON for text/link or multipart form-data for image/document using file field "media"

Use chat request statuses: 0 pending, 1 accepted, 2 rejected. Use message media types: text, image, document, link.

For realtime, connect Socket.IO using polling transport, path /nodejs/socket.io, and auth { token: accessToken }. After connect, emit "join" with { user_id: loggedInUserId }. When a chat screen opens, emit "chat_opened" with { chat_id }; when it closes, emit "chat_closed". Listen to "new_message" for saved message payloads and "chat_request_updated" for chat request send/accept/reject updates. The socket server only allows joining the room for the authenticated token user.

Update UI behavior:
- Member list should show request status and chat_id when available.
- Received requests can accept or reject via /chat-request-action.
- Accepted requests should create/open chat using returned chat_id.
- Chat list should use member, last_message, unread_count.
- Opening messages should call /chat-messages and mark received messages seen.
- Sending messages should optimistically render only if desired, but reconcile with /send-message response and new_message socket payload.
- On chat_request_updated, refresh member/request/chat list state depending on the current screen.
```
