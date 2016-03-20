<?PHP 
/* Example app for TTN workshops.  This is quick and dirty code!
 * Mark Stanley, Jan 2016
 *
 * Takes a TTN node ID as a parameter, gets its data from the TTN API, and displays it graphically
 *
 * Node is an Arduino with ThingInnovations shield, designed by Andrew Lindasay
 * Shield contains temperature, humidity, light sensor plus Microchip RN2483 LoraWAN radio
 *
 */

$nodeId =isset($_GET['node']) ? $_GET['node'] : "";
$noRecs =isset($_GET['recs']) ? $_GET['recs'] : 20;

//echo "<h1>Node $nodeId </h1>";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>TTN Workshop</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/workshop.css" rel="stylesheet">

    <script src="js/jquery.js"></script>
    <script src="js/jquery.sparkline.js"></script>
    <script src="js/raphael-2.1.4.min.js"></script>
    <script src="js/justgage.js"></script>

</head>
<body>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="banner">
        <h1>The Things Network workshop, GROW@GreenPark</h1>
        <p>This is a simple site showing data gathered from the TTN API for the node identified in the URL.</p>
        <p>If you are interested in building the Things Network in Reading or Thatcham, or if you are curious about our project please visit our websites.</p>
        <p><a href="http://ttnreading.org">TTN Reading</a>  and  <a href="http://thatcham.lpwan.uk">TTN Thatcham</a></p>
    </div>

    <div class="c-container">
        <!-- Example row of columns -->
        <div class="row-fluid span4 ">
            <div class="col-md-4 span4">
                <h2>Temperature</h2>
                <div id="gTemp"></div>
                <span class="dynamicsparkline" id='sp1'>Loading..</span>
            </div>
            <div class="col-md-4 span4">
                <h2>Humidity</h2>
                <div id="gHum"></div>
                <span class="dynamicsparkline" id='sp2'>Loading..</span>
            </div>
            <div class="col-md-4 span4">
                <h2>Light</h2>
                <div id="gLight"></div>
                <span class="dynamicsparkline" id='sp3'>Loading..</span>
            </div>
        </div>
    </div>
    <div class="infobox">
        <p>Node: <?PHP echo $nodeId; ?></p>
        <p id="datapoints"></p>
        <p id="range"></p>
        <?php echo"<form method='get' class='form-inline pull-right' action='".$_SERVER['PHP_SELF']."' >"; ?>
        <input type="text" class="input-small" id="node" name="node" value="<?php echo $nodeId; ?>" placeholder="Node ID">
        <input type="text" class="input-small" id="recs" name="recs" placeholder="No. Records">
        <button type="submit" class="btn" id="node_btn" value="node">Refresh</button>
        </form>
    </div>

    <div class="container control-group" id="nodeSelect">
    </div>


    <script>
        var xmlhttp = new XMLHttpRequest();
        //var url = "./php-proxy.php?url=http://thethingsnetwork.org/api/v0/nodes/02011E01/?format=json&limit=500";
        var url = "./php-proxy.php?url=http://thethingsnetwork.org/api/v0/nodes/<?PHP echo $nodeId; ?>/?format=json&limit=<?PHP echo $noRecs; ?>";

        xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            //document.getElementById("id01").innerHTML = xmlhttp.responseText;
            var myArr = JSON.parse(xmlhttp.responseText);
            myFunction(myArr);
            }
        };

        xmlhttp.open("GET", url, true);
        xmlhttp.send();
        function myFunction(arr) {
            var tVal;
            var hVal;
            var lVal;
            var gTemp;
            var gHum;
            var gLight;
            var out = "";
            var i;
            var j=0;
            var tempSparky=[];
            var humSparky=[];
            var lightSparky=[];
            for(i = arr.length;i>0; i--) {
                //out += arr[i-1].time+' : '+arr[i-1].data_json.sensor+' <br>' ;
                tempSparky[j]=arr[i-1].data_json.t;
                humSparky[j]=arr[i-1].data_json.h;
                lightSparky[j]=arr[i-1].data_json.l;
                j++;
            }
            tVal=tempSparky[j-1];
            hVal=humSparky[j-1];
            lVal=lightSparky[j-1];
            document.getElementById("datapoints").innerHTML = "Data points: "+arr.length;
            document.getElementById("range").innerHTML = "Start: "+arr[arr.length-1].time + "<br/> End : " +arr[0].time;
            $('#sp1').sparkline(tempSparky, {type:'bar', barColor:'#6666EE', negBarColor:'#4bacc6', barWidth:'5px', barSpacing:'2px', height:'50px'});
            $('#sp2').sparkline(humSparky, {type:'bar', barColor:'#6666EE', negBarColor:'#4bacc6', barWidth:'5px', barSpacing:'2px', height:'50px'});
            $('#sp3').sparkline(lightSparky, {type:'bar', barColor:'#6666EE', negBarColor:'#4bacc6', barWidth:'5px', barSpacing:'2px', height:'50px'});
            gTemp = new JustGage({
                id: "gTemp",
                value: tVal,
                min: -10,
                max: 40,
                gaugeWidthScale: 1,
                counter: true,
                hideInnerShadow: true
            });
            gHum = new JustGage({
                id: "gHum",
                value: hVal,
                min: 0,
                max: 100,
                gaugeWidthScale: 1,
                counter: true,
                hideInnerShadow: true

            });
            gLight = new JustGage({
                id: "gLight",
                value: lVal,
                min: 0,
                max: 1024,
                gaugeWidthScale: 1,
                counter: true,
                hideInnerShadow: true
            });

        }

    </script>
</body>
</html>
