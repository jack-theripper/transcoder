## Tools to transcoding/encoding audio or video, inspect and convert media formats.

```php
$options = [
	'ffprobe.path' => 'E:\devtools\bin\ffprobe.exe',
	'ffmpeg.path'  => 'E:\devtools\bin\ffmpeg.exe'
];

$audio = new \Arhitector\Jumper\Audio(__DIR__.'/audio.mp3');

//or
$factory = new \Arhitector\Jumper\Service\ServiceFactory($options);
$audio = new \Arhitector\Jumper\Audio(__DIR__.'/audio.mp3', $factory);

// or video
$audio = new \Arhitector\Jumper\Video(__DIR__.'/file.mp4', $factory);

var_dump($audio->getAudioChannels());
var_dump($audio->getFormat()->getTags());

$format = new \Arhitector\Jumper\Format\AudioFormat('mp3');
$format->setAudioBitrate(92000);
$format->setTagValue('artist', 'Performer 0123456789');

$audio->save($format, $audio->getFilePath().'_transcoding.mp3');

```

## Filters

### Audio filters

- **Volume**

```php
use \Arhitector\Jumper\Filter\Volume;
```

Halve the input audio volume.

```php
$filter = new Volume(0.5);
$filter = new Volume(1/2);
$filter = new Volume('6.0206dB');
```

Increase input audio power by 6 decibels using fixed-point precision.

```php
$filter = new Volume('6dB', Volume::PRECISION_FIXED);
```
