<script>
document.addEventListener("DOMContentLoaded", function () {

    if (!navigator.geolocation) {
        console.log("Geolocation not supported");
        return;
    }

    navigator.geolocation.getCurrentPosition(function(position) {

        let lat = position.coords.latitude;
        let long = position.coords.longitude;

        // Convert lat/long to city & country using free API
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${long}`)
            .then(response => response.json())
            .then(data => {

                let city = data.address.city || data.address.town || data.address.village || '';
                let country = data.address.country || '';

                fetch('/save-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        lat: lat,
                        long: long,
                        city: city,
                        country: country
                    })
                });

            });

    }, function(error) {
        console.log("Location permission denied");
    });

});
</script>
