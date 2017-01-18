## Tools to transcoding/encoding audio or video, inspect and convert media formats.

```php
$options = [
	'ffprobe.path' => 'E:\devtools\bin\ffprobe.exe',
	'ffmpeg.path'  => 'E:\devtools\bin\ffmpeg.exe'
];

$audio = new \Arhitector\Jumper\Audio(__DIR__.'/audio.mp3', $options);

var_dump($audio->getAudioChannels());
var_dump($audio->getFormat()->getTags());

$format = new \Arhitector\Jumper\Format\AudioFormat('mp3');
$format->setAudioBitrate(92000);
$format->setTagValue('artist', 'Performer 0123456789');

$audio->save($format, $audio->getFilePath().'_transcoding.mp3');

```
