# Тестовое задание

Имеется обычный `http access_log` файл.

Требуется написать PHP скрипт, обрабатывающий этот лог и выдающий информацию о нём в `json` виде.

Требуемые данные: 
* количество хитов/просмотров
* количество уникальных url
* объем трафика
* количество строк всего
* количество запросов от поисковиков
* коды ответов.

##### Пример лог файла

```JSON
{
  "views": 16,
  "urls": 5,
  "traffic": 187990,
  "crawlers": {
      "Google": 2,
      "Bing": 0,
      "Baidu": 0,
      "Yandex": 0
  },
  "statusCodes": {
      "200" : 14,
      "301" : 2
  }
}
```

