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

- PHP 8.5+
- MySQL 8.0+ or MariaDB 10.4+
- [Composer](https://getcomposer.org/)
- [Node.js / npm](https://nodejs.org/)
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

### 3. Configure environment

Copy the example environment file and update your database credentials:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database settings:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tuneopedia
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Build frontend assets

```bash
npm run build
```

### 6. Configure web server

Point your web server's document root to the `public/` directory. For XAMPP, place the project in `C:\xampp\htdocs\tuneopedia` and create a virtual host pointing to `tuneopedia/public`.

For local development you can also use:

```bash
php artisan serve
```

## Project Structure

```
tuneopedia/
  app/
    Http/
      Controllers/    Route handlers (Tune, Setting, Collection, Favorite, etc.)
    Models/            Eloquent models (Tune, Setting, Collection, User, etc.)
    Policies/          Authorization policies
    Providers/         Service providers
  bootstrap/           Application bootstrapping and middleware config
  config/              Framework and app configuration
  database/
    factories/         Model factories for testing
    migrations/        Database schema migrations
    seeders/           Database seeders
  public/              Web root -- entry point, compiled assets, images
    build/             Vite-compiled CSS and JS
  resources/
    css/               Tailwind stylesheets
    js/
      modules/         Application JS organized by feature
        favorites/     Favorite toggle and removal
        search/        Live search filtering
        tunes/         Tune display, ABC rendering, playback
        settings/      Setting editing, voting, drag-to-pitch
        collections/   Collection management
    views/             Blade templates
      tunes/           Tune index, show, create views
      settings/        Setting edit and add forms
      collections/     Collection views
      components/      Reusable Blade components
      layouts/         App layout and navigation
  routes/
    web.php            All web routes
    console.php        Artisan console commands
  tests/               Pest feature and unit tests
  abc_files/           ABC notation source files
  legacy/              Original vanilla PHP application (archived)
```

## Testing

Run the test suite with Pest:

```bash
php artisan test
```

Run a specific test:

```bash
php artisan test --filter=testName
```

## License

All rights reserved.
