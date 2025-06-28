-- 데이터베이스 생성 및 사용
CREATE DATABASE IF NOT EXISTS board_login;
USE board_login;

-- 게시글 테이블 제거 후 재생성
DROP TABLE IF EXISTS board;
CREATE TABLE board (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    regdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '작성일',
);

-- 댓글 테이블 제거 후 재생성
DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL COMMENT '게시글 ID',
    author VARCHAR(100) NOT NULL COMMENT '작성자',
    password VARCHAR(100) NOT NULL COMMENT '비밀번호',
    content TEXT NOT NULL COMMENT '댓글 내용',
    parent_id INT DEFAULT NULL COMMENT '부모 댓글 ID (NULL이면 일반 댓글)',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '작성일',

    FOREIGN KEY (post_id) REFERENCES board(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);
