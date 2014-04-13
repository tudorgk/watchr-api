-- Created by Vertabelo (http://vertabelo.com)
-- Script type: create
-- Scope: [tables, references, sequences, views, procedures]
-- Generated at Sat Apr 05 11:56:34 UTC 2014


-- tables
-- Table attachment
CREATE TABLE attachment (
    attachment_id int  NOT NULL AUTO_INCREMENT,
    fk_event int  NOT NULL,
    fk_photo int  NOT NULL,
    CONSTRAINT attachment_pk PRIMARY KEY (attachment_id)
);

-- Table conversation
CREATE TABLE conversation (
    conversation_id int  NOT NULL AUTO_INCREMENT,
    timestamp timestamp  NOT NULL,
    fk_conversation_status int  NOT NULL,
    fk_watchr_event int  NOT NULL,
    CONSTRAINT conversation_pk PRIMARY KEY (conversation_id)
);

-- Table conversation_reply
CREATE TABLE conversation_reply (
    reply_id int  NOT NULL AUTO_INCREMENT,
    reply_text varchar(500)  NOT NULL,
    timestamp timestamp  NOT NULL,
    fk_reply_status int  NOT NULL,
    fk_conversation int  NOT NULL,
    fk_user int  NOT NULL,
    CONSTRAINT conversation_reply_pk PRIMARY KEY (reply_id)
);

-- Table conversation_status
CREATE TABLE conversation_status (
    id int  NOT NULL AUTO_INCREMENT,
    status varchar(80)  NOT NULL,
    CONSTRAINT conversation_status_pk PRIMARY KEY (id)
);

-- Table country_t
CREATE TABLE country_t (
    country_id int  NOT NULL AUTO_INCREMENT,
    iso2 char(2)  NOT NULL,
    short_name varchar(80)  NOT NULL,
    long_name varchar(80)  NOT NULL,
    CONSTRAINT country_t_pk PRIMARY KEY (country_id)
);

-- Table device
CREATE TABLE device (
    device_id int  NOT NULL AUTO_INCREMENT,
    brand varchar(80)  NOT NULL,
    model varchar(80)  NOT NULL,
    device_uid varchar(120)  NOT NULL,
    device_token varchar(80)  NOT NULL,
    CONSTRAINT device_pk PRIMARY KEY (device_id)
);

-- Table event_status
CREATE TABLE event_status (
    status_id int  NOT NULL AUTO_INCREMENT,
    status_type int  NOT NULL,
    satus_value varchar(80)  NOT NULL,
    CONSTRAINT event_status_pk PRIMARY KEY (status_id)
);

-- Table gender
CREATE TABLE gender (
    gender_id int  NOT NULL AUTO_INCREMENT,
    text varchar(20)  NOT NULL,
    CONSTRAINT gender_pk PRIMARY KEY (gender_id)
);

-- Table institution
CREATE TABLE institution (
    institution_id int  NOT NULL AUTO_INCREMENT,
    institution_name varchar(100)  NOT NULL,
    institution_code int  NOT NULL,
    description varchar(100)  NOT NULL,
    fk_location int  NOT NULL,
    fk_country int  NOT NULL,
    CONSTRAINT institution_pk PRIMARY KEY (institution_id)
);

-- Table photo
CREATE TABLE photo (
    photo_id int  NOT NULL AUTO_INCREMENT,
    path varchar(100)  NOT NULL,
    description varchar(100)  NULL,
    caption varchar(100)  NULL,
    timestamp timestamp  NOT NULL,
    fk_photo_type int  NOT NULL,
    fk_post int  NOT NULL,
    size int  NOT NULL,
    width int  NOT NULL,
    height int  NOT NULL,
    CONSTRAINT photo_pk PRIMARY KEY (photo_id)
);

-- Table photo_type
CREATE TABLE photo_type (
    photo_type_id int  NOT NULL AUTO_INCREMENT,
    type_value varchar(20)  NOT NULL,
    CONSTRAINT photo_type_pk PRIMARY KEY (photo_type_id)
);

-- Table position
CREATE TABLE position (
    position_id int  NOT NULL AUTO_INCREMENT,
    latitude decimal(10,6)  NOT NULL,
    longitude decimal(10,6)  NOT NULL,
    CONSTRAINT position_pk PRIMARY KEY (position_id)
);

-- Table profile_device
CREATE TABLE profile_device (
    profile_device_id int  NOT NULL AUTO_INCREMENT,
    fk_user_profile int  NOT NULL,
    fk_device int  NOT NULL,
    CONSTRAINT profile_device_pk PRIMARY KEY (profile_device_id)
);

-- Table profile_status
CREATE TABLE profile_status (
    profile_status_id int  NOT NULL AUTO_INCREMENT,
    status_value varchar(20)  NOT NULL,
    CONSTRAINT profile_status_pk PRIMARY KEY (profile_status_id)
);

-- Table rating
CREATE TABLE rating (
    rating_id int  NOT NULL AUTO_INCREMENT,
    user_id int  NOT NULL,
    event_id int  NOT NULL,
    rating_value real(2,2)  NOT NULL,
    rating_text varchar(200)  NOT NULL,
    timestamp timestamp  NOT NULL,
    CONSTRAINT rating_pk PRIMARY KEY (rating_id)
);

-- Table reply_status
CREATE TABLE reply_status (
    id int  NOT NULL AUTO_INCREMENT,
    status varchar(80)  NOT NULL,
    CONSTRAINT reply_status_pk PRIMARY KEY (id)
);

-- Table user_profile
CREATE TABLE user_profile (
    user_id int  NOT NULL AUTO_INCREMENT,
    username varchar(40)  NOT NULL,
    email varchar(40)  NOT NULL,
    password varchar(40)  NOT NULL,
    salt varchar(32)  NOT NULL,
    first_name varchar(100)  NOT NULL,
    last_name varchar(100)  NOT NULL,
    fk_photo int  NOT NULL,
    fk_country int  NOT NULL,
    fk_gender int  NOT NULL,
    fk_profile_status int  NOT NULL,
    CONSTRAINT user_profile_pk PRIMARY KEY (user_id)
);

-- Table watchr_category
CREATE TABLE watchr_category (
    category_id int  NOT NULL AUTO_INCREMENT,
    category_name varchar(80)  NOT NULL,
    category_description varchar(80)  NOT NULL,
    fk_subcategory int  NULL,
    CONSTRAINT watchr_category_pk PRIMARY KEY (category_id)
);

-- Table watchr_event
CREATE TABLE watchr_event (
    event_id int  NOT NULL AUTO_INCREMENT,
    event_name varchar(100)  NOT NULL,
    description varchar(500)  NOT NULL,
    timestamp timestamp  NOT NULL,
    fk_event_category int  NOT NULL,
    fk_created_by_user int  NOT NULL,
    fk_event_status int  NOT NULL,
    fk_location int  NOT NULL,
    CONSTRAINT watchr_event_pk PRIMARY KEY (event_id)
);





-- foreign keys
-- Reference:  attachments_photo (table: attachment)


ALTER TABLE attachment ADD CONSTRAINT attachments_photo FOREIGN KEY attachments_photo (fk_photo)
    REFERENCES photo (photo_id);
-- Reference:  attachments_watchr_event (table: attachment)


ALTER TABLE attachment ADD CONSTRAINT attachments_watchr_event FOREIGN KEY attachments_watchr_event (fk_event)
    REFERENCES watchr_event (event_id);
-- Reference:  conversation_conversation_status (table: conversation)


ALTER TABLE conversation ADD CONSTRAINT conversation_conversation_status FOREIGN KEY conversation_conversation_status (fk_conversation_status)
    REFERENCES conversation_status (id);
-- Reference:  conversation_reply_conversation (table: conversation_reply)


ALTER TABLE conversation_reply ADD CONSTRAINT conversation_reply_conversation FOREIGN KEY conversation_reply_conversation (fk_conversation)
    REFERENCES conversation (conversation_id);
-- Reference:  conversation_reply_reply_status (table: conversation_reply)


ALTER TABLE conversation_reply ADD CONSTRAINT conversation_reply_reply_status FOREIGN KEY conversation_reply_reply_status (fk_reply_status)
    REFERENCES reply_status (id);
-- Reference:  conversation_reply_user_profile (table: conversation_reply)


ALTER TABLE conversation_reply ADD CONSTRAINT conversation_reply_user_profile FOREIGN KEY conversation_reply_user_profile (fk_user)
    REFERENCES user_profile (user_id);
-- Reference:  conversation_watchr_event (table: conversation)


ALTER TABLE conversation ADD CONSTRAINT conversation_watchr_event FOREIGN KEY conversation_watchr_event (fk_watchr_event)
    REFERENCES watchr_event (event_id);
-- Reference:  institution_country_t (table: institution)


ALTER TABLE institution ADD CONSTRAINT institution_country_t FOREIGN KEY institution_country_t (fk_country)
    REFERENCES country_t (country_id);
-- Reference:  photo_photo_type (table: photo)


ALTER TABLE photo ADD CONSTRAINT photo_photo_type FOREIGN KEY photo_photo_type (fk_photo_type)
    REFERENCES photo_type (photo_type_id);
-- Reference:  position_institution (table: institution)


ALTER TABLE institution ADD CONSTRAINT position_institution FOREIGN KEY position_institution (fk_location)
    REFERENCES position (position_id);
-- Reference:  profile_device_device (table: profile_device)


ALTER TABLE profile_device ADD CONSTRAINT profile_device_device FOREIGN KEY profile_device_device (fk_device)
    REFERENCES device (device_id);
-- Reference:  profile_device_user_profile (table: profile_device)


ALTER TABLE profile_device ADD CONSTRAINT profile_device_user_profile FOREIGN KEY profile_device_user_profile (fk_user_profile)
    REFERENCES user_profile (user_id);
-- Reference:  rating_user_profile (table: rating)


ALTER TABLE rating ADD CONSTRAINT rating_user_profile FOREIGN KEY rating_user_profile (user_id)
    REFERENCES user_profile (user_id);
-- Reference:  rating_watchr_event (table: rating)


ALTER TABLE rating ADD CONSTRAINT rating_watchr_event FOREIGN KEY rating_watchr_event (event_id)
    REFERENCES watchr_event (event_id);
-- Reference:  user_country_t (table: user_profile)


ALTER TABLE user_profile ADD CONSTRAINT user_country_t FOREIGN KEY user_country_t (fk_country)
    REFERENCES country_t (country_id);
-- Reference:  user_gender (table: user_profile)


ALTER TABLE user_profile ADD CONSTRAINT user_gender FOREIGN KEY user_gender (fk_gender)
    REFERENCES gender (gender_id);
-- Reference:  user_profile_profile_status (table: user_profile)


ALTER TABLE user_profile ADD CONSTRAINT user_profile_profile_status FOREIGN KEY user_profile_profile_status (fk_profile_status)
    REFERENCES profile_status (profile_status_id);
-- Reference:  watchr_category_watchr_category (table: watchr_category)


ALTER TABLE watchr_category ADD CONSTRAINT watchr_category_watchr_category FOREIGN KEY watchr_category_watchr_category (fk_subcategory)
    REFERENCES watchr_category (category_id);
-- Reference:  watchr_event_event_status (table: watchr_event)


ALTER TABLE watchr_event ADD CONSTRAINT watchr_event_event_status FOREIGN KEY watchr_event_event_status (fk_event_status)
    REFERENCES event_status (status_id);
-- Reference:  watchr_event_position (table: watchr_event)


ALTER TABLE watchr_event ADD CONSTRAINT watchr_event_position FOREIGN KEY watchr_event_position (fk_location)
    REFERENCES position (position_id);
-- Reference:  watchr_event_user_profile (table: watchr_event)


ALTER TABLE watchr_event ADD CONSTRAINT watchr_event_user_profile FOREIGN KEY watchr_event_user_profile (fk_created_by_user)
    REFERENCES user_profile (user_id);
-- Reference:  watchr_event_watchr_category (table: watchr_event)


ALTER TABLE watchr_event ADD CONSTRAINT watchr_event_watchr_category FOREIGN KEY watchr_event_watchr_category (fk_event_category)
    REFERENCES watchr_category (category_id);

-- End of file.

