@extends('web.layouts.admin')

@section('title', $page_title)

@section('content')
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa {{$page_icon}}"></i> {{$page_title}}</h1>
            <p>Use this page to verify the Node socket server and the protected chat APIs together.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="tile">
                <h3 class="tile-title">Test Plan</h3>
                <ol class="mb-0">
                    <li>Start the Node server with the same <code>SOCKET_SERVER_SECRET</code> configured in Laravel.</li>
                    <li>Click <strong>Check Socket Health</strong>; expect a JSON success response.</li>
                    <li>Enter the listener user ID and click <strong>Connect & Join</strong>; expect a joined room such as <code>user_5</code>.</li>
                    <li>Paste a Passport bearer token for the API user who will send or receive chat messages.</li>
                    <li>Run the chat APIs in order: member list, send request, accept request, chat list, messages, then send message.</li>
                    <li>When <strong>Send Message</strong> succeeds, the joined listener should receive a <code>new_message</code> socket event.</li>
                </ol>
            </div>

            <div class="tile">
                <h3 class="tile-title">Sample User IDs</h3>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sampleUsers as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No user records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="tile">
                <h3 class="tile-title">Socket Connection</h3>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="socketUrl">Socket HTTP URL</label>
                        <input class="form-control" id="socketUrl" value="{{ $socketUrl }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="listenerUserId">Listener User ID</label>
                        <input class="form-control" id="listenerUserId" type="number" min="1" placeholder="Receiver ID">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="socketIoOrigin">Socket.IO Origin</label>
                        <input class="form-control" id="socketIoOrigin" value="{{ $socketIoOrigin }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="socketIoPath">Socket.IO Path</label>
                        <input class="form-control" id="socketIoPath" value="{{ $socketIoPath }}">
                    </div>
                </div>
                <div class="btn-group mb-3" role="group">
                    <button class="btn btn-primary" type="button" id="healthBtn"><i class="fa fa-heartbeat"></i> Check Socket Health</button>
                    <button class="btn btn-info" type="button" id="connectBtn"><i class="fa fa-plug"></i> Connect & Join</button>
                    <button class="btn btn-secondary" type="button" id="disconnectBtn"><i class="fa fa-chain-broken"></i> Disconnect</button>
                </div>
                <div class="alert alert-light border mb-0" id="socketStatus">Socket status: idle</div>
            </div>

            <div class="tile">
                <h3 class="tile-title">Chat API Runner</h3>
                <div class="form-group">
                    <label for="apiBaseUrl">API Base URL</label>
                    <input class="form-control" id="apiBaseUrl" value="{{ $apiBaseUrl }}">
                </div>
                <div class="form-group">
                    <label for="apiToken">Bearer Token</label>
                    <input class="form-control" id="apiToken" type="password" placeholder="Paste user access_token from login/signup API">
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="associationId">Association ID</label>
                        <input class="form-control" id="associationId" type="number" min="1" placeholder="Optional">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="receiverId">Receiver ID</label>
                        <input class="form-control" id="receiverId" type="number" min="1">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="requestId">Request ID</label>
                        <input class="form-control" id="requestId" type="number" min="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="chatId">Chat ID</label>
                        <input class="form-control" id="chatId" type="number" min="1">
                    </div>
                    <div class="form-group col-md-8">
                        <label for="messageText">Message</label>
                        <input class="form-control" id="messageText" value="Admin socket test message">
                    </div>
                </div>

                <div class="btn-toolbar mb-3" role="toolbar">
                    <div class="btn-group mr-2 mb-2" role="group">
                        <button class="btn btn-outline-primary" type="button" data-api="member-list">Member List</button>
                        <button class="btn btn-outline-primary" type="button" data-api="send-request">Send Request</button>
                        <button class="btn btn-outline-primary" type="button" data-api="accept-request">Accept Request</button>
                    </div>
                    <div class="btn-group mr-2 mb-2" role="group">
                        <button class="btn btn-outline-success" type="button" data-api="chat-list">Chat List</button>
                        <button class="btn btn-outline-success" type="button" data-api="messages">Messages</button>
                        <button class="btn btn-success" type="button" data-api="send-message">Send Message</button>
                    </div>
                </div>

                <p class="text-muted mb-0">Tip: accept request must be run with the receiver user's token, because the API checks the logged-in receiver.</p>
            </div>
        </div>
    </div>

    <div class="tile">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="tile-title mb-0">Live Log</h3>
            <button class="btn btn-sm btn-outline-secondary" type="button" id="clearLogBtn"><i class="fa fa-trash"></i> Clear</button>
        </div>
        <pre id="testLog" class="p-3 mb-0" style="min-height: 260px; max-height: 520px; overflow: auto; color: #f8f9fa; background: #1f2933; border-radius: 4px;"></pre>
    </div>
</main>
@stop

