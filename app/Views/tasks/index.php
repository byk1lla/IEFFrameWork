<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body { font-family: -apple-system, system-ui, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; background: #f9fafb; }
        .card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        h1 { color: #1f2937; margin-top: 0; }
        form { display: flex; gap: 0.5rem; margin-bottom: 2rem; }
        input[type="text"] { flex: 1; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; }
        button { padding: 0.75rem 1.5rem; background: #2563eb; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 500; }
        button:hover { background: #1d4ed8; }
        ul { list-style: none; padding: 0; margin: 0; }
        li { display: flex; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f3f4f6; }
        li:last-child { border-bottom: none; }
        .completed { text-decoration: line-through; color: #9ca3af; }
        .flex-1 { flex: 1; }
        .actions { display: flex; gap: 0.5rem; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .btn-check { background: #10b981; }
        .btn-check:hover { background: #059669; }
        .btn-delete { background: #ef4444; }
        .btn-delete:hover { background: #dc2626; }
    </style>
</head>
<body>
    <div class="card">
        <h1>📝 Yapılacaklar</h1>
        
        <form action="/tasks/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="text" name="title" placeholder="Yeni görev ekle..." required autocomplete="off">
            <button type="submit">Ekle</button>
        </form>

        <ul>
            <?php foreach ($tasks as $task): ?>
            <li>
                <span class="flex-1 <?= $task['is_completed'] ? 'completed' : '' ?>">
                    <?= htmlspecialchars($task['title']) ?>
                </span>
                <div class="actions">
                    <form action="/tasks/<?= $task['id'] ?>/update" method="POST" style="margin:0">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn-sm btn-check">
                            <?= $task['is_completed'] ? 'Geri Al' : 'Tamamla' ?>
                        </button>
                    </form>
                    <form action="/tasks/<?= $task['id'] ?>/delete" method="POST" style="margin:0">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn-sm btn-delete">Sil</button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>
            
            <?php if (empty($tasks)): ?>
            <li style="color: #6b7280; text-align: center; display: block;">Henüz görev eklenmedi.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
