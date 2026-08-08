# Chat Media Handling

This guide explains how web and Android clients should render chat messages that contain uploaded media.

## API Fields

Every chat message can include these media fields:

```json
{
  "media_type": "image",
  "media_url": "https://biggulegalis.com/public/upload/chat/local-file.jpg",
  "media_extension": "jpg",
  "media_file_name": "local-file.jpg",
  "media_display_type": "image",
  "media_icon": null,
  "media_label": "JPG file"
}
```

For text-only messages, these fields are usually `null`:

```json
{
  "media_url": null,
  "media_file_name": null,
  "media_display_type": null,
  "media_icon": null,
  "media_label": null
}
```

## Send Media

Use `multipart/form-data` for image and document messages.

```text
POST /api/send-message
Authorization: Bearer <access_token>
Accept: application/json
Content-Type: multipart/form-data

chat_id: 10
message: optional caption
media_type: image OR document
media: <file>
```

The backend also detects file type from MIME:

- Image MIME, such as `image/jpeg` or `image/png`, becomes `media_type: image`.
- PDF, DOC, DOCX, XLS, ZIP, TXT, and other non-image files become `media_type: document`.

## Render Rules

### Text

If `media_url` is empty, render the message text normally.

### Link

If `media_type == "link"`, render the message as clickable link text.

### Image

If `media_display_type == "image"`:

- Show an image preview from `media_url`.
- On click/tap, open a full-screen preview or browser image viewer.
- If loading fails, show a small broken-image fallback and keep the URL tappable.

### Document

If `media_display_type == "document"`:

- Do not try to render the file inline as an image.
- Show a document bubble similar to WhatsApp.
- Display an icon based on `media_icon`.
- Display `media_label` or `media_file_name`.
- On click/tap, open `media_url`.

Example document payload:

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

## Icon Mapping

Use this mapping for document bubbles:

| `media_icon` | Suggested icon |
| --- | --- |
| `pdf` | PDF file icon |
| `word` | DOC/DOCX icon |
| `excel` | XLS/XLSX/CSV icon |
| `powerpoint` | PPT/PPTX icon |
| `text` | TXT/RTF icon |
| `archive` | ZIP/RAR/7Z icon |
| `file` | Generic file icon |

## Web Behavior

For documents:

```html
<a href="MEDIA_URL" target="_blank" rel="noopener">
  <div class="document-message">
    <span class="file-icon">pdf</span>
    <span>PDF file</span>
  </div>
</a>
```

For images:

```html
<img src="MEDIA_URL" alt="Chat image" />
```

If the frontend runs on a different origin during development, uploaded files include CORS headers from:

```text
public/upload/.htaccess
```

## Android Behavior

For images:

- Load `media_url` with the app image loader.
- Cache the image if possible.
- Open full-screen preview on tap.

For documents:

- Show a file bubble with icon and label.
- On tap, open `media_url` with the platform URL/file opener.
- If your app downloads first, save using `media_file_name` or a sanitized filename.
- Use `media_extension` to pick MIME type if the opener needs it.

Common MIME examples:

| Extension | MIME |
| --- | --- |
| `pdf` | `application/pdf` |
| `doc` | `application/msword` |
| `docx` | `application/vnd.openxmlformats-officedocument.wordprocessingml.document` |
| `xls` | `application/vnd.ms-excel` |
| `xlsx` | `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` |
| `txt` | `text/plain` |
| `zip` | `application/zip` |

## Failure Handling

If `media_url` fails to load:

- Do not show the raw error inside the chat bubble.
- Show a compact failed media state.
- Keep a retry/open action.
- Log the failed URL for debugging.

Common backend checks:

- File exists in `public/upload/chat`.
- File permission is `0644`.
- Folder permission is `0755`.
- URL uses the full extension, for example `.jpg`, not `.jp`.

