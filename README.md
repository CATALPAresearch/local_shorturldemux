# Short-url demux
local_shorturldemux is a small moodle plugin to set short url or to handle multiple course enrollments on the same course. When requesting a short-url identifier the plugin determines the corresponding course and forwards the request to the corresponding course activity.

Through this plugin short-urls can stay unchaged over several courses or semesters. In case a learner enrolled to two moodle courses that belong to the same course request will be forwarded to the most recent moodle course (or course activity).

## TODO
* Improve sanitization: https://www.php.net/manual/de/filter.filters.sanitize.php
* provide form the manage Short-URL and its redirect targets

## Setup and usage

1. Rename the plugin folder to 'shorturldemux' and move it in the folder `locale`
2. Open admin panel and follow the instructions to install the plugin
3. Enter some data in datebase table `shorturldemux_courses` by uing a tool like adminer (see details below)
4. Open a short-url in the browser: `<your-moodle-path>/local/shorturldemux/index.php?c=<short-url>`, e.g. https://aple.fernuni-hagen.de/local/shorturldemux/index.php?c=1801-unterbrechungsvektor


**Table shorturldemux_courses**
id: id numbder
short_id: id of shortURL stored in table shorturldemux_shorts
course_id: 
path: path within moodle leading to the shortURL target

CSV
```
short,course_id,path
1801-klasse-a-hosts,2,'/mod/quiz/view.php?id=155'
1801-klasse-a-hosts,5,'/mod/quiz/view.php?id=239'
```

SQL:
```
INSERT INTO moodleshorturldemux_courses (short,course_id,path) VALUES ('1801-klasse-a-hosts',2,'/mod/quiz/view.php?id=155');

INSERT INTO moodleshorturldemux_courses (short,course_id,path) VALUES ('1801-klasse-a-hosts',5,'/mod/quiz/view.php?id=239');
```

# Authors
Marc Burchart 

Niels Seidel

