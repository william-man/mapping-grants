import L from 'leaflet'
import 'leaflet/dist/leaflet.css';

const map = L.map('map').setView([54.251186, -4.463196], 7);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 40,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

async function fetchGrants()
{
    try {
        const response = await fetch('api/v1/grants/mappable')
        if (!response.ok) {
            throw new Error('Error fetching grants');
        }
        return await response.json();
    } catch (error) {
        console.error(`Error: ${error.message}`);
    }
}

function popupInformation(id, title, description, currency, amountAwarded, locationName)
{
    return`
    <div>
        <h3>${escapeHtml(id)}</h3>
        <p>${escapeHtml(title)}</p>
        <p>${escapeHtml(description)}</p>
        <p>${escapeHtml(currency)} ${escapeHtml(String(amountAwarded))}</p>
        <p>${escapeHtml(locationName)}</p>
    </div>
    `
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

const grants = await fetchGrants();

grants.forEach(grant => {
    
    const marker = L.marker([grant.latitude, grant.longitude]);
    const { grantsID, title, description, currency, amountAwarded, name } = grant
    marker.bindPopup(popupInformation(grantsID, title, description, currency, amountAwarded, name));
    marker.addTo(map);
});
