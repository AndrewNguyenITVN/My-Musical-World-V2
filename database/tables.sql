-- Tạo database
--CREATE DATABASE music_db;
--USE music_db;

-- Bảng người dùng
CREATE TABLE `user` (
    `user_id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `mobile_number` VARCHAR(10) NOT NULL,
    `email_address` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `activation_code` VARCHAR(255) NOT NULL DEFAULT '0',
    `confirm_status` INT(1) DEFAULT '0',
    `contributions` INT NOT NULL DEFAULT '0',
    `reset_token` VARCHAR(255) DEFAULT NULL,
    `reset_token_expiry` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`)
);

-- Đặt AUTO_INCREMENT bắt đầu từ 100
ALTER TABLE `user` AUTO_INCREMENT = 100;

-- Bảng thể loại
CREATE TABLE `category` (
    `cat_id` INT NOT NULL AUTO_INCREMENT,
    `cat_name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`cat_id`)
);

-- Bảng bài hát duy nhất
CREATE TABLE `songs` (
    `song_id` INT NOT NULL AUTO_INCREMENT,
    `singer_id` INT,
    `cat_id` INT NOT NULL,
    `song_name` VARCHAR(255) NOT NULL,
    `singer_name` VARCHAR(100) NOT NULL,
    `song_image` VARCHAR(255) NOT NULL,
    `audio_file` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`song_id`),
    FOREIGN KEY (`cat_id`) REFERENCES `category`(`cat_id`) ON DELETE CASCADE,
    FOREIGN KEY (`singer_id`) REFERENCES `user`(`user_id`) ON DELETE SET NULL
);

-- Bảng bài hát yêu thích
CREATE TABLE `favorite_songs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `song_id` INT NOT NULL,
    UNIQUE (`user_id`, `song_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`song_id`) REFERENCES `songs`(`song_id`) ON DELETE CASCADE
);


-- Trigger tăng contributions khi upload
DELIMITER $$

CREATE TRIGGER `IncrementCount`
AFTER INSERT ON `songs`
FOR EACH ROW
BEGIN
    IF NEW.singer_id IS NOT NULL THEN
        UPDATE user 
        SET contributions = contributions + 1 
        WHERE user_id = NEW.singer_id;
    END IF;
END$$

DELIMITER ;

-- Procedure upload bài hát
DELIMITER $$
CREATE PROCEDURE `upload_song`(
    IN `singer_id` INT,
    IN `cat_id` INT,
    IN `song_name` VARCHAR(255),
    IN `singer_name` VARCHAR(100),
    IN `song_image` VARCHAR(255),
    IN `audio_file` VARCHAR(255)
)
BEGIN
    INSERT INTO `songs`(`singer_id`, `cat_id`, `song_name`, `singer_name`, `song_image`, `audio_file`)
    VALUES (singer_id, cat_id, song_name, singer_name, song_image, audio_file);
END$$
DELIMITER ;

INSERT INTO
    `user` (
        `user_id`,
        `username`,
        `mobile_number`,
        `email_address`,
        `password`,
        `confirm_status`
    )
VALUES
    (
        1,
        'Admin',
        '012345678',
        'admin@gmail.com',
        '21232f297a57a5a743894a0e4a801fc3',
        1
    ),
    (
        2,
        'admin2',
        '012345678',
        'mymusicworld@gmail.com',
        '21232f297a57a5a743894a0e4a801fc3',
        1
    ),
    (
        100,
        'Tan Dat',
        '0399667938',
        'datB2203438@student.ctu.edu.vn',
        '5f6dc80c9e71da72aa801215f6d61a5e',
        1
    ),
    (
        101,
        'Minh Nhut',
        '0389612044',
        'nhutB2205896@student.ctu.edu.vn',
        '21232f297a57a5a743894a0e4a801fc3',
        1
    );

INSERT INTO
    `category` (`cat_id`, `cat_name`)
VALUES
    ('2', 'vietnam_albums'),
    ('3', 'english_albums'),
    ('4', 'upload_albums');


INSERT INTO `songs` (`singer_id`, `cat_id`, `song_name`, `singer_name`, `song_image`, `audio_file`) VALUES
(1, 2, 'Lạc Trôi', 'SƠN TÙNG MPT', 'Lạc trôi.jpg', 'Lạc trôi.mp3'),
(1, 2, 'Nơi Này Có Anh', 'SƠN TÙNG MPT', 'NƠI NÀY CÓ ANH.jpg', 'NƠI NÀY CÓ ANH.mp3'),
(1, 2, 'Âm Thầm Bên Em', 'SƠN TÙNG MPT', 'Âm Thầm Bên Em.jpg', 'Âm Thầm Bên Em.mp3'),
(1, 2, 'Chúng Ta Không Thuộc Về Nhau', 'SƠN TÙNG MPT', 'Chúng Ta Không Thuộc Về Nhau.jpg', 'Chúng Ta Không Thuộc Về Nhau.mp3'),
(1, 2, 'Đừng Làm Trái Tim Anh Đau', 'SƠN TÙNG MPT', 'ĐỪNG LÀM TRÁI TIM ANH ĐAU.jpg', 'ĐỪNG LÀM TRÁI TIM ANH ĐAU.mp3'),
(1, 2, 'Chúng Ta Của Tương Lai', 'SƠN TÙNG MPT', 'CHÚNG TA CỦA TƯƠNG LAI.jpg', 'CHÚNG TA CỦA TƯƠNG LAI.mp3'),
(1, 2, 'Theres No One At All', 'SƠN TÙNG MPT', 'THERES NO ONE AT ALL.jpg', 'THERES NO ONE AT ALL.mp3'),
(1, 2, 'Không Phải Dạng Vừa Đâu', 'SƠN TÙNG MPT', 'Không Phải Dạng Vừa Đâu.jpg', 'Không Phải Dạng Vừa Đâu.mp3'),
(1, 2, 'Bên Trên Tầng Lầu', 'TĂNG DUY TĂNG', 'BÊN TRÊN TẦNG LẦU.jpg', 'BÊN TRÊN TẦNG LẦU.mp3'),
(1, 2, 'Anh Đã Loop Trong Niềm Đau Này', 'TĂNG DUY TĂNG', 'Anh Đã Loop Trong Niềm Đau Này.jpg', 'Anh Đã Loop Trong Niềm Đau Này.mp3'),
(1, 2, 'Ikigai', 'TĂNG DUY TĂNG', 'Ikigai.jpg', 'Ikigai.mp3');


INSERT INTO `songs` (`singer_id`, `cat_id`, `song_name`, `singer_name`, `song_image`, `audio_file`) VALUES
(1, 3, 'Teeth (Lyric Video)', '5 Seconds of Summer', '5 Seconds of Summer - Teeth (Lyric Video).jpg', '5 Seconds of Summer - Teeth (Lyric Video).mp3'),
(1, 3, 'Lily ft. K-391 & Emelie Hollow (Official Lyric Video)', 'Alan Walker', 'Alan Walker - Lily ft. K-391 & Emelie Hollow (Official Lyric Video).jpg', 'Alan Walker - Lily ft. K-391 & Emelie Hollow (Official Lyric Video).mp3'),
(1, 3, 'The Nights (Lyrics)', 'Avicii', 'Avicii - The Nights (Lyrics).jpg', 'Avicii - The Nights (Lyrics).mp3'),
(1, 3, 'Bad Liar (Lyric Video)', 'Imagine Dragons', 'Imagine Dragons - Bad Liar (Lyric Video).jpg', 'Imagine Dragons - Bad Liar (Lyric Video).mp3'),
(1, 3, 'Believer (Official Music Video)', 'Imagine Dragons', 'Imagine Dragons - Believer (Official Music Video).jpg', 'Imagine Dragons - Believer (Official Music Video).mp3'),
(1, 3, 'Natural (Lyrics)', 'Imagine Dragons', 'Imagine Dragons - Natural (Lyrics).jpg', 'Imagine Dragons - Natural (Lyrics).mp3'),
(1, 3, 'One Day (Official Lyric Video)', 'Imagine Dragons', 'Imagine Dragons - One Day (Official Lyric Video).jpg', 'Imagine Dragons - One Day (Official Lyric Video).mp3'),
(1, 3, 'Radioactive (Lyric Video)', 'Imagine Dragons', 'Imagine Dragons - Radioactive (Lyric Video).jpg', 'Imagine Dragons - Radioactive (Lyric Video).mp3'),
(1, 3, 'Sharks (Official Lyric Video)', 'Imagine Dragons', 'Imagine Dragons - Sharks (Official Lyric Video).jpg', 'Imagine Dragons - Sharks (Official Lyric Video).mp3'),
(1, 3, 'Take Me To The Beach (feat. Ado) (Official Lyric Video)', 'Imagine Dragons', 'Imagine Dragons - Take Me To The Beach (feat. Ado) (Official Lyric Video).jpg', 'Imagine Dragons - Take Me To The Beach (feat. Ado) (Official Lyric Video).mp3'),
(1, 3, 'Thunder', 'Imagine Dragons', 'Imagine Dragons - Thunder.jpg', 'Imagine Dragons - Thunder.mp3');

-- Các bài hát do người dùng upload 
INSERT INTO `songs` (`singer_id`, `cat_id`, `song_name`, `singer_name`, `song_image`, `audio_file`) VALUES
(100, 3, 'Whatever It Takes (Official Music Video)', 'Imagine Dragons', 'Imagine Dragons - Whatever It Takes (Official Music Video).jpg', 'Imagine Dragons - Whatever It Takes (Official Music Video).mp3'),
(100, 3, 'Wrecked (Official Music Video)', 'Imagine Dragons', 'Imagine Dragons - Wrecked (Official Music Video).jpg', 'Imagine Dragons - Wrecked (Official Music Video).mp3'),
(100, 3, 'Wellerman (Sea Shanty)', 'Nathan Evans', 'Nathan Evans  Wellerman Sea Shanty.jpg', 'Nathan Evans  Wellerman Sea Shanty.mp3'),
(101, 3, 'Stressed Out', 'twenty one pilots', 'twenty one pilots - Stressed Out.jpg', 'twenty one pilots - Stressed Out.mp3'),
(101, 3, 'Let me down slowly', 'Alec Benjamin', '_Alec Benjamin - Let Me Down Slowly (feat Alessia Cara) [Lyrics-Vietsub].jpg', '_Alec Benjamin - Let Me Down Slowly (feat Alessia Cara) [Lyrics-Vietsub].mp3'),
(101, 3, 'Want you all the time', 'Bryant Barnes', 'Bryant Barnes - Want You All The Time (Lyrics).jpg', 'Bryant Barnes - Want You All The Time (Lyrics).mp3'),
(101, 3, 'Head In The Clouds (Official Video)', 'Hayd', 'Hayd - Head In The Clouds (Official Video).png', 'Hayd - Head In The Clouds (Official Video).mp3');
