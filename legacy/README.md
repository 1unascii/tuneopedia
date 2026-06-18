# Tuneopedia

A digital repository for traditional music, preserving tunes that have been passed down through history. Browse, play back, and contribute ABC notation transcriptions of jigs, reels, hornpipes, and more.

## Features

- **Tunebook** -- Browse tunes organized by type (Reel, Jig, Hornpipe, Strathspey, etc.) with search, key filtering, and pagination
- **ABC Notation Rendering** -- Sheet music rendered in the browser via [abcjs](https://www.abcjs.net/) with tablature support for fiddle, guitar, banjo, mandolin, and custom tunings
- **MIDI Playback** -- Play back tunes with per-setting volume control, tempo adjustment, looping, cursor tracking, and note highlighting
- **Settings & Voting** -- Each tune can have multiple settings (transcriptions). Users vote on settings to surface the best version
- **Draggable Notes** -- Drag notes up/down on the staff in the edit view to change pitch, with live audio feedback
- **Keystroke Playback** -- Hear notes as you type ABC notation in the editor, with instrument-aware playback
- **Collections** -- Import ABC files to create collections, or curate sets from your favorites
- **Discussions** -- Community discussion board with threads and replies
- **User Accounts** -- Register, log in, manage favorites, and submit tunes and settings
- **Light/Dark Theme** -- Toggle between light and dark modes

## Requirements

- PHP 8.0+
- MySQL 8.0+ or MariaDB 10.4+
- [Composer](https://getcomposer.org/)
- [Node.js / npm](https://nodejs.org/) (for abcjs)
- A web server (Apache with mod_rewrite, or XAMPP)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/1unascii/tuneopedia.git
cd tuneopedia
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Create the database

Create a MySQL database (e.g. `tuneopedia`), then import the schema:

**phpMyAdmin:** Run the SQL file `config/tuneopedia.sql`

**Command line (Windows/XAMPP):**
```bash
mysql -u root -p tuneopedia < config/tuneopedia.sql
```

**Command line (Linux):**
```bash
mysql -u root -p tuneopedia < config/tuneopedia.sql
```

### 4. Configure environment

Create a `.env` file in the project root:

```
DB_HOST=127.0.0.1
DB_NAME=tuneopedia
DB_USER=your_username
DB_PASS=your_password
```

### 5. Configure web server

Point your web server's document root to the project directory. The `.htaccess` files handle routing -- all requests are directed through `public/index.php` as the front controller.

For XAMPP, place the project in `C:\xampp\htdocs\tuneopedia` and access it at `http://localhost/tuneopedia`.

## Project Structure

```
tuneopedia/
  abc_files/        ABC notation source files
  config/           Database config and schema (tuneopedia.sql)
  controllers/      MVC controllers (Tune, Setting, Collection, Auth, Discussion)
  helpers/          Utility functions (tune type detection, ABC formatting)
  models/           Data models with SQL queries (models/sql/)
  public/           Web root -- front controller, CSS, JS, images
    css/            Stylesheets
    js/
      lib/          Third-party JS (abcjs, jQuery plugins)
      modules/      Application JS organized by feature
        tunes/      Tune listing and display
        settings/   Setting playback, editing, voting, notation
        collections/
        discussions/
        favorites/
        users/
  tests/            Model tests, API tests, integration tests
  views/            PHP templates
    tunes/          Tune index, show, create views
    settings/       Setting edit and add forms
    collections/    Collection index and show views
    auth/           Login and registration forms
    partials/       Header, footer, nav, shared components
```

## Testing

Run all model tests:

```bash
php tests/model_tests/run_all.php
```

## License

All rights reserved.
