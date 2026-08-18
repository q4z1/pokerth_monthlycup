# PokerTH Monthly Cup

Laravel 12 + Vue 3 (Element Plus) application for the PokerTH Monthly Cup series.
It replaces the previous hand-written PHP framework, which is kept under
`legacy/` for reference until this port is signed off.

## Stack

| Layer     | Choice                                             |
|-----------|----------------------------------------------------|
| Backend   | Laravel 12, PHP 8.2 (pinned to the php-fpm runtime) |
| Frontend  | Vue 3 + Element Plus, bundled with Vite            |
| Fonts     | Nunito, served locally from `public/fonts`         |
| Database  | MariaDB (`monthlycup_laravel`)                     |

The GUI follows `/var/www/bbc`: the same Element Plus component set, the same
grid navbar, and a light/dark switch stored in a cookie.

The php-fpm runtime is PHP **8.2.21**, so `composer.json` pins
`config.platform.php` to that version. Without the pin Composer resolves
against the CLI (which is newer in the dev container) and the deployed app
dies in `vendor/composer/platform_check.php`.

## Database

The legacy schema had one set of tables per season (`award2026`, `player2026`,
`signup2026`, `upload2026`, `settings2026`, …). Those are normalised into a
single set of tables carrying a `year` column:

| Table          | Replaces                          |
|----------------|-----------------------------------|
| `settings`     | `settings<year>`                  |
| `players`      | `player<year>`                    |
| `awards`       | `award<year>`                     |
| `award_player` | `player<year>.awards` (JSON blob)  |
| `signups`      | `signup<year>`                    |
| `uploads`      | `upload<year>`                    |
| `users`        | `admin`                           |

`configuration` and `controllerinc` are gone; asset loading is Vite's job now.

Award images and player avatars stay in the database as `LONGBLOB` and are
served by `MediaController` with an ETag and a one-day cache header.

### Migrating the legacy data

The application runs against **`monthlycup_laravel`**, a copy of the original
`monthlycup` database. The original is never written to. Inside the copy the
legacy tables are left in place, so the import can be repeated at any time:

```bash
php artisan migrate
php artisan mcup:import-legacy --fresh     # all seasons
php artisan mcup:import-legacy --year=2026 # a single season
```

Signup rows without a playername (leftover empty registrations, all
`valid = 0`) are skipped.

### Admin passwords

The legacy `admin` table stored bare MD5 hashes. They are imported as-is and
transparently re-hashed with bcrypt the first time a user logs in
successfully, so every existing admin keeps their password.

## Seasons

Starting a new season no longer needs `cron/new_year.php`: no tables have to be
created. Use **Admin → Settings → Start new season**, which copies the ranking
points of the previous season and leaves the cup dates empty.

## Data integrity

Two maintenance commands recompute derived data. Both report first and only
write when `--apply` is passed:

```bash
php artisan mcup:fix-award-assignments   # rebuild award holders from the cup results
php artisan mcup:fix-points              # check result points against the season configuration
```

Only the **admin** award may be held by several players. Every other award
belongs to exactly one finishing place; the assign endpoint rejects anything
else and the assign dialog limits the selection accordingly.

Corrections applied to the imported legacy data on 2026-08-18:

- 17 award assignments were rebuilt from the results. The legacy assign dialog
  appended players instead of replacing them, so single awards had ended up on
  two or three players while the award for the next place stayed empty.
- April 2026, bronze final: places 1 and 2 carried each other's points. The
  placement is confirmed by the bronze1st award handed out at the time, so the
  points were corrected, not the places.
- July 2022, first round table 9: one player was listed twice, on places 7 and
  8. The duplicate row was removed; the real eighth player cannot be recovered
  without the original game log, so that place stays empty.

## Commands

```bash
php artisan mcup:import-legacy   # import the legacy per-year tables
php artisan mcup:fetch-avatars   # pull the PokerTH game avatars (scheduled weekly)
```

The scheduler entry lives in `routes/console.php`; run it with a single cron
entry: `* * * * * php artisan schedule:run`.

## Development

```bash
composer install
npm install
npm run dev      # or: npm run build
php artisan test
```

Tests run against in-memory SQLite. `tests/TestCase.php` refuses to start
against any other connection, and `phpunit.xml` sets `APP_CONFIG_CACHE` to a
non-existent path so a production config cache cannot redirect the suite at the
live database.

## Deployment

```bash
npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

nginx serves `/var/www/monthlycup/public` (`/etc/nginx/conf.d/005-monthlycup.conf`).

## Legacy URLs

Bookmarked and forum-linked URLs of the old site keep working:

| Old                                        | New                          |
|--------------------------------------------|------------------------------|
| `/main/results/<action>/<month>?year=`      | `/results/<action>`          |
| `/main/signup`, `/main/signup/show`         | `/registration`, `/signups`  |
| `/main/settings`                            | `/table-settings`            |
| `/admin/award/upload`, `/admin/award/edit`  | `/admin/awards`              |
| `/res/award/?type=&month=&year=`            | `/media/award/{id}`          |
| `/res/avatar/?playername=`                  | `/media/avatar/{id}`         |
| `/res/pic/<template>/<file>`                | `/images/<file>`             |
