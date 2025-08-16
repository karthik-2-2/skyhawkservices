-- Add security questions tables to the database

-- Table for predefined security questions
CREATE TABLE IF NOT EXISTS security_questions (
    id SERIAL PRIMARY KEY,
    question TEXT NOT NULL
);

-- Table for user security answers
CREATE TABLE IF NOT EXISTS user_security_answers (
    id SERIAL PRIMARY KEY,
    user_phone VARCHAR(15) NOT NULL,
    question_id INTEGER NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES security_questions(id),
    FOREIGN KEY (user_phone) REFERENCES "user"(phone) ON DELETE CASCADE
);

-- Table for pilot security answers
CREATE TABLE IF NOT EXISTS pilot_security_answers (
    id SERIAL PRIMARY KEY,
    pilot_phone VARCHAR(15) NOT NULL,
    question_id INTEGER NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES security_questions(id),
    FOREIGN KEY (pilot_phone) REFERENCES pilot(phone) ON DELETE CASCADE
);

-- Insert 20 memorable security questions
INSERT INTO security_questions (question) VALUES 
('What was the name of your first pet?'),
('In which city were you born?'),
('What was the name of your elementary school?'),
('What was your childhood nickname?'),
('What is the name of the street you grew up on?'),
('What was the make and model of your first car?'),
('What is your mother''s maiden name?'),
('What was the name of your first best friend?'),
('In which year did you graduate from high school?'),
('What was the name of your favorite childhood teacher?'),
('What is the name of the hospital where you were born?'),
('What was your favorite food as a child?'),
('What was the name of your first boss or manager?'),
('What is the name of the city where your parents met?'),
('What was your favorite subject in school?'),
('What was the name of your first childhood crush?'),
('What is the brand of your first mobile phone?'),
('What was the title of your favorite childhood book?'),
('What was the name of your first job or workplace?'),
('What is the name of your favorite childhood cartoon character?');