@section('custom-js')
<script src="{{ rtrim($socketIoOrigin, '/') }}{{ $socketIoPath }}/socket.io.js"></script>
<script type="text/javascript">
    let testSocket = null;

    const logBox = document.getElementById('testLog');
    const socketStatus = document.getElementById('socketStatus');
    const fields = {
        socketUrl: document.getElementById('socketUrl'),
        socketIoOrigin: document.getElementById('socketIoOrigin'),
        socketIoPath: document.getElementById('socketIoPath'),
        listenerUserId: document.getElementById('listenerUserId'),
        apiBaseUrl: document.getElementById('apiBaseUrl'),
        apiToken: document.getElementById('apiToken'),
        associationId: document.getElementById('associationId'),
        receiverId: document.getElementById('receiverId'),
        requestId: document.getElementById('requestId'),
        chatId: document.getElementById('chatId'),
        messageText: document.getElementById('messageText')
    };

    function addLog(title, data) {
        const time = new Date().toLocaleTimeString();
        logBox.textContent += '[' + time + '] ' + title + '\n' + JSON.stringify(data, null, 2) + '\n\n';
        logBox.scrollTop = logBox.scrollHeight;
    }

    function value(name) {
        return fields[name].value.trim();
    }

    function apiHeaders() {
        const token = value('apiToken');
        const headers = {
            Accept: 'application/json'
        };

        if (token) {
            headers.Authorization = 'Bearer ' + token;
        }

        return headers;
    }

    function updateStatus(text, type) {
        socketStatus.className = 'alert mb-0 alert-' + (type || 'light');
        socketStatus.textContent = 'Socket status: ' + text;
    }

    async function postJson(path, body) {
        const response = await fetch(value('apiBaseUrl').replace(/\/$/, '') + '/' + path, {
            method: 'POST',
            headers: Object.assign({'Content-Type': 'application/json'}, apiHeaders()),
            body: JSON.stringify(body)
        });

        return response.json();
    }

    async function getJson(path) {
        const response = await fetch(value('apiBaseUrl').replace(/\/$/, '') + '/' + path, {
            headers: apiHeaders()
        });

        return response.json();
    }

    document.getElementById('healthBtn').addEventListener('click', async function () {
        try {
            const response = await fetch(value('socketUrl').replace(/\/$/, '') + '/health');
            addLog('Socket health', await response.json());
        } catch (error) {
            addLog('Socket health failed', {message: error.message});
        }
    });

    document.getElementById('connectBtn').addEventListener('click', function () {
        if (!value('listenerUserId')) {
            addLog('Join skipped', {message: 'Listener user ID is required.'});
            return;
        }

        if (typeof io !== 'function') {
            addLog('Socket client unavailable', {
                message: 'Socket.IO client script did not load. Check the Socket.IO origin and path.'
            });
            updateStatus('client script unavailable', 'danger');
            return;
        }

        if (testSocket) {
            testSocket.disconnect();
        }

        testSocket = io(value('socketIoOrigin'), {
            path: value('socketIoPath'),
            transports: ['polling'],
            auth: {
                token: value('apiToken')
            }
        });
        updateStatus('connecting', 'warning');

        testSocket.on('connect', function () {
            updateStatus('connected as ' + testSocket.id, 'success');
            addLog('Socket connected', {socket_id: testSocket.id});
            testSocket.emit('join', {user_id: Number(value('listenerUserId'))}, function (response) {
                addLog('Join response', response);
            });
        });

        testSocket.on('connect_error', function (error) {
            updateStatus('connection error', 'danger');
            addLog('Socket connection error', {message: error.message});
        });

        testSocket.on('disconnect', function (reason) {
            updateStatus('disconnected: ' + reason, 'secondary');
            addLog('Socket disconnected', {reason: reason});
        });

        testSocket.on('new_message', function (message) {
            addLog('new_message event', message);
        });

        testSocket.on('chat_request_updated', function (chatRequest) {
            addLog('chat_request_updated event', chatRequest);
        });
    });

    document.getElementById('disconnectBtn').addEventListener('click', function () {
        if (testSocket) {
            testSocket.disconnect();
        }
    });

    document.getElementById('clearLogBtn').addEventListener('click', function () {
        logBox.textContent = '';
    });

    document.querySelectorAll('[data-api]').forEach(function (button) {
        button.addEventListener('click', async function () {
            const action = button.getAttribute('data-api');

            try {
                const result = await runApiAction(action);
                addLog('API: ' + action, result);
            } catch (error) {
                addLog('API failed: ' + action, {message: error.message});
            }
        });
    });

    async function runApiAction(action) {
        if (action === 'member-list') {
            const body = {};
            if (value('associationId')) {
                body.association_id = Number(value('associationId'));
            }
            return postJson('chat-member-list', body);
        }

        if (action === 'send-request') {
            return postJson('send-chat-request', {
                receiver_id: Number(value('receiverId'))
            });
        }

        if (action === 'accept-request') {
            return postJson('chat-request-action', {
                request_id: Number(value('requestId')),
                status: 1
            });
        }

        if (action === 'chat-list') {
            return getJson('chat-list');
        }

        if (action === 'messages') {
            return postJson('chat-messages', {
                chat_id: Number(value('chatId'))
            });
        }

        if (action === 'send-message') {
            return postJson('send-message', {
                chat_id: Number(value('chatId')),
                message: value('messageText'),
                media_type: 'text'
            });
        }

        throw new Error('Unknown action: ' + action);
    }
</script>
@stop
