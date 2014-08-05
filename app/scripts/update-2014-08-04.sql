DROP TABLE IF EXISTS semester_info;

CREATE TABLE semester_info (
    id  int(11) NOT NULL,
    activity   tinyint(1) DEFAULT 0,
    visibility tinyint(1) DEFAULT 0
);

ALTER TABLE syllabus ADD COLUMN syllabus_sem_id int(11);

UPDATE syllabus SET syllabus_sem_id = (syllabus_class_year * 10) + syllabus_class_semester;