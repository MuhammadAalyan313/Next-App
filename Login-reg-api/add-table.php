<?php 

include('db.php');

$query = "ALTER TABLE user_subscriptions ADD COLUMN password VARCHAR(64) NOT NULL";


$fired = mysqli_query($con, $query);
if($fired){    
echo json_encode([
    'message' => 'Table created successfully..!',
    'status' => 'success',
]);

}
else{
    echo json_encode([
        'message' => 'Table not created..!',
        'status' => 'error',
    ]);
    
}

?>