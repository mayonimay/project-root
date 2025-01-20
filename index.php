<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Structure Analyzer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>File Structure Analyzer</h1>
        
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert <?php echo $_SESSION['message_type']; ?>">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <form action="process.php" method="post" enctype="multipart/form-data">
            <div class="upload-area">
                <input type="file" name="zipfile" accept=".zip" required>
                <button type="submit" name="submit">Upload & Create Structure</button>
            </div>
        </form>

        <div id="structure-result">
            <?php
            if(isset($_SESSION['structure'])) {
                echo '<div class="structure-header">';
                echo '<button onclick="copyStructure()" class="copy-btn">Copy Structure</button>';
                echo '</div>';
                echo $_SESSION['structure'];
                unset($_SESSION['structure']);
            }
            ?>
        </div>

        <script>
        function copyStructure() {
            const pre = document.querySelector('#structure-result pre');
            if (!pre) return;
            
            // Membuat text area temporary
            const textarea = document.createElement('textarea');
            textarea.value = pre.textContent;
            document.body.appendChild(textarea);
            
            // Select dan copy
            textarea.select();
            document.execCommand('copy');
            
            // Hapus text area temporary
            document.body.removeChild(textarea);
            
            // Tampilkan feedback
            const btn = document.querySelector('.copy-btn');
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }
        </script>
    </div>
</body>
</html> 