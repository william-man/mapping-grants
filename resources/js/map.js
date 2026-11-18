import L from 'leaflet'
import 'leaflet/dist/leaflet.css';

const corner1 = L.latLng(60.9, -11.23)
const corner2 = L.latLng(49.1, 2.0)
const map = L.map('map', {
    preferCanvas: true,
    maxBoundsViscosity: 1.0,
    maxBounds: L.latLngBounds(corner1, corner2),
    minZoom: 6,
    maxZoom: 15,
}).setView([54.9, -4.463196], 6);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 15,
	attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

const tileMarkers = new Map();

function tileKey(coords) {
    return `${coords.z}/${coords.x}/${coords.y}`;
}

const GrantTiles = L.GridLayer.extend({
    createTile(coords) {
        const tile = document.createElement('div');

        const group = L.layerGroup().addTo(map);
        tileMarkers.set(tileKey(coords), group);

        tile.style.outline = '1px solid red';
        tile.style.fontSize = '12px';
        tile.textContent = `${coords.z} / ${coords.x} / ${coords.y}`;
        fetchTile(coords.z, coords.x, coords.y).then(recipients => {
            recipients.forEach(recipient => {
                L.circleMarker([recipient.latitude, recipient.longitude], { radius: 5 })
                    .bindPopup(popupInformation(recipient))
                    .addTo(group);
            });
        });
        return tile;
    },
});
const grantTiles = new GrantTiles({ minZoom: 14 });

grantTiles.on('tileunload', event => {
    const key = tileKey(event.coords);
    const group = tileMarkers.get(key);
    if (group) {
        map.removeLayer(group);
        tileMarkers.delete(key);
    }
});
grantTiles.addTo(map);

async function fetchTile(coordZ, coordX, coordY)
{
    const url = `/api/v1/grants/tiles/${coordZ}/${coordX}/${coordY}`
    try{
        const response = await fetch(url);
        if(!response.ok) {
            throw new Error('Error fetching grants in a tile');
        }
        return await response.json();
    } catch (error) {
        console.log(`Error: ${error.message}`)
        return [];
    }
}

function popupInformation(recipient)
{
    if (recipient.type === 'individuals') {
        return `
        <div>
            <p>${escapeHtml(String(recipient.grants))} grants to
               ${escapeHtml(String(recipient.recipients))} individuals</p>
        </div>
        `;
    }

    return `
    <div>
        <h3>${escapeHtml(recipient.name)}</h3>
        <p>${escapeHtml(String(recipient.grants))} grants</p>
    </div>
    `;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

const SUMMARY_MAX_ZOOM = 13;   // the summary works below zoom 14; markers take over from 14

// Which tile is at this position, at the current zoom?
function tileAt(latlng) {
    const z = map.getZoom();
    const pixel = map.project(latlng, z);   // position in world pixels at this zoom
    return {
        z,
        x: Math.floor(pixel.x / 256),        // 256 px per tile
        y: Math.floor(pixel.y / 256),
    };
}

// The tile's edges, as a box Leaflet can draw
function tileLatLngBounds(tile) {
    const northWest = map.unproject([tile.x * 256, tile.y * 256], tile.z);
    const southEast = map.unproject([(tile.x + 1) * 256, (tile.y + 1) * 256], tile.z);
    return L.latLngBounds(northWest, southEast);
}

// Two rectangles: a light one that follows the mouse, a stronger one for the clicked tile
const hoverBox = L.rectangle([[0, 0], [0, 0]], { weight: 1, color: '#555', fillOpacity: 0.08, interactive: false });
const selectedBox = L.rectangle([[0, 0], [0, 0]], { weight: 2, color: '#1d6fd1', fillOpacity: 0.15, interactive: false });

map.on('mousemove', event => {
    if (map.getZoom() > SUMMARY_MAX_ZOOM) {
        hoverBox.remove();
        return;
    }
    hoverBox.setBounds(tileLatLngBounds(tileAt(event.latlng))).addTo(map);
});

map.on('mouseout', () => hoverBox.remove());

const panel = document.getElementById('tile-summary');
let latestRequest = null;

async function fetchSummary(coordZ, coordX, coordY)
{
    const url = `/api/v1/grants/tiles/${coordZ}/${coordX}/${coordY}/summary`;
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Error fetching summary ${coordZ}/${coordX}/${coordY}: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error(error.message);
        return [];
    }
}

function renderSummary(rows)
{
    const organisations = rows.find(row => row.type === 'organisations');
    const individuals = rows.find(row => row.type === 'individuals');

    if (!organisations || !individuals) {
        panel.innerHTML = '<p>Could not load this area.</p>';
        return;
    }

    const totalGrants = organisations.grants + individuals.grants;

    panel.innerHTML = `
        <h3>Selected area</h3>
        <p><strong>${totalGrants.toLocaleString('en-GB')}</strong> grants</p>
        <p>${organisations.grants.toLocaleString('en-GB')} to
           ${organisations.recipients.toLocaleString('en-GB')} organisations</p>
        <p>${individuals.grants.toLocaleString('en-GB')} to
           ${individuals.recipients.toLocaleString('en-GB')} individuals</p>
    `;
}

map.on('click', async event => {
    if (map.getZoom() > SUMMARY_MAX_ZOOM) {
        return;   // at zoom 14+, clicks are for the recipient markers
    }

    const tile = tileAt(event.latlng);
    selectedBox.setBounds(tileLatLngBounds(tile)).addTo(map);
    panel.innerHTML = '<p>Loading…</p>';

    const requestKey = tileKey(tile);
    latestRequest = requestKey;

    const rows = await fetchSummary(tile.z, tile.x, tile.y);

    if (latestRequest !== requestKey) {
        return;   // a newer click has happened since; ignore this older answer
    }
    renderSummary(rows);
});

// When zooming in to the marker levels, clear the selection
map.on('zoomend', () => {
    if (map.getZoom() > SUMMARY_MAX_ZOOM) {
        selectedBox.remove();
        hoverBox.remove();
    }
});