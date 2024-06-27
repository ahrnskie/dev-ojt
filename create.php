<?php
// Include config file
require_once "./db/config.php";
 
// Define variables and initialize with empty values
$accomplishments = $date = $evidences = $remarks = "";
$accomplishments_err = $date_err = $evidences_err = $remarks_err = "";

// Flag to check if the form was submitted successfully
$form_submitted = false;
$duplicate_record = false;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate accomplishments
    $input_accomplishments = trim($_POST["accomplishments"]);
    if(empty($input_ccomplishments)){
        $accomplishments_err = "Please enter a Last middle name.";
    } elseif(!filter_var($input_accomplishments, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $accomplishments_err = "Please enter a valid middle name.";
    } else{
        $accomplishments = $input_accomplishments;
    }
    
    // Validate date
    $input_date = trim($_POST["date"]);
    if(empty($input_date)){
        $first_date_err = "Please enter a first name.";
    } elseif(!filter_var($input_date, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $date_err = "Please enter a valid middle name.";
    } else{
        $date = $input_date;
    }

    // Validate evidences
    $input_evidences = trim($_POST["evidences"]);
    if(empty($input_evidences)){
        $middle_evidences_err = "Please enter a middle name.";
    } elseif(!filter_var($input_evidences, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $evidences_err = "Please enter a valid middle name.";
    } else{
        $evidences = $input_evidences;
    }

    // Validate remarks 
    $input_remarks = trim($_POST["remarks"]);
    if(empty($input_remarks)){
        $remarks_err = "Please enter an address.";     
    } else{
        $remarks = $input_remarks;
    }
    


    // Check input errors before inserting in database
    if(empty($last_name_err) && empty($first_name_err) && empty($middle_name_err) && empty($address_err) && empty($salary_err)){
        // Check for duplicate record
        $sql = "SELECT COUNT(*) FROM employees WHERE last_name = :last_name AND first_name = :first_name AND middle_name = :middle_name AND address = :address AND salary = :salary";
 
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_accomplishments = $accomplishments;
            $param_date = $date;
            $param_evidences = $evidences;
            $param_remarks = $remarks;
           
            
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":accomplishments", $param_accomplishments);
            $stmt->bindParam(":date", $param_date);
            $stmt->bindParam(":evidences", $param_evidences);
            $stmt->bindParam(":remarks", $param_remarks);


            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if ($stmt->fetchColumn() > 0) {
                    // Duplicate record found
                    $duplicate_record = true;
                } else {
                    // Prepare an insert statement
                    $sql = "INSERT INTO employees (last_name, first_name, middle_name, address, salary) VALUES (:last_name, :first_name, :middle_name, :address, :salary)";
 
                    if ($stmt = $pdo->prepare($sql)) {
                        // Set parameters
                        $param_accomplishments = $accomplishments;
                        $param_date = $date;
                        $param_evidences = $evidences;
                        $param_remarks = $remarks;

                        // Bind variables to the prepared statement as parameters
                        $stmt->bindParam(":accomplishments", $param_accomplishments);
                        $stmt->bindParam(":date", $param_date);
                        $stmt->bindParam(":evidences", $param_evidences);
                        $stmt->bindParam(":remarks", $param_remarks);

                        // Attempt to execute the prepared statement
                        if ($stmt->execute()) {
                            // Set the form submission flag to true
                            $form_submitted = true;
                            $accomplishments = $date = $evidences = $remarks = "";;
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }    
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        .toast-container {
            position: fixed;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Create Record</h2>
                    <p>Please fill this form and submit to add employee record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Accomplishments</label>
                            <input type="text" name="accomplishments" class="form-control <?php echo (!empty($accomplishments_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $accomplishments; ?>">
                            <span class="invalid-feedback"><?php echo $accomplishments_err;?></span>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="text" name="date" class="form-control <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $date; ?>">
                            <span class="invalid-feedback"><?php echo $date_err;?></span>
                        </div>

                        <div class="form-group">
                            <label>Evidences</label>
                            <input type="text" name="evidences" class="form-control <?php echo (!empty($evidences_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $evidences; ?>">
                            <span class="invalid-feedback"><?php echo $evidences_err;?></span>
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control <?php echo (!empty($remarks_err)) ? 'is-invalid' : ''; ?>"><?php echo $remarks; ?></textarea>
                            <span class="invalid-feedback"><?php echo $remarks_err;?></span>
                        </div>

                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="dashboard.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

        <!-- Toast HTML -->
        <div class="toast-container">
            <div id="successToast" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    New record added successfully!
                </div>
            </div>

            <div id="duplicateToast" class="toast text-bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Duplicate</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Duplicate record found. No new record added.
                </div>
            </div>
        </div>

        <!-- Trigger Toast JS -->
        <?php if ($form_submitted): ?>
        <script type="text/javascript">
            var successToastEl = document.getElementById('successToast');
            var successToast = new bootstrap.Toast(successToastEl);
            successToast.show();
        </script>
        <?php elseif ($duplicate_record): ?>
        <script type="text/javascript">
            var duplicateToastEl = document.getElementById('duplicateToast');
            var duplicateToast = new bootstrap.Toast(duplicateToastEl);
            duplicateToast.show();
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
