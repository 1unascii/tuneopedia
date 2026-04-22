<!--
    404 Error View

    This view is used in two contexts:
    1. AJAX (page/* route) — returned as an HTML fragment into #content via $.load()
    2. Full page (server route) — rendered inside the full layout (header + nav + footer)

    In both cases, the "Return to Home" link triggers the nav's home link click
    handler, which loads the home page content via AJAX with the fade transition.
-->
<div id="error-404">
    <h2>Page Not Found</h2>
    <p>The page you're looking for doesn't exist.</p>
    <p><a href="#" id="error-home-link">Return to Home</a></p>
</div>

<script>
// Use the nav's existing home link handler so the transition is consistent
$(document).on('click', '#error-home-link', function(e) {
    e.preventDefault();
    $('#home_link').click();
});
</script>

<style>
#error-404 {
    text-align: center;
    padding: 60px 20px;
}
#error-404 h2 {
    font-size: 2em;
    margin-bottom: 10px;
}
#error-404 p {
    color: #999;
    margin-bottom: 10px;
}
#error-home-link {
    color: #59b4d4;
    text-decoration: none;
}
#error-home-link:hover {
    text-decoration: underline;
}
</style>
