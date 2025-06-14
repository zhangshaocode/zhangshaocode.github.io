<?php
// 记录文件路径
$filename = 'lottery_records.txt';

// 检查是否有POST数据
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取时间戳和奖品
    $timestamp = $_POST['timestamp'] ?? '';
    $prize = $_POST['prize'] ?? '';
    
    // 验证数据
    if (!empty($timestamp) && !empty($prize)) {
        // 准备记录数据
        $record = "{$timestamp} - {$prize}\n";
        
        // 写入文件
        if (file_put_contents($filename, $record, FILE_APPEND | LOCK_EX) !== false) {
            echo "记录成功";
        } else {
            http_response_code(500);
            echo "记录失败";
        }
    } else {
        http_response_code(400);
        echo "无效数据";
    }
} else {
    http_response_code(405);
    echo "方法不允许";
}
?>
    
