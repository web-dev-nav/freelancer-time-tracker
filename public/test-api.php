<?php
header('Content-Type: application/json');
echo json_encode(['status' => 'API working', 'timestamp' => date('Y-m-d H:i:s')]);
?>