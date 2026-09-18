<!DOCTYPE html>
<html>
    <head>
        @vite(['resources/js/search.js', 'resources/js/map.js', 'resources/css/map.css'])
    </head>
    <body>
        <header>
            <h1>360Giving Grants Map</h1>
        </header>
        <main>
            <form id="postcode-search">
                <input type="text" id="postcode-input" placeholder="Enter a postcode">
                <button type="submit">Search</button>
            </form>
            <div id="map"></div>
        </main>
        <footer></footer>
    </body>
</html>