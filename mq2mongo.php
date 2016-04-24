<?php
 
// Mongodb Configuration
$dbhost = 'localhost';
$dbname = 'ttnWorkshop';

// Connect to ttnWorkshop database
$conn = new Mongo("mongodb://$dbhost");
$ttnDb = $conn->$dbname;
$c_nodes = $ttnDb->mqttNodes;

$client = new Mosquitto\Client();
$client->onConnect('connect');
$client->onDisconnect('disconnect');
$client->onSubscribe('subscribe');
$client->onMessage('message');
$client->connect("croft.thethings.girovito.nl", 1883, 60);

//Get an full list of nodes we should subscribe to
$nodeCursor = $c_nodes->find();
$numNodes = $nodeCursor->count();
if ( $numNodes > 0 ) {
  foreach ($nodeCursor as $obj) {
    $thisNode=$obj['node'];

    // Subscribe to the mqtt nodes listed in the database
    $client->subscribe("nodes/$thisNode/packets", 1);
  }
}

// Disconnect from the database
$conn->close(); 
 
while (true) {
        $client->loop();
        sleep(2);
}
 
$client->disconnect();
unset($client);
 
function connect($r) {
        echo "I got code {$r}\n";
}
 
function subscribe() {
        echo "Subscribed to a topic\n";
}
 
// must return "custom";

function message($message) {
	// Mongodb Configuration
	$dbhost = 'localhost';
	$dbname = 'ttnWorkshop';
	
	// Connect to test database
	$m = new Mongo("mongodb://$dbhost");
	$db = $m->$dbname;
	$c_senseData = $db->senseData;
	//$key=pack('n*', 0x2B,0x7E,0x15,0x16,0x28,0xAE,0xD2,0xA6,0xAB,0xF7,0x15,0x88,0x09,0xCF,0x4F,0x3C);
        printf("\nGot a message on topic %s with payload:%s", 
          $message->topic, $message->payload);
	$readableJson=json_decode($message->payload, true);
        foreach ($readableJson as $k => $v) {
          echo $k, " : ", $v, "\n";
	  if ($k == "nodeEui" ){
	    $node =$v;
	  }
	  if ($k == "time" ) {
	    $msgTime = $v;
	  }
          if ($k == "data" ) {
            //$msgData=mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $v, MCRYPT_MODE_ECB);
            $msgData=base64_decode($v);
            echo "\nmsgData is ", $msgData, "\n";
	    $msgDataJson=json_decode($msgData, true);
	    if($msgDataJson!="") {
	      $senserec = array( 
	        'node' => $node,
	        'time' => $msgTime,
    	        'msgData' => $msgDataJson
	      );
	    } else {
	      $senserec = array( 
	        'node' => $node,
	        'time' => $msgTime,
    	        'msgData' => $msgData
	      );
	    }

	    $c_senseData->save($senserec);
          }
        }
	$m->close();
}
 
function disconnect() {
        echo "Disconnected cleanly\n";
}

