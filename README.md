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

### Where the data came from

The application runs against **`monthlycup_laravel`**, a copy of the original
`monthlycup` database. Seasons 2021 to 2026 were imported into the normalised
tables once, in August 2026; the per-year tables were dropped afterwards and
the import command is gone with them. Empty signup rows without a playername
(all `valid = 0`) were skipped.

The untouched original database `monthlycup` still exists, so the import can
be redone from the project history if it ever has to be.

### Admin passwords

The legacy `admin` table stored bare MD5 hashes. They are imported as-is and
transparently re-hashed with bcrypt the first time a user logs in
successfully, so every existing admin keeps their password.

## Seasons

Starting a new season no longer needs `cron/new_year.php`: no tables have to be
created. Use **Admin → Settings → Start new season**, which copies the ranking
points of the previous season and leaves the cup dates empty.

## Forum posts (BBCode)

**Admin → Forum posts** generates the BBCode for the four posts boehmi writes
by hand for every cup on pokerth.net, modelled after
https://www.pokerth.net/viewtopic.php?t=1257:

1. **Announcement** — cup date, table admins and admin subs. The cup date is
   filled in from **Admin → Settings** and the theme image from
   `MCUP_THEME_IMAGE` (see below) whenever the field is left empty; type
   something in to override either for just this one post.
2. **1st round seeding** — set the number of tables and the admin of each (one
   name per table, in table order), and **players per table** (default 9 —
   set it to 8 on a month a table has to run with fewer seats). **Shuffle
   again** randomises the *accepted* signups into the tables; a name that is
   also listed as a table admin is left out of the shuffle, since that seat is
   already taken. Anyone left over once every table is full becomes a
   substitute. Table admins, admin subs and players-per-table are saved per
   month, so they survive a tab switch or reload.
3. **Final round seeding** — fully automatic once the 1st round tables have
   been uploaded under **Admin → Upload 1st round**: every table's 1st place
   goes to Gold, every 2nd place to Silver, every 3rd to Bronze, in table
   order. The log-links section is built from the game log pasted in at
   upload time (see `upload_logs` below) — nothing to type here.
4. **Results & awards** — fully automatic from the uploaded gold final table
   and the award images assigned under **Admin → Awards**.

Every tab has a **Copy BBCode** button; paste the result straight into the
forum's reply box.

Table admins, admin subs, players-per-table and any per-post text overrides
are stored per month as JSON in the `settings` table (type
`forum_post_config`) — the same per-month pattern already used for cup dates
and forum links.

`upload_logs` keeps the `pdb` hash and `game_id` parsed out of the game log
link pasted in at **Admin → Upload**, one row per uploaded table rather than
per player, so it isn't repeated on every result row. This is what lets the
final round seeding post quote the 1st round game logs without asking the
admin to paste them a second time.

### Season theme image

The banner on the homepage, in `og:image`, and at the top of the announcement
post all come from one place:

```
MCUP_THEME_IMAGE=images/mcup_2026_theme.jpg
```

For a new season, drop the image into `public/images/` and update this one
`.env` value — no code change needed.

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
php artisan mcup:fetch-avatars   # pull the PokerTH game avatars (scheduled weekly)
```

The scheduler entry lives in `routes/console.php`; run it with a single cron
entry: `* * * * * php artisan schedule:run`.

## Front end size

Element Plus is only imported per component, from its own module paths:

```js
import { ElButton } from 'element-plus/es/components/button/index';
```

Named imports from the package root (`from 'element-plus'`) do **not** tree
shake — the barrel drags in every component, and the bundle stays at ~920 kB no
matter how few components are named. The same applies to the icons: only the
ones in use are imported, not the full set of roughly a thousand.

Because Blade templates are not scanned by any bundler plugin, every component
used in a `.blade.php` file must be listed in `resources/js/app.js`. A missing
entry leaves a raw `<el-…>` element in the DOM. This check catches that:

```bash
node -e "…"   # see the component audit in the project history
```

Result of the trimming:

| chunk        | before   | after   |
|--------------|----------|---------|
| element-plus | 936 kB   | 480 kB  |
| vue          | 179 kB   | 184 kB  |
| app          | 96 kB    | 93 kB   |
| **total JS** | 1 353 kB | 757 kB  |
| gzip         | 436 kB   | 254 kB  |

Vendor code sits in its own chunks, so a deploy that only changes application
code leaves the Element Plus chunk in the browser cache. nginx serves
`/build` with `Cache-Control: public, immutable` (the filenames are
content-hashed) and fonts/images with a 30 day lifetime.

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
