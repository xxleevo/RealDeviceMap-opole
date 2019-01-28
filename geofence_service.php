<?php
class Geofence {
  public $name;
  public $polygon;
}
class GeofenceService {
  public $geofences = [];
  private $dir = "./geofences/";
	
  function __construct() {
    $this->create_directory();
    $this->load_geofences();
  }
	
  function load_geofences() {
    $files = scandir($this->dir);
    for ($i = 0; $i < count($files); $i++) {
      if ($files[$i] === '.' || $files[$i] === '..')
        continue;
			
      $geofence = $this->load_geofence($this->dir . $files[$i]);
      if ($geofence != null) {
        array_push($this->geofences, $geofence);
      }
    }	
    return $this->geofences;
  }

  function load_geofence($file) {
    $lines = file($file);
    $name = "Unknown";
    if (count($lines) !== 0 && strpos($lines[0], '[') === 0) {
      $name = str_replace('[', "", $lines[0]);
      $name = str_replace(']', "", $name);
    }

    $geofence = new Geofence();
    $geofence->name = $name;
    $geofence->polygon = $this->build_polygon(array_slice($lines, 1));
    return $geofence;
  }

  function build_polygon($lines) {
    $polygon = [];
    $count = count($lines);
    for ($i = 0; $i < $count; $i++) {
      $line = $lines[$i];
      if (strpos($line, '[') === 0)
        continue;
			
      $parts = explode(',', $line);
      $lat = $parts[0];
      $lon = $parts[1];
      array_push($polygon, [$lat, $lon]);
    }
    return $polygon;
  }

  public function get_geofence($lat, $lon) {
    for ($i = 0; $i < count($this->geofences); $i++) {
      if ($this->is_in_polygon($this->geofences[$i], $lat, $lon)) {
        return $this->geofences[$i];
      }
    }
    return null;
  }

  function is_in_polygon($geofence, $lat_x, $lon_y) {
    //[0]=x,[1]=y
    $c = 0;
    $count = count($geofence->polygon);
    for ($i = -1, $l = $count, $j = $l - 1; ++$i < $l; $j = $i) {
      try {
        if ($geofence->polygon == null)
          continue;

        $c = (($geofence->polygon[$i][0] <= $lat_x && $lat_x < $geofence->polygon[$j][0] || ($geofence->polygon[$j][0] <= $lat_x && $lat_x < $geofence->polygon[$i][0])) &&
            ($lon_y < ($geofence->polygon[$j][1] - $lon_y) * ($lat_x - $geofence->polygon[$i][0]) / $geofence->polygon[$j][0] - $geofence->polygon[$i][0]) + $geofence->polygon[$i][1]) &&
            ($c = !$c);
      } catch (Exception $e) {
        echo $e->getMessage();
      }
    }
    return $c;
  }

  function create_directory() {
    if (!file_exists($this->dir)) {
      mkdir($this->dir, 0777, true);
    }
  }
}
?>