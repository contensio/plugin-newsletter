# Newsletter Subscriptions — Contensio Plugin

Collect email subscribers with an embeddable signup form. Supports double opt-in, unique unsubscribe tokens, and a full admin dashboard with search and CSV export.

---

## Features

- **Signup form** — drop anywhere in your theme with a single `@include`
- **Double opt-in** (default on) — sends a confirmation email; subscriber activates only after clicking the link
- **Unsubscribe tokens** — every subscriber gets a unique token; one-click unsubscribe at `/newsletter/unsubscribe/{token}`
- **Resubscribe handling** — unsubscribed users who sign up again go through opt-in again automatically
- **Admin subscriber list** — search by email or name, filter by status (Active / Pending / Unsubscribed), paginated
- **CSV export** — download all or filtered subscribers; UTF-8 BOM included for Excel compatibility
- **Per-row delete** — remove individual subscribers from the admin
- **Configurable form copy** — title, description, placeholder, button label, and success messages all editable from settings
- **Sender override** — set a custom From name and email for confirmation emails

---

## How it works

1. A visitor enters their email in the signup form and clicks Subscribe.
2. If **double opt-in** is enabled (default), they receive a confirmation email with a link to `/newsletter/confirm/{token}`. Clicking it sets their status to **active**.
3. If double opt-in is disabled, they are added as **active** immediately.
4. If a visitor submits an email that previously unsubscribed, they go through the opt-in flow again.
5. Every email contains an unsubscribe link pointing to `/newsletter/unsubscribe/{token}`.

### Subscriber statuses

| Status | Meaning |
|--------|---------|
| `pending` | Signed up but hasn't confirmed yet (double opt-in) |
| `active` | Confirmed and receiving emails |
| `unsubscribed` | Opted out via the unsubscribe link |

---

## Installation

### Via admin panel

Go to **Plugins** in your Contensio admin, find **Newsletter Subscriptions**, and click **Install**.

### Via Composer

```bash
composer require contensio/plugin-newsletter
```

The plugin is auto-discovered. Go to **Plugins** in the admin and enable it. The migration runs automatically on first enable.

---

## Embedding the signup form

Add the form anywhere in your theme:

```blade
@include('newsletter::partials.subscribe-form')
```

The form reads its copy (title, description, button label) from the plugin settings — no hardcoded strings.

The form posts to `/newsletter/subscribe` and returns to the same page with a `newsletter_success` session flash on success.

---

## Settings

Go to **Tools → Newsletter → Settings** in the admin.

| Setting | Description |
|---------|-------------|
| **Double opt-in** | Send a confirmation email before activating the subscriber |
| **From name / From email** | Sender identity for confirmation emails. Leave blank to use the site's default mail settings |
| **Form title** | Heading shown above the signup form |
| **Form description** | Subtext below the title |
| **Email placeholder** | Placeholder text inside the email input |
| **Button label** | Submit button text |
| **Success message (opt-in)** | Shown after subscribing when double opt-in is on |
| **Success message (no opt-in)** | Shown after subscribing when double opt-in is off |

---

## Routes

| Method | URL | Description |
|--------|-----|-------------|
| `POST` | `/newsletter/subscribe` | Submit the signup form |
| `GET` | `/newsletter/confirm/{token}` | Confirm subscription (double opt-in link) |
| `GET` | `/newsletter/unsubscribe/{token}` | One-click unsubscribe |
| `GET` | `/account/newsletter` | Admin subscriber list |
| `GET` | `/account/newsletter/export` | Download subscribers as CSV |
| `DELETE` | `/account/newsletter/{id}` | Delete a subscriber |
| `GET` | `/account/settings/newsletter` | Plugin settings page |
| `POST` | `/account/settings/newsletter` | Save plugin settings |

---

## Database

Creates one table: `newsletter_subscribers`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `email` | varchar(255) | Unique email address |
| `name` | varchar(150) | Optional display name |
| `token` | varchar(64) | Unique token for confirm / unsubscribe links |
| `status` | enum | `pending`, `active`, `unsubscribed` |
| `confirmed_at` | timestamp | When the subscriber confirmed |
| `unsubscribed_at` | timestamp | When the subscriber opted out |
| `source` | varchar(50) | Where the signup came from (e.g. `widget`) |
| `ip_address` | varchar(45) | IP at time of signup |
| `created_at` | timestamp | Signup time |
| `updated_at` | timestamp | Last update |

---

## Requirements

- PHP 8.2+
- Contensio 2.0+
- Mail configured in `.env` (for double opt-in confirmation emails)

---

## License

AGPL-3.0-or-later — see [LICENSE](LICENSE).
