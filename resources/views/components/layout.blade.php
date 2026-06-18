{{--
    Main Layout Component
    =====================
    This is the master layout for the entire app. Every page wraps its
    content in <x-layout> ... </x-layout>.

    Components used:
      <x-head>          — <head> tag: meta, fonts, DaisyUI, Vite, theme script
      <x-banner>        — Site banner image
      <x-nav-bar>       — Navigation bar with links, auth controls, theme toggle
      <x-success-toast> — Green toast popup on successful actions
      <x-error-toast>   — Red toast popup on validation errors
      <x-setting-preview-modal> — Shared modal for ABC notation preview
      <x-footer>        — Site footer with links and theme toggle script

    Usage:
      <x-layout>
          <x-slot:title>Page Title</x-slot:title>
          ...page content here...
      </x-layout>
--}}
<!DOCTYPE html>
<html lang="en" data-theme="dark">

{{-- <head> with meta, fonts, styles, scripts --}}
<x-head :title="$title ?? null" />

<body class="min-h-screen flex flex-col bg-base-200 font">

    {{-- Site banner image --}}
    <x-banner />

    {{-- Navigation bar --}}
    <x-nav-bar />

    {{-- Flash message toasts --}}
    <x-success-toast />
    <x-error-toast />

    {{-- ============================================================== --}}
    {{-- PAGE CONTENT — each view's content is injected here via $slot --}}
    {{-- ============================================================== --}}
    <main class="flex-1 container mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    {{-- Shared modal for ABC notation preview (used by tune lists) --}}
    <x-setting-preview-modal />

    {{-- Site footer with theme toggle script --}}
    <x-footer />

</body>
</html>
