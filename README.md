# obihai2mqtt
Forward obihai lines states to MQTT.

I'm using VOIP with Obihai ATA. This script forward the lines states to MQTT. I wrote this in order to make Home Assistant aware of the status of my phone line.

# How to configure it
The configutation is done by passing parameters to the script when starting.

Available parameters:
* --poll_freq : Polling frequency in seconds. Default is *5*.
* --obihai_host : Hostname or IP address of your obihai voip adapter. This parameter is *mandatory*.
* --obihai_user : Username to connect to the web interface of your device. Default is *admin*.
* --obihai_pass : Password to connect to the web interface of your device. Default is *admin*.
* --mqtt_host : Hostname or IP address of your MQTT server. Default is *localhost*.
* --mqtt_user : Username to conect to the MQTT server. Default is an empty string.
* --mqtt_pass : Password to connect to the MQTT server. Default is an empty string.
* --mqtt_topic : Topic where messages will be sent to the MQTT server. Default is *obihai*.
