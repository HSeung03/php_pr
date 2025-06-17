CREATE DATABASE IF NOT EXISTS board_login;
USE board_login;

-- 테이블 삭제 (순서 중요: 자식 테이블 먼저)
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS board;

-- 게시판 테이블
CREATE TABLE board (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT '작성자',
    password VARCHAR(100) NOT NULL COMMENT '비밀번호',
    subject VARCHAR(200) NOT NULL COMMENT '제목',
    content TEXT NOT NULL COMMENT '내용',
    regdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '작성일'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 댓글 테이블 (대댓글 포함)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_id INT NOT NULL COMMENT '게시글 ID',
    parent_id INT DEFAULT NULL COMMENT '부모 댓글 ID (NULL이면 일반 댓글)',

    author VARCHAR(100) NOT NULL COMMENT '작성자',
    password VARCHAR(100) NOT NULL COMMENT '비밀번호',
    content TEXT NOT NULL COMMENT '댓글 내용',

    regdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '작성일',
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',

    FOREIGN KEY (board_id) REFERENCES board(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 인덱스
CREATE INDEX idx_board_id ON comments(board_id);
CREATE INDEX idx_parent_id ON comments(parent_id);
