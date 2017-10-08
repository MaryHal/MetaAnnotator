
create or replace function now_utc()
returns timestamp as $$
begin
	return now() at time zone 'utc';
end 
$$ language 'plpgsql';

create or replace function update_modified_date()
returns trigger as $$
begin
	new.modified_date = now_utc();
	return new;
end 
$$ language 'plpgsql';

create table AnnotationSet (
	id serial primary key,
	hash varchar(32) unique not null,
	youtubeId varchar(32) not null,
	created_date  timestamp default now_utc(),
	modified_date timestamp default now_utc()
)

create trigger annotation_set_update_modified_date
before update on AnnotationSet
for each row execute procedure update_modified_date();

create table Annotation (
	id serial primary key,
	setId int4 references AnnotationSet(id) not null,
	annotationTime int4 not null,
	annotationText text not null,
	created_date  timestamp default now_utc(),
	modified_date timestamp default now_utc()
)

create trigger annotation_update_modified_date
before update on Annotation
for each row execute procedure update_modified_date();
