create table safesnippets (key varchar(10), val varchar(10000));
CREATE UNIQUE INDEX idx_snippets ON safesnippets(key);
