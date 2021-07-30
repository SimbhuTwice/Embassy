<!DOCTYPE html>
<html lang="en">
<head></head>
<body>
    <form>
        @csrf
        <button type="submit" class="btn btn-success" onclick="getCurrentLocation()">Save</button>
    </form>

    <script>
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
            console.log("Geolocation is not supported by this browser.") ;
        }
    }

    function showPosition(position) {
        console.log("Latitude: " + position.coords.latitude + "Longitude: " + position.coords.longitude);
    }
    </script>
</body>
</html>