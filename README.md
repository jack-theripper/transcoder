## Tools to transcoding/encoding audio or video, inspect and convert media formats.

```php
$options = [
	'ffprobe.path' => 'E:\devtools\bin\ffprobe.exe',
	'ffmpeg.path'  => 'E:\devtools\bin\ffmpeg.exe'
];

$audio = new \Arhitector\Jumper\Audio(__DIR__.'/audio.mp3', $options);

var_dump($audio->getAudioChannels());

$format = new \Arhitector\Jumper\Format\AudioFormat('mp3');
$format->setAudioBitrate(92000);

$audio->save($format, $audio->getFilePath().'_transcoding.mp3');

```
