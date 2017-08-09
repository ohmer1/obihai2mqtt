#!/usr/bin/php
<?php

require __DIR__ . '/vendor/bluerhinos/phpmqtt/phpMQTT.php';

$longOpts = array(
  'poll_freq:',
  'obihai_host:',
  'obihai_user:',
  'obihai_pass:',
  'mqtt_host:',
  'mqtt_port:',
  'mqtt_user:',
  'mqtt_pass:',
  'mqtt_topic:',
);

$options = getopt(null, $longOpts);

if (!isset($options['obihai_host']))
{
  print "Parameter --obihai_host is mandatory!\n";
  exit(1);
}

$poll_freq = (isset($options['poll_freq']) ? $options['poll_freq'] : 5);

$obihai_host = $options['obihai_host'];
$obihai_user = (isset($options['obihai_user']) ? $options['obihai_user'] : 'admin');
$obihai_pass = (isset($options['obihai_pass']) ? $options['obihai_pass'] : 'admin');

$mqtt_host = (isset($options['mqtt_host']) ? $options['mqtt_host'] : 'localhost');
$mqtt_port = (isset($options['mqtt_port']) ? $options['mqtt_port'] : 1883);
$mqtt_user = (isset($options['mqtt_user']) ? $options['mqtt_user'] : '');
$mqtt_pass = (isset($options['mqtt_pass']) ? $options['mqtt_pass'] : '');
$mqtt_topic = (isset($options['mqtt_topic']) ? $options['mqtt_topic'] : 'obihai');

$mqtt = new phpMQTT($mqtt_host, $mqtt_port, 'obihai2mqtt_'.rand());

$url = 'http://' . $obihai_host . '/PI_FXS_1_Stats.xml';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($curl, CURLOPT_USERPWD, "$obihai_user:$obihai_pass");

while (true)
{
  $states = array();
  $xml = new SimpleXMLElement(curl_exec($curl));

  $parameters = $xml->xpath('//parameter');
  foreach ($parameters as $parameter)
  {
    foreach ($parameter->attributes() as $attribute => $value)
    {
      if ($attribute == 'name' && $value == 'State')
      {
        $states[] = $parameter->value['current'];
      }
    }
  }
  
  if ($mqtt->connect(true, null, $mqtt_user, $mqtt_pass))
  {
    $i = 1;
    foreach ($states as $state)
    {
      $mqtt->publish($mqtt_topic . '/state_line' . $i, $state);
      $i++;
    }
    
    $mqtt->close();
  }
  else
  {
    print "Cannot connect to MQTT.\n";
  }
    
  sleep($poll_freq);
}
