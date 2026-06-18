{{--
    Tune List Component
    ===================
    Reusable tabbed tune listing used by the tunes index and collections pages.

    Props:
      $tuneTypes — Collection of TuneType models, each eager-loaded with:
                   - tunes (with settings_count and topSetting)
                   Passed from the controller query.
      $title     — Page heading text. Defaults to 'Tunebook'.

    Usage:
      <x-tune-list :tuneTypes="$tuneTypes" title="My Collection" />

    How it works:
      - Tunes are grouped by tune type and displayed in tabs (Reels, Jigs, etc.)
      - Each tab shows a table of tunes with: preview button, title, settings count, favorite toggle
      - The preview button (magnifying glass) opens a modal rendering the top-voted setting's ABC notation
      - The favorite heart toggles via POST to favorites.toggle — uses the user's
        favoriteTunes relationship (loaded once, cached) to check current state
      - JS in modules/tunes/index.js handles tab switching, search filtering, and pagination
--}}
@props(['tuneTypes', 'collection' => null])

<div class="w-full">

    {{-- Search input filters tune rows by title, per-page select controls JS pagination --}}
    <div class="flex items-center gap-4 mt-6">
        <input type="text" id="tune-filter" placeholder="Search by title..."
            class="input input-bordered w-64" />
        <select id="per-page" class="select select-bordered select-sm">
            <option value="10">10 per page</option>
            <option value="25">25 per page</option>
            <option value="50">50 per page</option>
            <option value="100">100 per page</option>
        </select>
        @auth
            <a href="{{ route('tunes.create') }}" class="btn btn-primary btn-sm">Add Tune</a>
        @endauth
    </div>

    {{-- Tune Type Tabs — one tab per tune type, first tab active by default --}}
    <div class="mt-8">
        @if($tuneTypes->isEmpty())
            <div class="hero py-12">
                <div class="hero-content text-center">
                    <p class="text-base-content/60">No tunes in the database yet.</p>
                </div>
            </div>
        @else
            {{-- Tab headers — clicking switches which panel is visible (handled by JS) --}}
            <div class="tabs tabs-bordered" id="tune-tabs">
                @foreach($tuneTypes as $tuneType)
                    <a class="tab {{ $loop->first ? 'tab-active' : '' }}"
                       data-tab="tab-{{ $tuneType->id }}">
                        {{-- str()->plural() converts "Reel" to "Reels", "Jig" to "Jigs", etc. --}}
                        {{ str($tuneType->name)->plural() }}
                    </a>
                @endforeach
            </div>

            {{-- Tab panels — one table per tune type, only the first setting for each tune can be previewed --}}
            @foreach($tuneTypes as $tuneType)
                <div class="tune-panel {{ $loop->first ? '' : 'hidden' }}"
                     id="tab-{{ $tuneType->id }}">
                    @if($tuneType->tunes->isEmpty())
                        <p class="py-8 text-center text-base-content/60">No {{ str($tuneType->name)->lower()->plural() }} yet.</p>
                    @else
                        <div class="overflow-x-auto mt-4">
                            <table class="table table-sm w-full">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Settings</th>
                                        <th>Favorite</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tuneType->tunes as $tune)
                                        <tr class="tune-row {{ $tune->settings_count === 0 ? 'no-setting' : '' }}" data-settings="{{ $tune->settings_count }}" data-tune-id="{{ $tune->id }}">
                                            <td class="tune_title">
                                                {{-- Preview button: if the tune has a top-voted setting,
                                                     clicking the magnifying glass opens the modal and renders
                                                     the ABC notation via abcjs. data-abc holds the full ABC string. --}}
                                                @if($tune->topSetting)
                                                    <button class="setting-preview-btn hover:text-primary"
                                                            data-abc="{{ $tune->topSetting->toAbc() }}"
                                                            data-setting-name="{{ $tune->topSetting->name }}">
                                                        <i class="fa-solid fa-magnifying-glass-music"></i>
                                                    </button>
                                                @else
                                                    <i class="fa-solid fa-magnifying-glass-music opacity-30"></i>
                                                @endif
                                                <a href="{{ route('tunes.show', $tune) }}" class="hover:text-primary">
                                                    {{ $tune->name }}
                                                </a>
                                            </td>
                                            <td>{{ $tune->settings_count }}</td>
                                            <td>
                                                {{-- Favorite toggle: clicks send a fetch POST in the background
                                                     and swap the icon instantly without reloading the page.
                                                     The CSRF token is read from the meta tag. --}}
                                                @auth
                                                    <button class="cursor-pointer favorite-toggle"
                                                        data-tune-id="{{ $tune->id }}"
                                                        data-url="{{ route('favorites.toggle', $tune) }}">
                                                        @if(auth()->user()->favoriteTunes->contains($tune->id))
                                                            <i class="fa-solid fa-heart text-error"></i>
                                                        @else
                                                            <i class="fa-regular fa-heart"></i>
                                                        @endif
                                                    </button>
                                                @endauth
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- JS pagination controls are injected here by modules/tunes/index.js --}}
                        <div class="pagination-controls flex gap-2 mt-4"></div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>

