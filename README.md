# Short-url demux
local_shorturldemux is a small moodle plugin to set short url or to handle multiple course enrollments on the same course.

## Setup and usage

`<your-moodle-path>/local/cassign/index.php?c=<short-url>`

127.0.0.1/moodle/local/cassign/index.php?c=1801-unterbrechungsvektor

**Table cassign_courses**
id	
short_id: id of shortURL stored in table cassign_shorts
course_id: 
path: path within moodle leading to the shortURL target

short_id,course_id,path
1,2,'/mod/quiz/view.php?id=260'
2,2,'/mod/quiz/view.php?id=261'

INSERT INTO moodlecassign_courses (short,course_id,path) VALUES ('1801-klasse-a-hosts',2,'/mod/quiz/view.php?id=239');


**Table cassign_shorts**
id: index
short: Short URL

id,short
1,1801-schichtenmodell
2,1801-instruktionszyklus

INSERT INTO moodlecassign_shorts (id,short) VALUES (1,'1801-schichtenmodell');
INSERT INTO moodlecassign_shorts (id,short) VALUES (2,'1801-instruktionszyklus');


**(optional) Table cassign_links**
id
short
extern
link
