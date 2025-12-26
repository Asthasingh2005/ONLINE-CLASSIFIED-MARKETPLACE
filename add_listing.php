<?php
require 'config.php';
if(!is_logged_in()){ header("Location: login.php"); exit; }

$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $title = trim($_POST['title']);
  $description = trim($_POST['description']);
  $price = floatval($_POST['price']);
  $category = intval($_POST['category'] ?? 0);

  $phone = trim($_POST['phone']);
  $location = trim($_POST['location']);

  // LAT / LONG
  $latitude = $_POST['latitude'] ?? null;
  $longitude = $_POST['longitude'] ?? null;

  if(!$title) $errors[] = "Title required.";
  if(!$phone) $errors[] = "Phone number required.";
  if(!$location) $errors[] = "Location required.";

  $filename = null;
  if(!empty($_FILES['image']['name'])){
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if(!in_array($ext,$allowed)) $errors[] = "Invalid image type.";
    else {
      $filename = time().'_'.bin2hex(random_bytes(5)).'.'.$ext;
      move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/uploads/'.$filename);
    }
  }

  if(empty($errors)){
    $stmt = $mysqli->prepare("INSERT INTO listings 
      (user_id,category_id,title,description,price,image,phone,location,latitude,longitude) 
      VALUES (?,?,?,?,?,?,?,?,?,?)");

    $uid = current_user_id();
    $stmt->bind_param('iissdsssdd',$uid,$category,$title,$description,$price,$filename,$phone,$location,$latitude,$longitude);

    if($stmt->execute()){
      header("Location: dashboard.php?msg=Listing added"); exit;
    } else $errors[] = "DB error.";
  }
}

$cats = $mysqli->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
require 'header.php';
?>

<style>
.map-box {
    width: 100%;
    height: 280px;
    border-radius: 12px;
    margin-top: 10px;
    box-shadow: 0 0 15px rgba(0,255,255,0.4);
}
.detect-btn {
    background: #00bfff;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    color: #fff;
    border: none;
    margin-top: 6px;
}
.detect-btn:hover {
    background: #00eaff;
}
</style>

<div class="container">
  <div class="form-card">
    <h2 style="font-size:26px;color:#00eaff;">Post a new listing</h2>

    <?php foreach($errors as $e): ?>
      <div class="message" style="background:#fff4f4;color:#9b1c1c"><?=esc($e)?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
      
      <label>Title</label>
      <input name="title" required>

      <label>Description</label>
      <textarea name="description" rows="5"></textarea>

      <label>Category</label>
      <select name="category">
        <option value="0">Select category</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Price (INR)</label>
      <input name="price" type="number" step="0.01" required>

      <label>Phone Number</label>
      <input name="phone" type="text" required>

      <label>Location</label>
      <input id="locationInput" name="location" type="text" required>

      <button type="button" class="detect-btn" onclick="detectLocation()">📍 Auto Detect Location</button>

      <div id="map" class="map-box"></div>

      <input type="hidden" id="lat" name="latitude">
      <input type="hidden" id="long" name="longitude">

      <label>Image</label>
      <input type="file" name="image" accept="image/*">

      <button class="search-btn" style="margin-top:12px">Post Listing</button>
    </form>

  </div>
</div>

<script>
let map, marker, geocoder;

// ⭐ Default India location
function initMap() {
    geocoder = new google.maps.Geocoder();
    const def = { lat: 28.6139, lng: 77.2090 };

    map = new google.maps.Map(document.getElementById("map"), {
        center: def,
        zoom: 13,
    });

    marker = new google.maps.Marker({
        position: def,
        map: map,
        draggable: true
    });

    marker.addListener("dragend", function(e){
        document.getElementById('lat').value = e.latLng.lat();
        document.getElementById('long').value = e.latLng.lng();
    });
}

// ⭐ Manual location typed → map update
document.getElementById("locationInput").addEventListener("change", function () {
    let address = this.value;
    geocoder.geocode({ address: address }, function (res, status) {
        if (status === "OK") {
            let loc = res[0].geometry.location;

            map.setCenter(loc);
            marker.setPosition(loc);

            document.getElementById('lat').value = loc.lat();
            document.getElementById('long').value = loc.lng();
        }
    });
});

// ⭐ Auto detect location → only when button is pressed
function detectLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            let lat = position.coords.latitude;
            let lng = position.coords.longitude;

            const pos = { lat, lng };
            map.setCenter(pos);
            marker.setPosition(pos);

            document.getElementById('lat').value = lat;
            document.getElementById('long').value = lng;
        });
    }
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBOrpDh7ZemVBRv-UVrYt5TsI3F3yoUMIk&callback=initMap" async defer></script>

<?php require 'footer.php'; ?>
