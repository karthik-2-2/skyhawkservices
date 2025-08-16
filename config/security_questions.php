<?php
// Security Questions Helper Functions

function getRandomSecurityQuestions($conn, $count = 3) {
    $query = "SELECT id, question FROM security_questions ORDER BY RANDOM() LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$count]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function saveUserSecurityAnswers($conn, $phone, $questionsAndAnswers) {
    try {
        $conn->beginTransaction();
        
        foreach ($questionsAndAnswers as $qa) {
            $query = "INSERT INTO user_security_answers (user_phone, question_id, answer) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$phone, $qa['question_id'], strtolower(trim($qa['answer']))]);
        }
        
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function savePilotSecurityAnswers($conn, $phone, $questionsAndAnswers) {
    try {
        $conn->beginTransaction();
        
        foreach ($questionsAndAnswers as $qa) {
            $query = "INSERT INTO pilot_security_answers (pilot_phone, question_id, answer) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$phone, $qa['question_id'], strtolower(trim($qa['answer']))]);
        }
        
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function getUserSecurityQuestion($conn, $phone) {
    $query = "SELECT usa.question_id, sq.question 
              FROM user_security_answers usa 
              JOIN security_questions sq ON usa.question_id = sq.id 
              WHERE usa.user_phone = ? 
              ORDER BY RANDOM() 
              LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$phone]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPilotSecurityQuestion($conn, $phone) {
    $query = "SELECT psa.question_id, sq.question 
              FROM pilot_security_answers psa 
              JOIN security_questions sq ON psa.question_id = sq.id 
              WHERE psa.pilot_phone = ? 
              ORDER BY RANDOM() 
              LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$phone]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function verifyUserSecurityAnswer($conn, $phone, $question_id, $answer) {
    $query = "SELECT answer FROM user_security_answers WHERE user_phone = ? AND question_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$phone, $question_id]);
    $stored_answer = $stmt->fetchColumn();
    
    return $stored_answer && strtolower(trim($answer)) === strtolower(trim($stored_answer));
}

function verifyPilotSecurityAnswer($conn, $phone, $question_id, $answer) {
    $query = "SELECT answer FROM pilot_security_answers WHERE pilot_phone = ? AND question_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$phone, $question_id]);
    $stored_answer = $stmt->fetchColumn();
    
    return $stored_answer && strtolower(trim($answer)) === strtolower(trim($stored_answer));
}

function userExists($conn, $phone) {
    $query = "SELECT phone FROM \"user\" WHERE phone = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$phone]);
    return $stmt->fetchColumn() !== false;
}

function pilotExists($conn, $phone) {
    $query = "SELECT phone FROM pilot WHERE phone = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$phone]);
    return $stmt->fetchColumn() !== false;
}

function updateUserPassword($conn, $phone, $new_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $query = "UPDATE \"user\" SET password = ? WHERE phone = ?";
    $stmt = $conn->prepare($query);
    return $stmt->execute([$hashed_password, $phone]);
}

function updatePilotPassword($conn, $phone, $new_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $query = "UPDATE pilot SET password = ? WHERE phone = ?";
    $stmt = $conn->prepare($query);
    return $stmt->execute([$hashed_password, $phone]);
}
?>
