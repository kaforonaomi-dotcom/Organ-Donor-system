## Development Demo Seed

The active backend includes an idempotent synthetic-data seed script at `database/seed.php`. It uses the existing `.env` database configuration, PDO prepared statements, foreign-key order, and `password_hash()` for demo accounts. It does not create, drop, or recreate tables.

Run it from the project root:

```powershell
cd C:\xampp\htdocs\Organ-Donor-system
C:\xampp\php\php.exe database\seed.php
```

It reports each inserted or skipped user, profile, match, and notification. Running it again reuses the same demo emails and skips existing records.

Demo accounts:

| Email | Role |
| --- | --- |
| `demo.admin@example.test` | admin |
| `demo.donor.one@example.test` | donor |
| `demo.donor.two@example.test` | donor |
| `demo.donor.three@example.test` | donor |
| `demo.recipient.one@example.test` | recipient |
| `demo.recipient.two@example.test` | recipient |
| `demo.recipient.three@example.test` | recipient |

The script creates compatible donor and recipient profiles, approved, rejected, and pending matches, plus notifications for recipients and the administrator. Demo passwords are synthetic development-only values and are hashed before storage.

To verify the seed in phpMyAdmin, select the `organ_donor_system` database and inspect `users`, `donors`, `recipients`, `matches`, and `notifications`. Confirm the demo emails, profile relationships, match statuses, and notification user IDs. The API equivalents are:

```text
GET http://localhost/Organ-Donor-system/donors
GET http://localhost/Organ-Donor-system/recipients
GET http://localhost/Organ-Donor-system/matches
GET http://localhost/Organ-Donor-system/notifications?user_id=<demo-recipient-user-id>
```
