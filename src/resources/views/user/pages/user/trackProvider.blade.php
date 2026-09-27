@extends('user.main')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
<style>
    #track-map { height: 460px; border-radius: 6px; }
    .track-stat { font-size: 1.4rem; font-weight: 700; }
    .track-label { font-size: .8rem; text-transform: uppercase; color: #6c757d; }
</style>

<h4 class="mt-2"><i class="fa fa-map-marker-alt mr-1"></i> Track {{ $booking->provider->name }}</h4>
<a href="{{ route('user.request.history', $booking->user_id) }}"><i class="fa fa-arrow-left"></i> Back to request history</a>
<hr>

<div class="row">
    <div class="col-lg-8 mb-3">
        <div id="track-map"></div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <p class="mb-1"><strong>{{ $booking->provider->name }}</strong></p>
                <p class="text-muted mb-3">{{ $booking->provider->profession->name ?? 'N/A' }}
                    @if ($booking->provider->phone)
                    · <a href="tel:{{ $booking->provider->phone }}">{{ $booking->provider->phone }}</a>
                    @endif
                </p>

                {{-- Not an .alert: the panel footer auto-hides alerts after 8 seconds. --}}
                <div id="track-message" class="p-2 mb-3 rounded bg-info">Locating provider…</div>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="track-label">Estimated arrival</div>
                        <div class="track-stat" id="track-eta">--</div>
                    </div>
                    <div class="col-6">
                        <div class="track-label">Distance</div>
                        <div class="track-stat" id="track-distance">--</div>
                    </div>
                </div>
                <p class="small text-muted text-center mt-3 mb-0" id="track-updated"></p>
                <p class="small text-muted text-center mb-0" id="track-eta-source"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var locationUrl = @json(route('user.request.track.location', $booking->id));
    var destination = @json($destination);
    var POLL_MS = 10000;          // how often to ask the server for the provider's position
    var ROUTE_MIN_MS = 30000;     // don't ask the routing server more often than this
    var ARRIVED_KM = 0.05;        // within 50 m counts as arrived
    var FALLBACK_KMH = 25;        // average city speed for the straight-line estimate

    var map = L.map('track-map').setView(destination ? [destination.lat, destination.lng] : [27.7172, 85.3240], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var destinationMarker = destination
        ? L.marker([destination.lat, destination.lng]).addTo(map).bindPopup('Your location')
        : null;
    var providerMarker = null;
    var routeLine = null;
    var lastRoute = { at: 0, lat: null, lng: null };
    var fitted = false;
    var timer = null;

    function el(id) { return document.getElementById(id); }

    function setMessage(text, type) {
        var box = el('track-message');
        box.className = 'p-2 mb-3 rounded bg-' + type;
        box.textContent = text;
    }

    function haversineKm(a, b) {
        var R = 6371, toRad = Math.PI / 180;
        var dLat = (b.lat - a.lat) * toRad, dLng = (b.lng - a.lng) * toRad;
        var h = Math.pow(Math.sin(dLat / 2), 2) + Math.cos(a.lat * toRad) * Math.cos(b.lat * toRad) * Math.pow(Math.sin(dLng / 2), 2);
        return 2 * R * Math.asin(Math.sqrt(h));
    }

    function formatMinutes(minutes) {
        if (minutes < 1) return '< 1 min';
        if (minutes < 60) return Math.round(minutes) + ' min';
        return Math.floor(minutes / 60) + ' h ' + Math.round(minutes % 60) + ' min';
    }

    function showEstimate(km, minutes, source) {
        el('track-distance').textContent = km.toFixed(km < 10 ? 2 : 1) + ' km';
        el('track-eta').textContent = formatMinutes(minutes);
        el('track-eta-source').textContent = source;
    }

    function drawRoute(coords) {
        if (routeLine) map.removeLayer(routeLine);
        routeLine = L.polyline(coords, { color: '#007bff', weight: 5, opacity: 0.7 }).addTo(map);
    }

    // Road route + driving time from the free OSRM demo server; straight-line estimate if it fails.
    function updateRoute(provider) {
        var straightKm = haversineKm(provider, destination);
        var moved = lastRoute.lat === null || haversineKm(provider, lastRoute) > 0.05;
        if (!moved || Date.now() - lastRoute.at < ROUTE_MIN_MS) {
            return;
        }
        lastRoute = { at: Date.now(), lat: provider.lat, lng: provider.lng };

        var url = 'https://router.project-osrm.org/route/v1/driving/'
            + provider.lng + ',' + provider.lat + ';' + destination.lng + ',' + destination.lat
            + '?overview=full&geometries=geojson';
        fetch(url)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.code !== 'Ok' || !data.routes.length) throw new Error('no route');
                var route = data.routes[0];
                drawRoute(route.geometry.coordinates.map(function (c) { return [c[1], c[0]]; }));
                showEstimate(route.distance / 1000, route.duration / 60, 'Driving route via OpenStreetMap');
            })
            .catch(function () {
                drawRoute([[provider.lat, provider.lng], [destination.lat, destination.lng]]);
                showEstimate(straightKm, straightKm / FALLBACK_KMH * 60, 'Approximate (straight line)');
            });
    }

    function poll() {
        fetch(locationUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (res) {
                if (res.redirected || !res.ok) throw new Error('session');
                return res.json();
            })
            .then(function (data) {
                if (data.booking_status !== 'confirmed') {
                    setMessage('This booking is now ' + data.booking_status + '. Live tracking has ended.', 'secondary');
                    clearInterval(timer);
                    return;
                }
                if (!data.provider) {
                    setMessage('The provider is offline, so their location is not available right now.', 'warning');
                    return;
                }

                var provider = data.provider;
                var ago = provider.seconds_ago < 60 ? provider.seconds_ago + ' seconds ago' : Math.round(provider.seconds_ago / 60) + ' minutes ago';
                el('track-updated').textContent = 'Location updated ' + ago;

                if (!providerMarker) {
                    providerMarker = L.circleMarker([provider.lat, provider.lng], { radius: 10, color: '#fff', weight: 3, fillColor: '#28a745', fillOpacity: 1 })
                        .addTo(map).bindPopup(@json($booking->provider->name));
                } else {
                    providerMarker.setLatLng([provider.lat, provider.lng]);
                }

                if (!destination) {
                    setMessage('Showing the provider\'s location. Your location is unknown, so the arrival time can\'t be estimated.', 'info');
                    if (!fitted) { map.setView([provider.lat, provider.lng], 15); fitted = true; }
                    return;
                }

                if (!fitted) {
                    map.fitBounds(L.latLngBounds([[provider.lat, provider.lng], [destination.lat, destination.lng]]), { padding: [40, 40] });
                    fitted = true;
                }

                if (haversineKm(provider, destination) <= ARRIVED_KM) {
                    setMessage('Your provider has arrived.', 'success');
                    showEstimate(0, 0, '');
                    el('track-eta').textContent = 'Arrived';
                    if (routeLine) { map.removeLayer(routeLine); routeLine = null; }
                    return;
                }

                setMessage(provider.stale
                    ? 'The provider\'s location hasn\'t updated for a while. The position shown may be out of date.'
                    : 'Your provider is on the way.', provider.stale ? 'warning' : 'info');
                updateRoute(provider);
            })
            .catch(function () {
                setMessage('Couldn\'t reach the server. Retrying…', 'warning');
            });
    }

    poll();
    timer = setInterval(poll, POLL_MS);
})();
</script>
@endsection
