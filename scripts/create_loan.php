<?php
// create_loan.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require_once __DIR__ . '/db/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if borrower_id is set in the $_POST array
    if (!isset($_POST['nationalId'])) {
        die('National ID is not set.');
    }

    $borrower_id = $_POST['nationalId'];
    $loan_amount = $_POST['loan_amount'];
    $loan_date = $_POST['loan_date'];
    $due_date = $_POST['due_date'];
    $loan_type = $_POST['loan_type'];

    // Server-side validation
    if ($loan_amount < 5000) {
        die('Loan amount cannot be less than 5,000.');
    }

    // Calculate loan details
    $interest_rate = 0.1;
    $loan_period = 30;
    $interest = $loan_amount * $interest_rate;
    $repayment_amount = $loan_amount + $interest;
    $loan_balance = $repayment_amount;

    // Insert loan into database
    $sql = "INSERT INTO loans (borrower_id, loan_amount, interest_rate, loan_period, loan_date, due_date, loan_balance, loan_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisssss", $borrower_id, $loan_amount, $interest_rate, $loan_period, $loan_date, $due_date, $loan_balance, $loan_type);

    if ($stmt->execute()) {
        // Set a session variable with the success message
        $_SESSION['success_message'] = 'Loan created successfully.';

        // Redirect the user back to the create_loan.html page
        header('Location: dashboard.php');
    } else {
        echo 'Failed to create loan.';
    }

    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>