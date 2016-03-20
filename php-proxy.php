<?PHP

// Bare bones PHP Proxy to pull JSON from URL of TTN API
// Mark Stanley - TTN Reading UK
//

$url = $_GET['url'];


$limit=isset($_GET['limit']) ? $_GET['limit'] : "";
if ($limit) {
  $url="$url&limit=$limit";
  }

$ch = curl_init( $url );
  
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    
$contents = curl_exec( $ch );
  
curl_close( $ch );

print $contents;
  
?>