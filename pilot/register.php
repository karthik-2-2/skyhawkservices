<?php

include('../config/db.php');
include('../config/security_questions.php');

// Get random security questions for display
$security_questions = getRandomSecurityQuestions($conn, 3);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form inputs
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $pilot_license_number = $_POST['pilot_license_number'];
    $aadhaar_number = $_POST['aadhaar_number'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Collect security answers
    $security_answers = [];
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST["question_$i"]) && isset($_POST["answer_$i"])) {
            $security_answers[] = [
                'question_id' => $_POST["question_$i"],
                'answer' => trim($_POST["answer_$i"])
            ];
        }
    }

    // Validate security answers
    if (count($security_answers) != 3) {
        $error_message = "Please answer all security questions!";
    } else {
        // Upload folder
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Function to generate new filename based on field name and phone number
        function generateFileName($fieldName, $phone, $originalName) {
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            // Sanitize phone to avoid unwanted characters in filename
            $safePhone = preg_replace('/[^0-9]/', '', $phone);
            return $fieldName . '_' . $safePhone . '.' . $ext;
        }

        // Handle file uploads with new names
        $dgca_license_photo_name = generateFileName('dgca_license_photo', $phone, $_FILES["dgca_license_photo"]["name"]);
        $address_photo_name = generateFileName('address_photo', $phone, $_FILES["address_photo"]["name"]);
        $person_photo_name = generateFileName('person_photo', $phone, $_FILES["person_photo"]["name"]);

        $dgca_license_photo_path = $upload_dir . $dgca_license_photo_name;
        $address_photo_path = $upload_dir . $address_photo_name;
        $person_photo_path = $upload_dir . $person_photo_name;

        // Move uploaded files to upload directory with new names
        move_uploaded_file($_FILES["dgca_license_photo"]["tmp_name"], $dgca_license_photo_path);
        move_uploaded_file($_FILES["address_photo"]["tmp_name"], $address_photo_path);
        move_uploaded_file($_FILES["person_photo"]["tmp_name"], $person_photo_path);

        // Check if phone exists (PDO)
        $check_query = "SELECT * FROM pilot WHERE phone = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->execute([$phone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $error_message = "Phone number already exists!";
        } else {
            $insert_query = "INSERT INTO pilot (name, email, phone, address, password, pilot_license_number, dgca_license_photo, aadhaar_number, address_photo, person_photo) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->execute([
              $name, $email, $phone, $address, $password, $pilot_license_number, $dgca_license_photo_path, $aadhaar_number, $address_photo_path, $person_photo_path
            ]);
            
            // Save security answers
            if (savePilotSecurityAnswers($conn, $phone, $security_answers)) {
                header('Location: index.php');
                exit();
            } else {
                $error_message = "Registration failed! Could not save security questions.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Signup Page</title>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    /* Your existing CSS here (same as before) */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    html, body {
      background-color: #000;
      color: #c0c0c0; /* Changed from #fff to metallic silver */
      min-height: 100vh; /* Changed from height: 100% */
      overflow-y: auto;
    }
    body {
      display: flex;
      justify-content: center;
      align-items: center; /* Back to center for better positioning */
      padding: 20px;
    }
    .container {
      width: 100%;
      max-width: 850px;
      min-width: 320px;
      min-height: 100vh;
    }
    .login-box {
      display: flex;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 20px #39ff14;
      flex-wrap: nowrap;
      background-color: #000;
    }
    .left-panel,
    .right-panel {
      padding: 40px 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .left-panel {
      width: 50%;
      background-color: #000;
      z-index: 1;
    }
    .left-panel h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      color: #39ff14;
    }
    .input-box {
      position: relative;
      margin-bottom: 25px;
    }
    .input-box input {
      width: 100%;
      padding: 12px 40px 12px 10px;
      background-color: transparent;
      border: none;
      border-bottom: 1px solid #ccc;
      color: #fff;
      font-size: 16px;
      outline: none;
    }
    .input-box i {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #ccc;
    }
    button {
      width: 100%;
      padding: 12px;
      border: none;
      background: linear-gradient(135deg, #39ff14, #c0c0c0);
      color: black;
      font-weight: bold;
      font-size: 16px;
      border-radius: 25px;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px #39ff14;
    }
    .signup-text {
      margin-top: 15px;
      text-align: center;
      font-size: 14px;
      white-space: nowrap;
    }
    .signup-text a {
      color: #39ff14;
      text-decoration: none;
      margin-left: 4px;
    }
    .right-panel {
      width: 50%;
      background: linear-gradient(-45deg, #39ff14, #c0c0c0, #000, #39ff14);
      background-size: 400% 400%;
      animation: gradientBG 10s ease infinite;
      color: white;
      text-align: left;
      display: flex;
      align-items: center;
      justify-content: center; /* Changed from right to center */
      padding: 30px; /* Changed from padding-right to all sides */
      /* Removed clip-path completely */
    }
    .right-panel h1 {
      font-size: 32px;
      line-height: 1.2;
      text-shadow: 0 0 10px rgba(61, 201, 36, 0.3);
    }
    .right-panel h1 span {
      font-weight: bold;
      display: block;
    }
    .right-panel p {
      margin-top: 20px;
      font-size: 14px;
      opacity: 0.9;
    }
    @keyframes gradientBG {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }
    @media (max-width: 768px) {
      .login-box {
        flex-direction: column; /* Stack vertically on mobile */
        overflow-x: visible;
      }

      .left-panel, .right-panel {
        padding: 20px;
        width: 100%;
      }

      /* Welcome panel on top, remove clip-path for mobile */
      .right-panel {
        order: -1; /* Move to top */
        clip-path: none; /* Remove diagonal cut */
        padding: 30px 20px;
        text-align: center;
        justify-content: center;
      }

      .signup-text {
        font-size: 13px;
        white-space: normal;
      }

      .right-panel h1 {
        font-size: 24px;
      }

      .right-panel p {
        font-size: 14px;
      }
    }
    @media (max-width: 480px) {
      .login-box {
        transform: scale(0.75);
        transform-origin: center top;
      }
      .left-panel h2 {
        font-size: 22px;
      }
      .right-panel h1 {
        font-size: 20px;
      }
      .signup-text {
        font-size: 12px;
        white-space: nowrap;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="login-box">
      <div class="left-panel">
        <h2>Pilot Registration</h2>

        <?php if (isset($error_message)): ?>
          <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form id="signupForm" method="POST" action="" enctype="multipart/form-data">
          <div class="input-box">
            <input type="text" name="name" placeholder="Name" required />
            <i class="fas fa-user"></i>
          </div>
          <div class="input-box">
            <input type="email" name="email" placeholder="Email" required />
            <i class="fas fa-envelope"></i>
          </div>
          <div class="input-box">
            <input type="text" name="phone" placeholder="Phone" required />
            <i class="fas fa-phone"></i>
          </div>
          <div class="input-box">
            <input type="text" name="address" placeholder="Address" required />
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <div class="input-box">
            <input type="password" name="password" placeholder="Password" required />
            <i class="fas fa-lock"></i>
          </div>
          <div class="input-box">
            <input type="text" name="pilot_license_number" placeholder="Pilot License Number" required />
            <i class="fas fa-id-card"></i>
          </div>
          <div class="input-box">
            <input type="text" name="aadhaar_number" placeholder="Aadhaar Number" required />
            <i class="fas fa-address-card"></i>
          </div>
          <div class="input-box">
            <label style="color:#ccc;">DGCA License Photo</label>
            <input type="file" name="dgca_license_photo" accept="image/*" required />
          </div>
          <div class="input-box">
            <label style="color:#ccc;">Address Photo</label>
            <input type="file" name="address_photo" accept="image/*" required />
          </div>
          <div class="input-box">
            <label style="color:#ccc;">Your Photo</label>
            <input type="file" name="person_photo" accept="image/*" required />
          </div>
          
          <!-- Security Questions Section -->
          <div style="margin: 20px 0; padding: 15px; border: 1px solid #39ff14; border-radius: 15px; background: rgba(57, 255, 20, 0.1);">
            <h3 style="color: #39ff14; margin-bottom: 15px; text-align: center; font-size: 16px;">Security Questions</h3>
            <p style="color: #c0c0c0; font-size: 12px; text-align: center; margin-bottom: 15px;">Please answer these questions for account recovery</p>
            
            <?php for ($i = 0; $i < 3; $i++): ?>
              <div class="input-box" style="margin-bottom: 15px;">
                <label style="color: #c0c0c0; font-size: 13px; display: block; margin-bottom: 5px;">
                  <?php echo htmlspecialchars($security_questions[$i]['question']); ?>
                </label>
                <input type="hidden" name="question_<?php echo $i+1; ?>" value="<?php echo $security_questions[$i]['id']; ?>" />
                <input type="text" name="answer_<?php echo $i+1; ?>" placeholder="Your answer" required 
                       style="border-bottom: 1px solid #c0c0c0; background: transparent; color: #c0c0c0; padding: 8px 0; font-size: 14px;" />
              </div>
            <?php endfor; ?>
          </div>

          <button type="submit">Register</button>
          <p class="signup-text">
            Already have an account? <a href="index.php">Login</a>
          </p>
        </form>
      </div>
      <div class="right-panel">
        <h1>CREATE<br /><span>ACCOUNT!</span></h1>
        <p>Join us today and become a certified pilot with all your documents organized and stored securely.</p>
      </div>
    </div>
  </div>
</body>
</html>
