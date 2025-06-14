<?php
// 记录文件路径
$filename = 'lottery_records.txt';

// 处理保存操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $newContent = $_POST['content'] ?? '';
    
    if (file_put_contents($filename, $newContent, LOCK_EX) !== false) {
        $message = "保存成功";
    } else {
        $message = "保存失败";
    }
}

// 读取记录文件
$content = '';
if (file_exists($filename)) {
    $content = file_get_contents($filename);
}

// 分割记录为数组
$records = array_filter(explode("\n", $content));
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>抽奖记录管理</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E40AF',
                        secondary: '#3B82F6',
                        accent: '#60A5FA',
                        dark: '#1E293B',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-primary text-white p-6">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fa fa-trophy mr-3"></i>抽奖记录管理
                </h1>
                <p class="text-blue-100 mt-1">查看和编辑抽奖记录</p>
            </div>
            
            <div class="p-6">
                <?php if (isset($message)): ?>
                    <div class="mb-4 p-3 rounded-lg <?php echo strpos($message, '成功') !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-dark">抽奖记录列表</h2>
                    <div class="flex space-x-2">
                        <button id="toggle-edit" class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-primary transition-colors">
                            <i class="fa fa-pencil mr-2"></i>编辑模式
                        </button>
                        <button id="save-btn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors hidden">
                            <i class="fa fa-save mr-2"></i>保存修改
                        </button>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div id="records-view" class="space-y-2">
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $index => $record): ?>
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex justify-between items-center">
                                    <span><?php echo htmlspecialchars($record); ?></span>
                                    <button class="text-red-500 hover:text-red-700 delete-record" data-index="<?php echo $index; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-4 bg-gray-50 rounded-lg text-center text-gray-500">
                                暂无抽奖记录
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div id="records-edit" class="hidden">
                        <form method="POST" action="admin.php">
                            <textarea name="content" rows="15" class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all" placeholder="抽奖记录内容..."><?php echo htmlspecialchars($content); ?></textarea>
                            <div class="mt-4">
                                <button type="submit" name="save" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                    <i class="fa fa-save mr-2"></i>保存修改
                                </button>
                                <button type="button" id="cancel-edit" class="ml-3 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                    取消
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-600">
                    <p><i class="fa fa-info-circle mr-2"></i>记录格式：日期时间 - 奖项名称</p>
                    <p class="mt-1"><i class="fa fa-warning mr-2"></i>直接编辑模式下请小心不要删除或修改格式，以免造成数据混乱</p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 text-center text-gray-500 text-sm">
            <a href="index.html" class="text-primary hover:underline">返回抽奖页面</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleEditBtn = document.getElementById('toggle-edit');
            const saveBtn = document.getElementById('save-btn');
            const recordsView = document.getElementById('records-view');
            const recordsEdit = document.getElementById('records-edit');
            const cancelEditBtn = document.getElementById('cancel-edit');
            const deleteButtons = document.querySelectorAll('.delete-record');
            
            // 切换编辑模式
            toggleEditBtn.addEventListener('click', () => {
                recordsView.classList.toggle('hidden');
                recordsEdit.classList.toggle('hidden');
                toggleEditBtn.classList.toggle('hidden');
                saveBtn.classList.toggle('hidden');
            });
            
            // 取消编辑
            cancelEditBtn.addEventListener('click', () => {
                recordsView.classList.remove('hidden');
                recordsEdit.classList.add('hidden');
                toggleEditBtn.classList.remove('hidden');
                saveBtn.classList.add('hidden');
            });
            
            // 删除记录
            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    if (confirm('确定要删除这条记录吗？')) {
                        const index = button.getAttribute('data-index');
                        // 获取当前记录内容
                        const textarea = document.querySelector('textarea[name="content"]');
                        const lines = textarea.value.split('\n');
                        
                        // 删除指定行
                        lines.splice(index, 1);
                        
                        // 更新文本区域内容
                        textarea.value = lines.join('\n');
                        
                        // 隐藏记录视图，显示编辑视图
                        recordsView.classList.add('hidden');
                        recordsEdit.classList.remove('hidden');
                        toggleEditBtn.classList.add('hidden');
                        saveBtn.classList.remove('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>
    
