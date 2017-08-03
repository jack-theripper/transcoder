# Transcoder [![Latest Version](https://img.shields.io/github/release/jack-theripper/transcoder.svg?style=flat-square)](https://github.com/jack-theripper/transcoder/releases) 

Tools to transcoding/encoding audio or video, inspect and convert media formats.

Инструмент для кодирования аудио или видео, получения информации и конвертирования в другие форматы.

## Содержание

- [С чего начать](#С-чего-начать)
	- [Установка](#Установка)
	- [Требования](#Требования)
- [События](#События)
	- [before](#Событие-before)
	- [before.pass](#Событие-beforepass)
	- [before.queue](#Событие-beforequeue)
	- [successful](#Событие-successful)
	- [failure](#Событие-failure)
	- [failure.codec](#Событие-failurecodec)
	- [after](#Событие-after)
	- [after.pass](#Событие-afterpass)
	- [after.queue](#Событие-afterqueue)
	- [progress](#Событие-progress)
	- [stream](#Событие-stream)
- [Фильтры](#Фильтры)
	- [Простой фильтр](#Простой-фильтр-simplefilter)
	- [Задержка звука](#Задержка-звука)
- [Примеры](#Примеры)
	

## С чего начать

В зависимости от контента, вы можете использовать `Audio` для работы с аудио-файлами, `Frame` для изображений, а `Video` и `Subtitle` соответственно для видео-файлов и субтитров.

Конструктор в общем виде выглядит так:

```php
public < ... >::__construct(string $filePath, ServiceFactoryInterface $service = null)
```

`$filePath` - строка, путь до исходного файла.

> Вы не можете использовать удаленный источник или символические ссылки.

`$service` - параметр не обязателен. Экземпляр сервиса. По умолчанию   `ServiceFactory`.

**Пример №1**

```php
use Arhitector\Transcoder\Audio;
use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Frame;
use Arhitector\Transcoder\Subtitle;

// аудио
$audio = new Audio('sample.mp3');

// видео
$video = new Video('sample.avi');

// изображения
$frame = new Frame('sample.jpg');

// субтитры
$subtitle = new Subtitle('sample.srt');
```

**Пример №2**

Вы можете использовать свою сервис-фабрику или изменить некоторые опции.

```php
use Arhitector\Transcoder\Service\ServiceFactory;

$service = new ServiceFactory([
	'ffprobe.path'   => 'E:\devtools\bin\ffprobe.exe',
	'ffmpeg.path'    => 'E:\devtools\bin\ffmpeg.exe'
]);

// используем это
$video = new Video('sample.avi', $service);
```

## Установка

```bash
$ composer require --prefer-dist arhitector/transcoder dev-master
```

## Требования

* PHP >= 5.6
* Установленный [FFMpeg](http://ffmpeg.org)

## События

Экземпляр формата регистрирует обработчики, такой обработчик будет выполнен при наступлении определённого события.
Методы `addListener` или `addOneListener` регистрируют обработчик на событие. Читать подробнее [League\Event](http://event.thephpleague.com/2.0/).

```php
$format = new VideoFormat();
$format->addListener('*', function ($event) {
	// "*" - обработчик сработает на любое событие
});
```

В зависимости от события, обработчик может повлиять на дальнейший ход выполнения операции.

```php
$format->addListener('before', function ($event) {
	$event->stopPropagation(); // дальнейшее выполнение будет остановлено
});
```

### Событие before

Выполняется перед началом кодирования. Дальнейшее выполнение может быть остановлено.

```php
$format->addListener('before', function ($event, $media, $format, $filePath) {
	// обработчик сработает после вызова `$media->save($format, ...`
});
```

### Событие before.pass

### Событие before.queue

### Событие successful

Событие наступает в том случае, если кодирование завершино без ошибок.
При использовании очередей - в том случае, если задание добавлено в очередь.

```php
$format->addListener('successful', function ($event, $media, $format, $filePath) {
	// работа завершена без ошибок
});
```

### Событие failure

Операция завершилась с ошибкой или не может корректно завершиться.

```php
use Symfony\Component\Process\Exception\ProcessFailedException;

$format->addListener('failure', function ($event, ProcessFailedException $exception) {
	// кодирование не может быть завершено из-за возникшей ошибки
});
```

### Событие failure.codec

### Событие after

Обработчик будет вызван когда операция завершится, не зависимо от того была ли операция завершена успешно или нет. 

```php
$format->addListener('after', function ($event, $media, $format, $filePath) {
	// операция завершилась, но мы не знаем успешно или нет
});
```

### Событие after.pass

### Событие after.queue

### Событие progress

Событие наступает во время выполнения операции кодирования.

```php
use Arhitector/Transcoder/Event/EventProgress;

$format->addListener('progress', function (EventProgress $event) {
	// $event->getPercent();
});
```

### Событие stream

### Поддержка очередей

Вместо прямого транскодирования вы можете отправлять задачи в очередь, например, на сервер очередей. Такой функционал
 доступен прямо из коробки. Вы можете использовать опцию `ServiceFactoryInterface::OPTION_USE_QUEUE` при создании сервис-фабрики.
Читать подробнее [SimpleQueue](https://github.com/fguillot/simple-queue).

**Пример**

```php
$adapter = new SimpleQueue\Adapter\MemoryQueueAdapter();
$queue = new SimpleQueue\Queue($queue);

$service = new Arhitector\Transcoder\Service\ServiceFactory([
    Arhitector\Transcoder\Service\ServiceFactory::OPTION_USE_QUEUE => $queue
]);

$audio = new Arhitector\Transcoder\Audio('sample.mp3', $service);

// задача будет отправлена в очередь `$queue`
$audio->save($audio->getFormat(), 'new-sample.mp3');

var_dump($queue->pull()); // запросить задачу из очереди
```

## Что можно настроить? Поддерживаемые опции

###  Опции сервис-фабрики

Вы можете использовать свою реализацию сервис-фабрики. Для этого необходимо реализовать интерфейс `Arhitector\Transcoder\Service\ServiceFactoryInterface`.
 
`ServiceFactory` поддерживает следующие опции:

1. `ffmpeg.path` - путь до исполняемого файла ffmpeg

1. `ffmpeg.threads` - FFMpeg-опция threads. По умолчанию `0`.

1. `ffprobe.path` - путь до исполняемого файла ffprobe

1. `timeout` - задаёт таймаут выполнения команды кодирования.

1. `use_queue` - Отправляет задачу в очередь. Значение должно быть объектом, реализующим `SimpleQueue\QueueAdapterInterface`.

## Примеры 

### Извлечение информации из видео файла, аудио файла и т.д.

```php
use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Audio;

$video = new Video('sample.avi');

var_dump($video->getWidth(), $video->getHeight());

$audio = new Audio(__DIR__.'/audio.mp3', $factory);

var_dump($audio->getAudioChannels());
var_dump($audio->getFormat()->getTags());
```

### Извлечение звука из видео файла с последующим сохранением в формате MP3

Этот простой пример показывает лишь принцип, таким же способом можно сохранить субтитры или обложку из Mp3-файла и т.д.

```php
use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Stream\AudioStreamInterface;
use Arhitector\Transcoder\Format\Mp3;

$video = new Video('sample.mp4');

foreach ($video->getStreams() as $stream)
{
	// тут выбираем только аудио канал
	if ($stream instanceof AudioStreamInterface)
	{
		$stream->save(new Mp3(), __DIR__.'/only-audio.mp3');
		
		break; // видео может иметь несколько аудио потоков
	}
}
```

### Преобразование из одного формата в любой другой

```php
use Arhitector\Transcoder\Audio;
use Arhitector\Transcoder\Format\Mp3;

$audio = new Audio('audio-file.wav');
$audio->save(new Mp3(), 'audio-file.mp3');

use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Format\VideoFormat;

$video = new Video('video-file.avi');
$video->save(new VideoFormat('aac', 'h264'), 'video-file.mp4');
```

### Добавление/Изменение мета-информации

```php
use Arhitector\Transcoder\Audio;

$audio = new Audio('file.mp3');

$format = $audio->getFormat();
$format['artist'] = 'Новый артист';

$auiod->save($format, 'new-file.mp3');
```

### Как добавить/изменить обложку MP3-файла?

```php
use Arhitector\Transcoder\Audio;
use Arhitector\Transcoder\Frame;

$audio = new Audio(__DIR__.'/sample.mp3');
$streams = $audio->getStreams();

$new_cover = (new Frame(__DIR__.'/sample.jpg'))
    ->getStreams()
    ->getFirst();

// индекс `0` - аудио-дорожка, `1` - обложка.
$streams[1] = $new_cover;

$audio->save($audio->getFormat(), 'sample-with-new-cover.mp3');
```

## ООП-обёртки над форматами

Такие обёртки (например, Mp3 или Jpeg и т.д.) созданы для удобства. 

### Изображения

- Png, Jpeg, Ppm, Bmp, Gif

### Аудио-форматы

- Aac, Mp3, Oga, Flac

### Видео-форматы

- Flv, Mkv

## Фильтры

Фильтры необходимы для изменения исходного медиа контента. Могут иметь один или несколько входов и выходов, а также быть организованы в цепочки для изоляции некоторых фильтров друг от друга.

```php
public TranscodeInterface::addFilter(FilterInterface $filter, $priority = 0);
```

Аудио фильтры реализуют интерфейс `AudioFilterInterface` и могут использоваться совместно только с `Audio` или `Video`.

Видео фильтры реализуют интерфейсы `FrameFilterInterface` или `VideoFilterInterface`, используются либо с `Frame` либо с `Video`.

```php
// добавляем любой фильтр
$video->addFilter($filter);

// добавляем фильтр с приоритетом = 99.
$audio->addFilter($filter, 99);
```

#### Простой фильтр, SimpleFilter

Это самый простой фильтр, который позволяет устанавливать свои параметры для командной строки `ffmpeg`.

```php
use Arhitector\Transcoder\Filter\SimpleFilter;
```

**Конструктор**

```php
public SimpleFilter::__construct(array $parameters = [])
```

Создадим экземпляр и добавим параметр 'video_codec'.

```php
$filter = new SimpleFilter([
	'video_codec' => 'h264'
]);
```

Этот метод перезапишет ранее установленные значения.

```php
$filter->setParameters([
	'video_codec' => 'libx264'
]);

// ArrayAccess
$filter['video_codec'] = 'x264';
```

### Фильтр Cut

Аудио фильтр, который позволяет обрезать медиа-файл до определённых значений продолжительности.

```php
use Arhitector\Transcoder\Filter\Cut;
```

**Конструктор**

```php
public Cut::__construct(TimeInterval|int $start [, TimeInterval $duration = null])
```

**Пример №1**

Пропустить 20 секунд от начала и сохранить последующие 60 секунд.

```php
$filter = new Cut(new TimeInterval(20), new TimeInterval(60));
```

### Фильтр Volume

Аудио фильтр, который изменяет громкость аудио потока.

```php
use \Arhitector\Transcoder\Filter\Volume;
```

**Конструктор**

```php
public Volume::__construct(float $volume [, string $precision = null])
```

**Пример №1**

Пример показывает как уменьшить громкость аудио.

```php
$filter = new Volume(0.5);
$filter = new Volume(1/2);
$filter = new Volume('6.0206dB');
```

**Пример №2**

Увеличение входной мощности звука на 6 дБ с фиксированной точностью.

```php
$filter = new Volume('6dB', Volume::PRECISION_FIXED);
```

### Фильтр Fade

Фильтр накладывает эффект затухания звука на аудио дорожку.

```php
use \Arhitector\Transcoder\Filter\Fade;
```

**Конструктор**

```php
public Fade::__construct(TimeInterval|int $startTime = 0 [, TimeInterval|int $duration = null [, string $effectType = null]])
```

**Пример №1**

```php
new Fade(2, 10, Fade::FADE_OUT)
```

### Фильтр AudioDelay

```php
use \Arhitector\Transcoder\Filter\AudioDelay;
```

### Фильтр Rotate

```php
use \Arhitector\Transcoder\Filter\Rotate;
```

**Конструктор**

```php
public Rotate::__construct($angle = null)
```

### Фильтр Crop

```php
use \Arhitector\Transcoder\Filter\Crop;
```

**Конструктор**

```php
public Crop::__construct(Point $start, Dimension $dimension)
```

## Лицензия

Распространяется под лицензией <a href="http://opensource.org/licenses/MIT">MIT</a>.

```
Copyright (c) 2017 Dmitry Arhitector

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
